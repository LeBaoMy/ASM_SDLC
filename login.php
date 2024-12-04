<?php
include('db.php');
include('auth.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT users.id, users.password, roles.role_name 
            FROM users 
            JOIN user_roles ON users.id = user_roles.user_id 
            JOIN roles ON user_roles.role_id = roles.id 
            WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role_name'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid credentials.";
        }
        
    } else {
        $error = "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="login.css"> <!-- Nếu có file CSS riêng -->
</head>
<body>
    <div class="header">
        Login To Your Account!
    </div>
    <div class="form-container">

        <?php if (isset($error)) echo "<p>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>