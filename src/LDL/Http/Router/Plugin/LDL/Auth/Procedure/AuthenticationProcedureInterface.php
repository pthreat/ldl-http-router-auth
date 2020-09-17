<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure;

interface AuthenticationProcedureInterface
{
    public const NAMESPACE = 'LDLAuthPlugin';

    /**
     * @return string
     */
    public function getNamespace() : string;

    /**
     * @return string
     */
    public function getName() : string;

    /**
     * @return mixed
     */
    public function getDescription() : string;
}