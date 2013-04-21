<?php
namespace pelish8\FisherMan;


class Route
{
    /**
     * request method
     *
     * @var string
     */
    protected $method = null;

    /**
     *
     *
     */
    public $baseRoute = null;

    /**
     * full route
     *
     * @var string
     */
    public $route = null;

    /**
     * number of url parameters route has (/:par1/:par2)
     *
     */
    protected $numberOfParameter = null;

    /**
     *
     *
     */
    public $parameters = [];
    /**
     * call back function
     *
     * @var bool
     */
    public $isFunction = false;

    /**
     * function that will be call
     *
     *
     */
    public $callBack = null;

    /**
     *
     * @access public
     */
    public function __construct($method, $route, $call)
    {
        $this->method = $method;
        $this->parseRoute($route);

        if (is_callable($call)) {
            $this->isFunction = true;
        }

        $this->callBack = $call;
    }

    /**
     * parse route
     *
     * @access public
     */
    public function parseRoute($route)
    {
        // remove '/' from the end of string
        $route = (substr($route, -1) === '/') ? substr($route, 0, strlen($route) - 1) : $route;

        $this->route = $route;

        $pos = strpos($route, '/:');
        if ($pos === false) {
            $this->numberOfParameter = 0;
            $this->baseRoute = empty($route) ? '/' : $route;
            return;
        }

        if ($pos !== 0) {
            $this->baseRoute = substr($route, 0, $pos) . '/';
        } else {
            $this->baseRoute = '/';
        }

        $this->numberOfParameter = substr_count($route, '/:');
    }

    /**
     * compare to request
     *
     * @access public
     * @return bool
     */
    public function responseToRequest($request)
    {
        if ($request === $this->baseRoute) {
            return true;
        }

        $pos = strpos($request, $this->baseRoute);

        if ($pos === false) {
            return false;
        }
        $pos += strlen($this->baseRoute);

        $path = substr($request, $pos);

        $this->parameters = explode('/', $path);

        if (count($this->parameters) === $this->numberOfParameter) {
            return true;
        }

        return false;
    }

    /**
     * return route
     *
     * @access public
     * @return string
     */
    public function route()
    {
        return $this->route;
    }

    /**
     * compared with another route, and return route that is more plausible
     *
     * @access public
     * @return \pelish8\FisherMan\Route
     */
    public function isMorePlausibleThan($route) //@todo chage method name
    {
        $stronger = null;
        $weaker = null;
        if (!$route instanceof \pelish8\FisherMan\Route) {
            return $this;
        }

        if ($route->numberOfParameter > $this->numberOfParameter) {
            $stronger = $this;
            $weaker = $route;
        } else {
            $stronger = $route;
            $weaker = $this;
        }

        $pos = strpos($stronger->baseRoute, $weaker->baseRoute);

        if ($pos === 0) {
            return $stronger;
        }

        return $weaker;
    }
}
