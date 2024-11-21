<?php

namespace Zhortein\SymfonyToolboxBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Zhortein\SymfonyToolboxBundle\Enum\Day;
use Zhortein\SymfonyToolboxBundle\Service\BusinessDateTime;
use Zhortein\SymfonyToolboxBundle\Service\HolidayProviderFactory;
use Zhortein\SymfonyToolboxBundle\Service\HolidayProviderManager;
use Zhortein\SymfonyToolboxBundle\Service\HolidayProviders\FranceHolidayProvider;
use Zhortein\SymfonyToolboxBundle\Service\HolidayProviders\HolidayCalculator;

class BusinessDateTimeTest extends TestCase
{
    private HolidayProviderManager $holidayProviderManager;
    private BusinessDateTime $businessDateTime;

    protected function setUp(): void
    {
        $calculator = new HolidayCalculator();
        $factory = new HolidayProviderFactory($calculator);

        $providers = [];
        foreach (['FR', 'BE', 'DE', 'EN', 'US', 'ES'] as $countryCode) {
            $providers[$countryCode] = $factory->create($countryCode);
        }
        $this->holidayProviderManager = new HolidayProviderManager($providers);
        $this->businessDateTime = new BusinessDateTime($this->holidayProviderManager);
    }

    public function testSetWorkingDays(): void
    {
        $workingDays = [1, 2, 3, 4, 5]; // Monday to Friday
        $this->businessDateTime->setWorkingDays($workingDays);
        $this->assertEquals($workingDays, $this->businessDateTime->getWorkingDays(true));

        $workingDays = [Day::MONDAY, Day::TUESDAY, Day::WEDNESDAY, Day::THURSDAY, Day::FRIDAY]; // Monday to Friday
        $this->businessDateTime->setWorkingDays($workingDays);
        $this->assertEquals($workingDays, $this->businessDateTime->getWorkingDays());
    }

    public function testEmptyHolidays(): void
    {
        $this->businessDateTime->setHolidays(2024, 'FR');
        $this->assertNotEmpty($this->businessDateTime->getHolidays(2024, 'FR'));
        $this->assertFalse($this->businessDateTime->isHolidayListEmpty());

        $this->businessDateTime->emptyHolidays();
        $this->assertTrue($this->businessDateTime->isHolidayListEmpty());
    }

    public function testSetHolidays(): void
    {
        $holidays = $this->businessDateTime->setHolidays(2024, 'FR');
        $this->assertNotEmpty($holidays);
        $this->assertTrue($this->businessDateTime->isHoliday(new \DateTime('2024-12-25'), 'FR'));

        $holidays = $this->businessDateTime->setHolidays(2024, 'EN');
        $this->assertNotEmpty($holidays);
        $this->assertTrue($this->businessDateTime->isHoliday(new \DateTime('2024-12-26'), 'EN'));

        $holidays = $this->businessDateTime->setHolidays(2024, 'BE');
        $this->assertNotEmpty($holidays);
        $this->assertTrue($this->businessDateTime->isHoliday(new \DateTime('2024-07-21'), 'BE'));

        $holidays = $this->businessDateTime->setHolidays(2024, 'DE');
        $this->assertNotEmpty($holidays);
        $this->assertTrue($this->businessDateTime->isHoliday(new \DateTime('2024-10-03'), 'DE'));

        $holidays = $this->businessDateTime->setHolidays(2024, 'US');
        $this->assertNotEmpty($holidays);
        $this->assertTrue($this->businessDateTime->isHoliday(new \DateTime('2024-07-04'), 'US'));

        $holidays = $this->businessDateTime->setHolidays(2024, 'ES');
        $this->assertNotEmpty($holidays);
        $this->assertTrue($this->businessDateTime->isHoliday(new \DateTime('2024-10-12'), 'ES'));
    }

    public function testAddBusinessDays(): void
    {
        $date = new \DateTime('2024-01-01'); // A Monday
        $newDate = $this->businessDateTime->addBusinessDays($date, 5);
        $this->assertEquals('2024-01-08', $newDate->format('Y-m-d'));
    }

    public function testIsHoliday(): void
    {
        $date = new \DateTime('2024-12-25');
        $this->businessDateTime->setHolidays(2024, 'FR');
        $this->assertTrue($this->businessDateTime->isHoliday($date, 'FR'));
        $this->businessDateTime->setHolidays(2024, 'BE');
        $this->assertTrue($this->businessDateTime->isHoliday($date, 'BE'));
        $this->businessDateTime->setHolidays(2024, 'EN');
        $this->assertTrue($this->businessDateTime->isHoliday($date, 'EN'));
        $this->businessDateTime->setHolidays(2024, 'US');
        $this->assertTrue($this->businessDateTime->isHoliday($date, 'US'));
        $this->businessDateTime->setHolidays(2024, 'DE');
        $this->assertTrue($this->businessDateTime->isHoliday($date, 'DE'));
    }

    public function testIsMonday(): void
    {
        $date = new \DateTime('2024-01-01'); // A Monday
        $this->assertTrue($this->businessDateTime->isMonday($date));
    }

    public function testIsTuesday(): void
    {
        $date = new \DateTime('2024-01-02'); // A Tuesday
        $this->assertTrue($this->businessDateTime->isTuesday($date));
    }

    public function testIsWednesday(): void
    {
        $date = new \DateTime('2024-01-03'); // A Wednesday
        $this->assertTrue($this->businessDateTime->isWednesday($date));
    }

    public function testIsThursday(): void
    {
        $date = new \DateTime('2024-01-04'); // A Thursday
        $this->assertTrue($this->businessDateTime->isThursday($date));
    }

    public function testIsFriday(): void
    {
        $date = new \DateTime('2024-01-05'); // A Friday
        $this->assertTrue($this->businessDateTime->isFriday($date));
    }

    public function testIsSaturday(): void
    {
        $date = new \DateTime('2024-01-06'); // A Saturday
        $this->assertTrue($this->businessDateTime->isSaturday($date));
    }

    public function testIsSunday(): void
    {
        $date = new \DateTime('2024-01-07'); // A Sunday
        $this->assertTrue($this->businessDateTime->isSunday($date));
    }

    public function testIsWorkingDay(): void
    {
        $this->businessDateTime->setHolidays(2024, 'FR');
        $dates = ['2024-01-02', '2024-01-03', '2024-01-04', '2024-01-05', '2024-01-08'];
        foreach ($dates as $date) {
            $this->assertTrue($this->businessDateTime->isWorkingDay(new \DateTime($date)));
        }

        $dates = ['2024-01-01', '2024-01-06', '2024-01-07'];
        foreach ($dates as $date) {
            $this->assertFalse($this->businessDateTime->isWorkingDay(new \DateTime($date)));
        }
    }

    public function testIsWeekEnd(): void
    {
        $saturday = new \DateTime('2024-01-06'); // A Saturday
        $sunday = new \DateTime('2024-01-07');   // A Sunday
        $this->assertTrue($this->businessDateTime->isWeekEnd($saturday));
        $this->assertTrue($this->businessDateTime->isWeekEnd($sunday));

        $dates = ['2024-01-02', '2024-01-03', '2024-01-04', '2024-01-05', '2024-01-08'];
        foreach ($dates as $date) {
            $this->assertFalse($this->businessDateTime->isWeekEnd(new \DateTime($date)));
        }
    }

    public function testAddHoliday(): void
    {
        $date = new \DateTime('2024-12-26');
        $this->businessDateTime->addHoliday($date);
        $this->assertTrue($this->businessDateTime->isHoliday($date, 'FR'));
    }

    public function testGetProvider(): void
    {
        $provider = $this->holidayProviderManager->getProvider('FR');
        $this->assertInstanceOf(FranceHolidayProvider::class, $provider);

        $invalidProvider = $this->holidayProviderManager->getProvider('INVALID');
        $this->assertNull($invalidProvider);
    }

    public function testYearsLoaded(): void
    {
        $this->businessDateTime->setHolidays(2024, 'FR');
        $this->businessDateTime->addHolidaysForYear(2025);
        $this->assertCount(2, $this->businessDateTime->getYearsLoaded());
        $this->assertTrue($this->businessDateTime->isHoliday(new \DateTime('2025-12-25'), 'FR'));
        $this->assertFalse($this->businessDateTime->isHoliday(new \DateTime('2025-12-26'), 'FR'));

        $this->assertTrue($this->businessDateTime->isYearLoaded(2024));
        $this->assertTrue($this->businessDateTime->isYearLoaded(2025));
        $this->assertFalse($this->businessDateTime->isYearLoaded(2026));

        $this->assertEquals([2024, 2025], $this->businessDateTime->getYearsLoaded());
    }
}
