<?php

namespace Wildduck\Tests;

use Wildduck;

class BasicTest extends \Orchestra\Testbench\TestCase
{

    protected function getEnvironmentSetUp($application)
    {
        $application['config']->set('wildduck.host', 'http://localhost:8080');
        $application['config']->set('wildduck.debug', true);
    }

    protected function getPackageProviders($application)
    {
        return ['Wildduck\ServiceProvider'];
    }

    protected function getPackageAliases($application)
    {
        return [
            'Wildduck' => 'Wildduck\Facade',
        ];
    }

    public function testUserCreation()
    {
        $r = Wildduck::users()->create([
            'username' => 'ivan',
            'password' => 'Asd123',
        ]);

        var_export($r);

        $this->assertTrue($r['code'] === Wildduck\Http\Request::HTTP_OK);
        $this->assertArrayHasKey('data', $r);
        $this->assertTrue($r['data']['success']);
        $this->assertArrayHasKey('id', $r['data']);

        return $r['data']['id'];
    }

    public function testLogin()
    {
        $params = [
            'username' => 'ivan',
            'password' => 'Asd123',
        ];

        $r = Wildduck::authentication()->authenticate($params);
        var_export($r);

        $this->assertTrue($r['code'] === Wildduck\Http\Request::HTTP_OK);
        $this->assertArrayHasKey('data', $r);
        $this->assertTrue($r['data']['success']);
        $this->assertArrayHasKey('id', $r['data']);
        $this->assertTrue($r['data']['username'] === $params['username']);
    }

    /**
     * @depends testUserCreation
     */
    public function testUserDeletion($id)
    {
        $r = Wildduck::users()->delete([
            'id' => $id,
        ]);
        var_export($r);

        $this->assertTrue($r['code'] === Wildduck\Http\Request::HTTP_OK);
        $this->assertArrayHasKey('data', $r);
        $this->assertTrue($r['data']['success']);
    }
}
