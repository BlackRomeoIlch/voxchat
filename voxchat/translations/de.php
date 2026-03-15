<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

return [
    'menuIrc'               => 'VoxChat',
    'settings'              => 'Einstellungen',
    'manage'                => 'Nachrichten verwalten',
    'reset'                 => 'Zurücksetzen',

    // Sektionen
    'channelSection'        => 'Channel',
    'writeAccessSection'    => 'Schreibrechte',
    'chatBehaviourSection'  => 'Chat-Verhalten',
    'appearanceSection'     => 'Darstellung',

    // Channel
    'channel'               => 'Channel-Name',
    'channelDisplayHint'    => 'Nur zur Anzeige im Chat-Header, z. B. mein-kanal',

    // Schreibrechte / Sichtbarkeit
    'guestView'             => 'Gäste dürfen Chat sehen',
    'guestViewHint'         => 'Wenn deaktiviert, ist der Chat für nicht eingeloggte Besucher unsichtbar.',
    'writeAccess'           => 'Schreiben erlaubt für',
    'writeAccessHint'       => 'Mindestens eine Gruppe auswählen. Gruppe 1 = Gäste.',

    // Verhalten
    'chatLimit'             => 'Angezeigte Nachrichten',
    'chatLimitHint'         => 'Wie viele Nachrichten maximal geladen werden.',
    'maxLength'             => 'Max. Nachrichtenlänge',
    'chars'                 => 'Zeichen',
    'autoRefresh'           => 'Auto-Refresh',
    'seconds'               => 'Sekunden',
    'autoRefreshHint'       => '0 = deaktiviert. Empfohlen: 15–60 Sekunden.',
    'maxAge'                => 'Nachrichten behalten',
    'days'                  => 'Tage',
    'maxAgeHint'            => '0 = unbegrenzt. Alte Nachrichten werden beim nächsten Absenden automatisch gelöscht.',

    // Darstellung
    'height'                => 'Chat-Höhe',
    'heightHint'            => 'Höhe des Chat-Bereichs in Pixel (mind. 100 px).',

    // Box / Frontend
    'noMessages'            => 'Noch keine Nachrichten. Schreib die erste!',
    'newMessages'           => 'neue Nachrichten',
    'yourName'              => 'Dein Name',
    'messagePlaceholder'    => 'Nachricht eingeben … (Enter zum Senden)',
    'send'                  => 'Senden',
    'loginToChat'           => 'Einloggen um mitzuschreiben',
    'noWriteAccess'         => 'Du hast kein Schreibrecht in diesem Chat.',
    'missingName'           => 'Bitte gib einen Namen ein.',
    'missingMessage'        => 'Bitte gib eine Nachricht ein.',
    'saveFailed'            => 'Nachricht konnte nicht gespeichert werden.',

    // Admin
    'time'                  => 'Zeit',
    'name'                  => 'Name',
    'message'               => 'Nachricht',
    'actions'               => 'Aktionen',
    'guest'                 => 'Gast',
    'deleteSelected'        => 'Ausgewählte löschen',
    'confirmDelete'         => 'Nachricht wirklich löschen?',
    'confirmReset'          => 'Wirklich alle Nachrichten löschen?',
    'resetWarning'          => 'Dadurch werden ALLE Chat-Nachrichten unwiderruflich gelöscht.',
    'resetAll'              => 'Alle Nachrichten löschen',
    'cancel'                => 'Abbrechen',
    'deleteSuccess'         => 'Erfolgreich gelöscht.',
    'saveSuccess'           => 'Einstellungen gespeichert.',

    // Stil-Einstellungen
    'styleColors'       => 'Farben & Transparenz',
    'styleBg'           => 'Hintergrundfarbe',
    'styleHeader'       => 'Header-Farbe',
    'styleInput'        => 'Eingabefeld-Hintergrund',
    'styleBorder'       => 'Rahmenfarbe',
    'styleText'         => 'Nachrichtentext',
    'styleName'         => 'Nickname-Farbe',
    'styleTime'         => 'Zeitstempel-Farbe',
    'styleOpacity'      => 'Transparenz',
];
