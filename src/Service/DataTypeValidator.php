<?php

namespace Zhortein\SymfonyToolboxBundle\Service;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class DataTypeValidator
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    /**
     * Validates a given value against specified constraints.
     *
     * @param mixed        $value       the value to be validated
     * @param Constraint[] $constraints an array of constraints to apply during validation
     *
     * @return bool returns true if there are no validation errors; otherwise, false
     */
    private function validate(mixed $value, array $constraints): bool
    {
        $errors = $this->validator->validate($value, $constraints);

        return count($errors) < 1;
    }

    /**
     * Validate the syntax of the given email address.
     *
     * @param string $email the email address to validate
     * @param string $mode  the validation mode to use
     *
     * @return bool true if the email address is valid, false otherwise
     */
    public function emailValid(string $email, string $mode = Assert\Email::VALIDATION_MODE_HTML5): bool
    {
        return $this->validate($email, [new Assert\Email(['mode' => $mode])]);
    }

    /**
     * Validates whether a given URL is using the HTTP or HTTPS scheme.
     *
     * @param string $url the URL to validate
     *
     * @return bool returns true if the URL is valid and uses HTTP or HTTPS; otherwise, false
     */
    public function urlHttpValid(string $url, bool $requireTld = true): bool
    {
        return $this->urlValid($url, ['http', 'https'], $requireTld);
    }

    /**
     * Validates whether a given URL conforms to specified protocols.
     *
     * @param string   $url       the URL to validate
     * @param string[] $protocols The list of allowed protocols. Default protocols are 'http', 'https', 'ftp', and 'sftp'.
     *
     * @return bool returns true if the URL is valid within the specified protocols; otherwise, false
     */
    public function urlValid(string $url, array $protocols = ['http', 'https', 'ftp', 'sftp'], bool $requireTld = true): bool
    {
        if (!str_contains($url, '://')) {
            $url = 'https://'.$url;
        }

        // Assume protocols are configured via app parameters
        return $this->validate($url, [new Assert\Url(['protocols' => $protocols, 'requireTld' => $requireTld])]);
    }
}
