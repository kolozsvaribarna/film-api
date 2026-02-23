<?php
namespace App\Repositories;

class ActorRepository extends BaseRepository
{
    public string $tableName = 'actors';

    public function create(array $data): ?int
    {
        return parent::create($data);
    }

    public function getActorById(int $actorId): array
    {
        $query = $this->select() . "WHERE id = $actorId ORDER BY first_name";
        return $this->mysqli->query($query)->fetch_all(MYSQLI_ASSOC);
    }
}