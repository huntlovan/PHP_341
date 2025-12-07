<?php
/*******************************************************
 * @package  wdv341_final_project - Admin Module
 * @author   Hunter Lovan
 * @version  1.0.0
 * @link     http://kickshunter.com/WDV341/index_v1.php
 * *****************************************************
 * login_v1.php - Self-posting login page with session management
 * ToDo: implement the MVC design pattern
 */
ob_start();
require_once __DIR__ . '/dbConnect1.php';
ob_end_clean();
session_start();

// Initialize variables
$errorMessage = '';
$showAdminOptions = false;

// Check if user is already logged in
if (isset($_SESSION['validUser']) && $_SESSION['validUser'] === true) {
    $showAdminOptions = true;
}

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        $errorMessage = 'Please enter both username and password.';
    } else {
        try {
            //require_once __DIR__ . '/dbConnect1.php';
            
            // Query to validate user credentials
            $sql = "SELECT event_user_id, event_user_name, event_user_password 
                    FROM event_user 
                    WHERE event_user_name = :username";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':username', $username);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Validate password (plain text for now, as per test data)
            if ($user && $user['event_user_password'] === $password) {
                // Valid login - establish session
                $_SESSION['validUser'] = true;
                $_SESSION['username'] = $user['event_user_name'];
                $_SESSION['user_id'] = $user['event_user_id'];
                $showAdminOptions = true;
                // Redirect to landing page after successful login
                header('Location: index_v1.php');
                exit();
            } else {
                $errorMessage = 'Invalid username or password. Please try again.';
            }
        } catch (Exception $e) {
            $errorMessage = 'Login error: ' . $e->getMessage();
        }
    }
}

// Set page-specific variables
$pageTitle = 'Administrator Login - Mimi\'s Bakery';
$headerTitle = $showAdminOptions ? 'Administrator Panel' : 'Administrator Login';
$headerSubtitle = $showAdminOptions ? 'Manage your bakery products and settings' : 'Please sign in to access the admin panel';

// Include header
include __DIR__ . '/includes/header.php';
?>
    <style>
        /* Page-specific styles for login_v1.php */
        .login-container{max-width:500px;margin:40px auto;background:#fff;padding:32px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,.1)}
        h1{margin:0 0 8px;font-size:28px;font-weight:600;color:#1a1a2e}
        .login-subtitle{color:#666;margin:0 0 24px;font-size:15px}
        .error{background:#fee;border:1px solid #fcc;color:#c33;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-weight:500}
        .form-group{margin-bottom:18px}
        label{display:block;font-weight:600;margin-bottom:6px;color:#444}
        input[type="text"], input[type="password"]{width:100%;padding:12px 14px;border:1px solid #ddd;border-radius:8px;font-size:16px;box-sizing:border-box}
        input[type="text"]:focus, input[type="password"]:focus{outline:none;border-color:#4f46e5}
        button{width:100%;padding:12px;background:#4f46e5;color:#fff;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;margin-top:8px}
        button:hover{background:#4338ca}
        .admin-panel{background:#f9fafb;padding:24px;border-radius:10px;border:1px solid #e5e7eb}
        .admin-panel h2{margin:0 0 16px;font-size:22px;color:#1a1a2e}
        .admin-options{list-style:none;padding:0;margin:0}
        .admin-options li{margin-bottom:12px}
        .admin-options a{display:block;padding:14px 18px;background:#fff;border:1px solid #e0e0e0;border-radius:8px;text-decoration:none;color:#333;font-weight:500;transition:all .2s}
        .admin-options a:hover{background:#f5f5f5;border-color:#4f46e5;color:#4f46e5}
        .logout-link{display:inline-block;margin-top:20px;padding:10px 20px;background:#dc2626;color:#fff;text-decoration:none;border-radius:8px;font-weight:600}
        .logout-link:hover{background:#b91c1c}
        .welcome{color:#059669;font-weight:600;margin-bottom:20px;padding:12px;background:#ecfdf5;border-radius:8px;border:1px solid #a7f3d0}
    </style>
</head>
<body>
<?php
// Include navigation and banner
include __DIR__ . '/includes/nav-header.php';
?>

    <div class="container">
        <div class="login-container">
        <?php if ($showAdminOptions): ?>
            <!-- Administrator Options -->
            <h1>Administrator Panel</h1>
            <div class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?>!</div>
            
            <div class="admin-panel">
                <h2>Administrator Options</h2>
                <ul class="admin-options">
                    <li><a href="productInputForm.html">➕ Add New Product</a></li>
                    <li><a href="updateProducts.php">📋 Show List of Products (Update/Delete)</a></li>
                </ul>
            </div>
            
            <a href="logout_v2.php" class="logout-link">🔓 Logout of Administrator</a>
            
        <?php else: ?>
            <!-- Login Form -->
            <h1>Administrator Login</h1>
            <p class="login-subtitle">Please sign in to access the event management system.</p>
            
            <?php if ($errorMessage): ?>
                <div class="error"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>
            
            <form method="post" action="login_v1.php">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" name="login">Sign In</button>
            </form>
        <?php endif; ?>
        </div>
    </div>

<?php
// Include footer
include __DIR__ . '/includes/footer.php';
?>
