<?php
namespace pelish8\FisherMan;

use pelish8\scanner\Scanner as Scanner;
use pelish8\FisherMan\Routes as Routes;

class FisherMan
{
    protected $routes = null;
    
    protected $uri = null;
    
    protected $urlParams = null;
    /**
    * constructor
    *
    */
    public function __construct(array $urls = null, $autoLoad = true)
    {
        if (!isset($urls)) {
            return;
        }
        
        $this->routes = $urls;
        
        if ($autoLoad) {
            $this->run();
        }
    }
    
    public function run()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($this->isRouteExist()) {
            switch ($method) {
                case 'GET':
                $this->performGet();
                    break;
            }
        }
        
    }
    
    /**
    *
    * @return bool
    */
    protected function isRouteExist()
    {
        $uri = !empty($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/'; // home
        
        if (array_key_exists($uri, $this->routes)) {
            $this->instance = $this->routes[$uri];
            return true;
        }
        
        $out = false;
        $this->uri = explode('/', $uri);
        $uriLength = count($this->uri);

        foreach ($this->routes as $route => $class) {
            $pos = strpos($route, '/:');
            if ($pos === false) {
                $pos = strlen($route);
            }
            $path = substr($route, 0, $pos);
            $pathArray = explode('/', $path);

            $pathLength = count($pathArray);
            if ($pathLength <= $uriLength) {
                for ($i = 0; $i < $pathLength; $i++) {
                    if ($pathArray[$i] === $this->uri[$i]) {
                        $this->instance = $class;
                        $out = true;
                    } else {
                        $out = false;
                        break;
                    }
                }
                if ($out) {
                    return true;
                }
            }
        }
        
        return false;
        
    }
    
    public function performGet()
    {
        $instance = new $this->instance();
        if (method_exists($this->instance, 'get')) {
            $instance->get($this->urlParams, $this);
        } else {    
            $this->pageNotFound();
        }
    }
    
    public function pageNotFound()
    {
        echo '<h1>404 Page Not Found.</h1>';
    }
}