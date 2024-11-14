<?php

namespace Zhortein\SymfonyToolboxBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Zhortein\SymfonyToolboxBundle\Exception\UnsupportedTimeUnitException;
use Zhortein\SymfonyToolboxBundle\Service\TimeToolBox;

class TimeToolBoxTest extends TestCase
{
    public function testGetCurrentMicrotime(): void
    {
        $microtime = TimeToolBox::getCurrentMicrotime();
        $this->assertIsFloat($microtime);
        $this->assertGreaterThan(0, $microtime);
    }

    public function testGetWeekStartAndEnd(): void
    {
        $result = TimeToolBox::getWeekStartAndEnd(2024, 1);
        $this->assertArrayHasKey('start', $result);
        $this->assertArrayHasKey('end', $result);
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}/', $result['start']);
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}/', $result['end']);
    }

    public function testGetWeekStartAndEndInvalidWeek(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        TimeToolBox::getWeekStartAndEnd(2024, 54);
    }

    public function testDateIntervalToSeconds(): void
    {
        $interval = new \DateInterval('P1Y2M10DT2H30M10S');
        $seconds = TimeToolBox::dateIntervalToSeconds($interval);
        $expectedSeconds = (1 * 31536000) + (2 * 2592000) + (10 * 86400) + (2 * 3600) + (30 * 60) + 10;
        $this->assertSame($expectedSeconds, $seconds);
    }

    public function testDateIntervalDivide(): void
    {
        $numerator = new \DateInterval('P2D');
        $denominator = new \DateInterval('P1D');
        $this->assertSame(2.0, TimeToolBox::dateIntervalDivide($numerator, $denominator));
    }

    public function testDateIntervalDivideByZero(): void
    {
        $numerator = new \DateInterval('P1D');
        $denominator = new \DateInterval('PT0S');
        $this->assertNull(TimeToolBox::dateIntervalDivide($numerator, $denominator));
    }

    public function testDateIntervalAdd(): void
    {
        $interval1 = new \DateInterval('P1D');
        $interval2 = new \DateInterval('PT5H');
        $result = TimeToolBox::dateIntervalAdd($interval1, $interval2);

        $this->assertSame(1, $result->d);
        $this->assertSame(5, $result->h);
        $this->assertSame(0, $result->i);
        $this->assertSame(0, $result->s);
    }

    public function testDateIntervalSub(): void
    {
        $interval1 = new \DateInterval('P2D');
        $interval2 = new \DateInterval('P1D');
        $result = TimeToolBox::dateIntervalSub($interval1, $interval2);

        $this->assertSame(1, $result->d);
        $this->assertSame(0, $result->h);
        $this->assertSame(0, $result->i);
        $this->assertSame(0, $result->s);
    }

    public function testDateIntervalToIso8601(): void
    {
        $interval = new \DateInterval('P1Y2M3DT4H5M6S');
        $iso8601 = TimeToolBox::dateIntervalToIso8601($interval);
        $this->assertSame('P1Y2M3DT4H5M6S', $iso8601);
    }

    public function testNormalizeISO8601Duration(): void
    {
        $duration = 'P2Y4M10DT5H30M';
        $result = TimeToolBox::normalizeISO8601Duration($duration);
        $this->assertSame($duration, $result);
    }

    public function testNormalizeISO8601DurationInvalid(): void
    {
        $result = TimeToolBox::normalizeISO8601Duration('InvalidDuration');
        $this->assertSame('PT0S', $result);
    }

    public function testIsISO8601Duration(): void
    {
        $this->assertTrue(TimeToolBox::isISO8601Duration('P2Y4M10DT5H30M'));
        $this->assertFalse(TimeToolBox::isISO8601Duration('InvalidDuration'));
    }

    public function testConvertDateInterval(): void
    {
        $interval = new \DateInterval('P1D'); // Représente 1 jour

        $this->assertEqualsWithDelta(86400.0, TimeToolBox::convertDateInterval($interval, 'seconds'), 0.01);
        $this->assertEqualsWithDelta(1440.0, TimeToolBox::convertDateInterval($interval, 'minutes'), 0.01);
        $this->assertEqualsWithDelta(24.0, TimeToolBox::convertDateInterval($interval, 'hours'), 0.01);
        $this->assertEqualsWithDelta(1.0, TimeToolBox::convertDateInterval($interval, 'days'), 0.01);
    }

    public function testConvertDateIntervalInvalidUnit(): void
    {
        $this->expectException(UnsupportedTimeUnitException::class);
        $interval = new \DateInterval('P1D');
        TimeToolBox::convertDateInterval($interval, 'invalid_unit');
    }
}
