<?php 
  include  '../include/header.php';
  include  '../include/navbar.php';
  include '../db2.php'; 
?>

<?php
    // Handle form submission for adding or editing a category
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['category_name'])) {
        $category_name = $_POST['category_name'];
        $parent_category = !empty($_POST['parent_category']) ? $_POST['parent_category'] : NULL;
        
        if (!empty($_POST['edit_category_id'])) {
            $edit_category_id = $_POST['edit_category_id'];
            $sql = "UPDATE categories SET name='$category_name' WHERE id=$edit_category_id";
            if ($conn->query($sql) === TRUE) {
                $sql = "DELETE FROM category_relationships WHERE category_id=$edit_category_id";
                $conn->query($sql);
                if ($parent_category) {
                    $sql = "INSERT INTO category_relationships (category_id, parent_id) VALUES ('$edit_category_id', '$parent_category')";
                    $conn->query($sql);
                }
                //echo "Category updated successfully";
                echo "<script>alert('Category updated successfully');</script>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            $sql = "INSERT INTO categories (name) VALUES ('$category_name')";
            if ($conn->query($sql) === TRUE) {
                $new_category_id = $conn->insert_id;
                if ($parent_category) {
                    $sql = "INSERT INTO category_relationships (category_id, parent_id) VALUES ('$new_category_id', '$parent_category')";
                    $conn->query($sql);
                }
                echo "<script>alert('Category Created successfully');</script>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }

    // Handle delete category
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_category_id'])) {
        $delete_category_id = $_POST['delete_category_id'];

        // First, delete the related entries in category_relationships
        $sql = "DELETE FROM category_relationships WHERE category_id=$delete_category_id OR parent_id=$delete_category_id";
        if ($conn->query($sql) === TRUE) {
            // Then, delete the category itself
            $sql = "DELETE FROM categories WHERE id=$delete_category_id";
            $conn->query($sql);
            echo "<script>alert('Category deleted successfully');</script>";
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

    // Fetch parent categories
    $parent_categories = [];
    $sql = "SELECT r.category_id, c.name AS category_name, c2.name AS parent_name 
            FROM category_relationships r 
            JOIN categories c ON r.category_id = c.id 
            JOIN categories c2 ON r.parent_id = c2.id";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $parent_categories[$row['category_id']] = $row['parent_name'];
    }

?>

<!-- ========================================================================================================================================================== -->
<style> 
    .table_wrapper{height: 40vh; overflow-y: auto;white-space: nowrap;}
    .table_feild{"width:15px;font-size:12px;"}
  </style>

<div class="content-wrapper">
    <div class="row">
        <div class="col-12-lg grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title font-bold text-2xl">Category</h4>
                    <form  action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="forms-sample">

                        <div class="flex form-group">

                            <div  class="mr-4" style="width:600px;">
                                <label for="category_name">Category Name</label>
                                <input type="text" name="category_name" id="category_name" required class="form-control" >
                            </div>

                            <div class="ml-4" style="width:600px;">
                                <label for="exampleSelectGender">Parent_Category</label>
                                <select id="parent_category"  name="parent_category" class="form-select" id="exampleSelectGender" required>
                                    <option value="">-- Select Category --</option>
                                    <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>                                                            
                            </div>

                        </div>  

                        <div class="flex justify-end mr-16">
                            <button type="submit" class="btn btn-gradient-primary me-2">Save</button>
                            <button class="btn btn-light">Cancel</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
            <?php
                $sql = "SELECT * FROM ProjectDetails";
                $result = $conn->query($sql); 
            ?>
            <table class="table table-hover table_wrapper">
              <thead>
                <tr class="shadow-sm sticky top-0">
                    <th class="text-wrap table-feild" >ID </th>                  
                    <th class="text-wrap table-feild" >Category </th>
                    <th class="text-wrap table-feild" >Parent Category </th>
                    <th class="text-wrap table-feild " >Action </th> 
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td class="text-wrap table-feild"><?= $category['id'] ?></td>
                        <td class="text-wrap table-feild"><?= $category['name'] ?></td>
                        <td class="text-wrap table-feild"><?= isset($parent_categories[$category['id']]) ? $parent_categories[$category['id']] : 'N/A' ?></td>
                        <td class="text-wrap table-feild">
                            <div class="action-buttons">
                                <button  class="btn  btn-light btn-sm" onclick="editCategory('<?= $category['id'] ?>', '<?= $category['name'] ?>', '<?= isset($parent_categories[$category['id']]) ? array_search($parent_categories[$category['id']], array_column($categories, 'name', 'id')) : '' ?>')">Edit</button>
                                <form method="POST" action="" style="display:inline-block;">
                                    <input type="hidden" name="delete_category_id" value="<?= $category['id'] ?>">
                                    <input type="submit" value="Delete" class="btn  btn-light btn-sm " >
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach;?>
            </tbody>
    </table>
</div> 
<!-- ========================================================================================================================================================== -->
