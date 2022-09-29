<?php

namespace carlansell94\Liteblog;

use carlansell94\Liteblog\Model\{Database, User};
use carlansell94\Liteblog\Controller\{Controller, ControllerFactory};
use carlansell94\Liteblog\Session\Session;

class Router
{
    /** @var array<array<mixed>> */
    private array $route = array();
    private ?string $method = null;
    private bool $is_admin_url = false;

    public function __construct()
    {
        Session::start();
    }

    public function loginRequired(): bool
    {
        if (Session::isLoggedIn()) {
            return false;
        }

        if (!$this->is_admin_url || $this->isLoginPage()) {
            return false;
        }

        return true;
    }

    public function routeIsAuthorised(): bool
    {
        switch ($this->method) {
            case 'GET':
                return true;
            case 'POST':
                if ($this->isLoginPage()) {
                    return true;
                }
                // no break
            case 'PUT':
            case 'DELETE':
                return Session::isLoggedIn();
        }

        return false;
    }

    public function getDefaultRoute(): string
    {
        return 'postList';
    }

    public function getLoginPage(): void
    {
        header('Location: /' . SITE_ROOT . '/' . ADMIN_URL . '/login');
        die();
    }

    public function logout(): void
    {
        header('Location: ' . SITE_ROOT . '/');
        Session::end();
        die();
    }

    public function parseUrl(): void
    {
        if (str_contains($_SERVER['REQUEST_URI'], '//')) {
            http_response_code(400);
            die();
        }

        $this->method = $_SERVER['REQUEST_METHOD'];
        $uri = trim($_SERVER['REQUEST_URI'], '/\\');

        if (str_starts_with($uri, SITE_ROOT)) {
            $uri = str_replace(SITE_ROOT, '', $uri);
        }

        if (str_starts_with($uri, '/' . ADMIN_URL)) {
            $this->is_admin_url = true;
            $uri = str_replace('/' . ADMIN_URL, '', $uri);
        }

        $route = array_filter(explode("/", parse_url(
            trim($uri, '/\\'),
            PHP_URL_PATH
        )));

        if (!isset($route[0])) {
            return;
        }

        $valid_routes = $this->getValidRoutes();

        foreach ($route as $entity) {
            if (in_array($entity, $valid_routes['entities'])) {
                $this->route[] = array(
                    'type' => 'entity',
                    'name' => $entity
                );
            } elseif (in_array($entity, $valid_routes['filters'])) {
                $this->route[] = array(
                    'type' => 'filter',
                    'name' => $entity
                );
            } else {
                $index = max(count($this->route) - 1, 0);
                $this->route[$index]['values'][] = $entity;
            }
        }
    }

    public function execute(Database $db): void
    {
        Controller::setDatabase($db);

        if (!isset($this->route[0]['type']) || $this->route[0]['type'] !== 'entity') {
            array_unshift(
                $this->route,
                array(
                    'type' => 'entity',
                    'name' => $this->getDefaultRoute()
                )
            );
        }

        if ($this->route[0]['name'] === 'logout') {
            $this->logout();
        }

        if (
            !$controller = ControllerFactory::getController(
                route: array_shift($this->route),
                is_admin_url: $this->is_admin_url
            )
        ) {
            http_response_code(404);
            die();
        };

        if (!$controller->setFilters($this->route, $_GET)) {
            http_response_code(404);
            die();
        };

        $this->{'execute' . $this->method . 'Request'}($controller);

        if (http_response_code() !== 200) {
            die();
        }
    }

    /** @return array<array<string>> */
    private function getValidRoutes(): array
    {
        if ($this->is_admin_url) {
            return array(
                'entities' => array(
                    'category',
                    'categories',
                    'tag',
                    'tags',
                    'post',
                    'posts',
                    'status',
                    'login',
                    'logout'
                ),
                'filters' => array(
                    'page'
                )
            );
        }

        return array(
            'entities' => array(
                'post',
                'posts'
            ),
            'filters' => array(
                'category',
                'page',
                'tag'
            )
        );
    }

    private function executeGetRequest(Controller $controller): void
    {
        if (!$controller->load()) {
            http_response_code(404);
            die();
        }

        if (!$controller->loadView()) {
            http_response_code(404);
            die();
        }

        $controller->output();
    }

    private function executePostRequest(Controller $controller): void
    {
        if (is_a($controller, '\carlansell94\Liteblog\Controller\Session')) {
            if (!$controller->login()) {
                Session::failedLogin();
                $this->executeGetRequest($controller);
                die();
            }

            Session::login();
            header('Location: /' . SITE_ROOT . '/' . ADMIN_URL);
            die();
        }

        $controller->create();
    }

    private function executePutRequest(Controller $controller): void
    {
        if (isset(end($this->route)['name'])) {
            $method = "update" . ucfirst(end($this->route)['name']);

            if (method_exists($controller, $method)) {
                $controller->$method();
                return;
            }
        }

        if (!$controller->update()) {
            http_response_code(500);
            die();
        }
    }

    private function executeDeleteRequest(Controller $controller): void
    {
        $controller->delete();
    }

    private function isLoginPage(): bool
    {
        if (!isset($this->route[0])) {
            return false;
        }

        if (!isset($this->route[0]['name'])) {
            return false;
        }

        if ($this->route[0]['name'] !== 'login') {
            return false;
        }

        if (isset($this->route[1])) {
            return false;
        }

        return true;
    }
}
