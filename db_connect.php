<?php

// Load environment variables
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Create PDO connection for files that need it
try {
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    error_log("PDO connection error: " . $e->getMessage());
    $pdo = null;
}

/**
 * Encrypt a string using AES-256-GCM.
 * Requires PORTAL_ENCRYPTION_KEY environment variable (64-char hex = 256 bits).
 */
function portal_encrypt(string $plaintext): string
{
    $raw = trim($_ENV['PORTAL_ENCRYPTION_KEY'] ?? '');
    $key = @hex2bin($raw);
    if ($key === false || strlen($key) !== 32) {
        throw new RuntimeException('PORTAL_ENCRYPTION_KEY must be a 64-character hex string (256 bits).');
    }
    $iv = random_bytes(12); // 96-bit IV for GCM
    $ciphertext = openssl_encrypt($plaintext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    if ($ciphertext === false) {
        throw new RuntimeException('Encryption failed.');
    }
    // Format: base64(iv + tag + ciphertext)
    return base64_encode($iv . $tag . $ciphertext);
}

/**
 * Decrypt a string encrypted with portal_encrypt().
 */
function portal_decrypt(string $encoded): string
{
    $raw = trim($_ENV['PORTAL_ENCRYPTION_KEY'] ?? '');
    $key = @hex2bin($raw);
    if ($key === false || strlen($key) !== 32) {
        throw new RuntimeException('PORTAL_ENCRYPTION_KEY must be a 64-character hex string (256 bits).');
    }
    $data = base64_decode($encoded, true);
    if ($data === false || strlen($data) < 28) { // 12 IV + 16 tag minimum
        throw new RuntimeException('Invalid encrypted data.');
    }
    $iv = substr($data, 0, 12);
    $tag = substr($data, 12, 16);
    $ciphertext = substr($data, 28);
    $plaintext = openssl_decrypt($ciphertext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    if ($plaintext === false) {
        throw new RuntimeException('Decryption failed.');
    }
    return $plaintext;
}

/**
 * Database connection function for MySQL (mysqli)
 */
function get_db_connection()
{
    $host = $_ENV['DB_HOST'];
    $username = $_ENV['DB_USERNAME'];
    $password = $_ENV['DB_PASSWORD'];
    $database = $_ENV['DB_NAME'];

    // Create new connection
    $db = new mysqli($host, $username, $password, $database);

    // Check connection
    if ($db->connect_error) {
        error_log("Database connection error: " . $db->connect_error);
        die("Database connection failed: " . $db->connect_error);
    }

    // Set character set
    $db->set_charset("utf8mb4");

    return $db;
}
