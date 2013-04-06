<?php
namespace pelish8\FisherMan;

use pelish8\scanner\Scanner as Scanner;
use pelish8\FisherMan\Routes as Routes;

/**
 * FisherMan
 *
 * @package FisherMan
 * @author  pelish8
 * @since   0.1
 */
class FisherMan
{
    /**
     * @const string
     */
    const VERSION = '0.1';
    
    /**
     * @const string
     */
    const XML_HTTP_REQUEST = 'XMLHttpRequest';
    
    /**
     * routes
     *
     * @var array
     * @access protected
     */
    protected $routes = null;
    
    /**
     * request uri
     *
     * @var string
     * @access protected
     */
    protected $uri = null;
    
    /**
     * Variable that will pass as first parameter to request method.
     *
     * @var array
     * @access protected
     */
    protected $urlParams = [];
    
    /**
     * is ajax call
     *
     * @var bool
     * @access protected
     */
    protected $isAjax = null;
    
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
    
    /**
     * Execute script.
     *
     * @access public
     */
    public function run()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if (!$this->isUrlExist()) {
            $this->pageNotFound();
            return;
        }
        
        switch ($method) {
            case 'GET':
                $this->doGet();
                break;
            case 'POST':
                $this->doPost();
                break;
            case 'PUT':
                $this->doPut();
                break;
            case 'DELETE':
                $this->doPut();
                break;
        }
    }
    
    /**
     * Find if page exists.
     * 
     * @return bool
     */
    protected function isUrlExist()
    {   
        if (array_key_exists($this->uri(), $this->routes)) {
            $this->instance = $this->routes[$this->uri()];
            return true;
        }
        
        return $this->compareUrlAndUri();
    }
    
    /**
     * Return true if Url exists.
     *
     * @return bool
     */
    protected function compareUrlAndUri()
    {
        $out = false;
        $uriArray = explode('/', $this->uri());
        $uriLength = count($uriArray);
        
        foreach ($this->routes as $route => $class) {
            $pos = strpos($route, '/:');
            if ($pos === false) {
                continue;
            }
            
            $path = substr($route, 0, $pos);
            $pathArray = explode('/', $path);
            
            $pathLength = count($pathArray);
            $instance = null;
            
            if ($pathLength <= $uriLength) {
                for ($i = 0; $i < $pathLength; $i++) {
                    if ($pathArray[$i] !== $uriArray[$i]) {
                        $out = false;
                        break;
                    }
                    $instance = $class;
                    $out = true;
                }
            }
        }
        
        $this->urlParams = array_slice($uriArray, $i);
        $this->instance = $instance;
        
        return $out;
    }
    
    /**
     * Request uri.
     *
     * @return string
     */
    protected function uri()
    {
        if ($this->uri === null) {
            $this->uri = $this->findUriString();
        }
        
        return $this->uri;
    }
    
    /**
     * Request uri.
     *
     * @return string
     */
    protected function findUriString()
    {
        if (strpos($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME']) === 0) {
            $uri = !empty($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/'; // home
        } else {
            $uri = !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/'; // mode_rewrite
        }
        
        return $uri;
    }
    /**
     * Perform GET method.
     */
    protected function doGet()
    {
        $instance = new $this->instance();
        if (!method_exists($this->instance, 'get')) {    
            $this->pageNotFound();
            return;
        }
        
        $instance->get($this->urlParams, $this);
    }
    
    /**
     * Perform POST method.
     */
    protected function doPost()
    {
        $instance = new $this->instance();
        if (!method_exists($this->instance, 'post')) {    
            $this->pageNotFound();
            return;
        }
        
        $instance->post($this->urlParams, $this);
    }
    
    /**
     * Perform PUT method.
     */
    protected function doPut()
    {
        $instance = new $this->instance();
        if (!method_exists($this->instance, 'put')) {    
            $this->pageNotFound();
            return;
        }
        
        $instance->put($this->urlParams, $this);
    }
    
    /**
     * Perform DELETE method.
     */
    protected function doDelete()
    {
        $instance = new $this->instance();
        if (!method_exists($this->instance, 'delete')) {
            $this->pageNotFound();
            return;
        }
        
        $instance->delete($this->urlParams, $this);
    }
    
    /**
     * check if request is ajax
     *
     * @return bool
     * @access public
     */
    public function isAjax()
    {
        if ($this->isAjax === null) {
            $this->isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === self::XML_HTTP_REQUEST);
        }
        return $this->isAjax;
    }
    
    /**
     * 404 page
     *
     * @access protected
     */
    protected function pageNotFound()
    {
        if (array_key_exists(404, $this->routes)) {
            $this->instance = $this->routes[404];
            $instance = new $this->instance();
            $instance->get();
            return;
        }
        header('HTTP/1.0 404 Not Found');
        echo '<h1>404 Page Not Found.</h1>';
    }
}