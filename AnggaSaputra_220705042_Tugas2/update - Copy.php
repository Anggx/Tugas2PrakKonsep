<?php
$conn = new mysqli("localhost", "root", "", "crud_db");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM users WHERE id=$id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $email = $row['email'];
        $phone = $row['phone'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $sql = "UPDATE users SET name='$name', email='$email', phone='$phone' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
$conn->close();
?>

<form method="POST" action="">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    Nama: <input type="text" name="name" value="<?php echo $name; ?>" required><br>
    Email: <input type="email" name="email" value="<?php echo $email; ?>" required><br>
    Telepon: <input type="text" name="phone" value="<?php echo $phone; ?>" required><br>
    <button type="submit">Update</button>
</form>