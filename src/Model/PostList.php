<?php

namespace carlansell94\Liteblog\Model;

class PostList
{
    /** @var array<string> */
    private ?array $categories = null;
    /** @var array<string> */
    private ?array $tags = null;

    public function __construct(
        private Database $db,
        private ?int $limit = null,
        private ?int $offset = null,
        private bool $drafts = false
    ) {
    }

    public function setLimits(int $limit, ?int $offset = null): void
    {
        $this->limit = $limit;
        $this->offset = $offset;
    }

    public function includeDrafts(): void
    {
        $this->drafts = true;
    }

    public function addCategory(string $category): void
    {
        $this->categories[] = $category;
    }

    public function addTag(string $tag): void
    {
        $this->tags[] = $tag;
    }

    public function get(): \mysqli_result|false
    {
        if ($this->drafts === true) {
            $status = "0,1";
        } else {
            $status = "1";
        }

        $query = "SELECT *,
                    GROUP_CONCAT(
                        DISTINCT(
                            CONCAT_WS(',', category_name, category_slug)
                        ) SEPARATOR ';') AS categories,
                    GROUP_CONCAT(
                        DISTINCT(
                            CONCAT_WS(',', tag_label, tag_slug)
                        ) SEPARATOR ';') AS tags
                FROM
                    blog_posts
                LEFT JOIN
                    blog_post_categories USING (post_id)
                LEFT JOIN
                    blog_categories USING (category_id)
                LEFT JOIN
                    blog_post_tags USING (post_id)
                LEFT JOIN
                    blog_tags USING (tag_id)
                WHERE
                    post_status_id IN ($status)";

        if (isset($this->tags)) {
            $query .= " AND
                        tag_slug IN ('" . implode("','", $this->tags) . "')";
        }

        if (isset($this->categories)) {
            $query .= " AND
                        category_slug IN ('" . implode("','", $this->categories) . "')";
        }

        $query .= " GROUP BY
                    post_id
                ORDER BY
                    post_date DESC";

        if (is_int($this->offset) && is_int($this->limit)) {
            $query .= " LIMIT {$this->offset}, {$this->limit}";
        }

        if (!$output = $this->db->runQuery($query)) {
            return false;
        }

        return $output->get_result();
    }

    public function getMaxPageNumber(): int|false
    {
        if ($this->drafts === true) {
            $status = "0,1";
        } else {
            $status = "1";
        }

        $query = "SELECT
                    count(DISTINCT(post_id))
                FROM
                    blog_posts
                LEFT JOIN
                    blog_post_categories USING (post_id)
                LEFT JOIN
                    blog_categories USING (category_id)
                LEFT JOIN
                    blog_post_tags USING (post_id)
                LEFT JOIN
                    blog_tags USING (tag_id)
                WHERE
                    post_status_id IN ($status)";

        if (isset($this->categories)) {
            $query .= " AND
                        category_slug IN ('" . implode("','", $this->categories) . "')";
        }

        if (isset($this->tags)) {
            $query .= " AND
                        tag_slug IN ('" . implode("','", $this->tags) . "')";
        }

        if (!$output = $this->db->runQuery($query)) {
            return false;
        }

        $output->bind_result($count);
        $output->fetch();

        return $count;
    }
}
