<?php

namespace carlansell94\Liteblog\Controller;

class Session extends Controller
{
    public function setId(int|string $id): void
    {
        $this->model['post']->setId($id);
    }

    public function login(): bool
    {
        if (!$login = $this->readInput()) {
            return false;
        }

        if (!$login['user'] || !$login['pass']) {
            return false;
        }

        if (
            !$user = \carlansell94\Liteblog\Model\User::get(
                db: self::$db,
                user: $login['user']
            )
        ) {
            return false;
        }

        $user = $user->fetch_object();

        if ($user === null || !password_verify($_POST['pass'], $user->pass)) {
            return false;
        }

        return true;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(): bool
    {
        return true;
    }

    public function delete(): bool
    {
        return true;
    }

    public function load(): bool
    {
        return true;
    }

    public function loadView(): bool
    {
        return true;
    }

    public function output(): void
    {
        require_once __DIR__ . '/../Session/login.php';
    }

    /** @return array<string> */
    private function readInput(): array|false
    {
        $data = file_get_contents('php://input');

        if (!$data) {
            return false;
        }

        parse_str($data, $params);

        return $params;
    }
}
