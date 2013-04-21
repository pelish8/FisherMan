<?php

class RouteTest extends PHPUnit_Framework_TestCase
{
    public function testRouteWithOutUrlParams()
    {

        $route = new \pelish8\FisherMan\Route('GET', '/index', function (){});

        $this->assertTrue($route->responseToRequest('/index'));

    }

    public function testRouteWithOutUrlParamsEndSlash()
    {

        $route = new \pelish8\FisherMan\Route('GET', '/index/', function (){});

        $this->assertTrue($route->responseToRequest('/index'));

    }

    public function testRouteWithUrlParams()
    {

        $route = new \pelish8\FisherMan\Route('GET', '/index/:par', function (){});

        $this->assertTrue($route->responseToRequest('/index/par'));

    }

    public function testRouteWithUrlParamsEndSlash()
    {

        $route = new \pelish8\FisherMan\Route('GET', '/index/:par/', function (){});

        $this->assertTrue($route->responseToRequest('/index/par'));

    }

    public function testRouteWithOutUrlParamsFaild()
    {

        $route = new \pelish8\FisherMan\Route('GET', '/index', function (){});

        $this->assertFalse($route->responseToRequest('/home'));

    }

    public function testRouteWithTooManyUrlParamsFaild()
    {

        $route = new \pelish8\FisherMan\Route('GET', '/index/:par1', function (){});

        $this->assertFalse($route->responseToRequest('/index/par1/par2'));

    }

    public function testRouteEmptyString()
    {

        $route = new \pelish8\FisherMan\Route('GET', '', function (){});

        $this->assertFalse($route->responseToRequest('/index/par1/par2'));

    }

    public function testRouteOnlySlash()
    {

        $route = new \pelish8\FisherMan\Route('GET', '/', function (){});

        $this->assertTrue($route->responseToRequest('/'));

    }

    public function testRouteEmptyResponse()
    {

        $route = new \pelish8\FisherMan\Route('GET', '/', function (){});

        $this->assertFalse($route->responseToRequest(''));

    }

    public function testRouteMorePlausible1()
    {

        $route1 = new \pelish8\FisherMan\Route('GET', '/', function (){});
        $route2 = new \pelish8\FisherMan\Route('GET', '/as/:asd', function (){});

        $this->assertEquals($route2->route(), $route1->isMorePlausibleThan($route2)->route());

    }

    public function testRouteMorePlausible2()
    {

        $route1 = new \pelish8\FisherMan\Route('GET', '/:asd/:asd', function (){});
        $route2 = new \pelish8\FisherMan\Route('GET', '/as/:asd', function (){});

        $this->assertEquals($route2->route(), $route1->isMorePlausibleThan($route2)->route());

    }

    public function testRouteMorePlausible3()
    {

        $route1 = new \pelish8\FisherMan\Route('GET', '/par1/par2/par3/par4/:asd/:asd', function (){});
        $route2 = new \pelish8\FisherMan\Route('GET', '/par1/par2/par3/:asd', function (){});

        $this->assertEquals($route1->route(), $route1->isMorePlausibleThan($route2)->route());

    }

    public function testRouteMorePlausible4()
    {

        $route1 = new \pelish8\FisherMan\Route('GET', '', function (){});
        $route2 = new \pelish8\FisherMan\Route('GET', '', function (){});

        $this->assertEquals($route2->route(), $route1->isMorePlausibleThan($route2)->route());

    }
}
