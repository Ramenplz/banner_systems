<?php
// includes/config.php
session_start();

// ตั้งค่าเวลา
date_default_timezone_set('Asia/Bangkok');

// ตั้งค่า base URL และ paths
define('BASE_URL', 'https://ramenplz.free.nf');
define('BASE_PATH', __DIR__ . '/../');
define('UPLOAD_PATH', BASE_PATH . 'uploads/');
define('UPLOAD_URL', BASE_URL . 'uploads/');

// ตั้งค่าสำหรับ environment
define('ENVIRONMENT', 'development'); // หรือ 'production'

// ตั้งค่า error reporting
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', BASE_PATH . 'logs/error.log');
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', BASE_PATH . 'logs/error.log');
}

// ตั้งค่าฐานข้อมูล
define("DB_HOST", "sql100.infinityfree.com");
define("DB_NAME", "if0_39695555_banner_systems");
define("DB_USER", "if0_39695555");
define("DB_PASS", "3WzQqbqVM8d"); // ใส่ให้ถูกต้อง
define("DB_CHARSET", "utf8mb4");
// ตั้งค่า API
define('API_KEY_LENGTH', 32);
define('DEFAULT_API_EXPIRE_DAYS', 30);

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
        error_log(print_r($message, true));
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
}