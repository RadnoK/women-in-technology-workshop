<?php

declare(strict_types=1);

namespace App\Infrastructure\InMemory;

use App\Domain\Listing;
use App\Domain\Listings;
use Ramsey\Uuid\UuidInterface;

final class ListingsRepository implements Listings
{
    /** @var Listing[]|array */
    private $listings;

    public function get(UuidInterface $uuid): Listing
    {
        return $this->listings[$uuid->toString()];
    }

    public function add(Listing $listing): void
    {
        $this->listings[$listing->id()->toString()] = $listing;
    }
}
