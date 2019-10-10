<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\Title;
use Money\Money;
use Ramsey\Uuid\UuidInterface;

final class CreateListingCommand
{
    /** @var UuidInterface */
    private $id;

    /** @var Title */
    private $title;

    /** @var Money */
    private $price;

    /** @var string|null */
    private $description;

    public function __construct(UuidInterface $id, Title $title, Money $price, ?string $description = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->price = $price;
        $this->description = $description;
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function title(): Title
    {
        return $this->title;
    }

    public function price(): Money
    {
        return $this->price;
    }

    public function description(): ?string
    {
        return $this->description;
    }
}
