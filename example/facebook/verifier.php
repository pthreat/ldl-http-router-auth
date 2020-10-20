<?php declare(strict_types=1);

require __DIR__.'/../../vendor/autoload.php';

use LDL\Http\Core\Request\Request;
use LDL\Http\Core\Request\RequestInterface;
use LDL\Http\Core\Response\Response;
use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\Database\PDO\MySQL\MySQLCredentialsProvider;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\Facebook\FacebookProcedureOptions;
use LDL\Http\Router\Route\Dispatcher\RouteDispatcherInterface;
use LDL\Http\Router\Route\Factory\RouteFactory;
use LDL\Http\Router\Route\Group\RouteGroup;
use LDL\Http\Router\Router;
use LDL\Http\Router\Route\Config\Parser\RouteConfigParserCollection;
use LDL\Http\Router\Plugin\LDL\Auth\Config\AuthConfigParser;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\ProcedureRepository;
use LDL\Http\Router\Handler\Exception\Collection\ExceptionHandlerCollection;
use LDL\Http\Router\Plugin\LDL\Auth\Handler\Exception\AuthenticationExceptionHandler;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Verifier\AuthVerifierRepository;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Verifier\FalseVerifier;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator\TokenGeneratorRepository;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator\Token\LDLToken\PDO\LDLTokenPDOGenerator;
use LDL\Http\Router\Handler\Exception\Handler\HttpRouteNotFoundExceptionHandler;
use LDL\Http\Router\Handler\Exception\Handler\HttpMethodNotAllowedExceptionHandler;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\Facebook\FacebookProcedure;

class Dispatcher implements RouteDispatcherInterface
{

    public function __construct(ProcedureRepository $repository)
    {
        $this->procedures = $repository;
    }

    public function dispatch(
        RequestInterface $request,
        ResponseInterface $response
    ) : array
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
$procedures = new ProcedureRepository();

define('APP_ID', '');
define('APP_SECRET', '');

$dsn = 'mysql:host=localhost;dbname=ldl_auth';
$pdo = new \PDO($dsn,'root', '',[
    \PDO::ATTR_EMULATE_PREPARES => false,
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
]);

$fbProcedure = new FacebookProcedure(
    new MySQLCredentialsProvider(
        $pdo,
        'user',
        'password'
    ),
    new FacebookProcedureOptions(
        APP_ID,
        APP_SECRET,
        'http://localhost:8080/login/v1.0/verifier'
    )
);

$procedures->append($fbProcedure);

$verifiers = new AuthVerifierRepository();
$verifiers->append(new FalseVerifier());

$generators = new TokenGeneratorRepository();
$generators->append(new LDLTokenPDOGenerator($pdo));

/**
 * Add auth parsing capabilities to route factory
 */
$parserCollection = new RouteConfigParserCollection();
$parserCollection->append(new AuthConfigParser($procedures, $verifiers, $generators));

/**
 * Add global exception handler which handles AuthenticationRequired
 */
$exceptionHandlers = new ExceptionHandlerCollection();
$exceptionHandlers->append(new AuthenticationExceptionHandler());
$exceptionHandlers->append(new HttpRouteNotFoundExceptionHandler());
$exceptionHandlers->append(new HttpMethodNotAllowedExceptionHandler());

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

    $group = new RouteGroup('login', 'login', $routes);

    $router->addGroup($group);
    $router->dispatch()->send();

}catch(\Exception $e){
    echo $e->getMessage();
}
?>

<html>
    <body>
        <a href="<?php echo $fbProcedure->getAuthorizationEndpoint(); ?>">Login Facebook</a>
    </body>
</html>

