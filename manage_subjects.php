<?php
include('auth.php');
include('db.php');

if (!isAdmin()) {
    header("Location: dashboard.php");
    exit();
}


if (isset($_POST['add_subject'])) {
    $subject_name = $_POST['subject_name'];

    $sql = "INSERT INTO subjects (subject_name) VALUES ('$subject_name')";
    $conn->query($sql);
    header("Location: manage_subjects.php");
    exit();
}


if (isset($_POST['edit_subject'])) {
    $subject_id = $_POST['subject_id'];
    $subject_name = $_POST['subject_name'];

    $sql = "UPDATE subjects SET subject_name = '$subject_name' WHERE id = $subject_id";
    $conn->query($sql);
    header("Location: manage_subjects.php");
    exit();
}


if (isset($_GET['delete_id'])) {
    $subject_id = $_GET['delete_id'];

    $sql = "DELETE FROM subjects WHERE id = $subject_id";
    $conn->query($sql);
    header("Location: manage_subjects.php");
    exit();
}


$sql = "SELECT * FROM subjects";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Subjects</title>
    <link rel="stylesheet" href="subject.css">
</head>
<body>
    <h1>Manage Subjects</h1>
    <a href="dashboard.php">Back to Dashboard</a>
    <!-- Add Subject Form -->
    <form method="POST" style="margin-bottom: 20px;">
        <h2>Add Subject</h2>
        <input type="text" name="subject_name" placeholder="Subject Name" required>
        <button type="submit" name="add_subject">Add Subject</button>
    </form>


    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Subject Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['subject_name']; ?></td>
                    <td>
                    
                        <button onclick="editSubject(
                            <?php echo $row['id']; ?>,
                            '<?php echo $row['subject_name']; ?>'
                        )">Edit</button>

                       
                        <a href="manage_subjects.php?delete_id=<?php echo $row['id']; ?>" 
                           onclick="return confirm('Are you sure you want to delete this subject?');">
                           Delete
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>


    <form id="editSubjectForm" method="POST" style="display: none;">
        <h2>Edit Subject</h2>
        <input type="hidden" name="subject_id" id="edit_subject_id">
        <input type="text" name="subject_name" id="edit_subject_name" placeholder="Subject Name" required>
        <button type="submit" name="edit_subject">Save Changes</button>
        <button type="button" onclick="closeEditForm()">Cancel</button>
    </form>

    <script>
        function editSubject(id, name) {
            document.getElementById('edit_subject_id').value = id;
            document.getElementById('edit_subject_name').value = name;

            document.getElementById('editSubjectForm').style.display = 'block';
        }

        function closeEditForm() {
            document.getElementById('editSubjectForm').style.display = 'none';
        }
    </script>
</body>
</html>
