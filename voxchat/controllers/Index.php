<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\Voxchat\Controllers;

use Modules\Voxchat\Mappers\Chat as ChatMapper;
use Modules\Voxchat\Models\Chat as ChatModel;

class Index extends \Ilch\Controller\Frontend
{
    public function indexAction()
    {
        $chatMapper = new ChatMapper();
        $config     = $this->getConfig();
        $user       = $this->getUser();

        $this->getLayout()->getHmenu()
            ->add($this->getTranslator()->trans('menuIrc'), ['action' => 'index']);

        $limit    = (int)($config->get('voxchat_chat_limit')   ?: 50);
        $messages = $chatMapper->checkDB() ? $chatMapper->getMessages($limit) : [];
        $maxId    = $chatMapper->checkDB() ? $chatMapper->getMaxId()          : 0;

        if ($user !== null && !empty($user->getGroups())) {
            $writeAccess = array_map(fn($g) => $g->getId(), $user->getGroups());
        } else {
            $writeAccess = [3]; // Gast (Ilch Gruppe 3)
        }

        $guestView = ($config->get('voxchat_guest_view') !== '0');

        $this->getView()
            ->set('guestView',   $guestView)
            ->set('messages',    $messages)
            ->set('maxId',       $maxId)
            ->set('writeAccess', $writeAccess)
            ->set('userName',    $user ? $user->getName() : '')
            ->set('userId',      $user ? $user->getId()   : 0)
            ->set('loggedIn',    $user !== null)
            ->set('channel',     $config->get('voxchat_channel')         ?: '#general')
            ->set('maxlength',   (int)($config->get('voxchat_chat_maxlength')  ?: 300))
            ->set('writeaccess', $config->get('voxchat_chat_writeaccess') ?: '2')
            ->set('refresh',     (int)($config->get('voxchat_chat_refresh')    ?: 30))
            ->set('height',      (int)($config->get('voxchat_height')          ?: 500))
            ->set('style', [
                'bg'             => $config->get('voxchat_style_bg')             ?: '#1a1a2e',
                'bg_opacity'     => (int)($config->get('voxchat_style_bg_opacity')     ?? 100),
                'header'         => $config->get('voxchat_style_header')         ?: '#000000',
                'header_opacity' => (int)($config->get('voxchat_style_header_opacity') ?? 30),
                'text'           => $config->get('voxchat_style_text')           ?: '#e0e0e0',
                'name'           => $config->get('voxchat_style_name')           ?: '#7b8cde',
                'time'           => $config->get('voxchat_style_time')           ?: '#6c757d',
                'border'         => $config->get('voxchat_style_border')         ?: '#ffffff',
                'border_opacity' => (int)($config->get('voxchat_style_border_opacity') ?? 12),
                'input'          => $config->get('voxchat_style_input')          ?: '#000000',
                'input_opacity'  => (int)($config->get('voxchat_style_input_opacity')  ?? 20),
            ]);
    }

    /**
     * AJAX – GET: neue Nachrichten seit ID als JSON.
     */
    public function pollAction()
    {
        $this->getLayout()->setFile('modules/calendar/layouts/events');

        $chatMapper = new ChatMapper();
        $since      = (int)($this->getRequest()->getParam('since') ?? 0);
        $limit      = (int)($this->getConfig()->get('voxchat_chat_limit') ?: 50);
        $messages   = $chatMapper->checkDB() ? $chatMapper->getMessagesSince($since, $limit) : [];
        $userMapper = new \Modules\User\Mappers\User();

        $data = [];
        foreach ($messages as $msg) {
            $user   = $msg->getUserId() > 0 ? $userMapper->getUserById($msg->getUserId()) : null;
            $date   = new \Ilch\Date($msg->getTime());
            $data[] = [
                'id'      => $msg->getId(),
                'userId'  => $msg->getUserId(),
                'name'    => $user ? $user->getName() : $msg->getName(),
                'message' => $msg->getMessage(),
                'time'    => $date->format('H:i', true),
                'color'   => $msg->getColor(),
            ];
        }

        $lastId = !empty($data) ? end($data)['id'] : $since;

        $this->getView()->set('json', ['messages' => $data, 'lastId' => $lastId]);
    }

    /**
     * AJAX – POST: neue Nachricht speichern.
     */
    public function saveAction()
    {
        $this->getLayout()->setFile('modules/calendar/layouts/events');

        $result     = ['success' => false, 'error' => ''];
        $config     = $this->getConfig();
        $user       = $this->getUser();
        $chatMapper = new ChatMapper();

        if (!$this->getRequest()->isPost()) {
            $result['error'] = 'Bad request';
            $this->getView()->set('json', $result);
            return;
        }

        // Schreibrecht prüfen
        if ($user !== null && !empty($user->getGroups())) {
            $writeAccess = array_map(fn($g) => $g->getId(), $user->getGroups());
        } else {
            $writeAccess = [3]; // Gast (Ilch Gruppe 3)
        }

        $allowedGroups = explode(',', $config->get('voxchat_chat_writeaccess') ?: '2');
        if (!is_in_array($writeAccess, $allowedGroups)) {
            $result['error'] = $this->getTranslator()->trans('noWriteAccess');
            $this->getView()->set('json', $result);
            return;
        }

        // Honeypot
        if (!empty($this->getRequest()->getPost('bot'))) {
            $result['success'] = true; // Stille Ablehnung
            $this->getView()->set('json', $result);
            return;
        }

        $maxlength = (int)($config->get('voxchat_chat_maxlength') ?: 300);
        $message   = trim($this->getRequest()->getPost('message') ?? '');
        $namecolorRaw = $this->getRequest()->getPost('namecolor') ?? '';
        $color = preg_match('/^#[0-9a-fA-F]{6}$/', $namecolorRaw) ? $namecolorRaw : '';

        if ($message === '') {
            $result['error'] = $this->getTranslator()->trans('missingMessage');
            $this->getView()->set('json', $result);
            return;
        }

        if (mb_strlen($message) > $maxlength) {
            $message = mb_substr($message, 0, $maxlength);
        }

        if ($user !== null) {
            $name   = $user->getName();
            $userId = $user->getId();
        } else {
            $name   = trim($this->getRequest()->getPost('name') ?? '');
            $userId = 0;
            if ($name === '') {
                $result['error'] = $this->getTranslator()->trans('missingName');
                $this->getView()->set('json', $result);
                return;
            }
        }

        // Tabelle anlegen falls noch nicht vorhanden
        $chatMapper->ensureTable();

        $model = new ChatModel();
        $model->setUserId($userId)
              ->setName($name)
              ->setMessage($message)
              ->setTime(date('Y-m-d H:i:s'))
              ->setColor($color);

        try {
            $chatMapper->save($model);
            $result['success'] = true;

            $maxage = (int)($config->get('voxchat_chat_maxage') ?? 0);
            if ($maxage > 0) {
                $chatMapper->purgeOlderThan($maxage);
            }
        } catch (\Exception $e) {
            $result['error'] = $this->getTranslator()->trans('saveFailed');
        }

        $this->getView()->set('json', $result);
    }
}
