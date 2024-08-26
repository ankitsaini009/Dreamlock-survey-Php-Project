<?php 
  include  '../include/header.php';
  include  '../include/navbar.php';
  include '../db2.php'; 
?>

<?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        // Database connection
        $servername = "localhost";
        $username = "root";
        $password = ""; // Add your DB password here
        $dbname = "pmtool_db";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $created_at = date("Y-m-d H:i:s");
        $updated_at = date("Y-m-d H:i:s");

        // Handle form submission for adding or editing a question
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title']) && isset($_POST['question']) && isset($_POST['category'])) {
            $title = $_POST['title'];
            $question = $_POST['question'];
            $category_id = $_POST['category'];
            $options = json_decode($_POST['options'], true);

            if (!empty($_POST['edit_question_id'])) {
                $edit_question_id = $_POST['edit_question_id'];
                $sql = "UPDATE questionnaire 
                SET category_id='$category_id', title='$title', question_text='$question', options='" . json_encode($options) . "', updated_at='$updated_at' 
                WHERE id=$edit_question_id";
                if ($conn->query($sql) === TRUE) {
                
                    //session_start();
                    
                    // Assume form processing here
                    // Set a session message
                // $_SESSION['message'] = 'Your Quetions was successful!';
                    
                    // Redirect to dashboard
                    // header('Location: dashboard.php');
                    // exit();
                    echo "<script>alert(' Quetions Added successful!');</script>";
                
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                $sql = "INSERT INTO questionnaire (category_id, title, question_text, options , created_at) VALUES ('$category_id', '$title', '$question', '" . json_encode($options) . "')";
                $sql = "INSERT INTO questionnaire (category_id, title, question_text, options, created_at, updated_at)  VALUES ('$category_id', '$title', '$question', '" . json_encode($options) . "', '$created_at', '$updated_at')";

                if ($conn->query($sql) === TRUE) {
                    //$_SESSION['message'] = 'Your Quetions was successful!';
                    echo "<script>alert(' Quetions Added successful!');</script>";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }
        }

        // Handle delete question
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_question_id'])) {
            $delete_question_id = $_POST['delete_question_id'];

            // Then, delete the question itself
            $sql = "DELETE FROM questionnaire WHERE id=$delete_question_id";
            if ($conn->query($sql) === TRUE) {
                //echo "Question deleted successfully";
                echo "<script>alert(' Quetions deleted successful!');</script>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

        // Fetch categories
        $categories = [];
        $sql = "SELECT * FROM categories";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }

        // Fetch questions
        $questions = [];
        $sql = "SELECT q.id, q.title, q.question_text, q.category_id, q.options, c.name AS category_name FROM questionnaire q
                JOIN categories c ON q.category_id = c.id";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $row['options'] = json_decode($row['options'], true) ?: [];
            $questions[] = $row;
        }

        $conn->close();
?>
<style>
    .table_wrapper{display: block;max-height: 40vh; overflow-x: auto;overflow-y: auto;white-space: nowrap;}
</style>
    
<div class="content-wrapper">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title font-bold text-2xl">Question Master</h4>
                    <form method="POST" action="" onsubmit="collectOptions()">
                        <input type="hidden" name="edit_question_id" id="edit_question_id">
                        <div class="flex form-group">
                            <div  class="mr-4" style="width:600px;">
                                <label for="category">Category</label>
                                <select name="category" id="category" required required class="form-select">
                                    <option value="">-- Select Category --</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="ml-4" style="width:600px;">
                                <label for="title">Title</label>
                                <input type="text" name="title" id="title" required class="form-control" >
                            </div>
                        </div>
                        <div class="flex form-group">
                            <div class="ml-2" style="width:1218px;">
                                <label for="question">Question</label>
                                <input  type="text" name="question" id="question" required class="form-control" >
                            </div>
                        </div>                           
                        <div class="row">
                            <div class="col-sm-6 col-md-6 AddOpt has-feedback">
                                <label for="txtoptions" class="control-label" class="block text-gray-700 text-sm font-bold mb-2">Add Option<span class="required">*</span></label>
                                <textarea rows="7" type="text" name="txtoptions" id="txtoptions" class="form-control"></textarea>
                                <div class="row">
                                    <div class="mt-2">
                                        <input type="button" id="btnoptions" value="Add" class="btn btn-sm btn-primary" onclick="addOption()" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6">
                                <label for="Optionlist">Mapped Options</label>
                                <select style="height:120px;" class="form-control Optionlist" name="Optionlist" id="Optionlist" ondblclick="removeOption(this)" multiple class="form-select"></select>
                                <p>Please double click any option to remove from the list</p>
                            </div>
                            <div class="flex justify-end mr-14">
                                <button type="submit" class="btn btn-gradient-primary mr-2">Save</button>
                                <button type="submit" class="btn btn-light mr-2">Cancel</button>
                            </div>                    
                        </div>
                        <input type="hidden" name="options" id="options">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>            
<break>
<div class="container">
    <table class="table table-hover table_wrapper">
        <thead class="sticky top-0">
            <tr>
                <th class="py-4 px-4 border-b">ID</th>
                <th class="py-4 px-4 border-b">Question Title</th>
                <th class="py-4 px-4 border-b">Question Desc</th>
                <th class="py-4 px-4 border-b">Category</th>
                <th class="py-4 px-4 border-b">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($questions as $question): ?>
                <tr>
                    <td class="py-2 px-4 border-b"><?= $question['id'] ?></td>
                    <td class="py-2 px-4 border-b"><?= $question['title'] ?></td>
                    <td class="py-2 px-4 border-b"><?= $question['question_text'] ?></td>
                    <td class="py-2 px-4 border-b"><?= $question['category_name'] ?></td>

                    <td class="py-2 px-4 border-b">
                        <button class="btn btn-edit" onclick='editQuestion("<?= $question['id'] ?>", "<?= addslashes($question['title']) ?>", "<?= addslashes($question['question_text']) ?>", "<?= $question['category_id'] ?>","<?= json_encode($question['options']) ?>")'>Edit</button>
                        <form method="POST" action="" style="display:inline-block;">
                            <input type="hidden" name="delete_question_id" value="<?= $question['id'] ?>">
                            <input type="submit" value="Delete" class="btn btn-delete">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    // Function to add an option to the list
    function addOption() {
        var txtoptions = document.getElementById("txtoptions").value.trim();
        if (txtoptions !== "") {
            var optionList = document.getElementById("Optionlist");
            var option = document.createElement("option");
            option.value = txtoptions;
            option.text = txtoptions;
            optionList.add(option);

            document.getElementById("txtoptions").value = ""; // Clear input after adding
        } else {
            alert("Please enter an option.");
        }
    }

    // Function to remove an option from the list
    function removeOption(selectElement) {
        if (selectElement.selectedIndex >= 0) {
            selectElement.remove(selectElement.selectedIndex);
        } else {
            alert("Please select an option to remove.");
        }
    }

    // Collect options into a hidden input before submitting the form
    function collectOptions() {
        var optionList = document.getElementById("Optionlist");
        var options = [];
        for (var i = 0; i < optionList.options.length; i++) {
            options.push(optionList.options[i].value);
        }
        document.getElementById("options").value = JSON.stringify(options);
    }

    // Populate the form for editing a question
    function editQuestion(id, title, question, category_id, options) {
        document.getElementById("edit_question_id").value = id;
        document.getElementById("title").value = title;
        document.getElementById("question").value = question;
        document.getElementById("category").value = category_id;

        var optionList = document.getElementById("Optionlist");
        optionList.innerHTML = ""; // Clear current options
        var opts = JSON.parse(options);
        for (var i = 0; i < opts.length; i++) {
            var option = document.createElement("option");
            option.value = opts[i];
            option.text = opts[i];
            optionList.add(option);
        }
    }
</script>

<?php include '../include/footer.php'; ?>