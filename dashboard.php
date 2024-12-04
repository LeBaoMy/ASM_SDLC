<?php
include('auth.php');
include('db.php');

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}


$role = $_SESSION['role'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <header>
        <h1>Dashboard</h1>
        <nav>
            <ul>
                <?php if ($role === 'admin') { ?>
                    <li><a href="manage_students.php">Manage Students</a></li>
                    <li><a href="manage_teachers.php">Manage Teachers</a></li>
                    <li><a href="manage_classes.php">Manage Classes</a></li>
                    <li><a href="manage_subjects.php">Manage Subjects</a></li>
                    <li><a href="manage_attendance.php">Manage Attendance</a></li>
                <?php } elseif ($role === 'teacher') { ?>
                    <li><a href="teacher_classes.php">My Classes</a></li>
                    <li><a href="manage_attendance.php">Manage Attendance</a></li>
                <?php } elseif ($role === 'student') { ?>
                    <li><a href="student_classes.php">My Classes</a></li>
                    <li><a href="student_attendance.php">Attendance</a></li>
                <?php } ?>
                <li><a href="login.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Welcome, <?php echo $_SESSION['role']; ?>!</h2>
        <p>Select an option from the navigation to manage or view details.</p>
    </main>

    <footer>
        <p>&copy; 2024 Can Thi Tra My</p>
    </footer>
</body>
</html>
