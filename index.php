<?php

$timeStart = microtime(true);
require 'autoload.php';
require 'home.php';

use home\Home as h;

use pelish8\FisherMan\FisherMan as FisherMan;

$urls = [
    '/index.php' => 'Index2',
    '/sal/:firstName/:lastName' => 'Index',
    '/' => new h()
];

class Index
{
    public function get(array $urlParams = null, $fisherMan)
    {
        echo 'get';
    }

    public function post(array $urlParams, $fisherMan)
    {
        echo 'post';
    }

    public function put(array $urlParams, $fisherMan)
    {
        echo 'put';
    }

    public function delete(array $urlParams, $fisherMan)
    {
        echo 'delete';
    }

    public function ajax(array $urlParams, $fisherMan)
    {
        echo 'ajax';
    }
}

class Index2
{
    public function get(array $urlParams = null, $fisherMan)
    {
        echo 'GET';
    }
}


$www = new FisherMan($urls);


/* OR */

// $app = new FisherMan();

// $app->get('hello/:firstName/:lastName', function (array $urlParams, $fisherMan) {
//     echo 'Hello ' . $urlParams['firstName'] . ' ' . $urlParams['lastName'];
// });
//
// $app->post('hello/:firstName/:lastName', function (array $urlParams, $fisherMan) {
//     if (!isset($_POST)) {
//         return false;
//     }
// });
//
// $app->run();


echo '<br>';
echo '<br> Load Time: ';
echo microtime(true) - $timeStart;

