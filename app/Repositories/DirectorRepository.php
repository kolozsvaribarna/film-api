<?php
namespace App\Repositories;

class DirectorRepository extends BaseRepository
{
    public string $tableName = 'directors';

    public function create(array $data): ?int
    {

        return parent::create($data);
    }

    public function getDirectorById(int $id): array
    {
        $query = $this->select() . "WHERE id = $id ORDER BY first_name, last_name";
        return $this->mysqli->query($query)->fetch_all(MYSQLI_ASSOC);
    }
}