<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\Voxchat\Mappers;

use Modules\Voxchat\Models\Chat as ChatModel;

class Chat extends \Ilch\Mapper
{
    public $tablename = 'voxchat_chat';

    public function checkDB(): bool
    {
        return $this->db()->ifTableExists($this->tablename);
    }

    /**
     * Tabelle anlegen falls nicht vorhanden (z.B. nach Update von alter Modul-Version).
     */
    public function ensureTable(): void
    {
        if (!$this->checkDB()) {
            $this->db()->query(
                'CREATE TABLE IF NOT EXISTS `[prefix]_voxchat_chat` (
                    `id`      INT(11)      NOT NULL AUTO_INCREMENT,
                    `user_id` INT(11)      NOT NULL DEFAULT 0,
                    `name`    VARCHAR(100) NOT NULL,
                    `message` TEXT         NOT NULL,
                    `time`    DATETIME     NOT NULL,
                    `color`   VARCHAR(7)   NOT NULL DEFAULT \'\',
                    PRIMARY KEY (`id`),
                    KEY `idx_time` (`time`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;'
            );
        } else {
            // Spalte nachrüsten falls Tabelle aus älterer Version stammt
            $this->db()->query(
                'ALTER TABLE `[prefix]_voxchat_chat`
                 ADD COLUMN IF NOT EXISTS `color` VARCHAR(7) NOT NULL DEFAULT \'\';'
            );
        }
    }

    /**
     * Neueste $limit Nachrichten (für initialen Render), sortiert älteste zuerst.
     */
    public function getMessages(int $limit = 50): array
    {
        $rows = $this->db()->select('*')
            ->from($this->tablename)
            ->order(['id' => 'DESC'])
            ->limit($limit)
            ->execute()
            ->fetchRows();

        if (empty($rows)) return [];

        $rows = array_reverse($rows); // älteste zuerst
        return $this->hydrate($rows);
    }

    /**
     * Nachrichten mit ID > $since (für AJAX-Polling).
     */
    public function getMessagesSince(int $since, int $limit = 50): array
    {
        $rows = $this->db()->select('*')
            ->from($this->tablename)
            ->where(['id >' => $since])
            ->order(['id' => 'ASC'])
            ->limit($limit)
            ->execute()
            ->fetchRows();

        return $this->hydrate($rows ?: []);
    }

    /**
     * Alle Nachrichten (für Admin-Verwaltung), neueste zuerst.
     */
    public function getAllMessages(?\Ilch\Pagination $pagination = null): array
    {
        $select = $this->db()->select('*')
            ->from($this->tablename)
            ->order(['id' => 'DESC']);

        if ($pagination !== null) {
            $select->limit($pagination->getLimit())->useFoundRows();
            $result = $select->execute();
            $pagination->setRows($result->getFoundRows());
        } else {
            $result = $select->execute();
        }

        return $this->hydrate($result->fetchRows() ?: []);
    }

    public function save(ChatModel $model): int
    {
        $fields = $model->getArray(false);
        if ($model->getId()) {
            $this->db()->update($this->tablename)->values($fields)->where(['id' => $model->getId()])->execute();
            return $model->getId();
        }
        return $this->db()->insert($this->tablename)->values($fields)->execute();
    }

    public function delete(int $id): bool
    {
        return $this->db()->delete($this->tablename)->where(['id' => $id])->execute();
    }

    public function truncate(): bool
    {
        return (bool)$this->db()->truncate($this->tablename);
    }

    /**
     * Nachrichten löschen die älter als $days Tage sind.
     */
    public function purgeOlderThan(int $days): bool
    {
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        return $this->db()->delete($this->tablename)
            ->where(['time <' => $cutoff])
            ->execute();
    }

    /**
     * Höchste vorhandene ID (für initiales lastId im JS).
     */
    public function getMaxId(): int
    {
        $row = $this->db()->select('MAX(id) AS max_id')
            ->from($this->tablename)
            ->execute()
            ->fetchRow();
        return (int)($row['max_id'] ?? 0);
    }

    private function hydrate(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $model = new ChatModel();
            $model->setByArray($row);
            $result[] = $model;
        }
        return $result;
    }
}
