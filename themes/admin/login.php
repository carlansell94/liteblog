<?php

if (defined('SITE_NAME')) {
    $title = SITE_NAME . " Login";
} else {
    $title = 'Login';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?= str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__) ?>/login.css">
	<title><?= $title ?></title>
</head>
<body>
    <main>
        <header>
            <h1><?= $title ?></h1>
        </header>
        <form name="login" action="login" method="post">
            <?php
                if (isset($_SESSION['login_fails'])) {
                    echo "<div id='login-error'>" . ($_SESSION['login_fail_reason'] ?? "Incorrect Username/Password") . "</div>";
                }
            ?>
            <div>
                <label for="username">Username:</label>
                <input type="text" name="user" autocomplete="username" required="">
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" name="pass" autocomplete="current-password" required="">
            </div>
            <button type="submit" class="green-button">Login</button>
        </form>
    </main>
</body>
</html>
