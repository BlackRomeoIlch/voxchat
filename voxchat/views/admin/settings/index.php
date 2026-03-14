<?php

/** @var \Ilch\View $this */
?>
<h1><?= $this->getTrans('settings') ?></h1>

<form method="POST" action="<?= $this->getUrl(['action' => $this->getRequest()->getActionName()]) ?>">
    <?= $this->getTokenField() ?>

    <!-- ── Channel ─────────────────────────────────────── -->
    <div class="card mb-4">
        <div class="card-header fw-bold">
            <i class="fa-solid fa-hashtag me-2"></i><?= $this->getTrans('channelSection') ?>
        </div>
        <div class="card-body">
            <div class="row mb-3<?= $this->validation()->hasError('channel') ? ' has-error' : '' ?>">
                <label for="channel" class="col-xl-3 col-form-label"><?= $this->getTrans('channel') ?></label>
                <div class="col-xl-4">
                    <div class="input-group">
                        <span class="input-group-text">#</span>
                        <input type="text"
                               class="form-control"
                               id="channel"
                               name="channel"
                               placeholder="general"
                               value="<?= $this->escape(ltrim($this->originalInput('channel', $this->get('channel') ?? ''), '#')) ?>">
                    </div>
                    <small class="form-text text-muted"><?= $this->getTrans('channelDisplayHint') ?></small>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Zugriffsrechte ──────────────────────────────── -->
    <div class="card mb-4">
        <div class="card-header fw-bold">
            <i class="fa-solid fa-users me-2"></i><?= $this->getTrans('writeAccessSection') ?>
        </div>
        <div class="card-body">
            <div class="row mb-0">
                <label class="col-xl-3 col-form-label"><?= $this->getTrans('writeAccess') ?></label>
                <div class="col-xl-9 col-form-label">
                    <?php foreach ($this->get('groups') as $group) : ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="wg_<?= $group->getId() ?>"
                                   name="writeaccess[]"
                                   value="<?= $group->getId() ?>"
                                   <?= in_array((string)$group->getId(), $this->get('writeAccesses')) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="wg_<?= $group->getId() ?>">
                                <?= $this->escape($group->getName()) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                    <small class="d-block mt-1 text-muted"><?= $this->getTrans('writeAccessHint') ?></small>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Chat-Verhalten ──────────────────────────────── -->
    <div class="card mb-4">
        <div class="card-header fw-bold">
            <i class="fa-solid fa-sliders me-2"></i><?= $this->getTrans('chatBehaviourSection') ?>
        </div>
        <div class="card-body">

            <div class="row mb-3<?= $this->validation()->hasError('chat_limit') ? ' has-error' : '' ?>">
                <label for="chat_limit" class="col-xl-3 col-form-label"><?= $this->getTrans('chatLimit') ?></label>
                <div class="col-xl-3">
                    <input type="number" class="form-control" id="chat_limit" name="chat_limit" min="5" max="500"
                           value="<?= (int)$this->originalInput('chat_limit', $this->get('chat_limit') ?? 50) ?>">
                    <small class="form-text text-muted"><?= $this->getTrans('chatLimitHint') ?></small>
                </div>
            </div>

            <div class="row mb-3<?= $this->validation()->hasError('chat_maxlen') ? ' has-error' : '' ?>">
                <label for="chat_maxlen" class="col-xl-3 col-form-label"><?= $this->getTrans('maxLength') ?></label>
                <div class="col-xl-3">
                    <div class="input-group">
                        <input type="number" class="form-control" id="chat_maxlen" name="chat_maxlen" min="10" max="2000"
                               value="<?= (int)$this->originalInput('chat_maxlen', $this->get('chat_maxlen') ?? 300) ?>">
                        <span class="input-group-text"><?= $this->getTrans('chars') ?></span>
                    </div>
                </div>
            </div>

            <div class="row mb-3<?= $this->validation()->hasError('chat_refresh') ? ' has-error' : '' ?>">
                <label for="chat_refresh" class="col-xl-3 col-form-label"><?= $this->getTrans('autoRefresh') ?></label>
                <div class="col-xl-3">
                    <div class="input-group">
                        <input type="number" class="form-control" id="chat_refresh" name="chat_refresh" min="0" max="300"
                               value="<?= (int)$this->originalInput('chat_refresh', $this->get('chat_refresh') ?? 30) ?>">
                        <span class="input-group-text"><?= $this->getTrans('seconds') ?></span>
                    </div>
                    <small class="form-text text-muted"><?= $this->getTrans('autoRefreshHint') ?></small>
                </div>
            </div>

            <div class="row mb-3">
                <label for="chat_maxage" class="col-xl-3 col-form-label"><?= $this->getTrans('maxAge') ?></label>
                <div class="col-xl-3">
                    <div class="input-group">
                        <input type="number" class="form-control" id="chat_maxage" name="chat_maxage" min="0"
                               value="<?= (int)$this->originalInput('chat_maxage', $this->get('chat_maxage') ?? 0) ?>">
                        <span class="input-group-text"><?= $this->getTrans('days') ?></span>
                    </div>
                    <small class="form-text text-muted"><?= $this->getTrans('maxAgeHint') ?></small>
                </div>
            </div>

        </div>
    </div>

    <!-- ── Darstellung ─────────────────────────────────── -->
    <div class="card mb-4">
        <div class="card-header fw-bold">
            <i class="fa-solid fa-palette me-2"></i><?= $this->getTrans('appearanceSection') ?>
        </div>
        <div class="card-body">

            <!-- Chat-Höhe -->
            <div class="row mb-3<?= $this->validation()->hasError('height') ? ' has-error' : '' ?>">
                <label for="height" class="col-xl-3 col-form-label"><?= $this->getTrans('height') ?></label>
                <div class="col-xl-3">
                    <div class="input-group">
                        <input type="number" class="form-control" id="height" name="height" min="100"
                               value="<?= (int)$this->originalInput('height', $this->get('height') ?? 400) ?>">
                        <span class="input-group-text">px</span>
                    </div>
                    <small class="form-text text-muted"><?= $this->getTrans('heightHint') ?></small>
                </div>
            </div>

            <hr>
            <h6><?= $this->getTrans('styleColors') ?></h6>

            <!-- Hintergrund -->
            <div class="row mb-3">
                <label class="col-xl-3 col-form-label"><?= $this->getTrans('styleBg') ?></label>
                <div class="col-xl-5 d-flex align-items-center gap-3">
                    <input type="color" class="form-control form-control-color" name="style_bg"
                           value="<?= $this->originalInput('style_bg', $this->get('style_bg') ?? '#1a1a2e') ?>"
                           style="width:60px;height:38px">
                    <div class="flex-grow-1">
                        <label class="form-label small mb-1"><?= $this->getTrans('styleOpacity') ?>: <span id="lbl_bg_opacity"></span>%</label>
                        <input type="range" class="form-range" name="style_bg_opacity" id="style_bg_opacity"
                               min="0" max="100"
                               value="<?= (int)$this->originalInput('style_bg_opacity', $this->get('style_bg_opacity') ?? 100) ?>">
                    </div>
                </div>
            </div>

            <!-- Header -->
            <div class="row mb-3">
                <label class="col-xl-3 col-form-label"><?= $this->getTrans('styleHeader') ?></label>
                <div class="col-xl-5 d-flex align-items-center gap-3">
                    <input type="color" class="form-control form-control-color" name="style_header"
                           value="<?= $this->originalInput('style_header', $this->get('style_header') ?? '#000000') ?>"
                           style="width:60px;height:38px">
                    <div class="flex-grow-1">
                        <label class="form-label small mb-1"><?= $this->getTrans('styleOpacity') ?>: <span id="lbl_header_opacity"></span>%</label>
                        <input type="range" class="form-range" name="style_header_opacity" id="style_header_opacity"
                               min="0" max="100"
                               value="<?= (int)$this->originalInput('style_header_opacity', $this->get('style_header_opacity') ?? 30) ?>">
                    </div>
                </div>
            </div>

            <!-- Eingabefeld-Hintergrund -->
            <div class="row mb-3">
                <label class="col-xl-3 col-form-label"><?= $this->getTrans('styleInput') ?></label>
                <div class="col-xl-5 d-flex align-items-center gap-3">
                    <input type="color" class="form-control form-control-color" name="style_input"
                           value="<?= $this->originalInput('style_input', $this->get('style_input') ?? '#000000') ?>"
                           style="width:60px;height:38px">
                    <div class="flex-grow-1">
                        <label class="form-label small mb-1"><?= $this->getTrans('styleOpacity') ?>: <span id="lbl_input_opacity"></span>%</label>
                        <input type="range" class="form-range" name="style_input_opacity" id="style_input_opacity"
                               min="0" max="100"
                               value="<?= (int)$this->originalInput('style_input_opacity', $this->get('style_input_opacity') ?? 20) ?>">
                    </div>
                </div>
            </div>

            <!-- Rahmen -->
            <div class="row mb-3">
                <label class="col-xl-3 col-form-label"><?= $this->getTrans('styleBorder') ?></label>
                <div class="col-xl-5 d-flex align-items-center gap-3">
                    <input type="color" class="form-control form-control-color" name="style_border"
                           value="<?= $this->originalInput('style_border', $this->get('style_border') ?? '#ffffff') ?>"
                           style="width:60px;height:38px">
                    <div class="flex-grow-1">
                        <label class="form-label small mb-1"><?= $this->getTrans('styleOpacity') ?>: <span id="lbl_border_opacity"></span>%</label>
                        <input type="range" class="form-range" name="style_border_opacity" id="style_border_opacity"
                               min="0" max="100"
                               value="<?= (int)$this->originalInput('style_border_opacity', $this->get('style_border_opacity') ?? 12) ?>">
                    </div>
                </div>
            </div>

            <!-- Textfarben -->
            <div class="row mb-3">
                <label for="style_text" class="col-xl-3 col-form-label"><?= $this->getTrans('styleText') ?></label>
                <div class="col-xl-2">
                    <input type="color" class="form-control form-control-color w-100" name="style_text" id="style_text"
                           value="<?= $this->originalInput('style_text', $this->get('style_text') ?? '#e0e0e0') ?>"
                           style="height:38px">
                </div>
            </div>

            <div class="row mb-3">
                <label for="style_name" class="col-xl-3 col-form-label"><?= $this->getTrans('styleName') ?></label>
                <div class="col-xl-2">
                    <input type="color" class="form-control form-control-color w-100" name="style_name" id="style_name"
                           value="<?= $this->originalInput('style_name', $this->get('style_name') ?? '#7b8cde') ?>"
                           style="height:38px">
                </div>
            </div>

            <div class="row mb-3">
                <label for="style_time" class="col-xl-3 col-form-label"><?= $this->getTrans('styleTime') ?></label>
                <div class="col-xl-2">
                    <input type="color" class="form-control form-control-color w-100" name="style_time" id="style_time"
                           value="<?= $this->originalInput('style_time', $this->get('style_time') ?? '#6c757d') ?>"
                           style="height:38px">
                </div>
            </div>

        </div>
    </div>

<script>
['bg', 'header', 'input', 'border'].forEach(function(key) {
    var slider = document.getElementById('style_' + key + '_opacity');
    var label  = document.getElementById('lbl_' + key + '_opacity');
    if (!slider || !label) return;
    label.textContent = slider.value;
    slider.addEventListener('input', function() { label.textContent = this.value; });
});
</script>

    <?= $this->getSaveBar() ?>
</form>
