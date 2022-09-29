<?php

namespace carlansell94\Liteblog\Controller;

class Post extends Controller
{
    private ?\mysqli_result $categories = null;
    private ?\carlansell94\Liteblog\Lib\Post $post = null;

    public function __construct()
    {
        $this->model = new \stdClass();
        $this->model->post = new \carlansell94\Liteblog\Model\Post(self::$db);

        if (self::$is_admin_url) {
            $this->model->categories =
                new \carlansell94\Liteblog\Model\CategoryList(self::$db);
        }
    }

    public function setId(int|string $id): void
    {
        $this->model->post->setId($id);
    }

    public function setStatus(): bool
    {
        return true;
    }

    public function create(): bool
    {
        if (!$post = $this->readInput()) {
            return false;
        }

        $this->model->post->setPostData($post);
        return $this->model->post->create();
    }

    public function update(): bool
    {
        if (!$post = $this->readInput()) {
            return false;
        }

        $this->model->post->setPostData($post);

        return ($this->model->post->update() && $this->model->post->updateMeta());
    }

    public function updateStatus(): void
    {
        $this->model->post->changeStatus();
    }

    public function delete(): bool
    {
        return $this->model->post->delete();
    }

    public function load(): bool
    {
        if ($this->model->post->getId() !== null) {
            if (null === $data = $this->model->post->get()) {
                return false;
            }

            $this->post = $data->fetch_object('\carlansell94\Liteblog\Lib\Post');

            if ($this->post->getTitle() === null) {
                return false;
            }
        }

        if (self::$is_admin_url) {
            return $this->loadAdmin();
        }

        return true;
    }

    public function loadView(): bool
    {
        $this->view = new \carlansell94\Liteblog\View\View(
            template: 'post',
            is_admin_url: self::$is_admin_url
        );
        $this->view->setPageTitle($this->post?->getTitle() ?? 'New Post');
        $this->view->setData(
            post: $this->post,
            categories: $this->categories
        );

        return true;
    }

    private function readInput(): \carlansell94\Liteblog\Lib\Post|false
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

        $post = new \carlansell94\Liteblog\Lib\Post();

        foreach ($data as $key => $value) {
            $func = 'set' . str_replace('_', '', ucwords($key, '_'));

            if (method_exists($post, $func)) {
                $post->$func($value);
            } elseif (str_starts_with($key, 'post')) {
                $post->setValue($key, $value);
            } else {
                $this->model->post->$func($value);
            }
        }

        return $post;
    }

    private function loadAdmin(): bool
    {
        $this->categories = $this->model->categories->get();

        if ($this->categories === null) {
            return false;
        }

        return true;
    }
}
