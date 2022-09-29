<?php

namespace carlansell94\Liteblog\Controller;

class Category extends Controller
{
    public function __construct()
    {
        $this->model = new \carlansell94\Liteblog\Model\Category(self::$db);
    }

    public function setId(int|string $id): void
    {
        $this->model->setId($id);
    }

    public function create(): bool
    {
        if (!$category = $this->readInput()) {
            return false;
        }

        $this->model->setCategoryData($category);

        if ($this->model->nameExists()) {
            http_response_code(409);
            return false;
        }

        $this->model->create();

        return true;
    }

    public function update(): bool
    {
        if (!$category = $this->readInput()) {
            return false;
        }

        $this->model->setCategoryData($category);

        if ($this->model->nameExists()) {
            http_response_code(409);
            return false;
        }

        $this->model->update();

        return true;
    }

    public function delete(): bool
    {
        if (!$category = $this->readInput()) {
            return false;
        }

        $this->model->setCategoryData($category);
        $this->model->delete();

        return true;
    }

    public function load(): bool
    {
        return true;
    }

    public function loadView(): bool
    {
        return true;
    }

    private function readInput(): \carlansell94\Liteblog\Lib\Category|false
    {
        if (!$data = file_get_contents('php://input')) {
            return false;
        }

        if (!$data = json_decode($data)) {
            return false;
        };

        if ($data == null) {
            return false;
        }

        $category = new \carlansell94\Liteblog\Lib\Category();

        foreach ($data as $key => $value) {
            if (method_exists($category, 'set' . ucfirst($key))) {
                $category->{'set' . ucfirst($key)}($value);
            } else {
                $category->setValue($key, $value);
            }
        }

        return $category;
    }
}
