<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\Table\DummyTable;
use App\Entity\DummyEntity;
use App\Entity\DummyEntityInterface;

final class DummyRepository extends AbstractDatabaseRepository implements DummyRepositoryInterface
{
    public function find(int $id): DummyEntityInterface
    {
        $sql = 'SELECT * FROM ' . DummyTable::NAME . ' WHERE id = ?';

        /** @var array<string, string|null> $row */
        $row = $this->db
            ->row($sql, $id);

        return $this->createFromRow($row);
    }

    /**
     * @param array<string, string|null> $row
     */
    private function createFromRow(array $row): DummyEntityInterface
    {
        return new DummyEntity(
            (int)$row['id']
        );
    }
}
