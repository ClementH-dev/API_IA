<?php

namespace Middleware;

use Core\Controller;

class SanitizeMiddleware extends Controller {

    public static function sanitizeData(array $data): array {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = htmlspecialchars(strip_tags($value));
            }
        }

        return $data;
    }
}