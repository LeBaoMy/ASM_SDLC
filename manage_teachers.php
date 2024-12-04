<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}


$sql = "SELECT teacher_id, full_name, date_of_birth, gender, address, email, phone 
        FROM teachers";
$result = $conn->query($sql);


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_teacher'])) {
    $full_name = $_POST['full_name'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("INSERT INTO teachers (full_name, date_of_birth, gender, address, email, phone) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $full_name, $date_of_birth, $gender, $address, $email, $phone);

    if ($stmt->execute()) {
        header("Location: manage_teachers.php");
        exit();
    } else {
        die("Error adding teacher: " . $stmt->error);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_teacher'])) {
    $teacher_id = $_POST['teacher_id'];
    $full_name = $_POST['full_name'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("UPDATE teachers SET full_name = ?, date_of_birth = ?, gender = ?, address = ?, email = ?, phone = ? WHERE teacher_id = ?");
    $stmt->bind_param("ssssssi", $full_name, $date_of_birth, $gender, $address, $email, $phone, $teacher_id);

    if ($stmt->execute()) {
        header("Location: manage_teachers.php");
        exit();
    } else {
        die("Error updating teacher: " . $stmt->error);
    }
}


if (isset($_GET['delete_id'])) {
    $teacher_id = $_GET['delete_id'];

    $stmt = $conn->prepare("DELETE FROM teachers WHERE teacher_id = ?");
    $stmt->bind_param("i", $teacher_id);

    if ($stmt->execute()) {
        header("Location: manage_teachers.php");
        exit();
    } else {
        die("Error deleting teacher: " . $stmt->error);
    }
}


$teacher_to_edit = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];

    $stmt = $conn->prepare("SELECT * FROM teachers WHERE teacher_id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $teacher_to_edit = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Teachers</title>
    <link rel="stylesheet" href="teacher.css">
</head>
<body>
    <h1>Manage Teachers</h1>
    <a href="dashboard.php">Back to Dashboard</a>
    <hr>

 
    <form method="POST" action="">
        <?php if ($teacher_to_edit): ?>
            <input type="hidden" name="teacher_id" value="<?= $teacher_to_edit['teacher_id'] ?>">
            <h2>Edit Teacher</h2>
        <?php else: ?>
            <h2>Add Teacher</h2>
        <?php endif; ?>

        <input type="text" name="full_name" placeholder="Full Name" value="<?= htmlspecialchars($teacher_to_edit['full_name'] ?? '') ?>" required>
        <input type="date" name="date_of_birth" value="<?= $teacher_to_edit['date_of_birth'] ?? '' ?>" required>
        <select name="gender" required>
            <option value="male" <?= isset($teacher_to_edit) && $teacher_to_edit['gender'] == 'male' ? 'selected' : '' ?>>Male</option>
            <option value="female" <?= isset($teacher_to_edit) && $teacher_to_edit['gender'] == 'female' ? 'selected' : '' ?>>Female</option>
            <option value="other" <?= isset($teacher_to_edit) && $teacher_to_edit['gender'] == 'other' ? 'selected' : '' ?>>Other</option>
        </select>
        <input type="text" name="address" placeholder="Address" value="<?= htmlspecialchars($teacher_to_edit['address'] ?? '') ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($teacher_to_edit['email'] ?? '') ?>" required>
        <input type="text" name="phone" placeholder="Phone" value="<?= htmlspecialchars($teacher_to_edit['phone'] ?? '') ?>" required>
        <button type="submit" name="<?= $teacher_to_edit ? 'edit_teacher' : 'add_teacher' ?>">
            <?= $teacher_to_edit ? 'Save Changes' : 'Add Teacher' ?>
        </button>
    </form>

    <hr>

    <!-- Bảng danh sách giáo viên -->
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Date of Birth</th>
                <th>Gender</th>
                <th>Address</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['teacher_id'] ?></td>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= $row['date_of_birth'] ?></td>
                <td><?= ucfirst($row['gender']) ?></td>
                <td><?= htmlspecialchars($row['address']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td>
                    <a href="manage_teachers.php?edit_id=<?= $row['teacher_id'] ?>">Edit</a>
                    <a href="manage_teachers.php?delete_id=<?= $row['teacher_id'] ?>" onclick="return confirm('Are you sure you want to delete this teacher?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
