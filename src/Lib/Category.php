<?php

namespace carlansell94\Liteblog\Lib;

class Category
{
    private ?int $category_id = null;
    private ?string $category_slug = null;
    private ?string $category_name = null;
    private ?int $post_count = null;
    private ?string $latest_post = null;

    public function setUri(int|string $uri): void
    {
        if (is_int($uri)) {
            $this->category_id = $uri;
        } else {
            $this->category_slug = $uri;
        }
    }

    public function setValue(string $key, mixed $value): void
    {
        if ($value !== null) {
            $this->$key = $value;
        }
    }

    public function getUri(): int|string|false
    {
        return $this->getSlug();
    }

    public function getId(): int|false
    {
        if ($this->category_id === null) {
            return false;
        }

        return $this->category_id;
    }

    public function getSlug(): string|false
    {
        if ($this->category_slug === null) {
            return false;
        }

        return str_replace("'", "\'", $this->category_slug);
    }

    public function getName(): string|false
    {
        if ($this->category_name === null) {
            return false;
        }

        return $this->category_name;
    }

    public function getPostCount(): int
    {
        return $this->post_count;
    }

    public function getLatestPost(): string|false
    {
        if ($this->latest_post === null) {
            return false;
        }

        return $this->latest_post;
    }
}
