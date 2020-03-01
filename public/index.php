<?php
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Url as UrlProvider;

// Определяем некоторые константы с абсолютными путями
// для использования с локальными ресурасами
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Регистрируем автозагрузчик
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
    ]
);

$loader->register();

// Создаём контейнер в который складываем всякое
$di = new FactoryDefault();

// компонент представлений
$di->set('view', function () {
    $view = new View();
    $view->setViewsDir(APP_PATH . '/views/');
    return $view;
}
);

$di->set('db', function() use ($config) {
    return new Phalcon\Db\Adapter\Pdo\Mysql(array(
        'host'     => 'localhost',
        'username' => 'root',
        'password' => '',
        'dbname'   => 'catalog'));
}
);
// Setup a base URI
$di->set(
    'url',
    function () {
        $url = new UrlProvider();
        $url->setBaseUri('/');
        return $url;
    }
);

$application = new Application($di);

try {
    // Handle the request
    $response = $application->handle();//->getContent();
    //print_r($response);
    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}