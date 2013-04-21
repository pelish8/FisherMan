<?php
class FMTest extends \pelish8\FisherMan\FisherMan
{
    public function matchPath()
    {
        $ins = parent::isUrlExist();
        $this->instance = null;

        return $this->route;
    }

    public function setEnv($uri, $method = 'GET')
    {
        $this->env = \pelish8\FisherMan\Environment::mock([
            'ip' => '::1',
            'uri' => $uri,
            'host' => 'localhost',
            'method' => $method,
            'port' => 80,
            'serverName' => 'localhost'
        ]);
    }

    public function routes()
    {
        return $this->routes;
    }
}

class Test extends PHPUnit_Framework_TestCase
{
    public function testGetWithOutUrlParams()
    {

        $route = new \pelish8\FisherMan\Route('GET', '/index', function (){});

        var_dump($route->responseToRequest('/index'));

        $this->assertTrue($route->responseToRequest('/index'));

    }

    public function testGetWithUrlParams()
    {
        $fm = new FMTest();
        $fm->setEnv('/blog/04-17-2013/new-title');
        $aa = 'sale';
        $fm->get('/blog/:date/:title', function ($params, $fm) {
            $this->assertCount(2, $params, 'params count is not equel');
            $this->assertEquals('04-17-2013', $params[0], 'first parameter is not valid');
            $this->assertEquals('new-title', $params[1], 'second parameter is not valid');
            $this->assertFalse($fm->isAjax(), 'this should not be the ajax call'); // this is not ajax
        });
        $this->assertEquals('/blog/:date/:title', $fm->matchPath(), 'route is not match');
        $fm->run();
    }

    public function testGetWithMultiplePages()
    {
        // $this->setEnv('/');
        $fm = new FMTest();
        $fm->get('/', function () {});

        $fm->get('/sale', function () {});

        $fm->get('/', function () {});

        $fm->get('/blog/:date/:title', function ($params, $fm) {
            $this->assertCount(2, $params, 'params count is not equel');
            $this->assertEquals('04-18-2013', $params[0], 'first parameter is not valid');
            $this->assertEquals('title-for-new-post', $params[1], 'second parameter is not valid');
            $this->assertFalse($fm->isAjax(), 'this should not be the ajax call');
        });

        $fm->setEnv('/');
        $this->assertEquals('/', $fm->matchPath(), 'route is not match');

        $fm->setEnv('/sale');
        $this->assertEquals('/sale', $fm->matchPath(), 'route is not match');

        $fm->setEnv('/blog/04-18-2013/title-for-new-post');
        $this->assertEquals('/blog/:date/:title', $fm->matchPath(), 'route is not match');

        $fm->run();
    }
}
