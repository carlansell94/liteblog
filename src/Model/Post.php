<?php

namespace carlansell94\Liteblog\Model;

class Post
{
    private \carlansell94\Liteblog\Lib\Post $post;
    /** @var array<string> */
    private array $added_categories = array();
    /** @var array<string> */
    private array $removed_categories = array();
    /** @var array<string> */
    private array $added_tags = array();
    /** @var array<string> */
    private array $removed_tags = array();

    public function __construct(
        private readonly Database $db,
        private int|string|null $id = null
    ) {
    }

    public function setId(int|string $id): bool
    {
        if ($this->id !== null) {
            return false;
        }

        $this->id = $id;

        return true;
    }

    public function setPostData(\carlansell94\Liteblog\Lib\Post $post): void
    {
        $this->post = $post;
    }

    /**
     * @param array<object> $categories
     */
    public function setCategories(array $categories): void
    {
        foreach ($categories as $category) {
            $this->added_categories[] = str_replace("'", "\'", $category->value);
        }
    }

    /**
     * @param array<string> $categories
     */
    public function setAddedCategories(array $categories): void
    {
        foreach ($categories as $category) {
            $this->added_categories[] = str_replace("'", "\'", $category);
        }
    }

    /**
     * @param array<object> $tags
     */
    public function setTags(array $tags): void
    {
        foreach ($tags as $tag) {
            $this->added_tags[] = str_replace("'", "\'", $tag->value);
        }
    }

    /**
     * @param array<string> $tags
     */
    public function setAddedTags(array $tags): void
    {
        foreach ($tags as $tag) {
            $this->added_tags[] = str_replace("'", "\'", $tag);
        }
    }

    /**
     * @param array<string> $categories
     */
    public function setRemovedCategories(array $categories): void
    {
        foreach ($categories as $category) {
            $this->removed_categories[] = str_replace("'", "\'", $category);
        }
    }

    /**
     * @param array<string> $tags
     */
    public function setRemovedTags(array $tags): void
    {
        foreach ($tags as $tag) {
            $this->removed_tags[] = str_replace("'", "\'", $tag);
        }
    }

    public function getId(): int|string|null
    {
        return $this->id;
    }

    public function get(): \mysqli_result|false
    {
        $query = "SELECT
                    post_id,
                    post_slug,
                    post_title,
                    post_excerpt,
                    post_content,
                    post_image,
                    post_banner,
                    post_icon,
                    post_status_id,
                    post_date,
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
                    post_slug = '{$this->id}'";

        $output = $this->db->runQuery($query);

        return $output->get_result();
    }

    public function changeStatus(): bool
    {
        $query = "UPDATE
                    blog_posts
                SET
                    post_status_id = NOT post_status_id
                WHERE
                    post_slug = '{$this->id}'";

        if (!$this->db->runQuery($query)) {
            return false;
        }

        return true;
    }

    public function create(): bool
    {
        $fields = array();
        $values = array();

        foreach ($this->post->getAllValues() as $field => $value) {
            if ($value == null) {
                continue;
            }

            if ($field === 'post_tags') {
                $this->added_tags[] = $value;
                continue;
            }

            if ($field === 'post_categories') {
                $this->added_categories[] = $value;
                continue;
            }

            if ($value instanceof \UnitEnum) {
                $value = $value->value;
            }

            $fields[] = $field;
            $values[] = $value;
        }

        $query = "INSERT INTO blog_posts ("
            . implode(",", $fields)
            . ") VALUES ("
            . str_repeat('?,', count($values));

        $query = substr($query, 0, strlen($query) - 1) . ")";

        if (!$this->db->runQuery($query, ...$values)) {
            return false;
        }

        $this->setId($this->db->getLastInsertId());


        var_dump($this->added_categories);
        $this->addTags();
        $this->addCategories();

        return true;
    }

    public function update(): bool
    {
        $query = "UPDATE blog_posts SET ";
        $values = array();

        foreach ($this->post->getAllValues() as $field => $value) {
            if ($value == null) {
                continue;
            }

            $query .= "$field = ?,";

            if ($value instanceof \UnitEnum) {
                $value = $value->value;
            }

            $values[] = $value;
        }

        $query = substr($query, 0, strlen($query) - 1);
        $query .= " WHERE post_slug = ?";
        $values[] = $this->getId();

        if (!$this->db->runQuery($query, ...$values)) {
            return false;
        }

        return true;
    }

    public function updateMeta(): bool
    {
        if (isset($this->added_tags) && count($this->added_tags) > 0) {
            echo "added_tags";
            if (!$this->addTags()) {
                return false;
            }
        }

        if (isset($this->removed_tags) && count($this->removed_tags) > 0) {
            echo "removed_tags";
            if (!$this->removeTags()) {
                return false;
            }
        }

        if (
            isset($this->added_categories)
                && count($this->added_categories) > 0
        ) {
            echo "added_categories";
            if (!$this->addCategories()) {
                return false;
            }
        }

        if (
            isset($this->removed_categories)
                && count($this->removed_categories) > 0
        ) {
            echo "removed_categories";
            if (!$this->removeCategories()) {
                return false;
            }
        }

        return true;
    }

    public function delete(): bool
    {
        $query = "DELETE 
                    blog_post_categories,
                    blog_post_tags
                FROM
                    blog_posts
                LEFT JOIN
                    blog_post_categories USING (post_id)
                LEFT JOIN
                    blog_post_tags USING (post_id)
                WHERE
                    post_slug = '{$this->id}'";

        if (!$this->db->runQuery($query)) {
            return false;
        }

        $query = "DELETE FROM
                    blog_posts
                WHERE
                    post_slug = '{$this->id}'";

        if (!$this->db->runQuery($query)) {
            return false;
        }

        return true;
    }

    private function addCategories(): bool
    {
        $categories = implode("','", $this->added_categories);

        $query = "INSERT INTO
                    blog_post_categories (post_id, category_id)
                SELECT
                    post_id,
                    category_id
                FROM (
                    SELECT
                        category_id
                    FROM
                        blog_categories
                    WHERE
                        category_name in ('$categories')
                ) t1 JOIN (
                    SELECT
                        post_id
                    FROM
                        blog_posts
                    WHERE
                        post_slug = '{$this->id}'
                    OR
                        post_id = '{$this->id}'
                ) t2";

        echo $query;

        if (!$this->db->runQuery($query)) {
            return false;
        }

        return true;
    }

    private function removeCategories(): bool
    {
        $categories = implode("','", $this->removed_categories);

        $query = "DELETE
                    blog_post_categories
                FROM
                    blog_post_categories
                JOIN
                    blog_posts USING (post_id)
                JOIN
                    blog_categories USING (category_id)
                WHERE
                    post_slug = '{$this->id}'
                AND
                    category_name IN ('$categories')";

        if (!$this->db->runQuery($query)) {
            return false;
        }

        return true;
    }

    private function addTags(): bool
    {
        foreach ($this->added_tags as $tag) {
            $query = "INSERT INTO
                    blog_tags (tag_label)
                SELECT
                    '{$tag}' as tag_label
                FROM
                    blog_tags
                WHERE
                    '$tag' NOT IN (
                        SELECT
   		                    tag_label         
	                    FROM
   		                    blog_tags
                    )
                LIMIT 1";

            if (!$this->db->runQuery($query)) {
                return false;
            }
        }

        $tags = implode("','", $this->added_tags);

        $query = "INSERT INTO
                    blog_post_tags (post_id, tag_id)
                SELECT
                    post_id,
                    tag_id
                FROM (
                    SELECT
                        tag_id
                    FROM
                        blog_tags
                    WHERE
                        tag_label in ('$tags')
                ) t1 JOIN (
                    SELECT
                        post_id
                    FROM
                        blog_posts
                    WHERE
                        post_slug = '{$this->id}'
                    OR
                        post_id = '{$this->id}'
                ) t2";

        if (!$this->db->runQuery($query)) {
            return false;
        };

        return true;
    }

    private function removeTags(): bool
    {
        $tags = implode("','", $this->removed_tags);

        $query = "DELETE
                blog_post_tags
            FROM
                blog_post_tags
            JOIN
                blog_posts USING (post_id)
            JOIN
                blog_tags USING (tag_id)
            WHERE
                post_slug = '{$this->id}'
            AND
                tag_label IN ('$tags')";

        if (!$this->db->runQuery($query)) {
            return false;
        }

        return true;
    }
}
