<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

return [
    'menuIrc'               => 'VoxChat',
    'settings'              => 'Settings',
    'manage'                => 'Manage Messages',
    'reset'                 => 'Reset',

    // Sections
    'channelSection'        => 'Channel',
    'writeAccessSection'    => 'Write Access',
    'chatBehaviourSection'  => 'Chat Behaviour',
    'appearanceSection'     => 'Appearance',

    // Channel
    'channel'               => 'Channel Name',
    'channelDisplayHint'    => 'Display only (shown in chat header), e.g. my-channel',

    // Write access / visibility
    'guestView'             => 'Guests may see the chat',
    'guestViewHint'         => 'If disabled, the chat is invisible to non-logged-in visitors.',
    'writeAccess'           => 'Writing allowed for',
    'writeAccessHint'       => 'Select at least one group. Group 1 = Guests.',

    // Behaviour
    'chatLimit'             => 'Messages shown',
    'chatLimitHint'         => 'Maximum number of messages to load.',
    'maxLength'             => 'Max. message length',
    'chars'                 => 'chars',
    'autoRefresh'           => 'Auto-refresh',
    'seconds'               => 'seconds',
    'autoRefreshHint'       => '0 = disabled. Recommended: 15–60 seconds.',
    'maxAge'                => 'Keep messages for',
    'days'                  => 'days',
    'maxAgeHint'            => '0 = unlimited. Old messages are deleted automatically on the next send.',

    // Appearance
    'height'                => 'Chat height',
    'heightHint'            => 'Height of the chat area in pixels (min. 100 px).',

    // Box / Frontend
    'noMessages'            => 'No messages yet. Be the first to write!',
    'newMessages'           => 'new messages',
    'yourName'              => 'Your name',
    'messagePlaceholder'    => 'Type a message … (Enter to send)',
    'send'                  => 'Send',
    'loginToChat'           => 'Log in to participate',
    'noWriteAccess'         => 'You do not have write access to this chat.',
    'missingName'           => 'Please enter a name.',
    'missingMessage'        => 'Please enter a message.',
    'saveFailed'            => 'Message could not be saved.',

    // Admin
    'time'                  => 'Time',
    'name'                  => 'Name',
    'message'               => 'Message',
    'actions'               => 'Actions',
    'guest'                 => 'Guest',
    'deleteSelected'        => 'Delete selected',
    'confirmDelete'         => 'Really delete this message?',
    'confirmReset'          => 'Really delete all messages?',
    'resetWarning'          => 'This will permanently delete ALL chat messages.',
    'resetAll'              => 'Delete all messages',
    'cancel'                => 'Cancel',
    'deleteSuccess'         => 'Successfully deleted.',
    'saveSuccess'           => 'Settings saved.',

    // Style settings
    'styleColors'       => 'Colors & Transparency',
    'styleBg'           => 'Background color',
    'styleHeader'       => 'Header color',
    'styleInput'        => 'Input area background',
    'styleBorder'       => 'Border color',
    'styleText'         => 'Message text',
    'styleName'         => 'Nickname color',
    'styleTime'         => 'Timestamp color',
    'styleOpacity'      => 'Opacity',
];
