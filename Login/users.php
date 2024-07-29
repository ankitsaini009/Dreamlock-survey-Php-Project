<?php
include 'include/header.php';

$hostname = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'pmtool_db';
    $port = 3306;

$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle request for deleting a user
if (isset($_GET['delete_user']) && $role == 'Owner') {
    $user_id = $_GET['delete_user'];

    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('User Deleted Successfully!');</script>";
        header("Location: users.php"); // Redirect to user management page after successful deletion
        exit(); // Ensure no further code is executed after the redirect
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

// Fetch users based on role
$sql = "SELECT * FROM users";
if ($role == 'Manager') {
    $sql .= " WHERE created_by = " . $_SESSION['user_id'];
}
$result = $conn->query($sql);

?>


<!-- <?php //include 'Login/header.php'; ?> -->

<?php include '../include/dashboard.php'; ?>
    <div class="container"><break>
         <div class="row">
            <div class="col-md-10">
            <h1>User Management</h1>
            </div>
            <div class="col-md-2">
             <button class="btn edit-btn"><a href="register.php">Add User</a></button>
            </div>
           
        </div>
        <break>
        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Contact Number</th>
                <th>Role</th>
                <?php if ($role == 'Owner'): ?>
                    <th>Actions</th>
                <?php endif; ?>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['first_name']; ?></td>
                    <td><?php echo $row['last_name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['contact_number']; ?></td>
                    <td><?php echo $row['role']; ?></td>
                    <?php if ($role == 'Owner'): ?>
                        <td><button class="btn edit-btn">
                            <a href="update_user.php?id=<?php echo $row['id']; ?>">Update</a></button>
                            <button class="btn delete-btn">
                            <a href="users.php?delete_user=<?php echo $row['id']; ?>">Delete</a></button>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>
        </table>
      
    </div>
<?php include '../include/footer.php'; ?>