<?php
// Mengecek apakah form telah dikirim dengan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil data dari form
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = substr(preg_replace('/[^0-9]/', '', $_POST["phone"]), 0, 13);

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Email tidak valid.";
        exit;
    }

    // Membuat koneksi ke database
    $conn = new mysqli("localhost", "root", "", "crud_db");

    // Mengecek koneksi ke database
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    // Menyusun query menggunakan prepared statement untuk menghindari SQL Injection
    $stmt = $conn->prepare("INSERT INTO users (name, email, phone) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $phone);

    // Mengeksekusi query dan mengecek apakah berhasil
    if ($stmt->execute()) {
        header("Location: index.php"); // Redirect ke halaman utama jika berhasil
        exit;
    } else {
        echo "Error: " . $stmt->error; // Menampilkan pesan kesalahan jika gagal
    }

    // Menutup statement dan koneksi
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pengguna</title>
    <style>
        /* Mengatur gaya umum untuk body dengan warna biru lembut */
        body {
            font-family: Arial, sans-serif;
            background-color: #e8f4fa; /* Soft blue background */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Mengatur gaya container form */
        form {
            background-color: #ffffff; /* White background for form */
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 350px;
            border: 1px solid #cce0f0; /* Soft blue border */
        }

        /* Mengatur label input dan spasi antar elemen */
        form input {
            width: 100%;
            padding: 12px;
            margin: 12px 0;
            border: 1px solid #b3d4e5; /* Light blue border */
            border-radius: 5px;
            box-sizing: border-box;
            background-color: #f0faff; /* Very light blue background */
        }

        /* Mengatur gaya tombol submit */
        form button {
            width: 100%;
            padding: 12px;
            background-color: #4a90e2; /* Soft blue button */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        /* Mengatur gaya tombol submit saat di-hover */
        form button:hover {
            background-color: #357abd; /* Slightly darker blue on hover */
        }

        /* Mengatur gaya teks label */
        form label {
            font-weight: bold;
            color: #4a90e2; /* Soft blue text */
        }
    </style>
</head>
<body>
    <form method="POST" action="">
        <label for="name">Nama:</label>
        <input type="text" name="name" id="name" required>
        
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>
        
        <label for="phone">Telepon:</label>
        <input type="text" name="phone" id="phone" required>
        
        <button type="submit">Simpan</button>
    </form>
</body>
</html>
