<?php
session_start();
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
setcookie('PHPSESSID', '', time() - 1, '/');
echo "<script>alert('退出成功'); location.href='welcome.php';</script>";
?>
