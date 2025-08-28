<?php
// includes/config.php

// กำหนด BASE_PATH ก่อน
define('BASE_PATH', realpath(dirname(__FILE__) . '/../') . '/');

// เริ่ม session แค่ครั้งเดียว
session_start();

// ตั้งค่าเวลา
date_default_timezone_set('Asia/Bangkok');

// ตั้งค่า base URL และ paths
define('BASE_URL', 'https://www.ramenplzbanner.space/');
define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'] . '/uploads/');
define('UPLOAD_URL', BASE_URL . 'uploads/');

// ตั้งค่าฐานข้อมูล
define("DB_HOST", "sql100.infinityfree.com");
define("DB_NAME", "if0_39695555_banner_systems");
define("DB_USER", "if0_39695555");
define("DB_PASS", "3WzQqbqVM8d");
define("DB_CHARSET", "utf8mb4");

// ตั้งค่า API
define('API_KEY_LENGTH', 32);
define('DEFAULT_API_EXPIRE_DAYS', 30);

// กำหนด environment
define('ENVIRONMENT', 'development'); // เปลี่ยนเป็น 'production' เมื่อ deploy

// error reporting - ขึ้นอยู่กับ environment
if (ENVIRONMENT === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
}

// ฟังก์ชันช่วยเหลือ
function redirect($url, $statusCode = 303)
{
    header('Location: ' . $url, true, $statusCode);
    exit();
}

function sanitize_input($data)
{
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function is_valid_url($url)
{
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

function generate_api_key()
{
    return bin2hex(random_bytes(API_KEY_LENGTH / 2));
}

// ฟังก์ชันสำหรับ debug
function debug_log($message)
{
    if (ENVIRONMENT === 'development') {
        $log_file = BASE_PATH . 'logs/debug_' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        $message = is_array($message) ? print_r($message, true) : $message;
        file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND | LOCK_EX);
    }
}

// ฟังก์ชันสำหรับการเชื่อมต่อ database
function get_db_connection()
{
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        debug_log("Database connection failed: " . $e->getMessage());
        die("Database connection error. Please try again later.");
    }
}

// ตั้งค่า autoload สำหรับ classes
spl_autoload_register(function ($class_name) {
    $file = BASE_PATH . 'includes/classes/' . $class_name . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// ตรวจสอบและสร้างโฟลเดอร์ logs หากไม่มี
if (!file_exists(BASE_PATH . 'logs')) {
    mkdir(BASE_PATH . 'logs', 0755, true);
}

// ตรวจสอบและสร้างโฟลเดอร์ uploads หากไม่มี
if (!file_exists(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0755, true);
    // สร้างไฟล์ .htaccess สำหรับป้องกันการเข้าถึง直接
    $htaccess_content = "Order deny,allow\nDeny from all";
    file_put_contents(UPLOAD_PATH . '.htaccess', $htaccess_content);
}

// ตรวจสอบว่ามี table จำเป็นหรือไม่
function check_database_tables($db)
{
    $required_tables = ['platforms', 'banners', 'banner_platforms'];
    $missing_tables = [];

    foreach ($required_tables as $table) {
        $stmt = $db->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        if ($stmt->rowCount() === 0) {
            $missing_tables[] = $table;
        }
    }

    if (!empty($missing_tables) && ENVIRONMENT === 'development') {
        debug_log("Missing tables: " . implode(', ', $missing_tables));
    }
}

// เรียกตรวจสอบ tables เมื่อโหลด config
try {
    $db = get_db_connection();
    check_database_tables($db);
} catch (Exception $e) {
    debug_log("Database check failed: " . $e->getMessage());
}
