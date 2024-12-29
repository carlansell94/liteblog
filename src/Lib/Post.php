<?php

namespace carlansell94\Liteblog\Lib;

use carlansell94\Liteblog\Lib\PostStatus;
use Michelf\MarkdownExtra;

class Post
{
    private ?int $post_id = null;
    /** @phpstan-ignore-next-line */
    private ?string $post_date = null;
    /** @phpstan-ignore-next-line */
    private ?string $post_title = null;
    /** @phpstan-ignore-next-line */
    private ?string $post_excerpt = null;
    /** @phpstan-ignore-next-line */
    private ?string $post_content = null;
    /** @phpstan-ignore-next-line */
    private ?string $last_updated = null;
    /** @phpstan-ignore-next-line */
    private PostStatus|int|null $post_status_id = null;
    private string $post_slug;
    private string $categories;
    private string $tags;
    

    public function setId(int $id): void
    {
        $this->post_id = $id;
    }

    public function setPostStatusId(int $id): void
    {
        $this->post_status_id = PostStatus::from($id);
    }

    public function setValue(string $key, int|string $value): void
    {
        $this->$key = $value;
    }

    public function getUri(): string
    {
        return $this->post_slug;
    }

    public function getTitle(bool $escape = false): ?string
    {
        if ($escape && $this->post_title !== null) {
            return str_replace("'", "", $this->post_title);
        }

        return $this->post_title;
    }

    public function getExcerpt(int|false $limit = false): ?string
    {
        if (!$limit) {
            return $this->post_excerpt;
        }

        if ($this->post_excerpt === null) {
            return null;
        }

        $length = strlen($this->post_excerpt);

        if ($length <= EXCERPT_LENGTH) {
            return $this->post_excerpt;
        }

        $excerpt = substr(
            $this->post_excerpt,
            0,
            strrpos($this->post_excerpt, " ", EXCERPT_LENGTH - $length)
        );

        return $excerpt . "...";
    }

    public function getDate(string $format = 'jS F Y'): string|bool
    {
        if ($this->post_date) {
            $date = new \DateTime($this->post_date);
            return $date->format($format);
        }

        return false;
    }

    public function hasCategories(): bool
    {
        if (!isset($this->categories) || $this->categories === '') {
            return false;
        }

        return true;
    }

    /** @return array<string> */
    public function getCategories(): array
    {
        return self::getMeta($this->categories);
    }

    public function hasTags(): bool
    {
        if (!isset($this->tags) || $this->tags === '') {
            return false;
        }

        return true;
    }

    /** @return array<string> */
    public function getTags(): array
    {
        return self::getMeta($this->tags);
    }

    public function getRawContent(): string|bool
    {
        if (!$this->post_content) {
            return false;
        }

        return $this->post_content;
    }

    public function getContent(): string|bool
    {
        if (!$this->post_content) {
            return false;
        }

        $content = mb_convert_encoding($this->post_content, "Windows-1252", "UTF-8");
        $content = MarkdownExtra::defaultTransform($content);

        $doc = new \DOMDocument();
        @$doc->loadHTML($content, LIBXML_NOBLANKS);
        $content = $doc->saveXML($doc->documentElement);

        return $content;
    }

    public function getStatus(): PostStatus|bool
    {
        if ($this->post_status_id === null) {
            return false;
        }

        if (is_int($this->post_status_id)) {
            return PostStatus::from($this->post_status_id);
        }

        return $this->post_status_id;
    }

    /** @return array<int|string|PostStatus> */
    public function getAllValues(): array
    {
        $values = array();

        foreach (get_object_vars($this) as $attr => $value) {
            $values[$attr] = $value;
        }

        return $values;
    }

    /** @return array<string> */
    private static function getMeta(string $meta): array
    {
        $res = array();
        $meta_list = explode(";", $meta);

        foreach ($meta_list as $meta_data) {
            if ($meta_data === '') {
                continue;
            }

            list($val, $key) = explode(',', $meta_data);

            $res[$key] = $val;
        }

        return $res;
    }
}
