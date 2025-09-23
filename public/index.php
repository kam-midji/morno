<?php
// Madreseh Planner - Main Entry Point

// Start the session
session_start();

// Load core helpers
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Session.php';

// Check for "Remember Me" cookie before doing anything else
Session::checkRememberMe();
require_once __DIR__ . '/../app/core/Controller.php';
require_once __DIR__ . '/../app/core/helpers.php';
require_once __DIR__ . '/../app/core/Jalali.php';

// --- Basic MVC Router ---

// 1. Parse the URL
$url = $_GET['url'] ?? 'home/index';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$urlParts = explode('/', $url);

// 2. Set Controller
$controllerName = !empty($urlParts[0]) ? ucwords($urlParts[0]) : 'Home';
$controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    // Instantiate controller
    $controller = new $controllerName;
} else {
    // For simplicity, we'll just die for now. A real app would have a 404 page.
    die("Controller not found: " . htmlspecialchars($controllerName));
}

// 3. Set Method
$methodName = $urlParts[1] ?? 'index';

if (method_exists($controller, $methodName)) {
    // Unset controller and method from URL parts to get params
    unset($urlParts[0], $urlParts[1]);
    $params = $urlParts ? array_values($urlParts) : [];

    // 4. Call the method with parameters
    call_user_func_array([$controller, $methodName], $params);
} else {
    die("Method not found: " . htmlspecialchars($methodName) . " in controller " . htmlspecialchars($controllerName));
}
