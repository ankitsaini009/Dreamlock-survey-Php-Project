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

// Handle form submission for adding or editing a question
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title']) && isset($_POST['question']) && isset($_POST['category'])) {
    $title = $_POST['title'];
    $question = $_POST['question'];
    $category_id = $_POST['category'];
    $options = json_decode($_POST['options'], true);

    if (!empty($_POST['edit_question_id'])) {
        $edit_question_id = $_POST['edit_question_id'];
        $sql = "UPDATE questionnaire SET category_id='$category_id', title='$title', question_text='$question', options='" . json_encode($options) . "' WHERE id=$edit_question_id";
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
        $sql = "INSERT INTO questionnaire (category_id, title, question_text, options) VALUES ('$category_id', '$title', '$question', '" . json_encode($options) . "')";
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
<?php include '../Login/header.php'; ?>

<?php include '../include/dashboard.php'; ?>
    
<div class="container mx-full px-4">
    <form method="POST" action="" onsubmit="collectOptions()">
        <input type="hidden" name="edit_question_id" id="edit_question_id">
        <h1>Question Master</h1>
        
        <label for="category" class="block text-gray-700 text-sm font-bold mb-2">Category</label>
        <select name="category" id="category" required required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <option value="">-- Select Category --</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
            <?php endforeach; ?>
        </select>
        <br>

        <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title</label>
        <input type="text" name="title" id="title" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        <br>

        <label for="question" class="block text-gray-700 text-sm font-bold mb-2">Question</label>
        <input type="text" name="question" id="question" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        <br>

        <div class="row">
            <div class="col-sm-6 col-md-6 AddOpt has-feedback">
                <label for="txtoptions" class="control-label" class="block text-gray-700 text-sm font-bold mb-2">Add Option<span class="required">*</span></label>
                <textarea rows="7" type="text" name="txtoptions" id="txtoptions" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                <div class="row">
                    <div class="col-md-9">
                        <br>
                        <label for="btnoptions" class="control-label" style="visibility:hidden">Add</label>
                        <input type="button" id="btnoptions"value="Add Option" onclick="addOption()" >
                    </div>
                    <div class="col-md-3">
                        <label for="form_submit" class="control-label" style="visibility:hidden">Save</label>
                        <input type="submit" id="form_submit"value="Save" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                      
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-6">
                <label for="Optionlist" class="control-label">Mapped Options</label>
                <select style="height:140px;" class="form-control Optionlist" name="Optionlist" id="Optionlist" ondblclick="removeOption(this)" multiple class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></select>
                <p>Please double click any option to remove from the list</p>
            </div>
            <div class="col-md-1 Selectopt">
                <br><br>
            </div>
        </div>
        <input type="hidden" name="options" id="options">
        <br>

       
       
    </form>
    </div>
           
            
            <break>
<br>
<div class="container mx-full px-100">
    <table class="min-w-full bg-white">
    
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">ID</th>
                <th class="py-2 px-4 border-b">Question Title</th>
                <th class="py-2 px-4 border-b">Question Desc</th>
                <th class="py-2 px-4 border-b">Category</th>
                <th class="py-2 px-4 border-b">Action</th>
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
                        <button class="btn btn-edit" onclick='editQuestion("<?= $question['id'] ?>", "<?= addslashes($question['title']) ?>", "<?= addslashes($question['question_text']) ?>", "<?= $question['category_id'] ?>", <?= json_encode($question['options']) ?>)'>Edit</button>
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
    <?php include '../include/footer.php'; ?>