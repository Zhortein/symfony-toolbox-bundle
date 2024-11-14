<?php

namespace Zhortein\SymfonyToolboxBundle\Enum;

use Symfony\Contracts\Translation\TranslatorInterface;
use Zhortein\SymfonyToolboxBundle\Traits\EnumToArrayTrait;
use Zhortein\SymfonyToolboxBundle\Traits\TranslatableEnumTrait;

enum Action: string implements EnumActionInterface, EnumTranslatableInterface
{
    use EnumToArrayTrait;
    use TranslatableEnumTrait;

    case ACT_NONE = 'ACT_NONE';
    case ACT_CANCEL = 'ACT_CANCEL';
    case ACT_SAVE = 'ACT_SAVE';
    case ACT_LIST = 'ACT_LIST';
    case ACT_VIEW = 'ACT_VIEW';
    case ACT_ADD = 'ACT_ADD';
    case ACT_EDIT = 'ACT_EDIT';
    case ACT_INSTALL = 'ACT_INSTALL';
    case ACT_SOFT_DELETE = 'ACT_SOFT_DELETE';
    case ACT_DESTROY = 'ACT_DESTROY';
    case ACT_RESTORE = 'ACT_RESTORE';
    case ACT_SEARCH = 'ACT_SEARCH';
    case ACT_IMPORT = 'ACT_IMPORT';
    case ACT_EXPORT = 'ACT_EXPORT';
    case ACT_UPDATE = 'ACT_UPDATE';
    case ACT_SEND = 'ACT_SEND';
    case ACT_UPLOAD = 'ACT_UPLOAD';
    case ACT_DOWNLOAD = 'ACT_DOWNLOAD';
    case ACT_CHANGE_STATUS = 'ACT_CHANGE_STATUS';
    case ACT_FACTORY_RESET = 'ACT_FACTORY_RESET';
    case ACT_START = 'ACT_START';
    case ACT_PAUSE = 'ACT_PAUSE';
    case ACT_FIRST = 'ACT_FIRST';
    case ACT_PREVIOUS = 'ACT_PREVIOUS';
    case ACT_PLAY = 'ACT_PLAY';
    case ACT_STOP = 'ACT_STOP';
    case ACT_NEXT = 'ACT_NEXT';
    case ACT_LAST = 'ACT_LAST';
    case ACT_LOG = 'ACT_LOG';
    case ACT_API_CALL = 'ACT_API_CALL';
    case ACT_ENABLE = 'ACT_ENABLE';
    case ACT_DISABLE = 'ACT_DISABLE';
    case ACT_HISTORY = 'ACT_HISTORY';
    case ACT_CONNECT = 'ACT_CONNECT';
    case ACT_DISCONNECT = 'ACT_DISCONNECT';
    case ACT_LOGIN = 'ACT_LOGIN';
    case ACT_LOGOUT = 'ACT_LOGOUT';
    case ACT_ARCHIVED = 'ACT_ARCHIVED';
    case ACT_STATS = 'ACT_STATS';
    case ACT_DASHBOARD = 'ACT_DASHBOARD';
    case ACT_READ = 'ACT_READ';
    case ACT_UNREAD = 'ACT_UNREAD';
    case ACT_IMPERSONATE = 'ACT_IMPERSONATE';
    case ACT_EXIT_IMPERSONATE = 'ACT_EXIT_IMPERSONATE';
    case ACT_GRANT = 'ACT_GRANT';
    case ACT_REVOKE = 'ACT_REVOKE';
    case ACT_SHARE = 'ACT_SHARE';
    case ACT_COMMENT = 'ACT_COMMENT';
    case ACT_DUPLICATE = 'ACT_DUPLICATE';
    case ACT_PRINT = 'ACT_PRINT';
    case ACT_REFRESH = 'ACT_REFRESH';
    case ACT_PROCESS = 'ACT_PROCESS';

    public const string TRANSLATION_DOMAIN = 'zhortein_symfony_toolbox-actions';

    public const array ICONS = [
        self::ACT_NONE->value => 'fa-empty-set',
        self::ACT_CANCEL->value => 'fa-fa-ban',
        self::ACT_SAVE->value => 'fa-floppy-disk',
        self::ACT_LIST->value => 'fa-list',
        self::ACT_VIEW->value => 'fa-eye',
        self::ACT_ADD->value => 'fa-plus',
        self::ACT_EDIT->value => 'fa-edit',
        self::ACT_INSTALL->value => 'fa-boxes-packing',
        self::ACT_SOFT_DELETE->value => 'fa-times',
        self::ACT_EXIT_IMPERSONATE->value => 'fa-times',
        self::ACT_DESTROY->value => 'fa-trash-can',
        self::ACT_RESTORE->value => 'fa-trash-can-arrow-up',
        self::ACT_SEARCH->value => 'fa-searchengin',
        self::ACT_IMPORT->value => 'fa-arrow-right-from-file',
        self::ACT_EXPORT->value => 'fa-arrow-left-from-file',
        self::ACT_UPDATE->value => 'fa-rotate',
        self::ACT_SEND->value => 'fa-paper-plane',
        self::ACT_UPLOAD->value => 'fa-upload',
        self::ACT_DOWNLOAD->value => 'fa-download',
        self::ACT_CHANGE_STATUS->value => 'fa-retweet',
        self::ACT_FACTORY_RESET->value => 'fa-industry',
        self::ACT_START->value => 'fa-angles-left',
        self::ACT_FIRST->value => 'fa-angles-left',
        self::ACT_PAUSE->value => 'fa-pause',
        self::ACT_PREVIOUS->value => 'fa-angle-left',
        self::ACT_PLAY->value => 'fa-play',
        self::ACT_STOP->value => 'fa-stop',
        self::ACT_NEXT->value => 'fa-angle-right',
        self::ACT_LAST->value => 'fa-angles-right',
        self::ACT_LOG->value => 'fa-clock-rotate-left',
        self::ACT_HISTORY->value => 'fa-clock-rotate-left',
        self::ACT_DISABLE->value => 'fa-toggle-off',
        self::ACT_ENABLE->value => 'fa-toggle-on',
        self::ACT_API_CALL->value => 'fa-arrow-right-arrow-left',
        self::ACT_CONNECT->value => 'fa-link',
        self::ACT_DISCONNECT->value => 'fa-link-slash',
        self::ACT_LOGIN->value => 'fa-arrow-right-to-bracket',
        self::ACT_LOGOUT->value => 'fa-arrow-right-from-bracket',
        self::ACT_ARCHIVED->value => 'fa-file-zipper',
        self::ACT_STATS->value => 'fa-chart-line',
        self::ACT_READ->value => 'fa-envelope-open-text',
        self::ACT_UNREAD->value => 'fa-envelope',
        self::ACT_IMPERSONATE->value => 'fa-user-secret',
        self::ACT_GRANT->value => 'fa-user-check',
        self::ACT_REVOKE->value => 'fa-user-slash',
        self::ACT_SHARE->value => 'fa-share-alt',
        self::ACT_COMMENT->value => 'fa-comment',
        self::ACT_DUPLICATE->value => 'fa-clone',
        self::ACT_PRINT->value => 'fa-print',
        self::ACT_REFRESH->value => 'fa-sync',
        self::ACT_PROCESS->value => 'fa-cogs',
    ];

    public function icon(bool $withSpan = true, string $magnifyClass = '', string $faPrefix = 'fa', ?TranslatorInterface $translator = null, string $translationDomain = self::TRANSLATION_DOMAIN): string
    {
        $icon = self::ICONS[$this->value] ?? 'fa-question';
        $iconClass = trim(($faPrefix ? $faPrefix.' ' : '').$icon.($magnifyClass ? ' '.$magnifyClass : ''));

        return $withSpan
            ? sprintf('<span class="%s" title="%s"></span>', $iconClass, $this->label($translator, $translationDomain))
            : $iconClass;
    }

    public function badge(bool $icon = false, bool $text = true, string $faPrefix = 'fa', string $colorScheme = 'primary', string $framework = 'bootstrap', ?TranslatorInterface $translator = null, string $translationDomain = self::TRANSLATION_DOMAIN): string
    {
        $badgeIcon = $icon ? sprintf('<i class="%s"></i> ', $this->icon(false, '', $faPrefix)) : '';
        $badgeText = $text ? ucfirst($this->label($translator, $translationDomain)) : '';

        // Classes CSS selon le framework
        $badgeClass = match ($framework) {
            'tailwind' => sprintf('px-2 py-1 rounded bg-%s-500 text-white', $colorScheme),
            'custom' => $this->getCustomBadgeClass($colorScheme),
            default => sprintf('badge bg-%s', $colorScheme),
        };

        return sprintf('<span class="%s">%s%s</span>', $badgeClass, $badgeIcon, $badgeText);
    }

    protected function getCustomBadgeClass(string $colorScheme): string
    {
        // Logique personnalisée ici si nécessaire
        return sprintf('custom-badge custom-badge-%s', $colorScheme);
    }
}
