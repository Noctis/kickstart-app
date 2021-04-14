<?php

declare(strict_types=1);

namespace App\Repository;

use ParagonIE\EasyDB\EasyDB;

abstract class AbstractDatabaseRepository
{
    protected EasyDB $db;

    public function __construct(EasyDB $db)
    {
        $this->db = $db;
    }
}
