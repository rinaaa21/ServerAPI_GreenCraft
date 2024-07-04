<?php
include "config.php";

// Periksa jika parameter 'id' ada dalam request GET
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Persiapkan statement DELETE
    $sql = "DELETE FROM ideas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    // Eksekusi statement
    if ($stmt->execute()) {
        // Berhasil menghapus
        http_response_code(200); // Response OK
        header("Location: indexIdea.php?msg=Data deleted successfully");
        exit();
    } else {
        // Gagal menghapus
        http_response_code(500); // Server Error
        echo json_encode(array("message" => "Failed to delete idea."));
    }

    // Tutup statement dan koneksi database
    $stmt->close();
    $conn->close();
} else {
    // Jika parameter 'id' tidak ada
    http_response_code(400); // Bad Request
    echo json_encode(array("message" => "Missing 'id' parameter."));
}
?>