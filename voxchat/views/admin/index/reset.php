<?php

/** @var \Ilch\View $this */
?>
<h1><?= $this->getTrans('reset') ?></h1>

<div class="alert alert-danger">
    <i class="fa-solid fa-triangle-exclamation me-2"></i>
    <?= $this->getTrans('resetWarning') ?>
</div>

<a href="<?= $this->getUrl(['action' => 'reset'], null, true) ?>"
   class="btn btn-danger"
   data-confirm="<?= $this->getTrans('confirmReset') ?>">
    <i class="fa-solid fa-trash-can"></i> <?= $this->getTrans('resetAll') ?>
</a>
<a href="<?= $this->getUrl(['action' => 'index']) ?>" class="btn btn-secondary ms-2">
    <?= $this->getTrans('cancel') ?>
</a>
