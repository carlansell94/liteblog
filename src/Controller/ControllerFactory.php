<?php

namespace carlansell94\Liteblog\Controller;

use carlansell94\Liteblog\Controller\Controller;

class ControllerFactory
{
    /**
     * @param array<int|string> $route
     */
    public static function getController(
        array $route,
        bool $is_admin_url = false
    ): Controller|false {
        Controller::setAdminUrlStatus($is_admin_url);

        if ($is_admin_url && $route['name'] === 'category') {
            $type = 'Category';
        } else {
            $type = match ($route['name']) {
                'posts', 'category' => 'PostList',
                'categories'        => 'CategoryList',
                'tags'              => 'TagList',
                'login', 'logout'   => 'Session',
                default => ucfirst($route['name'])
            };
        }

        if (!class_exists(__NAMESPACE__ . '\\' . $type)) {
            return false;
        }

        $controller = new (__NAMESPACE__ . '\\' . $type);

        if (isset($route['values'])) {
            $controller->setId($route['values'][0]);
        }

        return $controller;
    }
}
