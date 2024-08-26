<?php 
    include  '../include/header.php';
    include  '../include/navbar.php';
    include '../db2.php';
?>
<?php
    // Handle request for deleting a user
    if (isset($_GET['delete_user']) && $role == 'Owner') {
        $user_id = $_GET['delete_user'];
        $sql = "DELETE FROM users WHERE id = $user_id";
        $result = mysqli_query($conn, $sql);

        if ($result === true) {
            echo '<script>alert("User deleted successfully!")</script>';
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
?>

<style>
        .table_wrapper{display:block ;max-height: 80vh;min-width: none;; overflow-x: auto;overflow-y: auto;white-space: nowrap;}
</style>

<div class="content-wrapper">
    <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
          <div class="card-body">
            <h4 class="card-title font-bold text-2xl">User List</h4>
            <?php
                $sql = "SELECT * FROM users";
                $result = $conn->query($sql); 
            ?>
            <table class="table table-hover table_wrapper">
                      <thead>
                        <tr class="sticky top-0">
                            <th class="text-wrap table-feild">ID</th>
                            <th class="text-wrap table-feild">Username</th>
                            <th class="text-wrap table-feild">First Name</th>
                            <th class="text-wrap table-feild">Last Name</th>
                            <th class="text-wrap table-feild">Email</th>
                            <th class="text-wrap table-feild">Contact Number</th>
                            <th class="text-wrap table-feild">Role</th>
                            <?php if ($role == 'Owner'): ?>
                                <th class="table-feild ">Actions</th>
                            <?php endif; ?>                                           
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="text-wrap table-feild"><?php echo $row['id']; ?></td>
                                <td class="text-wrap table-feild"><?php echo $row['username']; ?></td>
                                <td class="text-wrap table-feild"><?php echo $row['first_name']; ?></td>
                                <td class="text-wrap table-feild"><?php echo $row['last_name']; ?></td>
                                <td class="text-wrap table-feild"><?php echo $row['email']; ?></td>
                                <td class="text-wrap table-feild"><?php echo $row['contact_number']; ?></td>
                                <td class="text-wrap table-feild"><?php echo $row['role']; ?></td>
                                <?php if ($role == 'Owner'): ?>
                                    <td class="text-wrap table-feild">
                                        <button class="btn btn-md btn-light">
                                            <a href="update_user.php?id=<?php echo $row['id']; ?>" class="text-black">Update</a>
                                        </button>
                                        <button class="btn btn-md btn-light">
                                            <a href="list_user.php?delete_user=<?php echo $row['id']; ?>" class="text-black">Delete</a>
                                        </button>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                      </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php include '../include/footer.php'; ?>

