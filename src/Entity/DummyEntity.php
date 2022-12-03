<?php

declare(strict_types=1);

namespace App\Entity;

final class DummyEntity implements DummyEntityInterface
{
    public function __construct(private readonly int $id)
    {
    }

    public function getID(): int
    {
        return $this->id;
    }
}
