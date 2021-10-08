<?php
/**
 * @author Greenrivers
 * @copyright Copyright (c) 2021 Greenrivers
 * @package Grav\Plugin\Webp
 */

namespace Grav\Plugin\Webp\Utils;

use JsonException;

class Response
{
    /**
     * @param array $response
     * @param int $code
     * @throws JsonException
     */
    public static function sendJsonResponse(array $response, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');

        echo json_encode($response, JSON_THROW_ON_ERROR);
        exit();
    }
}
