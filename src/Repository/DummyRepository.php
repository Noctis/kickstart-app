<?php declare(strict_types=1);
namespace App\Repository;

use App\Database\Table\DummyTable;
use App\Entity\DummyEntity;
use App\Entity\DummyEntityInterface;
use Noctis\KickStart\Repository\AbstractDatabaseRepository;

final class DummyRepository extends AbstractDatabaseRepository implements DummyRepositoryInterface
{
    public function find(int $id): DummyEntityInterface
    {
        $sql = 'SELECT * FROM '. DummyTable::NAME .' WHERE id = ?';

        /** @psalm-suppress PossiblyNullArgument */
        return $this->createFromRow(
            $this->db
                ->row($sql, $id)
        );
    }

    private function createFromRow(array $row): DummyEntityInterface
    {
        return new DummyEntity(
            (int)$row['id']
        );
    }
}