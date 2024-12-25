<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "crud_db");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Variabel untuk pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Pagination setup
$limit = 5;  // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total halaman
$sqlCount = "SELECT COUNT(*) as total FROM users WHERE name LIKE ?";
$stmtCount = $conn->prepare($sqlCount);
$searchLike = "%$search%";
$stmtCount->bind_param("s", $searchLike);
$stmtCount->execute();
$resultCount = $stmtCount->get_result();
$totalData = $resultCount->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

// Query untuk menampilkan data dengan filter pencarian dan batas pagination
$sql = "SELECT * FROM users WHERE name LIKE ? LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $searchLike, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Tambah data pengguna
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $sqlInsert = "INSERT INTO users (name, email, phone) VALUES (?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("sss", $name, $email, $phone);
    if ($stmtInsert->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $stmtInsert->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD dengan Modal Tambah Pengguna</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        body {
         background-color: #d7ccc8; /* Coklat kopi muda */
            color: #4f4f4f; /* Warna teks */
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Full screen height */
            margin: 0;
        }

        .container {
         background-color: #ffffff; /* Warna putih untuk kontainer */
         border-radius: 10px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
         padding: 30px;
         max-width: 900px;
         width: 100%;
        }

        h2 {
          color: #6d4c41; /* Coklat tua untuk judul */
          font-weight: 700;
          margin-bottom: 30px;
          text-align: center; /* Center title */
        }

        .btn-primary, .btn-success {
         background-color: #8d6e63; /* Coklat medium untuk tombol */
         border: none;
        }

        .btn-primary:hover, .btn-success:hover {
         background-color: #7b5e57; /* Coklat gelap saat hover */
        }
        

        .btn-danger {
            background-color: #ef5350; /* Light red for delete button */
            border: none;
        }

        .btn-danger:hover {
            background-color: #e53935; /* Darker red on hover */
        }

        .table {
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 10px;
            width: 100%;
        }

        .thead-light {
         background-color: #bcaaa4; /* Coklat muda untuk header tabel */
         color: #4f4f4f;
          font-weight: 600;
        }

        .table-striped tbody tr:nth-child(odd) {
         background-color: #efebe9; /* Coklat sangat muda untuk baris ganjil */
        }

        .table-striped tbody tr:nth-child(even) {
           background-color: #ffffff; /* Warna putih untuk baris genap */
        }

        .pagination .page-item.active .page-link {
            background-color: #42a5f5;
            border-color: #42a5f5;
            color: #ffffff;
        }

        .pagination .page-item a {
            color: #007acc;
        }

        .modal-header {
         background-color: #8d6e63; /* Coklat medium untuk header modal */
            color: white;
        }

        .btn-secondary {
          background-color: #d7ccc8; /* Coklat kopi muda untuk tombol sekunder */
          color: #4f4f4f;
         border: 1px solid #8d6e63; /* Border coklat medium */
        }

        .btn-secondary:hover {
            background-color: #bbdefb;
        }

        input:focus {
            border-color: #42a5f5 !important;
            box-shadow: 0 0 5px rgba(66, 165, 245, 0.5);
        }

        .form-inline {
            justify-content: center; /* Center search form */
            margin-bottom: 20px;
        }

        .form-inline input {
            border-radius: 5px;
            border: 1px solid #64b5f6;
        }

        .add-user-btn {
            text-align: left;
            margin-bottom: 15px; /* Keep button above the table */
        }

        .form-group label {
            font-weight: bold;
            color: #42a5f5;
        }

        .form-group input {
            border: 1px solid #90caf9;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Daftar Pengguna</h2>

    <!-- Form Pencarian -->
    <form method="GET" action="" class="form-inline">
        <input type="text" name="search" value="<?php echo $search; ?>" class="form-control mr-2" placeholder="Cari nama..." style="width: 250px;">
        <button type="submit" class="btn btn-primary">Cari</button>
    </form>

    <!-- Tombol Tambah Pengguna -->
    <div class="add-user-btn">
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addUserModal">
            Tambah Pengguna
        </button>
    </div>

    <!-- Tabel Data Pengguna -->
    <table class="table table-bordered table-striped">
        <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Telepon</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['phone']; ?></td>
                <td>
                    <a href="update.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav>
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                    <a class="page-link" href="?search=<?php echo $search; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php } ?>
        </ul>
    </nav>

    <!-- Modal Tambah Pengguna -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Tambah Pengguna Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="form-group">
                            <label for="name">Nama:</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Telepon:</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
