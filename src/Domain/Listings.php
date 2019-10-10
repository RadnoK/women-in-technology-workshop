<?php

declare(strict_types=1);

namespace App\Domain;

use Ramsey\Uuid\UuidInterface;

interface Listings
{
    public function get(UuidInterface $uuid): Listing;

    public function add(Listing $listing): void;
}
