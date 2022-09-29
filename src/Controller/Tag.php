<?php

namespace carlansell94\Liteblog\Controller;

class Tag extends Controller
{
    public function __construct()
    {
        $this->model = new \carlansell94\Liteblog\Model\Tag(self::$db);
    }

    public function setId(int|string $id): void
    {
        $this->model->setId($id);
    }

    public function create(): bool
    {
        if (!$tag = $this->readInput()) {
            return false;
        }

        $this->model->setTagData($tag);

        return $this->model->create();
    }

    public function update(): bool
    {
        if (!$tag = $this->readInput()) {
            return false;
        }

        $this->model->setTagData($tag);

        if ($this->model->nameExists()) {
            http_response_code(409);
            return false;
        }

        $this->model->update();

        return true;
    }

    public function delete(): bool
    {
        if (!$tag = $this->readInput()) {
            return false;
        }

        $this->model->setTagData($tag);

        return $this->model->delete();
    }

    public function load(): bool
    {
        return true;
    }

    public function loadView(): bool
    {
        return true;
    }

    private function readInput(): \carlansell94\Liteblog\Lib\Tag|false
    {
        if (!$data = file_get_contents('php://input')) {
            return false;
        }

        if (!$data = json_decode($data)) {
            return false;
        }

        if ($data == null) {
            return false;
        }

        $tag = new \carlansell94\Liteblog\Lib\Tag();

        foreach ($data as $key => $value) {
            if (method_exists($tag, 'set' . ucfirst($key))) {
                $tag->{'set' . ucfirst($key)}($value);
            } else {
                $tag->setValue($key, $value);
            }
        }

        return $tag;
    }
}
