<?php

/** @var \Ilch\View $this */

$uid          = $this->get('uniqid');
$messages     = $this->get('messages')    ?? [];
$maxId        = (int)$this->get('maxId');
$writeAccess  = $this->get('writeAccess');
$writeaccess  = $this->get('writeaccess') ?? '1,2';
$channel      = $this->get('channel')     ?? '#general';
$maxlength    = (int)($this->get('maxlength') ?? 300);
$refresh      = (int)($this->get('refresh')   ?? 30);
$height       = (int)($this->get('height')    ?? 400);
$loggedIn     = (bool)$this->get('loggedIn');
$style = $this->get('style') ?? [];

// Hex+Opacity → rgba() Helper
$rgba = function(string $hex, int $opacity): string {
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    return 'rgba(' . hexdec(substr($hex,0,2)) . ',' . hexdec(substr($hex,2,2)) . ',' . hexdec(substr($hex,4,2)) . ',' . round($opacity/100,2) . ')';
};
$userName     = $this->get('userName') ?? '';

$canWrite     = is_in_array($writeAccess, explode(',', $writeaccess));
$userMapper   = new \Modules\User\Mappers\User();

$pollUrl = $this->getUrl('voxchat/index/poll');
$saveUrl = $this->getUrl('voxchat/index/save');
?>
<style>
#irc-wrap-<?= $uid ?> {
    display: flex;
    flex-direction: column;
    border: 1px solid <?= $rgba($style['border'] ?? '#ffffff', $style['border_opacity'] ?? 12) ?>;
    border-radius: 6px;
    overflow: hidden;
    background: <?= $rgba($style['bg'] ?? '#1a1a2e', $style['bg_opacity'] ?? 100) ?>;
    color: <?= $this->escape($style['text'] ?? '#e0e0e0') ?>;
    font-size: .875rem;
}
#irc-wrap-<?= $uid ?> .irc-header {
    display: flex;
    align-items: center;
    gap: .5rem;
    padding: .4rem .75rem;
    background: <?= $rgba($style['header'] ?? '#000000', $style['header_opacity'] ?? 30) ?>;
    border-bottom: 1px solid <?= $rgba($style['border'] ?? '#ffffff', $style['border_opacity'] ?? 12) ?>;
    font-weight: 600;
    font-size: .8rem;
    letter-spacing: .03em;
}
#irc-wrap-<?= $uid ?> .irc-header .irc-status {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: #2ecc71;
    flex-shrink: 0;
}
#irc-wrap-<?= $uid ?> .irc-header .irc-refresh-indicator {
    margin-left: auto;
    font-size: .7rem;
    opacity: .6;
}
#irc-messages-<?= $uid ?> {
    height: <?= $height ?>px;
    overflow-y: auto;
    padding: .5rem .75rem;
    display: flex;
    flex-direction: column;
    gap: 2px;
    scroll-behavior: smooth;
}
#irc-messages-<?= $uid ?> .irc-msg {
    line-height: 1.4;
    word-break: break-word;
    color: <?= $this->escape($style['text'] ?? '#e0e0e0') ?>;
}
#irc-messages-<?= $uid ?> .irc-time {
    color: <?= $this->escape($style['time'] ?? '#6c757d') ?>;
    font-size: .75rem;
    user-select: none;
    margin-right: .25rem;
}
#irc-messages-<?= $uid ?> .irc-name {
    color: <?= $this->escape($style['name'] ?? '#7b8cde') ?>;
    margin-right: .2rem;
}
#irc-messages-<?= $uid ?> .irc-name a {
    color: inherit;
    text-decoration: none;
}
#irc-messages-<?= $uid ?> .irc-name a:hover { text-decoration: underline; }
#irc-messages-<?= $uid ?> .irc-empty {
    text-align: center;
    opacity: .5;
    padding: 1rem 0;
}
#irc-new-badge-<?= $uid ?> {
    display: none;
    text-align: center;
    padding: .2rem;
    background: <?= $rgba($style['name'] ?? '#7b8cde', 25) ?>;
    cursor: pointer;
    font-size: .75rem;
    border-top: 1px solid <?= $rgba($style['name'] ?? '#7b8cde', 30) ?>;
}
#irc-wrap-<?= $uid ?> .irc-input-area {
    border-top: 1px solid <?= $rgba($style['border'] ?? '#ffffff', $style['border_opacity'] ?? 12) ?>;
    padding: .4rem .5rem;
    background: <?= $rgba($style['input'] ?? '#000000', $style['input_opacity'] ?? 20) ?>;
}
#irc-wrap-<?= $uid ?> .irc-input-area .char-count {
    font-size: .7rem;
    color: <?= $this->escape($style['time'] ?? '#6c757d') ?>;
    text-align: right;
    margin-top: 2px;
}
#irc-wrap-<?= $uid ?> .irc-input-area .char-count.over { color: #e74c3c; }
#irc-emoji-picker-<?= $uid ?> {
    position: absolute;
    bottom: 100%;
    left: 0;
    z-index: 1000;
    background: #1e1e2e;
    border: 1px solid rgba(255,255,255,.15);
    border-radius: 8px;
    padding: 6px;
    display: none;
    width: 260px;
    max-height: 200px;
    overflow-y: auto;
    flex-wrap: wrap;
    gap: 2px;
}
#irc-emoji-picker-<?= $uid ?>.open { display: flex; }
#irc-emoji-picker-<?= $uid ?> span {
    cursor: pointer;
    font-size: 1.2rem;
    padding: 2px 4px;
    border-radius: 4px;
    line-height: 1.4;
}
#irc-emoji-picker-<?= $uid ?> span:hover { background: rgba(255,255,255,.1); }
.irc-toolbar-<?= $uid ?> {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-bottom: 4px;
    position: relative;
}
.irc-color-btn-<?= $uid ?> {
    width: 28px; height: 28px;
    border-radius: 50%;
    border: 2px solid rgba(255,255,255,.3);
    cursor: pointer;
    padding: 0;
    background: var(--vc-namecolor, #7b8cde);
    flex-shrink: 0;
}
</style>

<div id="irc-wrap-<?= $uid ?>">

    <!-- Header -->
    <div class="irc-header">
        <span class="irc-status"></span>
        <span><?= $this->escape($channel) ?></span>
        <?php if ($refresh > 0) : ?>
            <span class="irc-refresh-indicator" id="irc-refresh-info-<?= $uid ?>">
                <i class="fa-solid fa-rotate fa-spin" style="display:none" id="irc-spin-<?= $uid ?>"></i>
                &#x21bb; <?= $refresh ?>s
            </span>
        <?php endif; ?>
    </div>

    <!-- Nachrichten -->
    <div id="irc-messages-<?= $uid ?>">
        <?php if (empty($messages)) : ?>
            <div class="irc-empty"><?= $this->getTrans('noMessages') ?></div>
        <?php else : ?>
            <?php foreach ($messages as $msg) : ?>
                <?php
                $user      = $msg->getUserId() > 0 ? $userMapper->getUserById($msg->getUserId()) : null;
                $date      = new \Ilch\Date($msg->getTime());
                $name      = $this->escape($msg->getName());
                $nameHtml  = ($user)
                    ? '<a href="' . $this->getUrl('user/profil/index/user/' . $user->getId()) . '">' . $name . '</a>'
                    : $name;
                $nameColor = $msg->getColor() ?: ($style['name'] ?? '#7b8cde');
                ?>
                <div class="irc-msg" data-id="<?= $msg->getId() ?>">
                    <span class="irc-time"><?= $date->format('H:i', true) ?></span><strong
                        class="irc-name" style="color:<?= $this->escape($nameColor) ?>">&lt;<?= $nameHtml ?>&gt;</strong><?= $this->escape($msg->getMessage()) ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- "Neue Nachrichten" Button -->
    <div id="irc-new-badge-<?= $uid ?>" onclick="ircChat<?= $uid ?>.scrollToBottom()">
        <i class="fa-solid fa-arrow-down"></i> <span id="irc-new-count-<?= $uid ?>">0</span>
        <?= $this->getTrans('newMessages') ?>
    </div>

    <!-- Eingabe -->
    <?php if ($canWrite) : ?>
        <div class="irc-input-area">
            <form id="irc-form-<?= $uid ?>" autocomplete="off">
                <?= $this->getTokenField() ?>
                <input type="color" id="irc-colorinput-<?= $uid ?>" style="display:none">
                <input type="hidden" name="namecolor" id="irc-namecolor-<?= $uid ?>" value="">
                <input type="text" name="bot" style="display:none" tabindex="-1" aria-hidden="true">
                <?php if (!$loggedIn) : ?>
                    <input type="text"
                           class="form-control form-control-sm mb-1"
                           name="name"
                           placeholder="<?= $this->getTrans('yourName') ?>"
                           maxlength="50"
                           required>
                <?php endif; ?>
                <div class="irc-toolbar-<?= $uid ?>">
                    <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-0" id="irc-emoji-btn-<?= $uid ?>" title="Emoji">&#x1F60A;</button>
                    <button type="button" class="irc-color-btn-<?= $uid ?>" id="irc-colorbtn-<?= $uid ?>" title="Schriftfarbe"></button>
                    <div id="irc-emoji-picker-<?= $uid ?>">
                        <!-- emojis inserted by JS -->
                    </div>
                </div>
                <div class="input-group input-group-sm">
                    <input type="text"
                           class="form-control"
                           id="irc-input-<?= $uid ?>"
                           name="message"
                           placeholder="<?= $this->getTrans('messagePlaceholder') ?>"
                           maxlength="<?= $maxlength ?>"
                           required
                           autocomplete="off">
                    <button class="btn btn-primary" type="submit">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
                <div class="char-count" id="irc-chars-<?= $uid ?>">0 / <?= $maxlength ?></div>
            </form>
        </div>
    <?php elseif (!$loggedIn) : ?>
        <div class="irc-input-area text-center">
            <small>
                <a href="<?= $this->getUrl('user/login/index') ?>"><?= $this->getTrans('loginToChat') ?></a>
            </small>
        </div>
    <?php else : ?>
        <div class="irc-input-area text-center">
            <small class="text-muted"><?= $this->getTrans('noWriteAccess') ?></small>
        </div>
    <?php endif; ?>
</div>

<script>
(function() {
    'use strict';

    var uid      = <?= json_encode($uid) ?>;
    var pollUrl  = <?= json_encode($pollUrl) ?>;
    var saveUrl  = <?= json_encode($saveUrl) ?>;
    var refresh  = <?= $refresh ?>;
    var newCount = 0;
    var polling  = false;

    var $messages  = $('#irc-messages-' + uid);
    var $badge     = $('#irc-new-badge-' + uid);
    var $newCount  = $('#irc-new-count-' + uid);
    var $spin      = $('#irc-spin-' + uid);

    // lastId und renderedIds aus dem DOM initialisieren (verhindert Duplikate)
    var renderedIds = {};
    var lastId = 0;
    $messages.find('.irc-msg[data-id]').each(function() {
        var id = parseInt($(this).attr('data-id')) || 0;
        renderedIds[id] = true;
        if (id > lastId) lastId = id;
    });

    window['ircChat' + uid] = {
        scrollToBottom: function() {
            $messages.scrollTop($messages[0].scrollHeight);
            newCount = 0;
            $badge.hide();
        }
    };

    function isAtBottom() {
        return $messages[0].scrollHeight - $messages.scrollTop() <= $messages.outerHeight() + 60;
    }

    function escapeHtml(str) {
        return $('<div>').text(str).html();
    }

    function profileUrl(userId) {
        // simple approach: build URL from existing user link pattern
        return <?= json_encode($this->getUrl('user/profil/index/user/')) ?> + userId;
    }

    function appendMessages(messages) {
        var wasAtBottom = isAtBottom();
        var isEmpty     = $messages.find('.irc-empty').length > 0;
        var added       = 0;

        $.each(messages, function(i, msg) {
            if (renderedIds[msg.id]) return; // Duplikat überspringen
            renderedIds[msg.id] = true;

            if (isEmpty) {
                $messages.empty();
                isEmpty = false;
            }
            var nameStyle = msg.color ? ' style="color:' + msg.color + '"' : '';
            var nameHtml = msg.userId > 0
                ? '<a href="' + profileUrl(msg.userId) + '">' + escapeHtml(msg.name) + '</a>'
                : escapeHtml(msg.name);

            var html = '<div class="irc-msg" data-id="' + msg.id + '">'
                + '<span class="irc-time">' + escapeHtml(msg.time) + '</span>'
                + '<strong class="irc-name"' + nameStyle + '>&lt;' + nameHtml + '&gt;</strong>'
                + escapeHtml(msg.message)
                + '</div>';

            $messages.append(html);
            if (msg.id > lastId) lastId = msg.id;
            added++;
        });
        if (added === 0) return;

        if (wasAtBottom) {
            window['ircChat' + uid].scrollToBottom();
        } else if (messages.length > 0) {
            newCount += messages.length;
            $newCount.text(newCount);
            $badge.show();
        }
    }

    function poll() {
        if (polling) return;
        polling = true;
        $spin.show();
        $.getJSON(pollUrl, { since: lastId }, function(data) {
            if (data.messages && data.messages.length > 0) {
                appendMessages(data.messages);
            }
        }).always(function() { $spin.hide(); polling = false; });
    }

    // Char counter
    var msgInput = document.getElementById('irc-input-' + uid);
    var charCnt  = document.getElementById('irc-chars-' + uid);
    var charMax  = <?= $maxlength ?>;
    var rafPending = false;

    // Emoji picker
    var EMOJIS = ['😀','😂','🥲','😊','😇','🙂','😉','😍','🤩','😘','😋','😜','🤪','😎','🥳',
        '🤔','😐','😶','🙄','😏','😒','😔','😟','😣','😖','😩','🥺','😢','😭','😤','😠','😡',
        '🤯','😳','🥵','😱','😨','🤗','😴','🤤','🤢','🤮','🤧','🥴','😵','🤠',
        '👍','👎','👌','✌️','🤞','🤙','👈','👉','👆','👇','👋','🙌','👏','🤝','🙏',
        '❤️','🧡','💛','💚','💙','💜','🖤','💔','💕','💖','💘','💝',
        '🎉','🎊','🎈','🏆','🥇','🎮','🎯','🔥','⭐','✨','🌟','💫','🌈','🌙','☀️',
        '🍕','🍔','🌮','🍣','🍜','🍰','🎂','🍺','🥂','☕','🧋'];

    var $emojiPicker = document.getElementById('irc-emoji-picker-' + uid);
    var emojiBtn     = document.getElementById('irc-emoji-btn-' + uid);

    EMOJIS.forEach(function(em) {
        var s = document.createElement('span');
        s.textContent = em;
        s.addEventListener('click', function() {
            var pos = msgInput.selectionStart;
            var val = msgInput.value;
            msgInput.value = val.slice(0, pos) + em + val.slice(pos);
            msgInput.focus();
            msgInput.selectionStart = msgInput.selectionEnd = pos + em.length;
            $emojiPicker.classList.remove('open');
        });
        $emojiPicker.appendChild(s);
    });

    if (emojiBtn) {
        emojiBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            $emojiPicker.classList.toggle('open');
        });
        document.addEventListener('click', function() {
            $emojiPicker.classList.remove('open');
        });
    }

    // Name color picker
    var colorInput  = document.getElementById('irc-colorinput-' + uid);
    var colorHidden = document.getElementById('irc-namecolor-' + uid);
    var colorBtn    = document.getElementById('irc-colorbtn-' + uid);
    var storedColor = localStorage.getItem('voxchat_namecolor') || '';

    function applyColor(hex) {
        if (hex) {
            colorBtn.style.background = hex;
            colorHidden.value = hex;
            localStorage.setItem('voxchat_namecolor', hex);
        }
    }
    if (storedColor) applyColor(storedColor);

    if (colorBtn && colorInput) {
        colorBtn.addEventListener('click', function() { colorInput.click(); });
        colorInput.addEventListener('input', function() { applyColor(this.value); });
    }

    if (msgInput) {
        msgInput.addEventListener('input', function() {
            var len = this.value.length;
            if (!rafPending) {
                rafPending = true;
                requestAnimationFrame(function() {
                    charCnt.textContent = len + ' / ' + charMax;
                    charCnt.classList.toggle('over', len >= charMax);
                    rafPending = false;
                });
            }
        });
        msgInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                $('#irc-form-' + uid).trigger('submit');
            }
        });
    }

    // Form submit
    $('#irc-form-' + uid).on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $btn  = $form.find('button[type=submit]');
        $btn.prop('disabled', true);

        $.ajax({
            type: 'POST',
            url:  saveUrl,
            data: $form.serialize(),
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    $form.find('input[name=message]').val('');
                    $('#irc-chars-' + uid).text('0 / ' + <?= $maxlength ?>);
                    poll();
                } else {
                    alert(data.error || <?= json_encode($this->getTrans('saveFailed')) ?>);
                }
            },
            error: function() {
                alert(<?= json_encode($this->getTrans('saveFailed')) ?>);
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });

    // Initial scroll + polling
    window['ircChat' + uid].scrollToBottom();
    if (refresh > 0) {
        setInterval(poll, refresh * 1000);
    }

    // Hide badge when user scrolls down manually
    $messages.on('scroll', function() {
        if (isAtBottom()) {
            newCount = 0;
            $badge.hide();
        }
    });
}());
</script>
