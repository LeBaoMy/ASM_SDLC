<?php
include('auth.php');
include('db.php');

if (!isAdmin() && !isTeacher()) {
    header("Location: dashboard.php");
    exit();
}


$students = $conn->query("SELECT student_id, full_name FROM students");
$classes = $conn->query("SELECT id, class_name FROM classes");
$subjects = $conn->query("SELECT id, subject_name FROM subjects");


if (isset($_POST['add_attendance'])) {
    $student_id = $_POST['student_id'];
    $class_id = $_POST['class_id'];
    $subject_id = $_POST['subject_id'];
    $attendance_date = $_POST['attendance_date'];
    $status = $_POST['status'];

    $sql = "INSERT INTO attendances (student_id, class_id, subject_id, attendance_date, status) 
            VALUES ($student_id, $class_id, $subject_id, '$attendance_date', '$status')";
    $conn->query($sql);
    header("Location: manage_attendance.php");
    exit();
}


if (isset($_POST['edit_attendance'])) {
    $attendance_id = $_POST['attendance_id'];
    $student_id = $_POST['student_id'];
    $class_id = $_POST['class_id'];
    $subject_id = $_POST['subject_id'];
    $attendance_date = $_POST['attendance_date'];
    $status = $_POST['status'];

    $sql = "UPDATE attendances 
            SET student_id = $student_id, class_id = $class_id, subject_id = $subject_id, 
                attendance_date = '$attendance_date', status = '$status' 
            WHERE id = $attendance_id";
    $conn->query($sql);
    header("Location: manage_attendance.php");
    exit();
}

if (isset($_GET['delete_id'])) {
    $attendance_id = $_GET['delete_id'];
    $sql = "DELETE FROM attendances WHERE id = $attendance_id";
    $conn->query($sql);
    header("Location: manage_attendance.php");
    exit();
}


$sql = "SELECT a.id, s.full_name AS student_name, c.class_name, sub.subject_name, a.attendance_date, a.status, 
        a.student_id, a.class_id, a.subject_id 
        FROM attendances a
        JOIN students s ON a.student_id = s.student_id
        JOIN classes c ON a.class_id = c.id
        JOIN subjects sub ON a.subject_id = sub.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Attendance</title>
    <link rel="stylesheet" href="attendance.css">
</head>
<body>
    <h1>Manage Attendance</h1>


    <div style="margin-bottom: 20px;">
        <a href="dashboard.php" class="back-button">Back to Dashboard</a>
    </div>


    <form method="POST" style="margin-bottom: 20px;">
        <h2>Add Attendance</h2>
        <label for="student_id">Student:</label>
        <select name="student_id" required>
            <?php while ($row = $students->fetch_assoc()) { ?>
                <option value="<?php echo $row['student_id']; ?>"><?php echo $row['full_name']; ?></option>
            <?php } ?>
        </select>

        <label for="class_id">Class:</label>
        <select name="class_id" required>
            <?php while ($row = $classes->fetch_assoc()) { ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['class_name']; ?></option>
            <?php } ?>
        </select>

        <label for="subject_id">Subject:</label>
        <select name="subject_id" required>
            <?php while ($row = $subjects->fetch_assoc()) { ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['subject_name']; ?></option>
            <?php } ?>
        </select>

        <label for="attendance_date">Date:</label>
        <input type="date" name="attendance_date" required>

        <label for="status">Status:</label>
        <select name="status" required>
            <option value="present">Present</option>
            <option value="absent">Absent</option>
            <option value="late">Late</option>
        </select>

        <button type="submit" name="add_attendance">Add Attendance</button>
    </form>

   
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Student</th>
                <th>Class</th>
                <th>Subject</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['student_name']; ?></td>
                    <td><?php echo $row['class_name']; ?></td>
                    <td><?php echo $row['subject_name']; ?></td>
                    <td><?php echo $row['attendance_date']; ?></td>
                    <td><?php echo ucfirst($row['status']); ?></td>
                    <td>
                   
                        <button onclick="editAttendance(
                            <?php echo $row['id']; ?>,
                            <?php echo $row['student_id']; ?>,
                            <?php echo $row['class_id']; ?>,
                            <?php echo $row['subject_id']; ?>,
                            '<?php echo $row['attendance_date']; ?>',
                            '<?php echo $row['status']; ?>'
                        )">Edit</button>

                     
                        <a href="manage_attendance.php?delete_id=<?php echo $row['id']; ?>" 
                           onclick="return confirm('Are you sure you want to delete this record?');">
                           Delete
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>


    <form id="editAttendanceForm" method="POST" style="display: none;">
        <h2>Edit Attendance</h2>
        <input type="hidden" name="attendance_id" id="edit_attendance_id">
        <label for="student_id">Student:</label>
        <select name="student_id" id="edit_student_id" required>
            <?php $students->data_seek(0); while ($row = $students->fetch_assoc()) { ?>
                <option value="<?php echo $row['student_id']; ?>"><?php echo $row['full_name']; ?></option>
            <?php } ?>
        </select>

        <label for="class_id">Class:</label>
        <select name="class_id" id="edit_class_id" required>
            <?php $classes->data_seek(0); while ($row = $classes->fetch_assoc()) { ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['class_name']; ?></option>
            <?php } ?>
        </select>

        <label for="subject_id">Subject:</label>
        <select name="subject_id" id="edit_subject_id" required>
            <?php $subjects->data_seek(0); while ($row = $subjects->fetch_assoc()) { ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['subject_name']; ?></option>
            <?php } ?>
        </select>

        <label for="attendance_date">Date:</label>
        <input type="date" name="attendance_date" id="edit_attendance_date" required>

        <label for="status">Status:</label>
        <select name="status" id="edit_status" required>
            <option value="present">Present</option>
            <option value="absent">Absent</option>
            <option value="late">Late</option>
        </select>

        <button type="submit" name="edit_attendance">Save Changes</button>
        <button type="button" onclick="closeEditForm()">Cancel</button>
    </form>

    <script>
        function editAttendance(id, studentId, classId, subjectId, date, status) {
            document.getElementById("edit_attendance_id").value = id;
            document.getElementById("edit_student_id").value = studentId;
            document.getElementById("edit_class_id").value = classId;
            document.getElementById("edit_subject_id").value = subjectId;
            document.getElementById("edit_attendance_date").value = date;
            document.getElementById("edit_status").value = status;

            document.getElementById("editAttendanceForm").style.display = "block";
        }

        function closeEditForm() {
            document.getElementById("editAttendanceForm").style.display = "none";
        }
    </script>
</body>
</html>
