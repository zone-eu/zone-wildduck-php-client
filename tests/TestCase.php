<?php

namespace Wildduck\Tests;

use Wildduck;

class TestCase extends \Orchestra\Testbench\TestCase
{

    protected function getEnvironmentSetUp($application)
    {
        $application['config']->set('wildduck.host', 'http://localhost:8080');
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

//    public function testUserCreation()
//    {
//        $r = Wildduck::users()->create([
//            'username' => 'ivan',
//            'password' => 'Asd123',
//        ]);
//
//        $this->assertTrue($r['code'] === Wildduck\Http\Request::HTTP_OK);
//        $this->assertArrayHasKey('data', $r);
//        $this->assertTrue($r['data']['success']);
//        $this->assertArrayHasKey('id', $r['data']);
//
//        return $r['data']['id'];
//    }

    public function testLogin()
    {
        $params = [
            'name' => 'ivan',
            'password' => 'Asd123',
        ];

        $r = Wildduck::authentication()->authenticate($params);

        $this->assertTrue($r['code'] === Wildduck\Http\Request::HTTP_OK);
        $this->assertArrayHasKey('data', $r);
        $this->assertTrue($r['data']['success']);
        $this->assertArrayHasKey('id', $r['data']);
        $this->assertTrue($r['data']['username'] === $params['username']);
    }

    /**
     * @depends testUserCreation
     */
//    public function testUserDeletion($id)
//    {
//        $r = Wildduck::users()->delete([
//            'id' => $id,
//        ]);
//
//        $this->assertTrue($r['code'] === Wildduck\Http\Request::HTTP_OK);
//        $this->assertArrayHasKey('data', $r);
//        $this->assertTrue($r['data']['success']);
//    }
}
