<?php
include 'db.php';
session_start();


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}


$sql = "SELECT c.id AS class_id, c.class_name, c.start_date, c.end_date, s.subject_name
        FROM classes c
        LEFT JOIN class_subjects cs ON c.id = cs.class_id
        LEFT JOIN subjects s ON cs.subject_id = s.id";
$result = $conn->query($sql);

$class_students_sql = "SELECT s.student_id, s.full_name, s.date_of_birth, s.class_id, c.class_name
                        FROM students s
                        JOIN classes c ON s.class_id = c.id";
$class_students_result = $conn->query($class_students_sql);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_class'])) {
    $class_name = $_POST['class_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $stmt = $conn->prepare("INSERT INTO classes (class_name, start_date, end_date) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $class_name, $start_date, $end_date);

    if ($stmt->execute()) {
        header("Location: manage_classes.php");
        exit();
    } else {
        die("Error adding class: " . $stmt->error);
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_class'])) {
    $class_id = $_POST['class_id'];
    $class_name = $_POST['class_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $stmt = $conn->prepare("UPDATE classes SET class_name = ?, start_date = ?, end_date = ? WHERE id = ?");
    $stmt->bind_param("sssi", $class_name, $start_date, $end_date, $class_id);

    if ($stmt->execute()) {
        header("Location: manage_classes.php");
        exit();
    } else {
        die("Error updating class: " . $stmt->error);
    }
}


if (isset($_GET['delete_id'])) {
    $class_id = $_GET['delete_id'];

    $stmt = $conn->prepare("DELETE FROM classes WHERE id = ?");
    $stmt->bind_param("i", $class_id);

    if ($stmt->execute()) {
        header("Location: manage_classes.php");
        exit();
    } else {
        die("Error deleting class: " . $stmt->error);
    }
}

$class_to_edit = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];

    $stmt = $conn->prepare("SELECT * FROM classes WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $class_to_edit = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Classes</title>
    <link rel="stylesheet" href="class.css">
</head>
<body>
    <h1>Manage Classes</h1>
    <a href="dashboard.php">Back to Dashboard</a>
    <hr>


    <form method="POST" action="">
        <?php if ($class_to_edit): ?>
            <input type="hidden" name="class_id" value="<?= $class_to_edit['id'] ?>">
            <h2>Edit Class</h2>
        <?php else: ?>
            <h2>Add Class</h2>
        <?php endif; ?>

        <input type="text" name="class_name" placeholder="Class Name" value="<?= htmlspecialchars($class_to_edit['class_name'] ?? '') ?>" required>
        <input type="date" name="start_date" value="<?= $class_to_edit['start_date'] ?? '' ?>" required>
        <input type="date" name="end_date" value="<?= $class_to_edit['end_date'] ?? '' ?>" required>
        <button type="submit" name="<?= $class_to_edit ? 'edit_class' : 'add_class' ?>">
            <?= $class_to_edit ? 'Save Changes' : 'Add Class' ?>
        </button>
    </form>

    <hr>


    <table border="1">
        <thead>
            <tr>
                <th>Class ID</th>
                <th>Class Name</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Subjects</th>
                <th>Students</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $classes = [];
            while ($row = $result->fetch_assoc()) {
                $classes[$row['class_id']]['class_name'] = $row['class_name'];
                $classes[$row['class_id']]['start_date'] = $row['start_date'];
                $classes[$row['class_id']]['end_date'] = $row['end_date'];
                $classes[$row['class_id']]['subjects'][] = $row['subject_name'];
            }

      
            $students_in_class = [];
            while ($row = $class_students_result->fetch_assoc()) {
                $students_in_class[$row['class_id']][] = [
                    'student_id' => $row['student_id'],
                    'full_name' => $row['full_name'],
                    'dob' => $row['date_of_birth']
                ];
            }

       
            foreach ($classes as $class_id => $class_data):
            ?>
            <tr>
                <td><?= $class_id ?></td>
                <td><?= htmlspecialchars($class_data['class_name']) ?></td>
                <td><?= $class_data['start_date'] ?></td>
                <td><?= $class_data['end_date'] ?></td>
                <td>
                    <?= implode(', ', $class_data['subjects']) ?: 'No subjects assigned' ?>
                </td>
                <td>
                    <?php
                    if (isset($students_in_class[$class_id])) {
                        echo "<table class='student-table'>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>DOB</th>
                            </tr>
                        </thead>
                        <tbody>";
            
  
                foreach ($students_in_class[$class_id] as $student) {
                    echo "<tr>
                            <td>{$student['student_id']}</td>
                            <td>{$student['full_name']}</td>
                            <td>{$student['dob']}</td>
                        </tr>";
                }
            
                echo "</tbody></table>"; // Đóng bảng
            } else {
                echo "<p>No students found for this class.</p>"; // Nếu không có học sinh
            }
                    ?>
                </td>
                <td>
                    <a href="manage_classes.php?edit_id=<?= $class_id ?>">Edit</a>
                    <a href="manage_classes.php?delete_id=<?= $class_id ?>" onclick="return confirm('Are you sure you want to delete this class?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
