<?php

namespace App\Html;

use App\Repositories\ActorRepository;
use App\Repositories\BaseRepository;
use App\Repositories\FilmRepository;
use App\Repositories\DirectorRepository;
use App\Repositories\GenreRepository;
use App\Repositories\StudioRepository;

class Request
{
    static array $acceptedRoutes = [
        'POST' => [
            '/films',
            '/directors',
            '/genres',
            '/actors',
            '/studios',
        ],
        'GET' => [
            '/films',
            '/films/{id}',
            '/directors',
            '/directors/{id}',
            '/genres',
            '/genres/{id}',
            '/actors',
            '/actors/{id}',
            '/studios',
            '/studios/{id}'
        ],
        'PUT' => [
            '/films/{id}',
            '/directors/{id}',
            '/genres/{id}',
            '/actors/{id}',
            '/studios/{id}',
        ],
        'DELETE' => [
            '/films/{id}',
            '/directors/{id}',
            '/genres/{id}',
            '/actors/{id}',
            '/studios/{id}'
        ],
    ];

    static function handle()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        if (!self::isRouteAllowed($requestMethod, $requestUri, self::$acceptedRoutes)) {
            return Response::jsonResponse(['error' => 'Route not allowed'], 400);
        }

        $requestData = self::getRequestData();
        $arrUri = self::requestUriToArray($_SERVER['REQUEST_URI']);
        $resourceName = self::getResourceName($arrUri);
        $resourceId = self::getResourceId($arrUri);
        $childResourceName = self::getChildResourceName($arrUri);

        // A metódus alapján meghívjuk a megfelelő függvényt
        switch ($requestMethod) {
            case "POST":
                self::postRequest($resourceName, $requestData);
                break;
            case "PUT":
                self::putRequest($resourceName, $resourceId, $requestData);
                break;
            case "GET":
                self::getRequest($resourceName, $resourceId, $childResourceName);
                break;
            case "DELETE":
                self::deleteRequest($resourceName, $resourceId);
                break;
            default:
                echo 'Unknown request type';
                break;
        }
    }

    private static function getRequestData(): array
    {
        $jsonData = json_decode(file_get_contents("php://input"), true) ?? [];
        $queryData = $_GET ?? [];
        return array_merge($queryData, $jsonData);
    }

    private static function requestUriToArray($uri): array
    {
        $arrUri = explode("/", trim($uri, "/"));
        return [
            'resourceName' => $arrUri[0] ?? null,
            'resourceId' => !empty($arrUri[1]) ? (int) $arrUri[1] : null,
            'childResourceName' => $arrUri[2] ?? null,
            'childResourceId' => !empty($arrUri[3]) ? (int) $arrUri[3] : null,
        ];
    }

    private static function getResourceId(array $arrUri): ?int
    {
        return $arrUri['resourceId'];
    }

    private static function getResourceName(array $arrUri): ?string
    {
        return $arrUri['resourceName'];
    }

    private static function getChildResourceId(array $arrUri): ?int
    {
        return $arrUri['childResourceId'];
    }

    private static function getChildResourceName(array $arrUri): ?string
    {
        return $arrUri['childResourceName'];
    }

    private static function isRouteMatch($route, $uri): bool
    {
        $routeParts = explode('/', trim($route, '/'));
        $uriParts = explode('/', trim($uri, '/'));

        if (count($routeParts) !== count($uriParts)) {
            return false;
        }

        foreach ($routeParts as $index => $routePart) {
            if (preg_match('/^{.*}$/', $routePart)) {
                continue;
            }
            if ($routePart !== $uriParts[$index]) {
                return false;
            }
        }

        return true;
    }

    private static function isRouteAllowed($method, $uri, $routes): bool
    {
        if (!isset($routes[$method])) {
            return false;
        }

        foreach ($routes[$method] as $route) {
            if (self::isRouteMatch($route, $uri)) {
                return true;
            }
        }

        return false;
    }

    private static function getRepository($resourceName): ?BaseRepository
    {
        switch ($resourceName) {
            case 'films':
                $repository = new FilmRepository();
                break;
            case 'directors':
                $repository = new DirectorRepository();
                break;
            case 'genres':
                $repository = new GenreRepository();
                break;
            case 'actors':
                $repository = new ActorRepository();
                break;
            case 'studios':
                $repository = new StudioRepository();
                break;
            default:
                $repository = null;
        }

        return $repository;
    }

    private static function postRequest($resourceName, $requestData)
    {
        $repository = self::getRepository($resourceName);
        if (!$repository) {
            Response::errorResponse("Couldn't get repository", 400);
            return;
        }

        $newId = $repository->create($requestData);
        if ($newId) {
            Response::jsonResponse(['id' => $newId], 201); // 201 Created
            return;
        }

        Response::errorResponse("Bad request", 400);
    }

    private static function deleteRequest($resourceName, $resourceId)
    {
        $repository = self::getRepository($resourceName);
        $result = $repository->delete($resourceId);
        if ($result) {
            $code = 204;
        }
        Response::jsonResponse([], $code);
    }
    private static function getRequest($resourceName, $resourceId = null, $childResourceName = null)
    {
        $repository = self::getRepository($resourceName);
        if ($resourceId) {
            $entity = $repository->find($resourceId);
            if (!$entity) {
                Response::jsonResponse([], 404);
                return;
            }
            Response::jsonResponse($entity, 200);
            return;
        }
        $entities = $repository->getAll();
        Response::jsonResponse($entities, 200);
    }

    private static function putRequest($resourceName, $resourceId, $requestData)
    {
        $repository = self::getRepository($resourceName);
        $code = 404;
        $entity = $repository->find($resourceId);
        if ($entity) {
            $data = [];
            foreach ($requestData as $key => $value) {
                $data[$key] = $value;
            }
            $result = $repository->update($resourceId, $data);
            if ($result) {
                $code = 202;
            }
        }
        Response::jsonResponse([], $code);
    }
}