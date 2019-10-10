<?php

declare(strict_types=1);

namespace App\Application\Handler;

use App\Application\Command\AcceptListingCommand;
use App\Domain\Listings;
use Symfony\Component\Workflow\WorkflowInterface;

final class AcceptListingHandler
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

    public function __invoke(AcceptListingCommand $acceptListingCommand): void
    {
        $listing = $this->listings->get($acceptListingCommand->id());
        $listing->activate($this->stateMachine);
    }
}
