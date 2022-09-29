<?php

namespace carlansell94\Liteblog\Controller;

use carlansell94\Liteblog\Model\{PostList, CategoryList, TagList};

class Sidebar extends Controller
{
    private \mysqli_result $posts;
    private \mysqli_result $categories;
    private \mysqli_result $tags;

    public function __construct()
    {
        $this->view = new \carlansell94\Liteblog\View\Sidebar(
            template: 'sidebar'
        );
    }

    public function create(): bool
    {
        return true;
    }

    public function update(): bool
    {
        return true;
    }

    public function delete(): bool
    {
        return true;
    }

    public function load(): bool
    {
        $posts = new PostList(db: self::$db);
        $this->posts = $posts->get();

        $categories = new CategoryList(
            db: self::$db,
            post_count: true,
            sort_by_count: true
        );
        $this->categories = $categories->get();

        $tags = new TagList(
            db: self::$db,
            post_count: true,
            sort_by_count: true
        );
        $this->tags = $tags->get();

        return true;
    }

    public function loadView(): bool
    {
        $this->view->setPosts($this->posts);
        $this->view->setCategories($this->categories);
        $this->view->setTags($this->tags);

        return true;
    }

    public function output(): void
    {
        $this->view->render();
    }
}
