<?php

declare(strict_types=1);

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../bootstrap.php";

use App\Application\Command\AcceptListingCommand;
use App\Application\Command\BuyListingCommand;
use App\Application\Command\CreateListingCommand;
use App\Domain\Title;
use Money\Currency;
use Money\Money;
use Ramsey\Uuid\Uuid;

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
