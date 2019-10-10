<?php

declare(strict_types=1);

namespace App\Application\Handler;

use App\Application\Command\BuyListingCommand;
use App\Domain\Listings;
use Symfony\Component\Workflow\WorkflowInterface;

final class BuyListingHandler
{
    /** @var Listings */
    private $listings;

    /** @var WorkflowInterface */
    private $stateMachine;

    public function __construct(Listings $listings, WorkflowInterface $stateMachine)
    {
        $this->listings = $listings;
        $this->stateMachine = $stateMachine;
    }

    public function __invoke(BuyListingCommand $buyListingCommand): void
    {
        $listing = $this->listings->get($buyListingCommand->id());
        $listing->buy($this->stateMachine);
    }
}
