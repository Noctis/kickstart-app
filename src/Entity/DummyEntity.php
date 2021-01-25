<?php declare(strict_types=1);
namespace App\Entity;

final class DummyEntity implements DummyEntityInterface
{
    private int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getID(): int
    {
        return $this->id;
    }
}