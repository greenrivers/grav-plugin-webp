<?php
/**
 * @author Greenrivers
 * @copyright Copyright (c) 2022 Greenrivers
 * @package Grav\Plugin\Webp
 */

namespace Grav\Plugin\Webp\Utils;

use JsonException;

class Response
{
    public const HTTP_STATUS_200 = 200;

    /**
     * @param array $response
     * @param int $code
     */
    public static function sendJsonResponse(array $response, int $code = self::HTTP_STATUS_200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');

        try {
            echo json_encode($response, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            Logger::addErrorMessage($e->getMessage());
            echo $e->getMessage();
        }

        exit();
    }
}
