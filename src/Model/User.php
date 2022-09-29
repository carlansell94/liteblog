<?php

namespace carlansell94\Liteblog\Model;

class User
{
    public static function get(
        Database $db,
        string $user
    ): \mysqli_result|false {
        $query = "SELECT
                    user_id,
                    user_name,
                    user_first_name,
                    user_last_name,
                    email,
                    pass
                FROM
                    users
                WHERE
                    user_name = ?";

        if (!$output = $db->runQuery($query, $user)) {
            return false;
        }

        return $output->get_result();
    }
}
