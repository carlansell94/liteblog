<?php

namespace carlansell94\Liteblog\Controller;

class PostList extends Controller
{
    private \mysqli_result|false $post_list;

    public function __construct()
    {
        $this->model = new \carlansell94\Liteblog\Model\PostList(self::$db);
    }

    public function setCategory(string $id): void
    {
        $this->model->addCategory($id);
    }

    public function setTag(string $id): void
    {
        $this->model->addTag($id);
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
        if (self::$is_admin_url) {
            $this->model->includeDrafts();
        }

        $this->model->setLimits(POSTS_PER_PAGE, $this->offset ?? 0);
        $this->post_list = $this->model->get();

        if ($this->post_list === null) {
            return false;
        }

        return true;
    }

    public function loadView(): bool
    {
        $this->view = new \carlansell94\Liteblog\View\View(
            template: 'post_list',
            is_admin_url: self::$is_admin_url
        );
        $this->view->setPageTitle('Posts');
        $this->view->setData(post_list: $this->post_list);

        if (
            !$this->view->setPageInfo(
                current_page: $this->page ?? 1,
                max_page: (int)max(
                    ceil(
                        $this->model->getMaxPageNumber() / POSTS_PER_PAGE
                    ),
                    1
                )
            )
        ) {
            return false;
        }

        return true;
    }
}
