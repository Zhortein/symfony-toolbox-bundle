<?php

namespace Zhortein\SymfonyToolboxBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Zhortein\SymfonyToolboxBundle\Enum\Day;
use Zhortein\SymfonyToolboxBundle\Service\DateToolBox;

class DateToolBoxTest extends TestCase
{
    private ?LoggerInterface $mockLogger;

    protected function setUp(): void
    {
        // Mock the LoggerInterface to test the logger functionality
        $this->mockLogger = $this->createMock(LoggerInterface::class);
        DateToolBox::setLogger($this->mockLogger);
    }

    public function testGetDayEnumFromName(): void
    {
        $this->assertEquals(Day::WEDNESDAY, DateToolBox::getDayEnumFromName('mer', 'fr'));

        // Test with different days and locales to ensure accurate mappings
        $this->assertEquals(Day::MONDAY, DateToolBox::getDayEnumFromName('lundi', 'fr'));
        $this->assertEquals(Day::MONDAY, DateToolBox::getDayEnumFromName('monday', 'en'));
        $this->assertEquals(Day::TUESDAY, DateToolBox::getDayEnumFromName('mardi', 'fr'));
        $this->assertEquals(Day::TUESDAY, DateToolBox::getDayEnumFromName('tuesday', 'en'));

        // Test with abbreviations
        $this->assertEquals(Day::WEDNESDAY, DateToolBox::getDayEnumFromName('mer.', 'fr'));
        $this->assertEquals(Day::WEDNESDAY, DateToolBox::getDayEnumFromName('mer', 'fr'));
        $this->assertEquals(Day::WEDNESDAY, DateToolBox::getDayEnumFromName('wed', 'en'));

        // Test with unknown name
        $this->assertNull(DateToolBox::getDayEnumFromName('unknown_day', 'en'));
    }

    public function testSetAndGetLogger(): void
    {
        // Ensure the logger was set and retrieved correctly
        $this->assertNotNull(DateToolBox::getLogger(), 'Logger should be set before testing.');
        $this->assertInstanceOf(LoggerInterface::class, DateToolBox::getLogger());
    }

    public function testSetTimezone(): void
    {
        // Test setting and getting the timezone
        $timezone = new \DateTimeZone('Europe/Paris');
        DateToolBox::setTimezone('Europe/Paris');
        $this->assertEquals($timezone, DateToolBox::getTimezone());

        $timezone = new \DateTimeZone('America/New_York');
        DateToolBox::setTimezone($timezone);
        $this->assertEquals($timezone, DateToolBox::getTimezone());
    }

    public function testGetDayEnumFromNameWithDifferentLocales(): void
    {
        // Testing additional locales to ensure internationalization support
        $this->assertEquals(Day::FRIDAY, DateToolBox::getDayEnumFromName('viernes', 'es'));
        $this->assertEquals(Day::SATURDAY, DateToolBox::getDayEnumFromName('samedi', 'fr'));
        $this->assertEquals(Day::SUNDAY, DateToolBox::getDayEnumFromName('domingo', 'es'));
    }
}
