<?php
/*******************************************************
 * @package  wdv341_final_project
 * @author   Hunter Lovan
 * @version  1.0.0
 * @link     http://kickshunter.com/WDV341/index_v1.php
 * *****************************************************
 * processEmailForm.php - Entry point for contact form processing
 * Uses MVC pattern: Controller handles logic, this file routes and displays view
 * 
 * Dependencies: called from contactForm.php (work in progress) and depends on ContactController.php, EmailHelper.php, & contact-result-view.php
 * ToDo: load Mimi's Bakery phone and web site from a configuration data storage or file.
 */
session_start();

// Load dependencies
require_once __DIR__ . '/EmailHelper.php';
require_once __DIR__ . '/controllers/ContactController.php';

$messageType = '';
$formData = [];

// Check if form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialize controller
    $emailHelper = new EmailHelper(__DIR__ . '/email_config.php');
    $controller = new ContactController($emailHelper);
    
    // Process form (Controller layer)
    $result = $controller->processContactForm($_POST);
    
    // Handle result
    if ($result['redirect']) {
        if ($result['success']) {
            $_SESSION['contact_message'] = $result['message'];
            $_SESSION['contact_data'] = $result['data'];
        } else {
            $_SESSION['access_denied'] = true;
        }
        header('Location: ' . $result['redirect']);
        exit;
    } else {
        // Validation error - redirect back to form
        $_SESSION['contact_error'] = $result['message'];
        header('Location: contactForm.php');
        exit;
    }
}

// Check if this is an access denied redirect (View layer)
$accessDenied = false;
if (isset($_GET['denied']) && $_GET['denied'] == 1) {
    $accessDenied = true;
    if (isset($_SESSION['access_denied'])) {
        unset($_SESSION['access_denied']);
    }
}

// Include view
include __DIR__ . '/views/contact-result-view.php';
