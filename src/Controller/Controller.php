<?php

namespace carlansell94\Liteblog\Controller;

use carlansell94\Liteblog\Model\Database;

abstract class Controller
{
    protected static Database $db;
    protected static bool $is_admin_url;
    protected ?int $page = null;
    protected ?int $limit = null;
    protected ?int $offset = null;
    protected mixed $model;
    protected mixed $view;

    public static function setDatabase(Database $db): void
    {
        self::$db = $db;
    }

    public static function setAdminUrlStatus(bool $is_admin_url): void
    {
        self::$is_admin_url = $is_admin_url;
    }

    /**
     * @param array<array<string>> $filters
     * @param array<string> $uri_filters
     */
    public function setFilters(array $filters, array $uri_filters): bool
    {
        foreach ($filters as $filter) {
            if (!isset($filter['name'])) {
                return false;
            }

            if (!method_exists($this, "set" . ucfirst($filter['name']))) {
                return false;
            }

            if (!isset($filter['values'])) {
                $this->{ "set" . ucfirst($filter['name'])}();
                continue;
            }

            if (!is_array($filter['values'])) {
                $this->{ "set" . ucfirst($filter['name'])}($filter['values']);
                continue;
            }

            foreach ($filter['values'] as $value) {
                $this->{ "set" . ucfirst($filter['name'])}($value);
            }
        }

        foreach ($uri_filters as $filter => $value) {
            if (!method_exists($this, "set" . ucfirst($filter))) {
                return false;
            }

            $this->{ "set" . ucfirst($filter)}($value);
        }

        return true;
    }

    public function setParams(string $name, mixed $value): bool
    {
        if (!method_exists($this->model, "add" . ucfirst($name))) {
            return false;
        }

        return $this->model->{ "add" . ucfirst($name) . 'Id'}($value);
    }

    public function setPage(int $id): void
    {
        $this->page = $id;
        $this->offset = ($id - 1) * POSTS_PER_PAGE;
    }

    public function output(): void
    {
        $this->view->getHead();
        $this->view->render();

        if (!self::$is_admin_url) {
            $this->view->elements['sidebar'] = new \carlansell94\Liteblog\Controller\Sidebar();
            $this->view->getSidebar();
            $this->view->getFooter();
        }
    }

    abstract public function create(): bool;

    abstract public function update(): bool;

    abstract public function delete(): bool;

    abstract public function load(): bool;

    abstract public function loadView(): bool;
}
