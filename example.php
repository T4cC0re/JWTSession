<?php
include "JWTSession.php";
JWTSession::setKey("ASDF");
JWTSession::load();
JWTSession::setWhitelist(array('asdf'));
register_shutdown_function('JWTSession::save');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION = $_POST;
    header('Location: example.php');
    die();
} elseif (true === isset($_GET['clear'])) {
    JWTSession::clear();
    header('Location: example.php');
    die();
}

?><!DOCTYPE html>
<html>
<head>
    <title>JWTSession Example page</title>
</head>
<body>
<b>Tested with PHP 5.2!</b>
<form action="example.php" method="post">
    <textarea name="text_foo" style="width: 100%" placeholder="Not whitelisted. This will not appear in the JWT"></textarea><br />
    <input name="asdf" type="text" style="width: 100%" placeholder="Whitelisted. This will appear in the JWT" /><br />
    <button type="submit">Save my $_SESSION</button>
</form>
<a href="example.php?clear">Clear $_SESSION</a><br />
<label>$_SESSION:</label>
<textarea rows="20" style="width: 100%"><?php var_dump($_SESSION); ?></textarea>
</body>
</html>
