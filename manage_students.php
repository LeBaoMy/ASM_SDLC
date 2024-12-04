<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}


$sql = "SELECT s.student_id, s.full_name, s.date_of_birth, s.gender, s.address, c.class_name 
        FROM students s
        LEFT JOIN classes c ON s.class_id = c.id";
$result = $conn->query($sql);


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_student'])) {
    $full_name = $_POST['full_name'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $class_id = $_POST['class_id'];
    $user_id = $_POST['user_id']; // Thêm user_id từ form

    $stmt = $conn->prepare("INSERT INTO students (full_name, date_of_birth, gender, address, class_id, user_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssii", $full_name, $date_of_birth, $gender, $address, $class_id, $user_id);

    if ($stmt->execute()) {
        header("Location: manage_students.php");
        exit();
    } else {
        die("Error adding student: " . $stmt->error);
    }
}



if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_student'])) {
    $student_id = $_POST['student_id'];
    $full_name = $_POST['full_name'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $class_id = $_POST['class_id'];

    $stmt = $conn->prepare("UPDATE students SET full_name = ?, date_of_birth = ?, gender = ?, address = ?, class_id = ? WHERE student_id = ?");
    $stmt->bind_param("ssssii", $full_name, $date_of_birth, $gender, $address, $class_id, $student_id);

    if ($stmt->execute()) {
        header("Location: manage_students.php");
        exit();
    } else {
        die("Error updating student: " . $stmt->error);
    }
}


if (isset($_GET['delete_id'])) {
    $student_id = $_GET['delete_id'];

    $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);

    if ($stmt->execute()) {
        header("Location: manage_students.php");
        exit();
    } else {
        die("Error deleting student: " . $stmt->error);
    }
}


$student_to_edit = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];

    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student_to_edit = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Students</title>
    <link rel="stylesheet" href="student.css">
</head>
<body>
    <h1>Manage Students</h1>
    <a href="dashboard.php">Back to Dashboard</a>
    <hr>


    <form method="POST" action="">
        <?php if ($student_to_edit): ?>
            <input type="hidden" name="student_id" value="<?= $student_to_edit['student_id'] ?>">
            <h2>Edit Student</h2>
        <?php else: ?>
            <h2>Add Student</h2>
        <?php endif; ?>

        <input type="text" name="full_name" placeholder="Full Name" value="<?= htmlspecialchars($student_to_edit['full_name'] ?? '') ?>" required>
        <input type="date" name="date_of_birth" value="<?= $student_to_edit['date_of_birth'] ?? '' ?>" required>
        <select name="gender" required>
            <option value="male" <?= isset($student_to_edit) && $student_to_edit['gender'] == 'male' ? 'selected' : '' ?>>Male</option>
            <option value="female" <?= isset($student_to_edit) && $student_to_edit['gender'] == 'female' ? 'selected' : '' ?>>Female</option>
            <option value="other" <?= isset($student_to_edit) && $student_to_edit['gender'] == 'other' ? 'selected' : '' ?>>Other</option>
        </select>
        <input type="text" name="address" placeholder="Address" value="<?= htmlspecialchars($student_to_edit['address'] ?? '') ?>" required>
        <select name="class_id" required>
            <option value="">Select Class</option>
            <?php
            $classes = $conn->query("SELECT id, class_name FROM classes");
            while ($class = $classes->fetch_assoc()) {
                $selected = isset($student_to_edit) && $student_to_edit['class_id'] == $class['id'] ? 'selected' : '';
                echo "<option value='{$class['id']}' $selected>{$class['class_name']}</option>";
            }
            ?>
        </select>
        <button type="submit" name="<?= $student_to_edit ? 'edit_student' : 'add_student' ?>">
            <?= $student_to_edit ? 'Save Changes' : 'Add Student' ?>
        </button>
    </form>

    <hr>

    
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Date of Birth</th>
                <th>Gender</th>
                <th>Address</th>
                <th>Class</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['student_id'] ?></td>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= $row['date_of_birth'] ?></td>
                <td><?= ucfirst($row['gender']) ?></td>
                <td><?= htmlspecialchars($row['address']) ?></td>
                <td><?= $row['class_name'] ?></td>
                <td>
                    <a href="manage_students.php?edit_id=<?= $row['student_id'] ?>">Edit</a>
                    <a href="manage_students.php?delete_id=<?= $row['student_id'] ?>" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
