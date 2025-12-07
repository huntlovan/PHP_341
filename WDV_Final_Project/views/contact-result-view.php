<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $accessDenied ? 'Access Denied' : 'Contact Form - Result'; ?></title>
    <style>
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,'Open Sans','Helvetica Neue',sans-serif;margin:0;background:linear-gradient(135deg,<?php echo $accessDenied ? '#dc2626 0%, #b91c1c 100%' : '#667eea 0%,#764ba2 100%'; ?>);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
        .container{max-width:600px;background:#fff;padding:48px;border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,.3);text-align:center}
        .icon{font-size:64px;margin-bottom:20px}
        h1{margin:0 0 16px;font-size:32px;font-weight:700;color:#1a1a2e}
        .message{padding:24px;border-radius:12px;margin:24px 0;font-size:18px;line-height:1.6}
        .message.denied{background:#fee;color:#991b1b;border:3px solid #dc2626;font-weight:600}
        .btn{display:inline-block;margin-top:24px;padding:14px 32px;background:linear-gradient(135deg,<?php echo $accessDenied ? '#dc2626 0%, #b91c1c 100%' : '#667eea 0%,#764ba2 100%'; ?>);color:#fff;text-decoration:none;border-radius:10px;font-weight:700;transition:transform .2s}
        .btn:hover{transform:translateY(-2px)}
    </style>
</head>
<body>
    <div class="container">
        <?php if ($accessDenied): ?>
            <div class="icon">🚫</div>
            <h1>Access Denied</h1>
            <div class="message denied">
                Your submission was blocked by our security system. This usually happens when automated bots try to access our form. If you are a legitimate user, please try again.
            </div>
            <a href="contactForm.php" class="btn">🔄 Try Again - Return to Contact Form</a>
        <?php else: ?>
            <div class="icon">📭</div>
            <h1>No Form Data</h1>
            <p>Please submit the contact form first.</p>
        <?php endif; ?>
        
        <a href="index_v1.php" class="btn">← Back to Home</a>
    </div>
</body>
</html>
