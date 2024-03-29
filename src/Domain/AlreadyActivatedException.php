<?php

declare(strict_types=1);

namespace App\Domain;

final class AlreadyActivatedException extends \DomainException
{
    protected $message = 'Listing is already accepted.';
}
