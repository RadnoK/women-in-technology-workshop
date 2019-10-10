<?php

declare(strict_types=1);

namespace App\Domain;

final class TooShortTitleException extends \DomainException
{
    protected $message = "Listing title must have name longer than 3 characters.";
}
