<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\Voxchat\Models;

class Chat extends \Ilch\Model
{
    protected $id      = 0;
    protected $userId  = 0;
    protected $name    = '';
    protected $message = '';
    protected $time    = '';
    protected $color   = '';

    public function setByArray(array $row): self
    {
        if (isset($row['id']))      $this->setId((int)$row['id']);
        if (isset($row['user_id'])) $this->setUserId((int)$row['user_id']);
        if (isset($row['name']))    $this->setName($row['name']);
        if (isset($row['message'])) $this->setMessage($row['message']);
        if (isset($row['time']))    $this->setTime($row['time']);
        if (isset($row['color']))   $this->setColor($row['color']);
        return $this;
    }

    public function getArray(bool $withId = true): array
    {
        return array_merge(
            $withId ? ['id' => $this->getId()] : [],
            [
                'user_id' => $this->getUserId(),
                'name'    => $this->getName(),
                'message' => $this->getMessage(),
                'time'    => $this->getTime(),
                'color'   => $this->getColor(),
            ]
        );
    }

    public function getId(): int      { return $this->id; }
    public function setId(int $v): self { $this->id = $v; return $this; }

    public function getUserId(): int      { return $this->userId; }
    public function setUserId(int $v): self { $this->userId = $v; return $this; }

    public function getName(): string      { return $this->name; }
    public function setName(string $v): self { $this->name = $v; return $this; }

    public function getMessage(): string      { return $this->message; }
    public function setMessage(string $v): self { $this->message = $v; return $this; }

    public function getTime(): string      { return $this->time; }
    public function setTime(string $v): self { $this->time = $v; return $this; }

    public function getColor(): string      { return $this->color; }
    public function setColor(string $v): self { $this->color = $v; return $this; }
}
