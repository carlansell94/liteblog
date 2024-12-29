<?php

namespace carlansell94\Liteblog\Model;

class CategoryList
{
    private ?int $limit = null;
    private ?int $offset = null;

    public function __construct(
        private Database $db,
        private bool $post_count = false,
        private bool $include_drafts = false,
        private bool $sort_by_count = false
    ) {
    }

    public function setLimits(int $limit, ?int $offset = null): void
    {
        $this->limit = $limit;
        $this->offset = $offset;
    }

    public function includePostCount(): void
    {
        $this->post_count = true;
    }

    public function includeDrafts(): void
    {
        $this->include_drafts = true;
    }

    public function sortByCount(): void
    {
        $this->sort_by_count = true;
    }

    public function get(): \mysqli_result|false
    {
        $this->include_drafts ? $status = "0,1" : $status = "1";

        if ($this->post_count) {
            $query = "SELECT
                        blog_categories.*,
                        COUNT(post_id) AS post_count,
                        MAX(post_date) AS latest_post
                    FROM
                        blog_categories
                    LEFT JOIN
                        blog_post_categories USING (category_id)
                    LEFT JOIN
                        blog_posts USING (post_id)
                    WHERE
                        post_status_id IN ($status)";

            if ($this->include_drafts) {
                $query .= " OR post_status_id IS NULL";
            }

            $query .= " GROUP BY
                        category_id";

            if ($this->sort_by_count) {
                $query .= " ORDER BY
                            post_count DESC";
            }
        } else {
            $query = "SELECT
                        *
                    FROM
                        blog_categories";
        }

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
        $query = "SELECT
                    count(category_id)
                FROM
                    blog_categories";

        if (!$output = $this->db->runQuery($query)) {
            return false;
        }

        $output->bind_result($count);
        $output->fetch();

        return $count;
    }
}
