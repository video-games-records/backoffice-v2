<?php

namespace App\BoundedContext\User\Domain\ValueObject;

use InvalidArgumentException;

final class Email
{
    private readonly string $value;

    public function __construct(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(sprintf('"%s" is not a valid email address', $email));
        }

        $this->value = strtolower($email);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(Email $other): bool
    {
        return $this->value === $other->getValue();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
