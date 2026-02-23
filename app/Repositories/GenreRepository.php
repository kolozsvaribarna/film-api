<?php
namespace App\Repositories;

class GenreRepository extends BaseRepository
{
    public string $tableName = 'genres';

    public function create(array $data): ?int
    {
        if (!isset($data['id'])) {
            throw new \Exception("GenreRepository error: id is required.");
        }

        return parent::create($data);
    }

    public function getByCategory(int $categoryId): array
    {
        $query = $this->select() . "WHERE id = $categoryId ORDER BY name";
        return $this->mysqli->query($query)->fetch_all(MYSQLI_ASSOC);
    }
}