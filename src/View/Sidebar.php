<?php

namespace carlansell94\Liteblog\View;

class Sidebar extends View
{
    /** @var array<mixed> */
    private ?array $posts = null;
    /** @var array<object> */
    private ?array $categories = null;
    /** @var array<object> */
    private ?array $tags = null;

    public function setPosts(\mysqli_result $posts): void
    {
        while ($post = $posts->fetch_object("\carlansell94\Liteblog\Lib\Post")) {
            $year = $post->getDate('Y');
            $month = $post->getDate('F');

            $this->posts[$year][$month][] = $post;
        }
    }

    public function setCategories(\mysqli_result $categories): void
    {
        while ($category = $categories->fetch_object("\carlansell94\Liteblog\Lib\Category")) {
            $this->categories[] = $category;
        }
    }

    public function setTags(\mysqli_result $tags): void
    {
        while ($tag = $tags->fetch_object("\carlansell94\Liteblog\Lib\Tag")) {
            $this->tags[] = $tag;
        }
    }

    /** @return array<mixed> */
    public function getPosts(): array|null
    {
        return $this->posts;
    }

    /** @return array<object> */
    public function getCategories(): array|null
    {
        return $this->categories;
    }

    /** @return array<object> */
    public function getTags(): array|null
    {
        return $this->tags;
    }

    public function render(): void
    {
        $this->getTemplate();
    }
}
