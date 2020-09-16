<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Provider;

use LDL\Http\Core\Request\RequestInterface;
use LDL\Http\Core\Response\ResponseInterface;

interface AuthTokenProviderInterface
{
    public function extractTokenFromRequest(RequestInterface $request) : ?string;

    public function validateToken(
        ResponseInterface $response,
        string $token = null,
        ...$args
    );
}

