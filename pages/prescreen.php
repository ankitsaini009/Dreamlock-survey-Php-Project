<?php
include '../include/header.php';
include '../include/navbar.php';
include '../db2.php';
?>
<?php
function safeOutput($string)
{
    return htmlspecialchars($string ?? '');
}

//  question list ===========================================================================================================
$sql = "SELECT * FROM questionnaire";
$result = $conn->query($sql);

$questions = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $questions[] = ['title' => $row["title"], 'question' => $row["question_text"]];
    }
}

function searchQuestions($query, $questions)
{
    $searchResults = [];
    foreach ($questions as $question) {
        if (stripos($question['question'], $query) !== false) {
            $searchResults[] = $question;
        }
    }
    return $searchResults;
}

if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $results = searchQuestions($query, $questions);

    if (empty($results)) {
        echo "<p style=\"background: #cab2ff4f; cursor: pointer; padding: 4px 8px; color: #393636; border-radius: 4px; margin:4px 0px;\">No questions found.</p>";
    } else {
        foreach ($results as $result) {
            echo "<p style=\"background: #cab2ff4f; cursor: pointer; padding: 4px 8px; color: #393636; border-radius: 4px; margin:4px 0px;\" data-title=\"" . safeOutput($result['title']) . "\" data-question=\"" . safeOutput($result['question']) . "\">" . safeOutput($result['question']) . "</p>";
        }
    }
    exit;
}
// ===========================================================================================================

function getClientCode($conn, $client)
{
    $sql = "SELECT client_code FROM Clientele WHERE Client_Name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $client);
    $stmt->execute();
    $result = $stmt->get_result();
    $client_code = $result->fetch_assoc()['client_code'] ?? '';
    $stmt->close();
    return $client_code;
}

function getLastComplete($conn, $project_code)
{
    $sql = "SELECT MAX(time_stamp) as last_complete FROM SurveyLog WHERE project_code = ? AND Status = 'Complete'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $project_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $last_complete = $result->fetch_assoc()['last_complete'] ?? '';
    $stmt->close();
    return $last_complete;
}

if (isset($_GET['project_code'])) {
    $project_code = $_GET['project_code'];
    $sql = "SELECT * FROM ProjectDetails WHERE project_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $project_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $project = $result->fetch_assoc();
    $stmt->close();

    $client_code = getClientCode($conn, $project['client']);
    $last_complete = getLastComplete($conn, $project_code);

    // Parse the links JSON safely
    $links = json_decode($project['links'], true)[0] ?? ['live' => '', 'test' => ''];
    $live_link = $links['live'];
    $test_link = $links['test'];

    // Decode the filters JSON
    $filters = json_decode($project['filters'], true) ?? [];
} else {
    die("Project code not provided.");
}

// Handle form submission for updating project details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_code = $_POST['project_code'];
    $project_name = $_POST['project_name'];
    $project_description = $_POST['project_description'];
    $client = $_POST['client'];
    $project_country = $_POST['project_country'];
    $LOI = $_POST['LOI'];
    $sample_size = $_POST['sample_size'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $IR = $_POST['IR'];
    $respondent_click_quota = $_POST['respondent_click_quota'];
    $CPI = $_POST['CPI'];
    $filters_json = json_encode($_POST['filters']);
    $links_json = json_encode([
        [
            'live' => $_POST['live_link'],
            'test' => $_POST['test_link']
        ]
    ]);

    $sql = "UPDATE ProjectDetails SET project_name=?, project_description=?, client=?, project_country=?, LOI=?, sample_size=?, start_date=?, end_date=?, IR=?, respondent_click_quota=?, CPI=?, filters=?, links=? WHERE project_code=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisssssissss", $project_name, $project_description, $client, $project_country, $LOI, $sample_size, $start_date, $end_date, $IR, $respondent_click_quota, $CPI, $filters_json, $links_json, $project_code);

    if ($stmt->execute()) {
        echo "<p class='text-green-500'>Project updated successfully.</p>";
    } else {
        echo "<p class='text-red-500'>ERROR: Could not execute the query. " . htmlspecialchars($stmt->error) . "</p>";
    }

    $stmt->close();
    $conn->close();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescreen</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../nav/css/style.css">
    <style>
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 3fr;
        }

        .edit-button {
            position: absolute;
            top: 1rem;
            right: 1rem;
        }

        .adjustWidth {
            width: 1425px;
        }

        .inputWidth {
            width: 400px
        }
    </style>
<script>
    function searchQuestions(query) {
        if (query.length == 0) {
            document.getElementById("tesjd").innerHTML = "";
            return;
        }
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var parser = new DOMParser();
                var doc = parser.parseFromString(xhr.responseText, 'text/html');
                var mainPanelContent = doc.querySelector('.main-panel').innerHTML;
                document.getElementById("tesjd").innerHTML = mainPanelContent;
                console.log('Extracted Content: ' + mainPanelContent);

                // Add click event listeners to search results
                document.querySelectorAll('#tesjd p').forEach(function(p) {
                    p.addEventListener('click', function() {
                        displayResult(p);
                    });
                });
            }
        };
        xhr.open("GET", "?query=" + query, true);
        xhr.send();
    }

    function displayResult(element) {
        console.log(element);
        var title = element.getAttribute('data-title');
        var question = element.getAttribute('data-question');
        document.getElementById('title-display').innerText = title;
        document.getElementById('question-display').innerText = question;
    }
</script>


</head>

<body class="bg-gray-100">
    <div class="wrapper d-flex align-items-stretch">
        <div class="main-content">
            <div class="container mx-auto px-4 py-8 adjustWidth">
                <div class="relative ">
                    <div class="flex">
                        <a href="project_details.php?project_code=<?= safeOutput($project_code) ?>" class="text-purple-500">
                            <h1 class="text-2xl font-bold ml-2 cursor-pointer px-4 py-1">Project Details</h1>
                        </a>
                        <h1 class="text-2xl font-bold ml-2 cursor-pointer bg-purple-500 text-white px-4 py-1 rounded-t-xl">
                            PreScreen</h1>
                        <a href="project_mapping.php?project_code=<?= safeOutput($project_code) ?>" class="text-purple-500">
                            <h1 class="text-2xl font-bold ml-2 cursor-pointer px-4 py-1">Project Mapping</h1>
                        </a>
                    </div>
                    <div class="absolute top-0 right-3">
                        <button class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded-t-xl" id="editButton" onclick="toggleEdit()">Edit</button>
                    </div>
                </div>

                <?php //if ($project): 
                ?>
                <!-- <form action="project_details.php?project_code=<?php // safeOutput($project_code) 
                                                                    ?>" method="POST" id="projectForm" > -->
                <!-- <input type="hidden" name="project_code" value="<?php // safeOutput($project['project_code']) 
                                                                        ?>"> -->
                <div class="bg-white shadow-md rounded-lg p-6 mb-8">
                    <div class="details-grid mb-4">
                        <div class="text-lg font-semibold">Language :
                            <span class="text-lg font-light">English</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="font-semibold">
                            <label for="htmlfor">Search Question <span class="text-red-500">*</span></label>
                            <div class="flex w-full justify-between">
                                <div class="search-container">
                                    <input type="text" name="query" class="form-control" onkeyup="searchQuestions(this.value)">
                                    <div id="tesjd" class="search-results"></div>
                                </div>
                                <div>
                                    <button class="bg-purple-500 text-white px-8 py-2 mx-1 text-[0px] rounded-xl hover:bg-purple-600">Add Temp Question</button>
                                    <button class="bg-purple-500 text-white px-8 py-2 mx-1 text-[0px] rounded-xl hover:bg-purple-600">View Temp Question</button>
                                    <button class="bg-purple-500 text-white px-8 py-2 mx-1 text-[0px] rounded-xl hover:bg-purple-600">Pre Screen Message</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div>
                            <div class="text-lg font-semibold mb-4">Title :
                                <span class="text-lg font-light" id="title-display"></span>
                            </div>
                        </div>
                        <div>
                            <div class="text-lg font-semibold my-2">Question :
                                <span class="text-lg font-light" id="question-display"></span>
                            </div>
                        </div>
                        <div>
                            <div class="text-lg font-semibold my-2">Control Type *:
                                <select name="control" id="control" class="border-solid border-2 border-black rounded-lg px-4 py-2 ">
                                    <option value="">-- Select Control Type --</option>
                                    <option value="1">Text</option>
                                    <option value="2">Radio</option>
                                    <option value="3">DropDown</option>
                                    <option value="4">Checkbox</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <div class="text-center">
                                <button type="submit" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 ml-3 rounded focus:outline-none focus:shadow-outline" id="saveButton" style="">Save</button>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 ml-3 rounded focus:outline-none focus:shadow-outline" id="saveButton" style="">Cancel</button>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- </form>-->

                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Category</th>
                            <th scope="col">Sub Category</th>
                            <th scope="col">Control</th>
                            <th scope="col">Question</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Mark</td>
                            <td>Otto</td>
                            <td>@mdo</td>
                            <td>@mdo</td>
                            <td>@mdo</td>
                        </tr>
                    </tbody>
                </table>


                <?php //else: 
                ?>
                <!-- <p class="text-center text-red-500">No project details found.</p> -->
                <?php //endif; 
                ?>
            </div>
        </div>
    </div>



    <script>
        function toggleEdit() {
            var form = document.getElementById('projectForm');
            var inputs = form.querySelectorAll('input[type="text"], input[type="date"]');
            var checkboxes = form.querySelectorAll('input[type="checkbox"]');
            var saveButton = document.getElementById('saveButton');
            var editButton = document.getElementById('editButton');

            if (editButton.innerText === 'Edit') {
                inputs.forEach(input => input.removeAttribute('readonly'));
                checkboxes.forEach(checkbox => checkbox.removeAttribute('disabled'));
                saveButtonyle.display = 'inline-block';
                editButton.innerText = 'Cancel';
            } else {
                inputs.forEach(input => input.setAttribute('readonly', true));
                checkboxes.forEach(checkbox => checkbox.setAttribute('disabled', true));
                saveButton.style.display = 'none';
                editButton.innerText = 'Edit';
            }
        }
    </script>

    <?php include '../include/footer.php'; ?>