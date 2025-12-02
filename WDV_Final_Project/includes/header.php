<?php
// includes/header.php - Common header/banner for all pages
// Expects $pageTitle to be set before including this file
// Expects session to be started before including this file

$isLoggedIn = isset($_SESSION['validUser']) && $_SESSION['validUser'] === true;
$username = $_SESSION['username'] ?? 'Guest';
$pageTitle = $pageTitle ?? 'Mimi\'s Bakery';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="shared-styles.css">
