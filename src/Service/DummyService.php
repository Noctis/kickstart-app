<?php declare(strict_types=1);
namespace App\Service;

final class DummyService implements DummyServiceInterface
{
    public function foo(): string
    {
        return 'foo';
    }
}