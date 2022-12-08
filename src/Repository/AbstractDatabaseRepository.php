<?php

declare(strict_types=1);

namespace App\Repository;

use ParagonIE\EasyDB\EasyDB;

abstract class AbstractDatabaseRepository
{
    public function __construct(protected EasyDB $db)
    {
    }
}
