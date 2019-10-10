<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\ORM;

use App\Domain\Listing;
use App\Domain\Listings;
use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\UuidInterface;

final class ListingRepository extends EntityRepository implements Listings
{
    public function get(UuidInterface $uuid): Listing
    {
        return $this->getEntityManager()->getRepository(Listing::class)->find($uuid->toString());
    }

    public function add(Listing $listing): void
    {
        $this->getEntityManager()->persist($listing);
        $this->getEntityManager()->flush();
    }
}
