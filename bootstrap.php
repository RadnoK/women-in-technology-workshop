<?php

declare(strict_types=1);

use App\Application\Command\AcceptListingCommand;
use App\Application\Command\BuyListingCommand;
use App\Application\Command\CreateListingCommand;
use App\Application\Handler\AcceptListingHandler;
use App\Application\Handler\BuyListingHandler;
use App\Application\Handler\CreateListingHandler;
use App\Domain\Listing;
use App\Infrastructure\InMemory\ListingsRepository;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\DBAL\Configuration as DBALConfiguration;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration as ORMConfiguration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\Proxy\ProxyFactory;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\SupportStrategy\InstanceOfSupportStrategy;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;

/*
 * TODO Should be replaced by Container :)
 */

$cache = new ArrayCache();

// Doctrine Configuration
$dbalConfiguration = new DBALConfiguration();
$ormConfiguration = new ORMConfiguration();
$ormConfiguration->setMetadataDriverImpl(new SimplifiedXmlDriver([__DIR__ . '/config/doctrine/orm' => 'App\Domain']));
$ormConfiguration->setNamingStrategy(new UnderscoreNamingStrategy(CASE_LOWER));
$ormConfiguration->setMetadataCacheImpl($cache);
$ormConfiguration->setQueryCacheImpl($cache);
$ormConfiguration->setProxyDir(__DIR__ . '/var/cache/orm');
$ormConfiguration->setProxyNamespace('DoctrineProxy');
$ormConfiguration->setAutoGenerateProxyClasses(true);
$ormConfiguration->setAutoGenerateProxyClasses(ProxyFactory::AUTOGENERATE_EVAL);

$connectionParams = [
    'url' => 'sqlite:///var/marketplace.sqlite',
];

$connection = DriverManager::getConnection($connectionParams, $dbalConfiguration);
$entityManager = EntityManager::create($connection, $ormConfiguration);

// Symfony Workflow Configuration
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

// Symfony Messenger Configuration
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
