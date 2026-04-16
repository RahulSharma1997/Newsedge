<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_connect.php'; 

// Define variables and initialize with empty values
$title = $content = $category = $tags = $image = "";
$title_err = $content_err = $image_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate title
    if(empty(trim($_POST["title"]))){
        $title_err = "Please enter a title.";
    } else{
        $title = trim($_POST["title"]);
    }
    
    // Validate content
    if(empty(trim($_POST["content"]))){
        $content_err = "Please enter content.";
    } else{
        $content = trim($_POST["content"]);
    }

    $category = trim($_POST["category"]);
    $tags = trim($_POST["tags"]);
    $author_name = $_SESSION["name"];

    // Check for image upload
    $image_path = "";
    if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0){
        $allowed = ["jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png"];
        $filename = $_FILES["image"]["name"];
        $filetype = $_FILES["image"]["type"];
        $filesize = $_FILES["image"]["size"];
    
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(!array_key_exists($ext, $allowed)) $image_err = "Please select a valid file format.";
    
        $maxsize = 5 * 1024 * 1024;
        if($filesize > $maxsize) $image_err = "File size is larger than the allowed limit.";
    
        if(in_array($filetype, $allowed) && empty($image_err)){
            $new_filename = uniqid() . "." . $ext;
            $upload_dir = "../uploads/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            if(move_uploaded_file($_FILES["image"]["tmp_name"], $upload_dir . $new_filename)){
                $image_path = "uploads/" . $new_filename; // Path to be stored in DB
            } else {
                $image_err = "Error uploading your file.";
            }
        } else{
            $image_err = "There was a problem uploading your file. Please try again."; 
        }
    }

    // Check input errors before inserting in database
    if(empty($title_err) && empty($content_err) && empty($image_err)){
        
        $sql = "INSERT INTO blogs (title, content, category, image, author_name, tags) VALUES (?, ?, ?, ?, ?, ?)";
         
        if($stmt = $conn->prepare($sql)){
            $stmt->bind_param("ssssss", $param_title, $param_content, $param_category, $param_image, $param_author, $param_tags);
            
            $param_title = $title;
            $param_content = $content;
            $param_category = $category;
            $param_image = $image_path;
            $param_author = $author_name;
            $param_tags = $tags;
            
            if($stmt->execute()){
                header("location: index.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
       <?php include 'master/link.php'; ?>
    </head>
    <body class="sb-nav-fixed">
        <?php include 'master/header.php'; ?>
        <div id="layoutSidenav">
             <?php include 'master/sidebar.php'; ?>
            <div id="layoutSidenav_content">
                <main>

<div class="container-fluid px-4">
    <h1 class="mt-4">Add New Blog Post</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Add New Blog</li>
    </ol>
    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-plus me-1"></i> New Post Details</div>
        <div class="card-body">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <div class="mb-3"><label for="title" class="form-label">Title</label><input type="text" name="title" id="title" class="form-control <?php echo (!empty($title_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $title; ?>"><span class="invalid-feedback"><?php echo $title_err; ?></span></div>
                <div class="mb-3"><label for="content" class="form-label">Content</label><textarea name="content" id="content" rows="10" class="form-control <?php echo (!empty($content_err)) ? 'is-invalid' : ''; ?>"><?php echo $content; ?></textarea><span class="invalid-feedback"><?php echo $content_err; ?></span></div>
                <div class="mb-3"><label for="category" class="form-label">Category</label><input type="text" name="category" id="category" class="form-control" value="<?php echo $category; ?>"></div>
                <div class="mb-3"><label for="tags" class="form-label">Tags (comma separated)</label><input type="text" name="tags" id="tags" class="form-control" value="<?php echo $tags; ?>"></div>
                <div class="mb-3"><label for="image" class="form-label">Featured Image</label><input type="file" name="image" id="image" class="form-control <?php echo (!empty($image_err)) ? 'is-invalid' : ''; ?>"><span class="invalid-feedback"><?php echo $image_err; ?></span></div>
                <button type="submit" class="btn btn-primary">Publish Post</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Your Website 2023</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <?php include 'master/footer.php'; ?>
    </body>
</html>