<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];

    $role = $_POST['role']; // 'admin', 'teacher', 'student'

    $sql = "INSERT INTO users (username, password, fullname, email) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $password, $fullname, $email);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;

       
        $role_sql = "INSERT INTO user_roles (user_id, role_id) VALUES (?, (SELECT id FROM roles WHERE role_name = ?))";
        $role_stmt = $conn->prepare($role_sql);
        $role_stmt->bind_param("is", $user_id, $role);
        $role_stmt->execute();

        echo "Registration successful!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
<form method="POST" class="Register">
    <h1>Admin Registration Page </h1>
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="text" name="fullname" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email">
    <select name="role" required>
        <option value="admin">Admin</option>
        <option value="teacher">Teacher</option>
        <option value="student">Student</option>
    </select>
    <button type="submit">Register</button>
    <input type="button" value="Login" onclick="location.href='login.php'">

</form>
</body>
</html>

