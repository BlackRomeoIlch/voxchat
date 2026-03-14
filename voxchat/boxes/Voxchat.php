<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\Voxchat\Boxes;

use Modules\Voxchat\Mappers\Chat as ChatMapper;

class Voxchat extends \Ilch\Box
{
    public function render()
    {
        $chatMapper = new ChatMapper();
        $config     = \Ilch\Registry::get('config');
        $user       = $this->getUser();

        $limit    = (int)($config->get('voxchat_chat_limit')   ?: 50);
        $messages = $chatMapper->checkDB() ? $chatMapper->getMessages($limit) : [];
        $maxId    = $chatMapper->checkDB() ? $chatMapper->getMaxId()          : 0;

        // Schreibrecht: Array aller Gruppen-IDs des Users (is_in_array erwartet iterierbares $needle)
        if ($user !== null && !empty($user->getGroups())) {
            $writeAccess = array_map(fn($g) => $g->getId(), $user->getGroups());
        } else {
            $writeAccess = [1]; // Gast
        }

        $this->getView()
            ->set('messages',     $messages)
            ->set('maxId',        $maxId)
            ->set('writeAccess',  $writeAccess)
            ->set('userName',     $user ? $user->getName() : '')
            ->set('userId',       $user ? $user->getId()   : 0)
            ->set('loggedIn',     $user !== null)
            ->set('channel',      $config->get('voxchat_channel')          ?: '#general')
            ->set('maxlength',    (int)($config->get('voxchat_chat_maxlength')   ?: 300))
            ->set('writeaccess',  $config->get('voxchat_chat_writeaccess')  ?: '1,2')
            ->set('refresh',      (int)($config->get('voxchat_chat_refresh')     ?: 30))
            ->set('height',       (int)($config->get('voxchat_height')           ?: 400))
            ->set('uniqid',       substr(md5(microtime(true)), 0, 8))
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
}
