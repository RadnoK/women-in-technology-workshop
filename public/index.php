<?php

declare(strict_types=1);

require_once __DIR__ . "/../vendor/autoload.php";

use App\Application\Command\AcceptListingCommand;
use App\Application\Command\BuyListingCommand;
use App\Application\Command\CreateListingCommand;
use App\Application\Handler\AcceptListingHandler;
use App\Application\Handler\BuyListingHandler;
use App\Application\Handler\CreateListingHandler;
use App\Domain\Listing;
use App\Domain\Title;
use App\Infrastructure\InMemory\ListingsRepository;
use Money\Currency;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\SupportStrategy\InstanceOfSupportStrategy;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;

$definitionBuilder = new DefinitionBuilder();
$definition = $definitionBuilder
    ->addPlaces(['awaiting', 'accepted', 'bought'])
    ->addTransition(new Transition('accept', 'awaiting', 'accepted'))
    ->addTransition(new Transition('buy', 'accepted', 'bought'))
    ->build()
;

$marking = new MethodMarkingStore(true, 'state');
$workflow = new Workflow($definition, $marking);

$registry = new Registry();
$registry->addWorkflow($workflow, new InstanceOfSupportStrategy(Listing::class));

$listingRepository = new ListingsRepository();

$bus = new MessageBus([
    new HandleMessageMiddleware(
        new HandlersLocator([
            CreateListingCommand::class => [new CreateListingHandler($listingRepository)],
            AcceptListingCommand::class => [new AcceptListingHandler($listingRepository, $workflow)],
            BuyListingCommand::class => [new BuyListingHandler($listingRepository, $workflow)],
        ])
    ),
]);

$id = Uuid::uuid4();

$bus->dispatch(
    new CreateListingCommand(
        $id,
        new Title('Alfa Romeo Giulia Veloce Q4'),
        new Money(23000000, new Currency('PLN'))
    )
);

$listing = $listingRepository->get($id);

dump($listing->getState());

$bus->dispatch(new AcceptListingCommand($id));

$listing = $listingRepository->get($id);

dump($listing->getState());

$bus->dispatch(new BuyListingCommand($id));

$listing = $listingRepository->get($id);

dump($listing->getState());

$workflow = $registry->get($listing);
