<?php

namespace Wildduck\Tests;

use AllowDynamicProperties;
use Orchestra\Testbench\TestCase;
use Wildduck\Http\Request;
use Wildduck;

#[AllowDynamicProperties]
class BasicTest extends TestCase
{

    protected function getEnvironmentSetUp($application): void
    {
        $application['config']->set('wildduck.host', 'http://localhost:8080');
        $application['config']->set('wildduck.debug', false);
    }

    protected function getPackageProviders($application): array
    {
        return ['Wildduck\ServiceProvider'];
    }

    protected function getPackageAliases($application): array
    {
        return [
            'Wildduck' => 'Wildduck\Facades\Wildduck',
        ];
    }

    public function testUserCreation(): array
    {
        $r = Wildduck::users()->create([
            'username' => 'ivan',
            'password' => 'Asd123',
        ]);

        var_export($r);

        $this->assertTrue($r['code'] === Request::HTTP_OK);
        $this->assertArrayHasKey('data', $r);
        $this->assertTrue($r['data']['success']);
        $this->assertArrayHasKey('id', $r['data']);

        return $r['data']['id'];
    }

    public function testLogin(): void
    {
        $params = [
            'username' => 'ivan',
            'password' => 'Asd123',
        ];

        $r = Wildduck::authentication()->authenticate($params);
        var_export($r);

        $this->assertTrue($r['code'] === Request::HTTP_OK);
        $this->assertArrayHasKey('data', $r);
        $this->assertTrue($r['data']['success']);
        $this->assertArrayHasKey('id', $r['data']);
        $this->assertTrue($r['data']['username'] === $params['username']);
    }

    /**
     * @depends testUserCreation
     */
    public function testUserDeletion($id): void
    {
        $r = Wildduck::users()->delete([
            'id' => $id,
        ]);
        var_export($r);

        $this->assertTrue($r['code'] === Request::HTTP_OK);
        $this->assertArrayHasKey('data', $r);
        $this->assertTrue($r['data']['success']);
    }
}
