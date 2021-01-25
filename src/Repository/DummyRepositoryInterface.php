<?php declare(strict_types=1);
namespace App\Repository;

use App\Entity\DummyEntityInterface;

interface DummyRepositoryInterface
{
    public function find(int $id): DummyEntityInterface;
}