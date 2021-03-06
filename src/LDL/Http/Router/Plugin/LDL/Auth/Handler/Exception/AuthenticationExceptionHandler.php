<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Handler\Exception;

use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Handler\Exception\AbstractExceptionHandler;
use LDL\Http\Router\Plugin\LDL\Auth\Dispatcher\Exception\AuthenticationFailureException;
use LDL\Http\Router\Plugin\LDL\Auth\Dispatcher\Exception\AuthenticationRequiredException;
use LDL\Http\Router\Router;
use Symfony\Component\HttpFoundation\ParameterBag;

class AuthenticationExceptionHandler extends AbstractExceptionHandler
{
    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return 'LDLAuthPlugin';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'AuthExceptionHandler';
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return 1;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return true;
    }

    public function handle(
        Router $router,
        \Exception $e,
        string $context,
        ParameterBag $urlParameters = null
    ): ?int
    {
        if($e instanceof AuthenticationRequiredException){
            return ResponseInterface::HTTP_CODE_UNAUTHORIZED;
        }

        if($e instanceof AuthenticationFailureException){
            return ResponseInterface::HTTP_CODE_FORBIDDEN;
        }

        return null;
    }

}