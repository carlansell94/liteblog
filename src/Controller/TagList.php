<?php

namespace carlansell94\Liteblog\Controller;

class TagList extends Controller
{
    private ?\mysqli_result $tag_list = null;

    public function __construct()
    {
        $this->model = new \carlansell94\Liteblog\Model\TagList(
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
        $this->tag_list = $this->model->get();

        if ($this->tag_list === null) {
            return false;
        }

        return true;
    }

    public function loadView(): bool
    {
        $this->view = new \carlansell94\Liteblog\View\View(
            template: 'tag_list',
            is_admin_url: self::$is_admin_url
        );

        $this->view->setPageTitle('Tags');
        $this->view->setData(tag_list: $this->tag_list);

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
