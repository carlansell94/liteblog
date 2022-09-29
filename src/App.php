<?php

namespace carlansell94\Liteblog;

use carlansell94\Liteblog\Router;
use carlansell94\Liteblog\Config\Config;
use carlansell94\Liteblog\Model\Database;

class App
{
    public function run(): never
    {
        $config = new Config();

        if (!$config->load() || !$config->isValid()) {
            http_response_code(500);
            die();
        }

        if (DEBUG_MODE) {
            ini_set('display_errors', 1);
        }

        $db = new Database();

        if (!$db->connect()) {
            http_response_code(500);
            die();
        }

        $router = new Router();
        $router->parseUrl();

        if ($router->loginRequired()) {
            $router->getLoginPage();
        }

        if (!$router->routeIsAuthorised()) {
            http_response_code(403);
            die();
        }

        $router->execute($db);
        die();
    }
}
