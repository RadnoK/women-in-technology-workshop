<?php

declare(strict_types=1);

namespace App\Application\Handler;

use App\Application\Command\CreateListingCommand;
use App\Domain\Listing;
use App\Domain\Listings;

final class CreateListingHandler
{
    /** @var Listings */
    private $listings;

    public function __construct(Listings $listings)
    {
        $this->listings = $listings;
    }

    public function __invoke(CreateListingCommand $createListingCommand): void
    {
        $this->listings->add(
            Listing::create(
                $createListingCommand->id(),
                $createListingCommand->title(),
                $createListingCommand->price()
            )
        );
    }
}
