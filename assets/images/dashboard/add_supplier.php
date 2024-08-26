<?php 
    include  '../include/header.php';
    include  '../include/navbar.php';
    include '../db2.php';
    ?>
    <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {        
            $supplier_name = $_POST['supplier_name'];
            $supplier_website = mysqli_real_escape_string($conn, $_POST['supplier_website']);
            $contact_number = $_POST['contact_number'];
            $email_id = $_POST['email_id'];
            $country = $_POST['country'];
            $panel_size = $_POST['panel_size'];
            $complete_url = $_POST['complete_url'];
            $terminate_url = $_POST['terminate_url'];
            $quality_term_url = $_POST['quality_term_url'];
            $survey_close_url = $_POST['survey_close_url'];
            $over_quota_url = $_POST['over_quota_url'];
            $about_supplier = $_POST['about_supplier'];
            
            // Insert project details along with filters
            $sql = "INSERT INTO Suppliers ( supplier_name, supplier_website, contact_number, email_id, country, panel_size, complete_url, terminate_url, quality_term_url, survey_close_url, over_quota_url, about_supplier) VALUES ('$supplier_name', '$supplier_website', '$contact_number', '$email_id', '$country', '$panel_size', '$complete_url', '$terminate_url', '$quality_term_url', '$survey_close_url', '$over_quota_url', '$about_supplier')";
            if(mysqli_query($conn, $sql)){
                // $_SESSION['message'] = 'Supplier was Saveed successful!';
                //     // Redirect to dashboard
                //     header('Location: list_suppliers.php');
                //     exit();
                echo "<p class='text-green-500'>Add supplier saved successfully.</p>";
                echo "<script type='text/javascript'>
                    setTimeout(function() {
                        window.location.href = 'list_suppliers.php';
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
                    <h4 class="card-title font-bold text-2xl">Add Supplier</h4>
                    <form  action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="forms-sample">
                        <div class="flex form-group">
                            <div  class="mr-4" style="width:600px;">
                                <label for="supplier_name">Supplier Name</label>
                                <input type="text" name="supplier_name" id="supplier_name" required class="form-control" >
                            </div>
                            <div class="mr-4" style="width:600px;">
                                <label for="supplier_website">Supplier Website</label>
                                <input  type="text" name="supplier_website" id="supplier_website" required class="form-control" >
                            </div>
                        </div>
                        <div class="flex form-group">
                            <div class="mr-4" style="width:600px;">
                                <label for="contact_number">Contact Number</label>
                                <input type="number" name="contact_number" id="contact_number" required class="form-control" >
                            </div>
                            <div class="ml-4" style="width:600px;">
                                <label for="email_id">Email ID</label>
                                <input  type="email" name="email_id" id="email_id" required class="form-control" >
                            </div>
                        </div>
                        <div class="flex form-group">
                        <div class="ml-4" style="width:600px;">
                                <label for="exampleSelectGender">Country:</label>
                                <select id="project_country" name="country" class="form-select" id="exampleSelectGender" required>
                                    <option value="">Select a Country</option>
                                    <option>Multi Country</option>
                                </select>
                            </div>
                            <div  class="ml-4" style="width:600px;">
                                <label for="panel_size">Panel Size</label>
                                <input type="text" name="panel_size" id="panel_size" required class="form-control" >
                            </div>
                        </div>  
                        <div class="flex form-group">                            
                            <div  class="ml-4" style="width:600px;">
                                <label for="complete_url">Complete URL</label>
                                <input type="text" name="complete_url" id="complete_url" required class="form-control" >
                            </div>
                            <div  class="ml-4" style="width:600px;">
                                <label for="terminate_url">Terminate URL</label>
                                <input type="text" name="terminate_url" id="terminate_url" required class="form-control" >
                            </div>
                        </div>  
                        <div class="flex form-group">                            
                            <div  class="ml-4" style="width:600px;">
                                <label for="quality_term_url">Quality Termination URL</label>
                                <input type="text" name="quality_term_url" id="quality_term_url" required class="form-control" >
                            </div>
                            <div  class="ml-4" style="width:600px;">
                                <label for="survey_close_url">Survey Close URL</label>
                                <input type="text" name="survey_close_url" id="survey_close_url" required class="form-control" >
                            </div>
                        </div>  
                        <div class="flex form-group">                            
                            <div  class="ml-4" style="width:600px;">
                                <label for="over_quota_url">Over Quota URL</label>
                                <input type="text" name="over_quota_url" id="over_quota_url" required class="form-control" >
                            </div>
                            <div  class="ml-4" style="width:600px;">
                                <label for="about_supplier">About Supplier</label>
                                <input type="text" name="about_supplier" id="about_supplier" required class="form-control" >
                            </div>
                        </div>  

                        <div>
                            <button type="submit" class="btn btn-gradient-primary me-2">Add Supplier</button>
                            <button class="btn btn-light">Cancel</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
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

