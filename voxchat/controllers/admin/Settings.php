<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\Voxchat\Controllers\Admin;

use Ilch\Validation;

class Settings extends \Ilch\Controller\Admin
{
    public function init()
    {
        $items = [
            [
                'name'   => 'manage',
                'active' => false,
                'icon'   => 'fa-solid fa-table-list',
                'url'    => $this->getLayout()->getUrl(['controller' => 'index', 'action' => 'index'])
            ],
            [
                'name'   => 'reset',
                'active' => false,
                'icon'   => 'fa-solid fa-trash-can',
                'url'    => $this->getLayout()->getUrl(['controller' => 'index', 'action' => 'reset'])
            ],
            [
                'name'   => 'settings',
                'active' => true,
                'icon'   => 'fa-solid fa-gears',
                'url'    => $this->getLayout()->getUrl(['controller' => 'settings', 'action' => 'index'])
            ],
        ];

        $this->getLayout()->addMenu('menuIrc', $items);
    }

    public function indexAction()
    {
        $this->getLayout()->getAdminHmenu()
            ->add($this->getTranslator()->trans('menuIrc'), ['controller' => 'index', 'action' => 'index'])
            ->add($this->getTranslator()->trans('settings'), ['action' => 'index']);

        if ($this->getRequest()->isPost()) {
            $validation = Validation::create($this->getRequest()->getPost(), [
                'channel'      => 'required',
                'chat_limit'   => 'required|min:5',
                'chat_maxlen'  => 'required|min:10',
                'height'       => 'required|min:100',
                'chat_refresh' => 'required|min:0',
            ]);

            if ($validation->isValid()) {
                $this->getConfig()
                    ->set('voxchat_guest_view',        (int)($this->getRequest()->getPost('guest_view') == '1'))
                    ->set('voxchat_channel',          trim($this->getRequest()->getPost('channel')))
                    ->set('voxchat_chat_limit',        (int)$this->getRequest()->getPost('chat_limit'))
                    ->set('voxchat_chat_maxlength',    (int)$this->getRequest()->getPost('chat_maxlen'))
                    ->set('voxchat_chat_writeaccess',  implode(',', array_map('intval',
                        (array)$this->getRequest()->getPost('writeaccess'))))
                    ->set('voxchat_chat_refresh',      (int)$this->getRequest()->getPost('chat_refresh'))
                    ->set('voxchat_chat_maxage',       (int)$this->getRequest()->getPost('chat_maxage'))
                    ->set('voxchat_height',            (int)$this->getRequest()->getPost('height'))
                    ->set('voxchat_style_bg',             $this->getRequest()->getPost('style_bg')             ?: '#1a1a2e')
                    ->set('voxchat_style_bg_opacity',     (int)$this->getRequest()->getPost('style_bg_opacity'))
                    ->set('voxchat_style_header',         $this->getRequest()->getPost('style_header')         ?: '#000000')
                    ->set('voxchat_style_header_opacity', (int)$this->getRequest()->getPost('style_header_opacity'))
                    ->set('voxchat_style_text',           $this->getRequest()->getPost('style_text')           ?: '#e0e0e0')
                    ->set('voxchat_style_name',           $this->getRequest()->getPost('style_name')           ?: '#7b8cde')
                    ->set('voxchat_style_time',           $this->getRequest()->getPost('style_time')           ?: '#6c757d')
                    ->set('voxchat_style_border',         $this->getRequest()->getPost('style_border')         ?: '#ffffff')
                    ->set('voxchat_style_border_opacity', (int)$this->getRequest()->getPost('style_border_opacity'))
                    ->set('voxchat_style_input',          $this->getRequest()->getPost('style_input')          ?: '#000000')
                    ->set('voxchat_style_input_opacity',  (int)$this->getRequest()->getPost('style_input_opacity'));

                $this->redirect()->withMessage('saveSuccess')->to(['action' => 'index']);
            }

            $this->addMessage($validation->getErrorBag()->getErrorMessages(), 'danger', true);
            $this->redirect()->withInput()->withErrors($validation->getErrorBag())->to(['action' => 'index']);
        }

        // Gruppen für die Zugriffsrecht-Checkboxen laden
        $groupMapper   = new \Modules\User\Mappers\Group();
        $groups        = $groupMapper->getGroupList();
        $writeAccesses = array_map('trim', explode(',', $this->getConfig()->get('voxchat_chat_writeaccess') ?? '1,2'));

        $this->getView()
            ->set('groups',        $groups)
            ->set('writeAccesses', $writeAccesses)
            ->set('guest_view',    $this->getConfig()->get('voxchat_guest_view') ?? '1')
            ->set('channel',       $this->getConfig()->get('voxchat_channel'))
            ->set('chat_limit',    $this->getConfig()->get('voxchat_chat_limit'))
            ->set('chat_maxlen',   $this->getConfig()->get('voxchat_chat_maxlength'))
            ->set('chat_refresh',  $this->getConfig()->get('voxchat_chat_refresh'))
            ->set('chat_maxage',   $this->getConfig()->get('voxchat_chat_maxage'))
            ->set('height',        $this->getConfig()->get('voxchat_height'))
            ->set('style_bg',             $this->getConfig()->get('voxchat_style_bg')             ?? '#1a1a2e')
            ->set('style_bg_opacity',     $this->getConfig()->get('voxchat_style_bg_opacity')     ?? 100)
            ->set('style_header',         $this->getConfig()->get('voxchat_style_header')         ?? '#000000')
            ->set('style_header_opacity', $this->getConfig()->get('voxchat_style_header_opacity') ?? 30)
            ->set('style_text',           $this->getConfig()->get('voxchat_style_text')           ?? '#e0e0e0')
            ->set('style_name',           $this->getConfig()->get('voxchat_style_name')           ?? '#7b8cde')
            ->set('style_time',           $this->getConfig()->get('voxchat_style_time')           ?? '#6c757d')
            ->set('style_border',         $this->getConfig()->get('voxchat_style_border')         ?? '#ffffff')
            ->set('style_border_opacity', $this->getConfig()->get('voxchat_style_border_opacity') ?? 12)
            ->set('style_input',          $this->getConfig()->get('voxchat_style_input')          ?? '#000000')
            ->set('style_input_opacity',  $this->getConfig()->get('voxchat_style_input_opacity')  ?? 20);
    }
}
