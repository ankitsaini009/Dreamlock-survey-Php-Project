<?php
include 'header.php';

// $hostname = 'localhost';
//     $username = 'pmtool_db';
//     $password = 'Admin@123[];';
//     $database = 'pmtool_db';
//     $port = 3306;

// $conn = new mysqli($hostname, $username, $password, $database);

// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

$hostname = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'pmtool_db';
    $port = 3306;

$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Handle form submission for creating a new user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $role == 'Owner') {
    $username = $_POST['username'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $contact_number = $_POST['contact_number'];
    $role = $_POST['role'];

    $sql = "INSERT INTO users (username, password, first_name, last_name, email, contact_number, role) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $username, $password, $first_name, $last_name, $email, $contact_number, $role);

    if ($stmt->execute()) {
        
        echo "<script>alert('Registration successful!');</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

// Handle form submission for updating a user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user']) && $role == 'Owner') {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $contact_number = $_POST['contact_number'];
    $role = $_POST['role'];

    $sql = "UPDATE users SET username = ?, password = ?, first_name = ?, last_name = ?, email = ?, contact_number = ?, role = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $username, $password, $first_name, $last_name, $email, $contact_number, $role, $user_id);

    if ($stmt->execute()) {
        echo "Update successful!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

// Handle request for deleting a user
if (isset($_GET['delete_user']) && $role == 'Owner') {
    $user_id = $_GET['delete_user'];

    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo "User deleted successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

?>
<!-- 
<?php //include '../include/dashboard.php'; ?> -->
   
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: left;
        }
        h1 {
            color: #0073e6;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #0073e6;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #005bb5;
        }
    </style>
</head>
<body>
    <div class="container">
        <break>
        <h1><?php echo ($role == 'Owner') ? 'Register' : 'Welcome, Team Member!'; ?></h1>
        <?php if ($role == 'Owner'): ?>
            <form action="register.php" method="post">
                <input type="text" name="username" placeholder="User Name" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="contact_number" placeholder="Contact Number" required>
                <select name="role" required>
                    <option value="" disabled selected>-- Select Role --</option>
                    <option value="User">User</option>
                    <option value="Manager">Manager</option>
                    <option value="Owner">Owner</option>
                </select>
                <button type="submit">Register</button>
            </form>
        <?php else: ?>
            <p>Hope to see you in the future again.</p>
            <a href="logout.php">Logout</a>
        <?php endif; ?>
    </div>
</body>
</html>
