<?php
header('Content-Type: application/json');
include 'config.php';

// Aktifkan laporan kesalahan untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['userId'])) {
    echo json_encode(["error" => "User ID not provided"]);
    exit();
}

$userId = $_GET['userId'];

// Log userId yang diterima
error_log("User ID received: $userId");

// Prepared statement untuk mengamankan query
$query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
if ($stmt === false) {
    error_log("Failed to prepare statement: " . mysqli_error($conn));
    echo json_encode(["error" => "Failed to prepare statement"]);
    exit();
}

mysqli_stmt_bind_param($stmt, "i", $userId);
if (!mysqli_stmt_execute($stmt)) {
    error_log("Failed to execute statement: " . mysqli_stmt_error($stmt));
    echo json_encode(["error" => "Failed to execute statement"]);
    exit();
}

$result = mysqli_stmt_get_result($stmt);
if ($result === false) {
    error_log("Failed to get result: " . mysqli_stmt_error($stmt));
    echo json_encode(["error" => "Failed to get result"]);
    exit();
}

$user = mysqli_fetch_assoc($result);
if ($user) {
    echo json_encode($user);
} else {
    echo json_encode(["error" => "User not found"]);
}

// Log jika user tidak ditemukan
if (!$user) {
    error_log("User not found for User ID: $userId");
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
