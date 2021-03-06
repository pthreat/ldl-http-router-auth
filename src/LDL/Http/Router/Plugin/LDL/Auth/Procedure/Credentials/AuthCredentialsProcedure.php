<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure\Credentials;

use LDL\Http\Core\Request\RequestInterface;
use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\CredentialsProviderInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Verifier\AuthVerifierInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AuthProcedureInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AbstractAuthProcedure;

class AuthCredentialsProcedure extends AbstractAuthProcedure
{
    public const NAME = 'Credentials';

    public const DESCRIPTION = 'Provides an authentication based on credentials';

    /**
     * @var AuthCredentialsOptionsInterface
     */
    private $options;

    public function __construct(
        CredentialsProviderInterface $provider,
        AuthCredentialsOptionsInterface $options,
        bool $isDefault = false
    )
    {
        $this->options = $options;

        $this->setCredentialsProvider($provider)
            ->setDefault($isDefault)
            ->setNamespace(AuthProcedureInterface::NAMESPACE)
            ->setName(self::NAME)
            ->setDescription(self::DESCRIPTION);
    }

    public function getKeyFromRequest(RequestInterface $request): ?string
    {
        return $request->get($this->options->getKey());
    }

    public function getSecretFromRequest(RequestInterface $request): ?string
    {
        return $request->get($this->options->getSecret());
    }

    public function handle(
        RequestInterface $request,
        ResponseInterface $response
    ) : void
    {
        // TODO: Implement validateCredentials() method.
    }
}
