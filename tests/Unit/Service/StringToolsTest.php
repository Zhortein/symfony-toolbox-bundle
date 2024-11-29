<?php

namespace Zhortein\SymfonyToolboxBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Zhortein\SymfonyToolboxBundle\Service\StringTools;

class StringToolsTest extends TestCase
{
    public function testIsArrayOfStrings(): void
    {
        $this->assertTrue(StringTools::isArrayOfStrings(['one', 'two', 'three']));
        $this->assertFalse(StringTools::isArrayOfStrings(['one', 2, 'three']));
        $this->assertFalse(StringTools::isArrayOfStrings('not an array'));
    }

    public function testSanitizeFileName(): void
    {
        $this->assertSame('filename-example', StringTools::sanitizeFileName('FileName Example!@#'));
        $this->assertSame('filename', StringTools::sanitizeFileName('FileName Example!@#', 8));
    }

    public function testRemoveDiacritics(): void
    {
        $this->assertSame('resume', StringTools::removeDiacritics('résumé'));
        $this->assertSame('eclair', StringTools::removeDiacritics('éclair'));
    }

    public function testSanitizeString(): void
    {
        $this->assertSame('some text', StringTools::sanitizeString('<p>some text</p>'));
        $this->assertSame('à é è', StringTools::sanitizeString('e0 e9 e8'));
        $this->assertNull(StringTools::sanitizeString(null));
    }

    public function testExplodeCsvLine(): void
    {
        $this->assertSame(['one', 'two', 'three'], StringTools::explodeCsvLine('one,two,three'));
        $this->assertSame(['one', 'two', 'three'], StringTools::explodeCsvLine('"one","two","three"', ',', '"'));
        $this->assertSame(['one;two', 'three'], StringTools::explodeCsvLine('"one;two",three', ','));
    }

    public function testTruncate(): void
    {
        $this->assertSame('hello wor…', StringTools::truncate('hello world example', 12));
        $this->assertSame('short', StringTools::truncate('short', 10));
    }

    public function testText2Boolean(): void
    {
        $this->assertTrue(StringTools::text2Boolean('yes'));
        $this->assertTrue(StringTools::text2Boolean('oui'));
        $this->assertTrue(StringTools::text2Boolean('1'));
        $this->assertTrue(StringTools::text2Boolean('o'));
        $this->assertFalse(StringTools::text2Boolean('no'));
        $this->assertFalse(StringTools::text2Boolean(null));
    }

    public function testReplaceSpecialChar(): void
    {
        $this->assertSame('oe', StringTools::replaceSpecialChar('œ'));
        $this->assertSame('C', StringTools::replaceSpecialChar('Ç'));
        $this->assertSame('ae', StringTools::replaceSpecialChar('æ'));
    }

    public function testCountWords(): void
    {
        $this->assertSame(4, StringTools::countWords('this is a test'));
        $this->assertSame(4, StringTools::countWords('<p>this is a</p> <b>test</b>'));
        $this->assertSame(0, StringTools::countWords(null));
    }

    public function testGetStringOrEmpty(): void
    {
        $this->assertSame('text', StringTools::getStringOrEmpty('text'));
        $this->assertSame('123', StringTools::getStringOrEmpty(123));
        $this->assertSame('', StringTools::getStringOrEmpty(null));
    }
}
