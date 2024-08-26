<?php 
    include  '../include/header.php';
    include  '../include/navbar.php';
    include '../db2.php';
?>
<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {        
        $client_name = $_POST['client_name'];
        $contact_person = $_POST['contact_person'];
        $contact_number = $_POST['contact_number'];
        $client_email = $_POST['client_email'];
        $website_url = mysqli_real_escape_string($conn, $_POST['website_url']);
        $country = $_POST['country'];
        
        // Insert project details along with filters
        $sql = "INSERT INTO clientele (Client_Name,Contact_Person,Contact_Number,Email,Website,Country) VALUES ('$client_name','$contact_person','$contact_number','$client_email','$website_url','$country' )";
        if(mysqli_query($conn, $sql)){
           // echo "<script>alert('Add Client save was successful!');</script>";
            // $_SESSION['message'] = 'Add project save was successful!';
                // Redirect to dashboard
                // header('Location: list_client.php');
                // exit();
                echo "<p class='text-green-500'>Add client saved successfully.</p>";
                echo "<script type='text/javascript'>
                    setTimeout(function() {
                        window.location.href = 'list_client.php';
                    }, 3000); // Redirect after 1 second
                  </script>";
                exit;
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
                    <h4 class="card-title font-bold text-2xl">Add Client</h4>
                    <form  action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="forms-sample">
                        <div class="flex form-group">
                            <div  class="mr-4" style="width:600px;">
                                <label for="client_name">Client Name</label>
                                <input type="text" name="client_name" id="client_name" required class="form-control" >
                            </div>
                            <div  class="ml-4" style="width:600px;">
                                <label for="contact_person">Contact Person</label>
                                <input type="text" name="contact_person" id="contact_person" required class="form-control" >
                            </div>
                        </div>
                        <div class="flex form-group">
                            <div class="mr-4" style="width:600px;">
                                <label for="contact_number">Contact Number</label>
                                <input type="number" name="contact_number" id="contact_number" required class="form-control" >
                            </div>
                            <div class="ml-4" style="width:600px;">
                                <label for="client_email">Email ID</label>
                                <input  type="email" name="client_email" id="client_email" required class="form-control" >
                            </div>
                        </div>
                        <div class="flex form-group">
                            <div class="mr-4" style="width:600px;">
                                <label for="website_url">Website URL</label>
                                <input  type="text" name="website_url" id="website_url" required class="form-control" >
                            </div>
                            <div class="ml-4" style="width:600px;">
                                <label for="exampleSelectGender">Country:</label>
                                <select id="project_country" name="country" class="form-select" id="exampleSelectGender" required>
                                    <option value="">Select a Country</option>
                                    <option>Multi Country</option>
                                </select>
                            </div>
                        </div>  
                        <div>
                            <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                            <button class="btn btn-light">Cancel</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>  


      
<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {        
        $client_name = $_POST['client_name'];
        $contact_person = $_POST['contact_person'];
        $contact_number = $_POST['contact_number'];
        $client_email = $_POST['client_email'];
        $website_url = mysqli_real_escape_string($conn, $_POST['website_url']);
        $country = $_POST['country'];
        
        // Insert project details along with filters
        $sql = "INSERT INTO clientele (Client_Name,Contact_Person,Contact_Number,Email,Website,Country) VALUES ('$client_name','$contact_person','$contact_number','$client_email','$website_url','$country' )";
        if(mysqli_query($conn, $sql)){
            $_SESSION['message'] = 'Add project save was successful!';
                // Redirect to dashboard
                //header('Location: dashboard.php');
                exit();
        } else{
            echo "<script>alert('ERROR: Could not execute $sql.');</script>";
        }

        // Close connection
        mysqli_close($conn);

    }

?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
        $(document).ready(function() {
            $.getJSON('https://restcountries.com/v3.1/all', function(data) {
                // Sort the countries alphabetically by name
                data.sort((a, b) => {
                    let nameA = a.name.common.toUpperCase(); // ignore upper and lowercase
                    let nameB = b.name.common.toUpperCase(); // ignore upper and lowercase
                    if (nameA < nameB) {
                        return -1;
                    }
                    if (nameA > nameB) {
                        return 1;
                    }
                    return 0;
                });

                // Create the options
                var options = '';
                data.forEach(function(country) {
                    options += `<option value="${country.cca2}">${country.name.common}</option>`;
                });

                // Append the options to the dropdown
                $('#project_country').append(options);
            });
        });
    </script>


<?php include '../include/footer.php'; ?>



