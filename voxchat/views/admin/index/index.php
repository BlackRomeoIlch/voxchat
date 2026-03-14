<?php

/** @var \Ilch\View $this */

/** @var \Modules\Voxchat\Models\Chat[] $messages */
$messages   = $this->get('messages')   ?? [];
$userMapper = $this->get('userMapper');
$pagination = $this->get('pagination');
?>
<h1><?= $this->getTrans('manage') ?></h1>

<form method="POST" action="<?= $this->getUrl(['controller' => 'index', 'action' => 'index']) ?>">
    <?= $this->getTokenField() ?>
    <input type="hidden" name="action" value="delete">

    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th style="width:30px">
                        <input type="checkbox" id="irc-check-all">
                    </th>
                    <th style="width:140px"><?= $this->getTrans('time') ?></th>
                    <th style="width:160px"><?= $this->getTrans('name') ?></th>
                    <th><?= $this->getTrans('message') ?></th>
                    <th style="width:70px"><?= $this->getTrans('actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($messages)) : ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">
                            <?= $this->getTrans('noMessages') ?>
                        </td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($messages as $msg) : ?>
                        <?php
                        $user = $msg->getUserId() > 0 ? $userMapper->getUserById($msg->getUserId()) : null;
                        $date = new \Ilch\Date($msg->getTime());
                        ?>
                        <tr>
                            <td>
                                <input type="checkbox" name="check_entries[]" value="<?= $msg->getId() ?>">
                            </td>
                            <td><?= $date->format('d.m.Y H:i', true) ?></td>
                            <td>
                                <?php if ($user) : ?>
                                    <a href="<?= $this->getUrl('user/profil/index/user/' . $user->getId()) ?>">
                                        <?= $this->escape($user->getName()) ?>
                                    </a>
                                <?php else : ?>
                                    <?= $this->escape($msg->getName()) ?>
                                    <small class="text-muted">(<?= $this->getTrans('guest') ?>)</small>
                                <?php endif; ?>
                            </td>
                            <td><?= $this->escape($msg->getMessage()) ?></td>
                            <td class="text-center">
                                <a href="<?= $this->getUrl(['action' => 'delete', 'id' => $msg->getId()]) ?>"
                                   class="btn btn-danger btn-xs"
                                   data-confirm="<?= $this->getTrans('confirmDelete') ?>">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (!empty($messages)) : ?>
        <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap gap-2">
            <button type="submit" class="btn btn-danger btn-sm">
                <i class="fa-solid fa-trash-can"></i> <?= $this->getTrans('deleteSelected') ?>
            </button>
            <?= $pagination->getHtml($this, ['action' => 'index']) ?>
        </div>
    <?php endif; ?>
</form>

<script>
document.getElementById('irc-check-all').addEventListener('change', function() {
    var checked = this.checked;
    document.querySelectorAll('input[name="check_entries[]"]').forEach(function(cb) {
        cb.checked = checked;
    });
});
</script>
