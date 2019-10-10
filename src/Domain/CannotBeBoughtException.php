<?php

declare(strict_types=1);

namespace App\Domain;

final class CannotBeBoughtException extends \DomainException
{
    protected $message = 'Listing already bought or archived';
}
