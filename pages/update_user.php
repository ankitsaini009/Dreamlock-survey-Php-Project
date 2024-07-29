<?php 
    include  '../include/header.php';
    include  '../include/navbar.php';
    include '../db2.php';
?>
<?php
    if ($role != 'Owner') {
        echo "Access denied.";
        exit();
    }
    if($_SERVER['REQUEST_METHOD'] == 'GET'){
        $user_id = $_GET['id'];
        $sql = "SELECT * FROM users WHERE id = $user_id ";
        $result = $conn->query($sql); 
        $row = $result->fetch_assoc();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
        $user_id = $_POST['user_id'];
        $username = $_POST['username'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $contact_number = $_POST['contact_number'];
        $role = $_POST['role'];

        $sql = "UPDATE users SET username = '$username, password = '$password', first_name = '$first_name', last_name = '$last_name', email = '$email, contact_number = '$contact_number, role = '$role WHERE id = $user_id";
        if ($conn->query($sql)) {
            echo "Update successful!";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $stmt->close();
    }


    $conn->close();
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title font-bold text-2xl">Update User</h4>
                    <form  action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="forms-sample">
                        <div class="flex form-group">
                            <input type="hidden" name="user_id" id="user_id" value="<?php echo $row['id'];?>" >
                            <div  class="mr-4" style="width:600px;">
                                <label for="username">User Name</label>
                                <input type="text" name="username" id="username" value="<?php echo $row['username'];?>" required class="form-control" >
                            </div>
                            <div  class="ml-4" style="width:600px;">
                                <label for="password">Password</label>
                                <input type="text" name="password" id="password" value="<?php echo $row['password'];?>" required class="form-control" readonly>
                            </div>
                        </div>
                        <div class="flex form-group">
                            <div class="mr-4" style="width:600px;">
                                <label for="first_name">First Name</label>
                                <input  type="text" name="first_name" id="first_name" value="<?php echo $row['first_name'];?>" required class="form-control" >
                            </div>
                            <div class="ml-4" style="width:600px;">
                                <label for="last_name">Last Name</label>
                                <input type="text" name="last_name" id="last_name" value="<?php echo $row['last_name'];?>" required class="form-control" >
                            </div>
                        </div>
                        <div class="flex form-group">
                            <div class="mr-4" style="width:600px;">
                                <label for="email">Email ID</label>
                                <input  type="email" name="email" id="email" value="<?php echo $row['email'];?>" required class="form-control" >
                            </div>
                            <div class="ml-4" style="width:600px;">
                                <label for="contact_number">Contact Number</label>
                                <input type="number" name="contact_number" id="contact_number" value="<?php echo $row['contact_number'];?>" required class="form-control" >
                            </div>
                        </div>
                        <div class="flex form-group">                          
                            <div class="" style="width:600px;">
                                <label for="exampleSelectGender">Role:</label>
                                <select id="role"  name="role" class="form-select" id="exampleSelectGender" value="<?php echo $row['role'];?>" required>
                                    <option>Select a Role</option>
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