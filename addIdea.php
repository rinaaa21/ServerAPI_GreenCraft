<?php
include 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = ['status' => 'error'];

if (isset($_POST['title'], $_POST['description'])) {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);

    error_log("Data diterima - Judul: $title, Deskripsi: $description");

    $uploadDir = 'uploads/';
    $imageUrl = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $tmpName = $_FILES['image']['tmp_name'];
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $tmpName);
        finfo_close($fileInfo);

        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($mimeType, $allowedMimeTypes)) {
            $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
            $uploadPath = $uploadDir . $fileName;

            if (move_uploaded_file($tmpName, $uploadPath)) {
                $imageUrl = $uploadPath;
            }
        } else {
            $response['message'] = 'Format file tidak didukung.';
            echo json_encode($response);
            exit;
        }
    } else {
        $response['message'] = 'Tidak ada gambar yang diunggah atau terjadi kesalahan unggah.';
        echo json_encode($response);
        exit;
    }

    $query = $conn->prepare("INSERT INTO ideas (title, description, image_url) VALUES (?, ?, ?)");
    if ($query) {
        error_log("Pernyataan SQL disiapkan dengan sukses.");
        $query->bind_param('sss', $title, $description, $imageUrl);

        if ($query->execute()) {
            $response['status'] = 'success';
            $response['image_url'] = $imageUrl;
            error_log("Ide berhasil ditambahkan.");
            header("Location: indexIdea.php");
            exit();
        } else {
            error_log('Gagal menyisipkan ide: ' . $query->error);
            $response['message'] = 'Gagal menyisipkan ide: ' . $query->error;
        }

        $query->close();
    } else {
        error_log('Gagal menyiapkan pernyataan SQL: ' . $conn->error);
        $response['message'] = 'Gagal menyiapkan pernyataan SQL: ' . $conn->error;
    }
} else {
    $response['message'] = 'Data formulir tidak lengkap';
}

$conn->close();
echo json_encode($response);
?>
