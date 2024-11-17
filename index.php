<?php
// Hiển thị lỗi để dễ dàng gỡ lỗi
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Thông tin kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "quang";
$password = "123456";
$database = "crud";

// Tạo kết nối với cơ sở dữ liệu
$conn = new mysqli($servername, $username, $password, $database);

// Kiểm tra kết nối cơ sở dữ liệu
if ($conn->connect_error) {
  die("Connection Error: " . $conn->connect_error);
}

// Cập nhật bản ghi
if (isset($_POST['update'])) {
  $id = $_POST['id'];
  $name = $_POST['name'];
  $email = $_POST['email'];
  $mobile = $_POST['mobile'];
  $password = $_POST['password'];

  if (empty($name) || empty($email) || empty($mobile) || empty($password)) {
    echo "All fields are required";
  } else {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Mã hóa mật khẩu
    $sql = "UPDATE `userdetails` SET `name` = '$name', `email` = '$email', `mobile` = '$mobile', `password` = '$hashed_password' WHERE `id` = $id";

    if ($conn->query($sql) === TRUE) {
      echo "Record updated successfully";
    } else {
      die("Error: " . $conn->error);
    }
  }
}

// Thêm bản ghi mới
if (isset($_POST["submit"])) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $mobile = $_POST['mobile'];
  $password = $_POST['password'];

  if (empty($name) || empty($email) || empty($mobile) || empty($password)) {
    echo "All fields are required";
  } else {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Mã hóa mật khẩu
    $sql = "INSERT INTO `userdetails` (`name`, `email`, `mobile`, `password`) VALUES ('$name', '$email', '$mobile', '$hashed_password')";

    if ($conn->query($sql) === TRUE) {
      echo "New record created successfully";
    } else {
      die("Error: " . $conn->error);
    }
  }
}

// Xóa bản ghi
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $sql = "DELETE FROM `userdetails` WHERE `id` = $id";

  if ($conn->query($sql) === TRUE) {
    echo "Record deleted successfully";
  } else {
    die("Error: " . $conn->error);
  }
}

// Kiểm tra nếu có yêu cầu chỉnh sửa (edit)
if (isset($_GET['edit'])) {
  $id = $_GET['edit'];
  $result = $conn->query("SELECT * FROM userdetails WHERE id = $id");

  if ($result->num_rows > 0) {
    $editRecord = $result->fetch_assoc(); // Lấy bản ghi để chỉnh sửa
  } else {
    echo "No record found.";
  }
}

// Đóng kết nối cơ sở dữ liệu
$conn->close();
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CRUD App using PHP MySQL</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>

  <style>
    html,
    body {
      background-color: gainsboro;
    }
  </style>

  <div class="container py-5 px-5">
    <div class="container text-center py-3">
      <h2>CRUD OPERATIONS</h2>
    </div>

    <!-- Form để nhập dữ liệu (CREATE và UPDATE) -->
    <form method="post">
      <input type="hidden" name="id" value="<?= isset($editRecord) ? $editRecord['id'] : '' ?>"> <!-- Nếu đang sửa, truyền id -->
      <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" class="form-control" name="name" id="name" value="<?= isset($editRecord) ? $editRecord['name'] : '' ?>" placeholder="Enter Your Name">
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input type="email" class="form-control" name="email" id="email" value="<?= isset($editRecord) ? $editRecord['email'] : '' ?>" placeholder="Enter Your Email address">
      </div>

      <div class="mb-3">
        <label for="mobile" class="form-label">Mobile Number</label>
        <input type="text" class="form-control" name="mobile" id="mobile" value="<?= isset($editRecord) ? $editRecord['mobile'] : '' ?>" placeholder="Enter Your Mobile Number">
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" name="password" id="password" placeholder="Enter Your Password" value="<?= isset($editRecord) ? $editRecord['password'] : '' ?>">
      </div>

      <?php if (isset($editRecord)): ?>
        <button type="submit" name="update" class="btn btn-warning">Update</button>
      <?php else: ?>
        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
      <?php endif; ?>
    </form>

    <!-- Hiển thị danh sách người dùng -->
    <h3 class="mt-5">User List</h3>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Mobile</th>
          <th>Password</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Kết nối lại để lấy dữ liệu
        $conn = new mysqli($servername, $username, $password, $database);
        $result = $conn->query("SELECT * FROM userdetails");

        if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["id"]. "</td>";
            echo "<td>" . $row["name"]. "</td>";
            echo "<td>" . $row["email"]. "</td>";
            echo "<td>" . $row["mobile"]. "</td>";
            echo "<td>" . $row["password"]. "</td>";
            echo "<td>
                    <a href='?edit=" . $row["id"] . "' class='btn btn-warning btn-sm'>Edit</a>
                    <a href='?delete=" . $row["id"] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                  </td>";
            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='6'>No records found</td></tr>";
        }
        $conn->close();
        ?>
      </tbody>
    </table>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>
