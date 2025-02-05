<?php
namespace Core;

use Core\Response;

class Controller {
    protected function jsonResponse($data, $status = Response::HTTP_OK): Response {
        Response::json($data, $status);
    }

    protected function createdResponse($data): Response {
        Response::json($data, Response::HTTP_CREATED);
    }

    protected function errorResponse($message, $status = Response::HTTP_BAD_REQUEST): Response {
        Response::json(['error' => $message], $status);
    }

    protected function unauthorizedResponse($message = 'Unauthorized', $status = Response::HTTP_UNAUTHORIZED): Response {
        Response::json(['error' => $message], $status);
    }

    protected function forbiddenResponse($message = 'Forbidden', $status = Response::HTTP_FORBIDDEN): Response {
        Response::json(['error' => $message], $status);
    }

    protected function notFoundResponse($message = 'Not Found', $status = Response::HTTP_NOT_FOUND): Response {
        Response::json(['error' => $message], $status);
    }

    protected function methodNotAllowedResponse($message = 'Method Not Allowed', $status = Response::HTTP_METHOD_NOT_ALLOWED): Response {
        Response::json(['error' => $message], $status);
    }

    protected function internalServerErrorResponse($message = 'Internal Server Error', $status = Response::HTTP_INTERNAL_SERVER_ERROR): Response {
        Response::json(['error' => $message], $status);
    }
}
