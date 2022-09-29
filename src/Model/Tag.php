<?php

namespace carlansell94\Liteblog\Model;

class Tag
{
    private \carlansell94\Liteblog\Lib\Tag $tag;

    public function __construct(
        private Database $db,
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

    public function setTagData(
        \carlansell94\Liteblog\Lib\Tag $tag
    ): void {
        $this->tag = $tag;
    }

    public function nameExists(): bool
    {
        if (!$tag_name = $this->tag->getName()) {
            return false;
        }

        $query = "SELECT
                    tag_label
                FROM 
                    blog_tags
                WHERE
                    tag_label = ?";

        if (!$stmt = $this->db->runQuery($query, $tag_name)) {
            return false;
        }

        $stmt->store_result();

        if ($stmt->num_rows <= 0) {
            return false;
        }

        return true;
    }

    public function create(): bool
    {
        if (!$tag_name = $this->tag->getName()) {
            return false;
        }

        $query = "INSERT INTO
                    blog_tags (tag_label)
                VALUES
                    (?)";

        if (!$this->db->runQuery($query, $tag_name)) {
            return false;
        }

        return true;
    }

    public function update(): bool
    {
        if (!$tag_name = $this->tag->getName()) {
            return false;
        }

        $query = "UPDATE
                    blog_tags
                SET
                    tag_label = ?
                WHERE
                    post_slug = '{$this->id}'";

        if (!$this->db->runQuery($query, $tag_name)) {
            return false;
        }

        return true;
    }

    public function delete(): bool
    {
        $query = "DELETE
                    blog_post_tags
                FROM
                    blog_post_tags
                JOIN
                    blog_tags USING (tag_id)
                WHERE
                    tag_slug = '{$this->tag->getUri()}'";

        if (!$this->db->runQuery($query)) {
            return false;
        }

        $query = "DELETE FROM
                    blog_tags
                WHERE
                    tag_slug = '{$this->tag->getUri()}'";

        if (!$this->db->runQuery($query)) {
            return false;
        }

        return true;
    }
}
