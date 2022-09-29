<?php

namespace carlansell94\Liteblog\Model;

class Category
{
    private \carlansell94\Liteblog\Lib\Category $category;

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

    public function setCategoryData(
        \carlansell94\Liteblog\Lib\Category $category
    ): void {
        $this->category = $category;
    }

    public function nameExists(): bool
    {
        $query = "SELECT
                    category_name
                FROM 
                    blog_categories
                WHERE
                    category_name = ?";

        $stmt = $this->db->runQuery($query, $this->category->getName());
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            return true;
        }

        return false;
    }

    public function create(): void
    {
        $query = "INSERT INTO
                    blog_categories (category_name)
                VALUES
                    (?)";

        $this->db->runQuery($query, $this->category->getName());
    }

    public function update(): void
    {
        $query = "UPDATE
                    blog_categories
                SET
                    category_name = ?
                WHERE
                    post_slug = '{$this->id}'";

        $this->db->runQuery($query, $this->category->getName());
    }

    public function delete(): void
    {
        $query = "DELETE
                    blog_post_categories
                FROM
                    blog_post_categories
                JOIN
                    blog_categories USING (category_id)
                WHERE
                    category_slug = '{$this->category->getUri()}'";

        $this->db->runQuery($query);

        $query = "DELETE FROM
                    blog_categories
                WHERE
                    category_slug = '{$this->category->getUri()}'";

        $this->db->runQuery($query);
    }
}
