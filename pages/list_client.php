<?php 
    include  '../include/header.php';
    include  '../include/navbar.php';
    include '../db2.php';
?>

<style>
        .table_wrapper{display: block;max-height: 80vh; overflow-x: auto;overflow-y: auto;white-space: nowrap;}
</style>

<div class="content-wrapper">
    <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
            <h4 class="card-title font-bold text-2xl">Clients List</h4>
            <?php
                $sql = "SELECT * FROM clientele";
                $result = $conn->query($sql); 
            ?>
            <table class="table table-hover table_wrapper">
                      <thead>
                        <tr class="sticky top-0">
                            <th class="text-wrap table-feild" >Client ID</th>                  
                            <th class="text-wrap table-feild" >Client Name</th>
                            <th class="text-wrap table-feild" >Contact Person</th>
                            <th class="text-wrap table-feild" >Contact Number</th>
                            <th class="text-wrap table-feild" >Email</th>
                            <th class="text-wrap table-feild" >Website</th>
                            <th class="text-wrap table-feild" >Country</th>                                           
                            <th class="text-wrap table-feild" >Client Code</th>                                           
                        </tr>
                      </thead>
                      <tbody>
                            <?php
                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {                            
                                            echo '<tr>';
                                            echo '<td class="text-wrap table-feild">' . htmlspecialchars($row['client_id']) . '</td>';
                                            echo '<td class="text-wrap table-feild">' . htmlspecialchars($row['Client_Name']) . '</td>';
                                            echo '<td class="text-wrap table-feild">' . htmlspecialchars($row['Contact_Person']) . '</td>';
                                            echo '<td class="text-wrap table-feild">' . htmlspecialchars($row['Contact_Number']) . '</td>';
                                            echo '<td class="text-wrap table-feild">' . htmlspecialchars($row['Email']) . '</td>';
                                            echo '<td class="text-wrap table-feild">' . htmlspecialchars($row['Website']) . '</td>';
                                            echo '<td class="text-wrap table-feild">' . htmlspecialchars($row['Country']) . '</td>';
                                            echo '<td class="text-wrap table-feild">' . htmlspecialchars($row['client_code']) . '</td>';
                                            echo '</tr>';                                                             
                                        }
                                }else{
                                echo "<p class='text-center text-red-500'>No results found.</p>";
                                }
                                $conn->close();
                            ?>
                      </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
<?php include '../include/footer.php'; ?>

