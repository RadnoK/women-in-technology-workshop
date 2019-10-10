<?php

declare(strict_types=1);

namespace App\Domain;

final class Title
{
    /** @var string */
    private $value;

    public function __construct(string $value)
    {
        if (\strlen($value) <= 3) {
            throw new TooShortTitleException();
        }

        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }
}
