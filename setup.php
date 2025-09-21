<?php

// Madreseh Planner - Setup Script
// This script initializes the database. Run it once.

// --- Configuration ---
// This should be the only place you need to change database settings for the setup.
// Make sure these match the credentials in app/core/Database.php
$db_host = '127.0.0.1';
$db_name = 'madreseh_planner';
$db_user = 'root';
$db_pass = 'password';
$admin_pass = 'admin123'; // Default password for the admin user

echo "<pre>"; // Use <pre> for better formatting in browser

// --- 1. Connect to MySQL and Create Database ---
try {
    $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_persian_ci;");
    $pdo->exec("USE `$db_name`;");
    echo "Database '$db_name' created or already exists.\n";
    echo "Switched to database '$db_name'.\n";
} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}

// --- 2. Read and Execute SQL from setup.sql ---
try {
    echo "\n--- Reading setup.sql to create tables ---\n";
    $sql = file_get_contents('setup.sql');
    if ($sql === false) {
        die("Error: Could not read setup.sql file.");
    }
    // PDO::exec can execute multiple statements but it's not recommended for all drivers.
    // Since this is a setup script and we control the SQL, it's acceptable here.
    // For more complex scenarios, parsing the file statement by statement is safer.
    $pdo->exec($sql);
    echo "Tables created successfully from setup.sql.\n";
} catch (PDOException $e) {
    die("TABLE CREATION ERROR: " . $e->getMessage());
}

// --- 3. Seed the Database with Initial Data ---
try {
    echo "\n--- Seeding database with initial data ---\n";

    // Hash the admin password
    $hashed_password = password_hash($admin_pass, PASSWORD_DEFAULT);

    // Insert Admin User
    $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['admin', $hashed_password, 'مدیر کل سیستم', 'admin']);
    echo "Admin user created with username 'admin' and password '$admin_pass'.\n";

    // Insert Default Semester
    $stmt = $pdo->prepare("INSERT INTO semesters (name, start_date, end_date) VALUES (?, ?, ?)");
    $stmt->execute(['ترم اول 1404-1405', '2025-09-23', '2026-01-20']);
    echo "Default semester created.\n";

    // Insert Default Audiences
    $audiences = ['پایه هفتم', 'پایه هشتم', 'پایه نهم', 'والدین', 'معلمان'];
    $stmt = $pdo->prepare("INSERT INTO audiences (name) VALUES (?)");
    foreach ($audiences as $audience) {
        $stmt->execute([$audience]);
    }
    echo count($audiences) . " default audiences inserted.\n";

    // Insert Default Organizers
    $organizers = ['معاونت آموزش', 'معاونت فرهنگی', 'معاونت پژوهشی', 'معاونت فناوری'];
    $stmt = $pdo->prepare("INSERT INTO organizers (name) VALUES (?)");
    foreach ($organizers as $organizer) {
        $stmt->execute([$organizer]);
    }
    echo count($organizers) . " default organizers inserted.\n";

    echo "\n--- Database setup and seeding complete! ---\n";

} catch (PDOException $e) {
    // Check for duplicate entry error, which might happen if script is run twice.
    if ($e->errorInfo[1] == 1062) {
        echo "Data seeding was already completed or there was a duplicate entry conflict.\n";
    } else {
        die("DATA SEEDING ERROR: " . $e->getMessage());
    }
}

echo "</pre>";
