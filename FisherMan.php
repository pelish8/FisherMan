<?php
namespace pelish8\FisherMan;

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
    const VERSION = '0.1.1';

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

    protected $methods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTION'];
    /**
     * constructor
     *
     */
    public function __construct(array $urls = null, $autoLoad = false)
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

        if (!in_array($method, $this->methods)) {
            // trow error unsuported http method
            $this->pageNotFound();
            return;
        }

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
        $instance = null;
        $counter = 0;
        $uriArray = explode('/', $this->uri());
        $uriLength = count($uriArray);

        foreach ($this->routes as $route => $callBack) {
            $pos = strpos($route, '/:');
            if ($pos === false) {
                continue;
            }

            $path = substr($route, 0, $pos);
            $pathArray = explode('/', $path);

            $pathLength = count($pathArray);

            $instance = $callBack;
            if ($pathLength <= $uriLength) {
                for (; $counter < $pathLength; $counter++) {
                    if ($pathArray[$counter] !== $uriArray[$counter]) {
                        $out = false;
                        break;
                    }
                    $out = true;
                }
            }

            if ($out) {
                break;
            }
        }

        $this->urlParams = array_slice($uriArray, $counter);
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
     * Perform http method.
     */
    protected function doRequest($method)
    {
        if (is_array($this->instance)) {

            if ($this->instance[0] !== $method) {
                $this->pageNotFound();
                return;
            }
            $f = $this->instance[1];
            $f($this->urlParams, $this);
            return;
        }

        $instance = new $this->instance();
        if (!method_exists($this->instance, $method)) {
            $this->pageNotFound();
            return;
        }

        $instance->$method($this->urlParams, $this);

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
            return strtoupper($a);
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
        $this->routes[$route] = [strtoupper($method), $function];
        $this->register([$method]);
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