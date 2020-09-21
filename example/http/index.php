<?php declare(strict_types=1);

require __DIR__.'/../../vendor/autoload.php';

use LDL\Http\Core\Request\Request;
use LDL\Http\Core\Request\RequestInterface;
use LDL\Http\Core\Response\Response;
use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Route\Dispatcher\RouteDispatcherInterface;
use LDL\Http\Router\Route\Factory\RouteFactory;
use LDL\Http\Router\Route\Group\RouteGroup;
use LDL\Http\Router\Router;
use LDL\Http\Router\Route\Config\Parser\RouteConfigParserCollection;
use LDL\Http\Router\Plugin\LDL\Auth\Config\AuthConfigParser;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\ProcedureRepository;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\Http\AuthHttpProcedure;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\File\Plain\PlainFileCredentialsProvider;
use LDL\Http\Router\Handler\Exception\Collection\ExceptionHandlerCollection;
use LDL\Http\Router\Plugin\LDL\Auth\Handler\Exception\AuthenticationExceptionHandler;
use LDL\Http\Router\Plugin\LDL\Auth\Verifier\AuthVerifierRepository;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Verifier\FalseVerifier;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator\TokenGeneratorRepository;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator\Token\LDLToken\LDLTokenPDOGenerator;

class Dispatcher implements RouteDispatcherInterface
{
    public function dispatch(
        RequestInterface $request,
        ResponseInterface $response
    )
    {
        return [
            'age' => (int) $request->get('age'),
            'name' => $request->get('name')
        ];
    }
}

/**
 * Create a provider repository which holds different authentication methods
 */
$providers = new ProcedureRepository();
$providers->append(
    new AuthHttpProcedure(
        new PlainFileCredentialsProvider(
            'users.txt'
        ),
        null,
        true
    )
);

$verifiers = new AuthVerifierRepository();
$verifiers->append(new FalseVerifier());

$generators = new TokenGeneratorRepository();
$generators->append(new LDLTokenPDOGenerator());

/**
 * Add auth parsing capabilities to route factory
 */
$parserCollection = new RouteConfigParserCollection();
$parserCollection->append(new AuthConfigParser($providers, $verifiers));

/**
 * Add global exception handler which handles AuthenticationRequired
 */
$exceptionHandlers = new ExceptionHandlerCollection();
$exceptionHandlers->append(new AuthenticationExceptionHandler());

try {
    $response = new Response();

    $router = new Router(
        Request::createFromGlobals(),
        $response,
        $exceptionHandlers
    );

    $routes = RouteFactory::fromJsonFile(
        __DIR__ . '/routes.json',
        $router,
        null,
        $parserCollection
    );

    $group = new RouteGroup('student', 'student', $routes);

    $router->addGroup($group);
    $router->dispatch()->send();

}catch(\Exception $e){

}

