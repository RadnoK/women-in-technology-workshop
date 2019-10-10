<?php

declare(strict_types=1);

namespace App\Domain;

use Money\Money;
use Ramsey\Uuid\UuidInterface;

class Listing
{
    public const AWAITING = 'awaiting';

    /** @var UuidInterface */
    private $id;

    /** @var Title */
    private $title;

    /** @var string */
    private $state = self::AWAITING;

    /** @var Money */
    private $price;

    /** @var string|null */
    private $description;

    private function __construct(
        UuidInterface $id,
        Title $title,
        string $state,
        Money $price,
        ?string $description = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->state = $state;
        $this->price = $price;
        $this->description = $description;
    }

    public static function create(UuidInterface $id, Title $title, Money $price, ?string $description = null): self
    {
        return new self(
            $id,
            $title,
            self::AWAITING,
            $price,
            $description
        );
    }
}
