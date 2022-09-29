<?php

namespace carlansell94\Liteblog\Controller;

class CategoryList extends Controller
{
    private ?\mysqli_result $category_list = null;

    public function __construct()
    {
        $this->model = new \carlansell94\Liteblog\Model\CategoryList(
            db: self::$db,
            post_count: true,
            include_drafts: self::$is_admin_url,
            sort_by_count: true
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
        $this->model->setLimits(POSTS_PER_PAGE, $this->offset ?? 0);
        $this->category_list = $this->model->get();

        if ($this->category_list === null) {
            return false;
        }

        return true;
    }

    public function loadView(): bool
    {
        $this->view = new \carlansell94\Liteblog\View\View(
            template: 'category_list',
            is_admin_url: self::$is_admin_url,
        );

        $this->view->setPageTitle('Categories');
        $this->view->setData(category_list: $this->category_list);

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
