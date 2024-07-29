<?php
// function openConnection() {
//     $hostname = 'localhost';
//     $username = 'pmtool_db';
//     $password = 'Admin@123[];';
//     $database = 'pmtool_db';
//     $port = 3306;

//     $conn = new mysqli($hostname, $username, $password, $database, $port);
//     if ($conn->connect_error) {
//         error_log("Connection failed: " . $conn->connect_error);
//         die("Connection failed: " . $conn->connect_error);
//     }
//     return $conn;
// }

// function closeConnection($conn) {
//     $conn->close();
// }

// function generateHashIdentifier() {
//     return bin2hex(random_bytes(16));
// }

// function logSurveyStart($project_code, $supplier_identifier, $hash_identifier) {
//     $conn = openConnection();
//     $sql = "INSERT INTO SurveyLog (project_code, status, time_stamp, Hash_Identifier, Supplier_Identifier) VALUES (?, 'start', NOW(), ?, ?)";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("sss", $project_code, $hash_identifier, $supplier_identifier);
//     $stmt->execute();
//     $stmt->close();
//     closeConnection($conn);
// }

// if (isset($_GET['project_code']) && isset($_GET['supplier_identifier'])) {
//     $project_code = $_GET['project_code'];
//     $supplier_identifier = $_GET['supplier_identifier'];
//     $hash_identifier = generateHashIdentifier();

//     // Log the survey start with the unique hash identifier
//     logSurveyStart($project_code, $supplier_identifier, $hash_identifier);

//     $conn = openConnection();
//     $sql = "SELECT links FROM ProjectDetails WHERE project_code = ?";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("s", $project_code);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     if ($result->num_rows > 0) {
//         $project = $result->fetch_assoc();
//         $stmt->close();
//         closeConnection($conn);

//         // Decode links and get the client's survey URL
//         $links = json_decode($project['links'], true);
//         if (isset($links[0]['live'])) {
//             $client_survey_url = $links[0]['live'];

//             // Replace [identifier] with the unique hash identifier
//             $client_survey_url = str_replace('[identifier]', $hash_identifier, $client_survey_url);

//             // Redirect to the client's survey URL
//             header("Location: $client_survey_url");
//             exit();
//         } else {
//             die("Error: 'live' link not found in project details.");
//         }
//     } else {
//         $stmt->close();
//         closeConnection($conn);
//         die("Error: Project code not found.");
//     }
// } else {
//     die("Invalid request.");
// }
?>
<?php
// start_survey.php

// // Check if project_code and supplier_identifier are set and valid
// if(isset($_GET['project_code']) && isset($_GET['supplier_identifier'])) {
//     $project_code = $_GET['project_code'];
//     $supplier_identifier = $_GET['supplier_identifier'];

//     // Generate the HTML form based on the parameters
//     $formHTML = '<form id="surveyForm" method="post" action="process_survey.php">
//                     <!-- Your form fields here -->
//                     <input type="text" name="name" placeholder="Your Name" required>
//                     <input type="email" name="email" placeholder="Your Email" required>
//                     <textarea name="message" placeholder="Your Message"></textarea>
//                     <button type="submit">Submit</button>
//                 </form>';

//     echo $formHTML;
// } else {
//     // Error handling if parameters are missing
//     echo 'Error: Parameters project_code and supplier_identifier are required.';
// }
// 
//<a href="#" id="openSurveyLink">Open Survey Form</a>
// <div id="surveyFormContainer"></div>

// <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
// <script>
// $(document).ready(function() {
//     $('#openSurveyLink').click(function(e) {
//         e.preventDefault(); // Prevent the default action of the link
        
//         // Parameters for the survey form
//         var project_code = 'DLM06026';
//         var supplier_identifier = 'your_supplier_identifier'; // Replace with actual identifier

//         // AJAX request to fetch form content
//         $.ajax({
//             url: 'http://localhost/app.dreamlock/pages/start_survey.php',
//             method: 'GET',
//             data: {
//                 project_code: project_code,
//                 supplier_identifier: supplier_identifier
//             },
//             success: function(response) {
//                 $('#surveyFormContainer').html(response); // Display form in container
//             },
//             error: function(xhr, status, error) {
//                 console.error('Error fetching form:', error);
//             }
//         });
//     });
// });
// </script>?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Satisfaction Survey form Wizard by Ansonika.">
    <meta name="author" content="Ansonika">
    <title>Satisfyc | Satisfaction Survey form Wizard</title>

    <!-- Favicons-->
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" type="image/x-icon" href="img/apple-touch-icon-57x57-precomposed.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="72x72" href="img/apple-touch-icon-72x72-precomposed.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="114x114" href="img/apple-touch-icon-114x114-precomposed.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="144x144" href="img/apple-touch-icon-144x144-precomposed.png">

    <!-- GOOGLE WEB FONT -->
    <link href="https://fonts.googleapis.com/css?family=Caveat|Poppins:300,400,500,600,700&display=swap" rel="stylesheet">

    <!-- BASE CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
	<link href="css/vendors.css" rel="stylesheet">

    <!-- YOUR CUSTOM CSS -->
    <link href="css/custom.css" rel="stylesheet">

</head>

<body class="style_2">
	
	<div id="preloader">
		<div data-loader="circle-side"></div>
	</div><!-- /Preload -->
	
	<div id="loader_form">
		<div data-loader="circle-side-2"></div>
	</div><!-- /loader_form -->

	<header>
	    <div class="container-fluid">
	        <div class="row">
	            <div class="col-5">
	                <a href="index.html"><img src="img/logo.svg" alt="" width="50" height="55"></a>
	            </div>
	            <div class="col-7">
	                <div id="social">
	                    <ul>
	                        <li><a href="#0"><i class="social_facebook"></i></a></li>
	                        <li><a href="#0"><i class="social_twitter"></i></a></li>
	                        <li><a href="#0"><i class="social_instagram"></i></a></li>
	                        <li><a href="#0"><i class="social_linkedin"></i></a></li>
	                    </ul>
	                </div>
	            </div>
	        </div>
	        <!-- /row -->
	    </div>
	    <!-- /container -->
	</header>
	<!-- /header -->

	<div class="wrapper_centering">
	    <div class="container_centering">
	        <div class="container">
	            <div class="row justify-content-between">
	                <div class="col-xl-6 col-lg-6 d-flex align-items-center">
	                    <div class="main_title_1">
	                        <h3><img src="img/main_icon_1.svg" width="80" height="80" alt=""> Survey</h3>
	                        <p>An mei sadipscing dissentiet, eos ea partem viderer facilisi. Brute nostrud democritum in vis, nam ei erat zril mediocrem. No postea diceret vix.</p>
	                        <p><em>- The Satisfyc Team</em></p>
	                    </div>
	                </div>
	                <!-- /col -->
	                <div class="col-xl-5 col-lg-5">
	                    <div id="wizard_container">
	                        <div id="top-wizard">
	                            <div id="progressbar"></div>
	                        </div>
	                        <!-- /top-wizard -->
	                        <form id="wrapped" method="POST" autocomplete="off">
	                            <input id="website" name="website" type="text" value="">
	                            <!-- Leave for security protection, read docs for details -->
	                            <div id="middle-wizard">

	                                <div class="step">
	                                    <h3 class="main_question"><strong>1 of 5</strong>How was the service provided?</h3>
	                                    <div class="review_block_smiles">
	                                    	<ul class="clearfix">
	                                    		<li>
	                                    			 <div class="container_smile">
	                                                    <input type="radio" id="very_bad_1" name="question_1" class="required" value="Very bad<" onchange="getVals(this, 'question_1');">
	                                                    <label class="radio smile_1" for="very_bad_1"><span>Very bad</span></label>
	                                                </div>
	                                    		</li>
	                                    		<li>
	                                    		 <div class="container_smile">
	                                                    <input type="radio" id="bad_1" name="question_1" class="required" value="Bad" onchange="getVals(this, 'question_1');">
	                                                    <label class="radio smile_2" for="bad_1"><span>Bad</span></label>
	                                                </div>
	                                    		</li>
	                                    		<li>
	                                                <div class="container_smile">
	                                                    <input type="radio" id="average_1" name="question_1" class="required" value="Average" onchange="getVals(this, 'question_1');">
	                                                    <label class="radio smile_3" for="average_1"><span>Average</span></label>
	                                                </div>
	                                            </li>
	                                            <li>
	                                                <div class="container_smile">
	                                                    <input type="radio" id="good_1" name="question_1" class="required" value="Good" onchange="getVals(this, 'question_1');">
	                                                    <label class="radio smile_4" for="good_1"><span>Good</span></label>
	                                                </div>
	                                            </li>
	                                            <li>
	                                                <div class="container_smile">
	                                                    <input type="radio" id="very_good_1" name="question_1" class="required" value="Very Good" onchange="getVals(this, 'question_1');">
	                                                    <label class="radio smile_5" for="very_good_1"><span>Very Good</span></label>
	                                                </div>
	                                            </li>
	                                    	</ul>
	                                    	<div class="row justify-content-between add_bottom_25">
	                                    		<div class="col-4">
	                                    			<em>Very Bad</em>
	                                    		</div>
	                                    		<div class="col-4 text-end">
	                                    			<em>Excellent</em>
	                                    		</div>
	                                    	</div>
	                                    </div>
	                                    <div class="form-group">
	                                        <label for="additional_message_label">Your Review</label>
	                                        <textarea name="additional_message" id="additional_message_label" class="form-control" style="height:180px;" onkeyup="getVals(this, 'additional_message');"></textarea>
	                                    </div>
	                                </div>
	                                <!-- /step 1-->

	                                <div class="step">
	                                    <h3 class="main_question mb-4"><strong>2 of 5</strong>Would you reccomend our company?</h3>
	                                    <div class="review_block">
	                                        <ul>
	                                            <li>
	                                                <div class="checkbox_radio_container">
	                                                    <input type="radio" id="no" name="question_2" class="required" value="No" onchange="getVals(this, 'question_2');">
	                                                    <label class="radio" for="no"></label>
	                                                    <label for="no" class="wrapper">No</label>
	                                                </div>
	                                            </li>
	                                            <li>
	                                                <div class="checkbox_radio_container">
	                                                    <input type="radio" id="maybe" name="question_2" class="required" value="Maybe" onchange="getVals(this, 'question_2');">
	                                                    <label class="radio" for="maybe"></label>
	                                                    <label for="maybe" class="wrapper">Maybe</label>
	                                                </div>
	                                            </li>
	                                            <li>
	                                                <div class="checkbox_radio_container">
	                                                    <input type="radio" id="probably" name="question_2" class="required" value="Probably" onchange="getVals(this, 'question_2');">
	                                                    <label class="radio" for="probably"></label>
	                                                    <label for="probably" class="wrapper">Probably</label>
	                                                </div>
	                                            </li>
	                                            <li>
	                                                <div class="checkbox_radio_container">
	                                                    <input type="radio" id="sure" name="question_2" class="required" value="Sure" onchange="getVals(this, 'question_2');">
	                                                    <label class="radio" for="sure"></label>
	                                                    <label for="sure" class="wrapper">100% Sure</label>
	                                                </div>
	                                            </li>
	                                        </ul>
	                                    </div>
	                                    <!-- /review_block-->
	                                    <div class="form-group">
	                                        <label for="additional_message_2_label">Additional message</label>
	                                        <textarea name="additional_message_2" id="additional_message_2_label" class="form-control" style="height:120px;" onkeyup="getVals(this, 'additional_message_2');"></textarea>
	                                    </div>
	                                </div>
	                                <!-- /step 2-->

	                                <div class="step">
	                                    <h3 class="main_question"><strong>3 of 5</strong>How did you hear about us?</h3>
	                                    <div class="review_block">
	                                        <ul>
	                                            <li>
	                                                <div class="checkbox_radio_container">
	                                                    <input type="checkbox" id="question_3_opt_1" name="question_3[]" class="required" value="Google and Search Engines" onchange="getVals(this, 'question_3');">
	                                                    <label class="checkbox" for="question_3_opt_1"></label>
	                                                    <label for="question_3_opt_1" class="wrapper">Google and Search Engines</label>
	                                                </div>
	                                            </li>
	                                            <li>
	                                                <div class="checkbox_radio_container">
	                                                    <input type="checkbox" id="question_3_opt_2" name="question_3[]" class="required" value="Emails or Newsletter" onchange="getVals(this, 'question_3');">
	                                                    <label class="checkbox" for="question_3_opt_2"></label>
	                                                    <label for="question_3_opt_2" class="wrapper">Emails or Newsletter</label>
	                                                </div>
	                                            </li>
	                                            <li>
	                                                <div class="checkbox_radio_container">
	                                                    <input type="checkbox" id="question_3_opt_3" name="question_3[]" class="required" value="Friends or other people" onchange="getVals(this, 'question_3');">
	                                                    <label class="checkbox" for="question_3_opt_3"></label>
	                                                    <label for="question_3_opt_3" class="wrapper">Friends or other people</label>
	                                                </div>
	                                            </li>
	                                            <li>
	                                                <div class="checkbox_radio_container">
	                                                    <input type="checkbox" id="question_3_opt_4" name="question_3[]" class="required" value="Print Advertising" onchange="getVals(this, 'question_3');">
	                                                    <label class="checkbox" for="question_3_opt_4"></label>
	                                                    <label for="question_3_opt_4" class="wrapper">Print Advertising</label>
	                                                </div>
	                                            </li>
	                                            <li>
	                                                <div class="checkbox_radio_container">
	                                                    <input type="checkbox" id="question_3_opt_5" name="question_3[]" class="required" value="Print Advertising" onchange="getVals(this, 'question_3');">
	                                                    <label class="checkbox" for="question_3_opt_5"></label>
	                                                    <label for="question_3_opt_5" class="wrapper">Other</label>
	                                                </div>
	                                            </li>
	                                        </ul>
	                                        <small><em>Multiple selections *</em></small>
	                                    </div>
	                                </div>
	                                <!-- /step 3-->

	                                <div class="step">
	                                    <h3 class="main_question"><strong>4 of 5</strong>Summary</h3>
	                                    <div class="summary">
	                                        <ul>
	                                            <li>
	                                                <strong>1</strong>
	                                                <h5>How was the service provided?</h5>
	                                                <p id="question_1" class="mb-2"></p>
	                                                <p id="additional_message"></p>
	                                            </li>
	                                            <li>
	                                                <strong>2</strong>
	                                                <h5>Would you reccomend our company?</h5>
	                                                <p id="question_2" class="mb-2"></p>
	                                                <p id="additional_message_2"></p>
	                                            </li>
	                                            <li>
	                                                <strong>3</strong>
	                                                <h5>How did you hear about our company?</h5>
	                                                <p id="question_3"></p>
	                                            </li>
	                                        </ul>
	                                    </div>
	                                </div>
	                                <!-- /step 4-->

	                                <div class="submit step">
	                                    <h3 class="main_question"><strong>5 of 5</strong>Please fill with your details</h3>
	                                    <div class="form-group">
	                                        <label for="firstname">First Name</label>
	                                        <input type="text" name="firstname" id="firstname" class="form-control required">
	                                    </div>
	                                    <div class="form-group">
	                                        <label for="lastname">Last Name</label>
	                                        <input type="text" name="lastname" id="lastname" class="form-control required">
	                                    </div>
	                                    <div class="form-group">
	                                        <label for="email">Your Email</label>
	                                        <input type="email" name="email" id="email" class="form-control required">
	                                    </div>
	                                    <div class="form-group">
	                                        <label for="telephone">Telephone</label>
	                                        <input type="text" name="telephone" id="telephone" class="form-control">
	                                    </div>
	                                    <div class="row">
	                                        <div class="col-lg-3 col-md-3 col-3">
	                                            <div class="form-group">
	                                                <label for="age">Age</label>
	                                                <input type="text" name="age" id="age" class="form-control">
	                                            </div>
	                                        </div>
	                                        <div class="col-9">
	                                            <div class="form-group radio_input">
	                                                <label class="container_radio">Male
	                                                    <input type="radio" name="gender" value="Male" class="required">
	                                                    <span class="checkmark"></span>
	                                                </label>
	                                                <label class="container_radio">Female
	                                                    <input type="radio" name="gender" value="Female" class="required">
	                                                    <span class="checkmark"></span>
	                                                </label>
	                                            </div>
	                                        </div>
	                                    </div>
	                                    <!-- /row -->
	                                    <div class="form-group terms">
	                                        <label class="container_check">Please accept our <a href="#" data-bs-toggle="modal" data-bs-target="#terms-txt" style="color:#fff; text-decoration: underline;">Terms and conditions</a>
	                                            <input type="checkbox" name="terms" value="Yes" class="required">
	                                            <span class="checkmark"></span>
	                                        </label>
	                                    </div>
	                                </div>
	                                <!-- /step 5-->

	                            </div>
	                            <!-- /middle-wizard -->

	                            <div id="bottom-wizard">
	                                <button type="button" name="backward" class="backward">Prev</button>
	                                <button type="button" name="forward" class="forward">Next</button>
	                                <button type="submit" name="process" class="submit">Submit</button>
	                            </div>
	                            <!-- /bottom-wizard -->

	                        </form>
	                    </div>
	                    <!-- /Wizard container -->
	                </div>
	                <!-- /col -->
	            </div>
	        </div>
	        <!-- /row -->
	    </div>
	    <!-- /container_centering -->
	    <footer>
	        <div class="container-fluid">
	            <div class="row">
	                <div class="col-md-3">
	                    ©2023 Satisfyc
	                </div>
	                <div class="col-md-9">
	                    <ul class="clearfix">
	                        <li><a href="https://1.envato.market/0oe9P" class="animated_link" target="_parent">Purchase this template</a></li>
	                        <li><a href="index.html" class="animated_link">Demo 1</a></li>
	                        <li><a href="index-2.html" class="animated_link">Demo 2</a></li>
	                        <li><a href="index-3.html" class="animated_link">Demo 3</a></li>
	                    </ul>
	                </div>
	            </div>
	            <!-- /row -->
	        </div>
	        <!-- /container-fluid -->
	    </footer>
	    <!-- /footer -->
	</div>
	<!-- /wrapper_centering -->

	<!-- Modal terms -->
	<div class="modal fade" id="terms-txt" tabindex="-1" role="dialog" aria-labelledby="termsLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="termsLabel">Terms and conditions</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<p>Lorem ipsum dolor sit amet, in porro albucius qui, in <strong>nec quod novum accumsan</strong>, mei ludus tamquam dolores id. No sit debitis meliore postulant, per ex prompta alterum sanctus, pro ne quod dicunt sensibus.</p>
					<p>Lorem ipsum dolor sit amet, in porro albucius qui, in nec quod novum accumsan, mei ludus tamquam dolores id. No sit debitis meliore postulant, per ex prompta alterum sanctus, pro ne quod dicunt sensibus. Lorem ipsum dolor sit amet, <strong>in porro albucius qui</strong>, in nec quod novum accumsan, mei ludus tamquam dolores id. No sit debitis meliore postulant, per ex prompta alterum sanctus, pro ne quod dicunt sensibus.</p>
					<p>Lorem ipsum dolor sit amet, in porro albucius qui, in nec quod novum accumsan, mei ludus tamquam dolores id. No sit debitis meliore postulant, per ex prompta alterum sanctus, pro ne quod dicunt sensibus.</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn_1" data-dismiss="modal">Close</button>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<!-- /.modal -->
	
	<!-- COMMON SCRIPTS -->
	<script src="js/jquery-3.7.1.min.js"></script>
    <script src="js/common_scripts.min.js"></script>
	<script src="js/functions.js"></script>

	<!-- Wizard script -->
	<script src="js/survey_func.js"></script>

</body>
</html>
