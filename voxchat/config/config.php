<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\Voxchat\Config;

class Config extends \Ilch\Config\Install
{
    public $config = [
        'key'        => 'voxchat',
        'version'    => '2.0.0',
        'icon_small' => 'fa-solid fa-comments',
        'author'     => 'Ilch.de',
        'link'       => 'https://ilch.de',
        'languages'  => [
            'de_DE' => [
                'name'        => 'VoxChat',
                'description' => 'Nativer Chat mit Datenbank, Auto-Refresh und konfigurierbaren Zugriffsrechten.',
            ],
            'en_EN' => [
                'name'        => 'VoxChat',
                'description' => 'Native chat using a database with auto-refresh and configurable access rights.',
            ],
        ],
        'boxes' => [
            'voxchat' => [
                'de_DE' => ['name' => 'VoxChat'],
                'en_EN' => ['name' => 'VoxChat'],
            ]
        ],
        'ilchCore'   => '2.2.0',
        'phpVersion' => '7.3'
    ];

    public function install()
    {
        $this->db()->queryMulti($this->getInstallSql());

        $databaseConfig = new \Ilch\Config\Database($this->db());
        $databaseConfig
            ->set('voxchat_channel',          '#general')
            ->set('voxchat_chat_limit',       '50')
            ->set('voxchat_chat_maxlength',   '300')
            ->set('voxchat_chat_writeaccess', '1,2')
            ->set('voxchat_chat_refresh',     '30')
            ->set('voxchat_chat_maxage',      '0')
            ->set('voxchat_height',           '400')
            ->set('voxchat_style_bg',             '#1a1a2e')
            ->set('voxchat_style_bg_opacity',     '100')
            ->set('voxchat_style_header',         '#000000')
            ->set('voxchat_style_header_opacity', '30')
            ->set('voxchat_style_text',           '#e0e0e0')
            ->set('voxchat_style_name',           '#7b8cde')
            ->set('voxchat_style_time',           '#6c757d')
            ->set('voxchat_style_border',         '#ffffff')
            ->set('voxchat_style_border_opacity', '12')
            ->set('voxchat_style_input',          '#000000')
            ->set('voxchat_style_input_opacity',  '20');
    }

    public function uninstall()
    {
        $this->db()->drop('voxchat_chat', true);

        $databaseConfig = new \Ilch\Config\Database($this->db());
        $databaseConfig
            ->delete('voxchat_channel')
            ->delete('voxchat_chat_limit')
            ->delete('voxchat_chat_maxlength')
            ->delete('voxchat_chat_writeaccess')
            ->delete('voxchat_chat_refresh')
            ->delete('voxchat_chat_maxage')
            ->delete('voxchat_height')
            ->delete('voxchat_style_bg')
            ->delete('voxchat_style_bg_opacity')
            ->delete('voxchat_style_header')
            ->delete('voxchat_style_header_opacity')
            ->delete('voxchat_style_text')
            ->delete('voxchat_style_name')
            ->delete('voxchat_style_time')
            ->delete('voxchat_style_border')
            ->delete('voxchat_style_border_opacity')
            ->delete('voxchat_style_input')
            ->delete('voxchat_style_input_opacity')
            // alte Einträge aufräumen
            ->delete('irc_network')
            ->delete('irc_server')
            ->delete('irc_port')
            ->delete('irc_ssl')
            ->delete('irc_nick')
            ->delete('irc_theme')
            ->delete('irc_client')
            ->delete('irc_kiwiirc_url');
    }

    public function getInstallSql(): string
    {
        return 'CREATE TABLE IF NOT EXISTS `[prefix]_voxchat_chat` (
            `id`      INT(11)      NOT NULL AUTO_INCREMENT,
            `user_id` INT(11)      NOT NULL DEFAULT 0,
            `name`    VARCHAR(100) NOT NULL,
            `message` TEXT         NOT NULL,
            `time`    DATETIME     NOT NULL,
            `color`   VARCHAR(7)   NOT NULL DEFAULT \'\',
            PRIMARY KEY (`id`),
            KEY `idx_time` (`time`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;';
    }

    public function getUpdate(string $installedVersion): string
    {
        switch ($installedVersion) {
            case '1.0.0':
                // Alte KiwiIRC-Einstellungen entfernen, neue DB-Tabelle + Einstellungen anlegen
                $this->db()->queryMulti($this->getInstallSql());
                $this->db()->query('ALTER TABLE `[prefix]_voxchat_chat` ADD COLUMN IF NOT EXISTS `color` VARCHAR(7) NOT NULL DEFAULT \'\';');
                $databaseConfig = new \Ilch\Config\Database($this->db());
                $databaseConfig
                    ->delete('irc_network')
                    ->delete('irc_server')
                    ->delete('irc_port')
                    ->delete('irc_ssl')
                    ->delete('irc_nick')
                    ->delete('irc_theme')
                    ->delete('irc_client')
                    ->delete('irc_kiwiirc_url')
                    ->set('voxchat_channel',          '#general')
                    ->set('voxchat_chat_limit',       '50')
                    ->set('voxchat_chat_maxlength',   '300')
                    ->set('voxchat_chat_writeaccess', '1,2')
                    ->set('voxchat_chat_refresh',     '30')
                    ->set('voxchat_chat_maxage',      '0');
                if (!$databaseConfig->get('voxchat_height')) {
                    $databaseConfig->set('voxchat_height', '400');
                }
                $databaseConfig
                    ->set('voxchat_style_bg',             '#1a1a2e')
                    ->set('voxchat_style_bg_opacity',     '100')
                    ->set('voxchat_style_header',         '#000000')
                    ->set('voxchat_style_header_opacity', '30')
                    ->set('voxchat_style_text',           '#e0e0e0')
                    ->set('voxchat_style_name',           '#7b8cde')
                    ->set('voxchat_style_time',           '#6c757d')
                    ->set('voxchat_style_border',         '#ffffff')
                    ->set('voxchat_style_border_opacity', '12')
                    ->set('voxchat_style_input',          '#000000')
                    ->set('voxchat_style_input_opacity',  '20');
                break;
            case '2.0.0':
                $this->db()->query('ALTER TABLE `[prefix]_voxchat_chat` ADD COLUMN IF NOT EXISTS `color` VARCHAR(7) NOT NULL DEFAULT \'\';');
                break;
        }

        return '"' . $this->config['key'] . '" Update-function executed.';
    }
}
