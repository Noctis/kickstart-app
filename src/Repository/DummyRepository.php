<?php declare(strict_types=1);
namespace App\Repository;

use App\Entity\DummyEntity;
use App\Entity\DummyEntityInterface;
use Noctis\KickStart\Repository\DatabaseRepository;

final class DummyRepository extends DatabaseRepository implements DummyRepositoryInterface
{
    public const TABLE_NAME = 'dummy';

    public function find(int $id): DummyEntityInterface
    {
        $sql = 'SELECT * FROM '. self::TABLE_NAME .' WHERE id = ?';

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