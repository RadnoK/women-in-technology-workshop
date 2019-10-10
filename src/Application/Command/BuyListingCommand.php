<?php

declare(strict_types=1);

namespace App\Application\Command;

use Ramsey\Uuid\UuidInterface;

final class BuyListingCommand
{
    /** @var UuidInterface */
    private $id;

    public function __construct(UuidInterface $id)
    {
        $this->id = $id;
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }
}
