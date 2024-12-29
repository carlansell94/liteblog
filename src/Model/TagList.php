<?php

namespace carlansell94\Liteblog\Model;

class TagList
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

    public function setLimits(int $limit, ?int $offset = null): void
    {
        $this->limit = $limit;
        $this->offset = $offset;
    }

    public function get(): \mysqli_result|false
    {
        if ($this->post_count) {
            $query = "SELECT
                        blog_tags.*,
                        count(post_id) AS post_count,
                        max(post_date) as latest_post
                    FROM
                        blog_tags
                    LEFT JOIN
                        blog_post_tags USING (tag_id)
                    LEFT JOIN
                        blog_posts USING (post_id)
                    WHERE
                        post_status_id IN ";

            if ($this->include_drafts) {
                $query .= " (0,1) OR post_status_id IS NULL";
            } else {
                $query .= " (1)";
            }

            $query .= " GROUP BY
                        tag_id";

            if ($this->sort_by_count) {
                $query .= " ORDER BY
                            post_count DESC";
            }
        } else {
            $query = "SELECT
                        *
                    FROM
                        blog_tags";
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
                    count(tag_id)
                FROM
                    blog_tags";

        if (!$output = $this->db->runQuery($query)) {
            return false;
        }

        $output->bind_result($count);
        $output->fetch();

        return $count;
    }
}
