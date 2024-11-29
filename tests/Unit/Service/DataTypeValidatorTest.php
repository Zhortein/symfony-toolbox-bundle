<?php

namespace Zhortein\SymfonyToolboxBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Zhortein\SymfonyToolboxBundle\Service\DataTypeValidator;

class DataTypeValidatorTest extends TestCase
{
    private DataTypeValidator $validator;

    protected function setUp(): void
    {
        $symfonyValidator = Validation::createValidator();
        $this->validator = new DataTypeValidator($symfonyValidator);
    }

    public function testEmailValid(): void
    {
        $this->assertTrue($this->validator->emailValid('test@example.com'));
        $this->assertFalse($this->validator->emailValid('invalid-email'));
    }

    public function testUrlHttpValid(): void
    {
        $this->assertTrue($this->validator->urlHttpValid('http://example.com'));
        $this->assertTrue($this->validator->urlHttpValid('https://example.com'));
        $this->assertTrue($this->validator->urlHttpValid('example.com'));
        $this->assertFalse($this->validator->urlHttpValid('example'));
        $this->assertTrue($this->validator->urlHttpValid('example', false));

        $this->assertFalse($this->validator->urlHttpValid('ftp://example.com'));
    }

    public function testUrlValid(): void
    {
        $this->assertTrue($this->validator->urlValid('http://example.com'));
        $this->assertTrue($this->validator->urlValid('https://example.com'));
        $this->assertTrue($this->validator->urlValid('ftp://example.com'));
        $this->assertTrue($this->validator->urlValid('sftp://example.com'));
        $this->assertTrue($this->validator->urlValid('example.com'));
    }
}
