<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fce4ec; 
        }
        header {
            background-color: #ec407a; 
            color: white;
            text-align: center;
            padding: 15px 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .section {
            background-color: white;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #ad1457; 
        }
        .btn {
            background-color: #ec407a; 
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 10px;
        }
        .btn:hover {
            background-color: #d81b60; 
        }
        p, li {
            color: #6a1b9a; 
        }
    </style>
</head>
<body>

    <header>
        <h1>Welcome to Your Student Dashboard</h1>
    </header>

    <div class="container">

        <div class="section">
            <h2>Your Information</h2>
            <p><strong>Name:</strong> John Doe</p>
            <p><strong>Student ID:</strong> 123456789</p>
            <p><strong>Class:</strong> Computer Science</p>
            <p><strong>Enrollment Date:</strong> 2020-08-15</p>
        </div>

        <div class="section">
            <h2>Attendance</h2>
            <p>Here you can check your attendance record for each subject.</p>
            <a href="attendance.php" class="btn">View Attendance</a>
        </div>


        <div class="section">
            <h2>Your Schedule</h2>
            <p>Check your class schedule for the week below:</p>
            <ul>
                <li><strong>Monday:</strong> Math - 9:00 AM</li>
                <li><strong>Tuesday:</strong> Physics - 10:00 AM</li>
                <li><strong>Wednesday:</strong> Programming - 1:00 PM</li>
                <li><strong>Thursday:</strong> Chemistry - 9:00 AM</li>
                <li><strong>Friday:</strong> Data Structures - 11:00 AM</li>
            </ul>
        </div>

        <div class="section">
            <h2>Recent Notifications</h2>
            <ul>
                <li>Assignment due for Programming class - Submit by 2024-12-10</li>
                <li>Midterm exam schedule released for Math - Exam on 2024-12-15</li>
                <li>Reminder: Don't forget to attend your Chemistry class tomorrow at 9:00 AM</li>
            </ul>
        </div>

        <div class="section">
            <a href="login.php" class="btn">Logout</a>
        </div>
    </div>

</body>
</html>
