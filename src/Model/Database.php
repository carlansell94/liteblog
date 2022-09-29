<?php

namespace carlansell94\Liteblog\Model;

final class Database
{
    private \mysqli $conn;

    public function __construct()
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    }

    public function connect(): bool
    {
        try {
            $this->conn = new \mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        } catch (\mysqli_sql_exception $e) {
            if (DEBUG_MODE) {
                echo $e->getMessage() . ": " . $e->getCode();
            }

            return false;
        }

        return true;
    }

    public function getLastInsertId(): int|string
    {
        return $this->conn->insert_id;
    }

    /**
     * @param int|string|array<int|string> $params
     */
    public function runQuery(
        string $query,
        int|string|array ...$params
    ): \mysqli_stmt|false {
        try {
            $stmt = $this->conn->prepare($query);
        } catch (\mysqli_sql_exception $e) {
            return false;
        }

        if (count($params) > 0) {
            $values = array_values($params);
            $stmt->bind_param(str_repeat('s', count($values)), ...$values);
        }

        if (!$stmt->execute()) {
            return false;
        }

        return $stmt;
    }
}
