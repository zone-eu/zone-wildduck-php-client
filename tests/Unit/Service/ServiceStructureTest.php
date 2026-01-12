<?php

declare(strict_types=1);

namespace Zone\Wildduck\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use Zone\Wildduck\BaseWildduckClient;

/**
 * Smoke tests to verify all services have proper structure
 */
class ServiceStructureTest extends TestCase
{
    private array $services = [
        \Zone\Wildduck\Service\AddressService::class,
        \Zone\Wildduck\Service\ApplicationPasswordService::class,
        \Zone\Wildduck\Service\ArchiveService::class,
        \Zone\Wildduck\Service\AuditService::class,
        \Zone\Wildduck\Service\AuthenticationService::class,
        \Zone\Wildduck\Service\AutoreplyService::class,
        \Zone\Wildduck\Service\DkimService::class,
        \Zone\Wildduck\Service\DomainAliasService::class,
        \Zone\Wildduck\Service\FilterService::class,
        \Zone\Wildduck\Service\MailboxService::class,
        \Zone\Wildduck\Service\MessageService::class,
        \Zone\Wildduck\Service\StorageService::class,
        \Zone\Wildduck\Service\SubmissionService::class,
        \Zone\Wildduck\Service\TwoFactorAuthenticationService::class,
        \Zone\Wildduck\Service\UserService::class,
        \Zone\Wildduck\Service\WebhookService::class,
    ];

    /**
     * Test that all services can be instantiated
     */
    public function testAllServicesCanBeInstantiated(): void
    {
        $mockClient = $this->createMock(BaseWildduckClient::class);
        $mockClient->method('getAccessToken')->willReturn('test-token');

        foreach ($this->services as $serviceClass) {
            $service = new $serviceClass($mockClient);
            $this->assertInstanceOf($serviceClass, $service);
        }
    }

    /**
     * Test that all services have public methods
     */
    public function testAllServicesHavePublicMethods(): void
    {
        $mockClient = $this->createMock(BaseWildduckClient::class);

        foreach ($this->services as $serviceClass) {
            $reflection = new ReflectionClass($serviceClass);
            $publicMethods = array_filter(
                $reflection->getMethods(ReflectionMethod::IS_PUBLIC),
                fn($method) => !$method->isConstructor() && !$method->isStatic()
            );

            $this->assertGreaterThan(
                0,
                count($publicMethods),
                "Service {$serviceClass} should have at least one public method"
            );
        }
    }

    /**
     * Test that all service methods have return types
     */
    public function testAllServiceMethodsHaveReturnTypes(): void
    {
        foreach ($this->services as $serviceClass) {
            $reflection = new ReflectionClass($serviceClass);
            $publicMethods = array_filter(
                $reflection->getMethods(ReflectionMethod::IS_PUBLIC),
                fn($method) => !$method->isConstructor() && !$method->isStatic()
            );

            foreach ($publicMethods as $method) {
                $this->assertTrue(
                    $method->hasReturnType(),
                    "Method {$method->getName()} in {$serviceClass} should have a return type"
                );
            }
        }
    }

    /**
     * Test that all service methods use type hints
     */
    public function testAllServiceMethodsUseTypeHints(): void
    {
        foreach ($this->services as $serviceClass) {
            $reflection = new ReflectionClass($serviceClass);
            $publicMethods = array_filter(
                $reflection->getMethods(ReflectionMethod::IS_PUBLIC),
                fn($method) => !$method->isConstructor() && !$method->isStatic()
            );

            foreach ($publicMethods as $method) {
                foreach ($method->getParameters() as $param) {
                    $this->assertTrue(
                        $param->hasType(),
                        "Parameter \${$param->getName()} in method {$method->getName()} of {$serviceClass} should have a type hint"
                    );
                }
            }
        }
    }
}
