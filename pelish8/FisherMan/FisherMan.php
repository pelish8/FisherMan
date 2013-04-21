<?php
namespace pelish8\FisherMan;


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
    const VERSION = '0.1.1';

    /**
     * routes
     *
     * @var array
     * @access protected
     */
    protected $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
        'OPTION' => []
    ];

    /**
     *
     *
     */
    protected $requestRoutes = null;

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
     * standard HTTP methods
     *
     * @var array
     * @access protected
     */
    protected $methods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTION'];

    /**
     * Environment object
     *
     * @var \FisherMan\Environment
     * @access protected
     */
    protected $env = null;

    /**
     * Environment object
     *
     * @var \FisherMan\Logger
     * @access protected
     */
    protected $logger = null;

    /**
     *
     *
     *
     */
    protected $route = null;

    /**
     * constructor
     *
     */
    public function __construct(array $urls = null, $autoLoad = false)
    {
        $this->logger = \pelish8\FisherMan\Logger::sharedLogger();

        $this->env = \pelish8\FisherMan\Environment::sharedEnvironment();

        if (!isset($urls)) {
            return;
        }

        // $this->routes = $urls;

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
        // replace with Environment
        $method = $this->env->method();

        if (!in_array($method, $this->methods)) {
            // trow error unsuported http method
            $this->pageNotFound();
            return;
        }
        $this->requestRoutes = $this->routes[$method];

        if (!$this->isUrlExist()) {
            $this->pageNotFound();
            return;
        }

        $this->doRequest($method);
    }

    /**
     * Find if page exists.
     *
     * @return bool
     */
    protected function isUrlExist()
    {
        $uri = $this->env->uri();
        $potentialRoutes = null;

        foreach ($this->requestRoutes as $route) {
            // echo $route->route . '<br>';
            if ($route->responseToRequest($uri)) {
                $potentialRoutes = $route->isMorePlausibleThan($potentialRoutes);
            }

        }
        var_dump($potentialRoutes);
        if ($potentialRoutes) {
            $this->route = $potentialRoutes;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Perform http method.
     */
    protected function doRequest($method)
    {
        $route = $this->route;
        if ($route->isFunction) {
            $f = $route->callBack;
            $f($route->parameters, $this);
        }
        return;
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
            $this->isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');
        }
        return $this->isAjax;
    }

    /**
     * 404 page
     *
     * @access public
     */
    public function pageNotFound()
    {
        if (array_key_exists(404, $this->routes)) {
            $this->instance = $this->routes[404];
            $instance = new $this->instance();
            $instance->get();
            return;
        }
        header('HTTP/1.0 404 Not Found');
        echo '<h1>404 Page Not Found.</h1><p>The page you are looking for could not be found.</p>';
    }

    /**
     * Register custom HTTP method types.
     *
     * @access protected
     */
    public function register(array $newMethods)
    {

        $methods = array_map(function ($a) {
            $method = strtoupper($a);

            if (!array_key_exists($method, $this->routes)) {
                $this->routes[$method] = [];
                echo 1;
            }
        }, $newMethods);

        $this->methods = array_unique(array_merge($this->methods, $methods)); // @TODO find more efficient way
    }

    /**
     *
     *
     *
     */
    public function map($method, $route, $function)
    {
        $method = strtoupper($method);
        $this->register([$method]);
        $this->routes[$method][] = new \pelish8\FisherMan\Route($method, $route, $function);
        #if DEV
            // var_dump($this->routes);
        #indif
    }

    /**
     *
     *
     *
     */
    public function get($route, $function)
    {
        $this->map('GET', $route, $function);
    }

    /**
     *
     *
     *
     */
    public function post($route, $function)
    {
        $this->map('POST', $route, $function);
    }

    /**
     *
     *
     *
     */
    public function put($route, $function)
    {
        $this->map('PUT', $route, $function);
    }

    /**
     *
     *
     *
     */
    public function delete($route, $function)
    {
        $this->map('DELETE', $route, $function);
    }

    /**
     *
     *
     *
     */
    public function options($route, $function)
    {
        $this->map('OPTIONS', $route, $function);
    }

    /**
     *
     *
     *
     */
    protected function createPageObject($path, $pageObject)
    {
        // this is test option need additional investigation
        if (function_exists('apc_fetch')) {
            $data = apc_fetch($path);
            if ($data === false) {
                apc_add($path, $data = new $pageObject);
            }
            return $data;
        } else {
            return new $pageObject();
        }
    }
}