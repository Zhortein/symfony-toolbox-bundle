<?php

namespace Zhortein\SymfonyToolboxBundle\Tests\Enum;

use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zhortein\SymfonyToolboxBundle\Enum\Action;

class ActionTest extends TestCase
{
    private TranslatorInterface $translator;

    protected function setUp(): void
    {
        // Mock du TranslatorInterface
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->translator
            ->method('trans')
            ->willReturnCallback(function ($key) {
                return strtoupper($key); // Mock : retourne le label en majuscules
            });
    }

    public function testEnumValues(): void
    {
        // Vérifie que chaque case d'Action possède une valeur unique
        $expectedValues = [
            'ACT_NONE', 'ACT_CANCEL', 'ACT_SAVE', 'ACT_LIST', 'ACT_VIEW', 'ACT_ADD',
            'ACT_EDIT', 'ACT_INSTALL', 'ACT_SOFT_DELETE', 'ACT_DESTROY', 'ACT_RESTORE',
            'ACT_SEARCH', 'ACT_IMPORT', 'ACT_EXPORT', 'ACT_UPDATE', 'ACT_SEND', 'ACT_UPLOAD',
            'ACT_DOWNLOAD', 'ACT_CHANGE_STATUS', 'ACT_FACTORY_RESET', 'ACT_START', 'ACT_PAUSE',
            'ACT_FIRST', 'ACT_PREVIOUS', 'ACT_PLAY', 'ACT_STOP', 'ACT_NEXT', 'ACT_LAST',
            'ACT_LOG', 'ACT_API_CALL', 'ACT_ENABLE', 'ACT_DISABLE', 'ACT_HISTORY',
            'ACT_CONNECT', 'ACT_DISCONNECT', 'ACT_LOGIN', 'ACT_LOGOUT', 'ACT_ARCHIVED',
            'ACT_STATS', 'ACT_DASHBOARD', 'ACT_READ', 'ACT_UNREAD', 'ACT_IMPERSONATE',
            'ACT_EXIT_IMPERSONATE', 'ACT_GRANT', 'ACT_REVOKE', 'ACT_SHARE', 'ACT_COMMENT',
            'ACT_DUPLICATE', 'ACT_PRINT', 'ACT_REFRESH', 'ACT_PROCESS',
        ];

        $actualValues = array_map(fn ($action) => $action->value, Action::cases());
        $this->assertEquals($expectedValues, $actualValues, 'Les valeurs des cases Action ne correspondent pas aux valeurs attendues.');
    }

    public function testLabelTranslation(): void
    {
        foreach (Action::cases() as $action) {
            $translatedLabel = $action->label($this->translator);
            $this->assertEquals(strtoupper($action->name), $translatedLabel, "La traduction pour l'action {$action->name} est incorrecte.");
        }
    }

    public function testIcons(): void
    {
        foreach (Action::cases() as $action) {
            $iconClass = $action->icon(false); // Sans balise <span>
            $this->assertStringStartsWith('fa fa-', $iconClass, "L'icône pour l'action {$action->name} devrait commencer par 'fa-'.");
        }
    }

    public function testBadgeGeneration(): void
    {
        foreach (Action::cases() as $action) {
            $badge = $action->badge(true, true, 'fa', 'primary', 'bootstrap', $this->translator);
            $this->assertStringContainsString('badge', $badge, "Le badge pour l'action {$action->name} devrait contenir la classe 'badge'.");
            $this->assertStringContainsString('fa-', $badge, "Le badge pour l'action {$action->name} devrait contenir une icône 'fa-'.");
            $this->assertStringContainsString(strtoupper($action->name), $badge, "Le badge pour l'action {$action->name} devrait contenir le label en majuscules.");

            $badge = $action->badge(true, true, 'fa', 'grey', 'tailwind', $this->translator);
            $this->assertStringContainsString('bg-grey-500', $badge, "Le badge pour l'action {$action->name} devrait contenir la classe 'rounded'.");
            $this->assertStringContainsString('fa-', $badge, "Le badge pour l'action {$action->name} devrait contenir une icône 'fa-'.");
            $this->assertStringContainsString(strtoupper($action->name), $badge, "Le badge pour l'action {$action->name} devrait contenir le label en majuscules.");
        }
    }
}
