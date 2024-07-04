<?php
include 'config.php';

// Aktifkan laporan kesalahan untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inisialisasi array respons
$response = ['status' => 'error'];

// Periksa apakah semua data POST sudah diset
if (isset($_POST['id'], $_POST['title'], $_POST['description'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $imageUrl = '';

    // Tentukan direktori upload
    $uploadDir = 'uploads/';

    // Periksa apakah gambar diunggah
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $tmpName = $_FILES['image']['tmp_name'];
        $fileName = uniqid() . '_' . basename($_FILES['image']['name']); // Tambahkan uniqid untuk menghindari duplikasi nama
        $uploadPath = $uploadDir . $fileName;

        if (move_uploaded_file($tmpName, $uploadPath)) {
            $imageUrl = $uploadPath; // URL yang dapat diakses publik
        } else {
            $response['message'] = 'Gagal memindahkan file yang diunggah.';
            echo json_encode($response);
            exit;
        }
    }

    // Siapkan query SQL untuk pembaruan
    if ($imageUrl) {
        $query = $conn->prepare("UPDATE ideas SET title = ?, description = ?, image_url = ? WHERE id = ?");
        $query->bind_param('sssi', $title, $description, $imageUrl, $id);
    } else {
        $query = $conn->prepare("UPDATE ideas SET title = ?, description = ? WHERE id = ?");
        $query->bind_param('ssi', $title, $description, $id);
    }

    if ($query) {
        if ($query->execute()) {
            $response['status'] = 'success';
            // Redirect ke indexIdea.php setelah sukses memperbarui data
            header("Location: indexIdea.php");
            exit();
        } else {
            $response['message'] = 'Gagal memperbarui ide: ' . $query->error;
        }
        $query->close();
    } else {
        $response['message'] = 'Gagal menyiapkan pernyataan SQL: ' . $conn->error;
    }

} else {
    $response['message'] = 'Data formulir tidak lengkap';
}

// Tutup koneksi database
$conn->close();

echo json_encode($response);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <title>Application</title>
</head>
<body>
  <nav class="navbar navbar-light justify-content-center fs-3 mb-5" style="background-color: #00ff5573;">
    Update Manage Idea
  </nav>

  <div class="container">
    <div class="text-center mb-4">
      <h3>Edit Idea Information</h3>
      <p class="text-muted">Click update after changing any information</p>
    </div>

    <?php
    // Pastikan ID diambil dari GET atau POST
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    } elseif (isset($_POST['id'])) {
        $id = $_POST['id'];
    } else {
        die("ID tidak ditemukan.");
    }

    // Koneksi ulang untuk mengambil data
    include 'config.php'; // Pastikan file config.php termasuk pengaturan koneksi

    $sql = "SELECT * FROM `ideas` WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    ?>

    <div class="container d-flex justify-content-center">
      <form action="" method="post" enctype="multipart/form-data" style="width:50vw; min-width:300px;">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="row mb-3">
          <div class="col">
            <label class="form-label">Title</label>
            <input type="text" class="form-control" name="title" value="<?php echo $row['title'] ?>">
          </div>
          <div class="col">
              <label class="form-label">Description</label>
              <textarea class="form-control" id="description" name="description"><?php echo $row['description'] ?></textarea>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Image</label>
          <input type="file" class="form-control" name="image">
        </div>

        <div>
          <button type="submit" class="btn btn-success" name="submit">Update</button>
          <a href="indexIdea.php" class="btn btn-danger">Cancel</a>
        </div>
      </form>
    </div>
  </div>

  <!-- Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</body>
</html>
