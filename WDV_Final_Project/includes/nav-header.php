    <!-- Navigation Bar -->
    <nav>
        <ul>
            <li><a href="index_v1.php">🏠 Home</a></li>
            <li><a href="baked-products.php">🛍️ View Bakery</a></li>
            <?php if ($isLoggedIn): ?>
                <li><a href="login_v1.php">⚙️ Admin Panel</a></li>
                <li><a href="logout_v1.php" class="logout">🔓 Logout</a></li>
            <?php else: ?>
                <li><a href="login_v1.php" class="login">🔐 Admin Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Top Banner -->
    <div class="top-banner">
        <div class="banner-content">
            <span class="banner-icon">🎂</span>
            <span class="banner-text">
                <strong>Fresh Daily!</strong> All items baked fresh with love by Mimi • 
                <a href="baked-products.php">Order Now</a>
            </span>
            <span class="banner-icon">🍪</span>
        </div>
    </div>

    <!-- Header -->
    <header>
        <h1><?php echo isset($headerTitle) ? htmlspecialchars($headerTitle) : 'Mimi\'s Bakery'; ?></h1>
        <p><?php echo isset($headerSubtitle) ? htmlspecialchars($headerSubtitle) : 'Freshly Baked with Love'; ?></p>
        <?php if ($isLoggedIn): ?>
            <div class="user-info">👤 Logged in as: <strong><?php echo htmlspecialchars($username); ?></strong></div>
        <?php endif; ?>
    </header>
