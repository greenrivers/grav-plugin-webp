<?php
/**
 * @author Greenrivers Team
 * @copyright Copyright (c) 2021 Greenrivers
 * @package Grav\Plugin
 */

namespace Grav\Plugin\Webp\Utils;

class Response
{
    /**
     * @param array $response
     * @param int $code
     */
    public static function sendJsonResponse(array $response, int $code = 200)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');

        echo json_encode($response);
        exit();
    }
}
