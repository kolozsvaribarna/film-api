<?php
namespace App\Repositories;

class FilmRepository extends BaseRepository
{
    public string $tableName = 'films';

    public function create(array $data): ?int
    {

        return parent::create($data);
    }

    public function getByFilm(int $filmId): array
    {
        $query = $this->select() . "WHERE id = $filmId ORDER BY title";
        return $this->mysqli->query($query)->fetch_all(MYSQLI_ASSOC);
    }
}