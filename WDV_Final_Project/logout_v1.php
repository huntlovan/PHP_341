<?php
// logout.php - Destroys the session and redirects to login page
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie if it exists
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="refresh" content="2;url=login_v2.php">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Logged Out</title>
    <style>
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,'Open Sans','Helvetica Neue',sans-serif;margin:0;background:#f0f2f5;color:#333;display:flex;align-items:center;justify-content:center;min-height:100vh}
        .message{max-width:450px;background:#fff;padding:40px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,.1);text-align:center}
        h1{margin:0 0 12px;font-size:26px;color:#059669}
        p{color:#666;margin:0 0 24px}
        a{display:inline-block;padding:12px 24px;background:#4f46e5;color:#fff;text-decoration:none;border-radius:8px;font-weight:600}
        a:hover{background:#4338ca}
    </style>
</head>
<body>
    <div class="message">
        <h1>✓ Logged Out Successfully</h1>
        <p>You have been logged out of the administrator system.</p>
        <p>Redirecting to login page in 2 seconds...</p>
        <a href="login_v1.php">Return to Login</a>
    </div>
</body>
</html>