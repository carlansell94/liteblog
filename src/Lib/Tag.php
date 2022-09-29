<?php

namespace carlansell94\Liteblog\Lib;

class Tag
{
    private ?int $tag_id = null;
    private ?string $tag_slug = null;
    private ?string $tag_label = null;
    private ?int $post_count = null;
    private ?string $latest_post = null;

    public function setUri(int|string $uri): void
    {
        if (is_int($uri)) {
            $this->tag_id = $uri;
        } else {
            $this->tag_slug = $uri;
        }
    }

    public function setValue(string $key, int|string $value): void
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
        if ($this->tag_id === null) {
            return false;
        }

        return $this->tag_id;
    }

    public function getSlug(): string|false
    {
        if ($this->tag_slug === null) {
            return false;
        }

        return $this->tag_slug;
    }

    public function getName(): string|false
    {
        if ($this->tag_label === null) {
            return false;
        }

        return $this->tag_label;
    }

    public function getPostCount(): int|false
    {
        if ($this->post_count === null) {
            return false;
        }

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
