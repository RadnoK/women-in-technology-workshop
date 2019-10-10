<?php

declare(strict_types=1);

namespace App\Domain;

use Money\Money;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Workflow\WorkflowInterface;

class Listing
{
    public const AWAITING = 'awaiting';

    /** @var UuidInterface */
    private $id;

    /** @var Title */
    private $title;

    /** @var string */
    private $state = self::AWAITING;

    /** @var Money */
    private $price;

    /** @var string|null */
    private $description;

    private function __construct(
        UuidInterface $id,
        Title $title,
        string $state,
        Money $price,
        ?string $description = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->state = $state;
        $this->price = $price;
        $this->description = $description;
    }

    public static function create(UuidInterface $id, Title $title, Money $price, ?string $description = null): self
    {
        return new self(
            $id,
            $title,
            self::AWAITING,
            $price,
            $description
        );
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function activate(WorkflowInterface $workflow): void
    {
        if (false === $workflow->can($this, 'accept')) {
            throw new AlreadyActivatedException();
        }

        $workflow->apply($this, 'accept');
    }

    public function buy(WorkflowInterface $workflow): void
    {
        if (false === $workflow->can($this, 'buy')) {
            throw new CannotBeBoughtException();
        }

        $workflow->apply($this, 'buy');
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }
}
