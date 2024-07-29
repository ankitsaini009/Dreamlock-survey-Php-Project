<?php 
    include  '../include/header.php';
    include  '../include/navbar.php';
    include '../db2.php';
?>
<?php 
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $role == 'Owner') {
        $username = $_POST['username'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $contact_number = $_POST['contact_number'];
        $role = $_POST['role'];

        $sql = "INSERT INTO users (username, password, first_name, last_name, email, contact_number, role) VALUES ('$username', '$password', '$first_name', '$last_name', '$email', '$contact_number', '$role')";
        if(mysqli_query($conn, $sql)){
            echo "<script>alert('Add User successful!');</script>";
                // Redirect to dashboard
                // header('Location: list_client.php');
                // exit();
        } else{
            echo "<script>alert('ERROR: Could not execute $sql.');</script>";
        }

        // Close connection
        mysqli_close($conn);
    }
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title font-bold text-2xl">Add User</h4>
                    <form  action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="forms-sample">
                        <div class="flex form-group">
                            <div  class="mr-4" style="width:600px;">
                                <label for="username">User Name</label>
                                <input type="text" name="username" id="username" required class="form-control" >
                            </div>
                            <div  class="ml-4" style="width:600px;">
                                <label for="password">Password</label>
                                <input type="text" name="password" id="password" required class="form-control" >
                            </div>
                        </div>
                        <div class="flex form-group">
                            <div class="mr-4" style="width:600px;">
                                <label for="first_name">First Name</label>
                                <input  type="text" name="first_name" id="first_name" required class="form-control" >
                            </div>
                            <div class="ml-4" style="width:600px;">
                                <label for="last_name">Last Name</label>
                                <input type="text" name="last_name" id="last_name" required class="form-control" >
                            </div>
                        </div>
                        <div class="flex form-group">
                            <div class="mr-4" style="width:600px;">
                                <label for="email">Email ID</label>
                                <input  type="email" name="email" id="email" required class="form-control" >
                            </div>
                            <div class="ml-4" style="width:600px;">
                                <label for="contact_number">Contact Number</label>
                                <input type="number" name="contact_number" id="contact_number" required class="form-control" >
                            </div>
                        </div>
                        <div class="flex form-group">                          
                            <div class="" style="width:600px;">
                                <label for="exampleSelectGender">Role:</label>
                                <select id="role"  name="role" class="form-select" id="exampleSelectGender" required>
                                    <option value="">Select a Role</option>
                                    <option>Owner</option>
                                    <option>Manager</option>
                                    <option>User</option>
                                </select>
                            </div>
                        </div>  
                        <div class="flex justify-end mr-16">
                            <button class="btn btn-light">Cancel</button>
                            <button type="submit" class="btn btn-gradient-primary ml-2">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../include/footer.php'; ?>

