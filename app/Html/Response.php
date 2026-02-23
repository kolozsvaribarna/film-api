<?php

namespace App\Html;

class Response
{
    public static function jsonResponse(array $data, int $code = 200): never {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['code' => $code, 'data' => $data], JSON_THROW_ON_ERROR);
        exit;
    }   

    public static function errorResponse(string $message, int $code = 400): never {
        self::jsonResponse(['error' => $message], $code);
    }

    
}