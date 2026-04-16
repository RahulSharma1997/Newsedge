<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_connect.php'; 

$title = $content = $category = $tags = $current_image = "";
$title_err = $content_err = $image_err = "";
$blog_id = 0;

if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    $blog_id = trim($_GET["id"]);

    $sql = "SELECT * FROM blogs WHERE id = ?";
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("i", $param_id);
        $param_id = $blog_id;
        
        if($stmt->execute()){
            $result = $stmt->get_result();
            if($result->num_rows == 1){
                $row = $result->fetch_assoc();
                $title = $row["title"];
                $content = $row["content"];
                $category = $row["category"];
                $tags = $row["tags"];
                $current_image = $row["image"];
            } else{
                header("location: blog_list.php");
                exit();
            }
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
        $stmt->close();
    }
} else {
    header("location: blog_list.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $blog_id = $_POST["id"];

    if(empty(trim($_POST["title"]))){ $title_err = "Please enter a title."; } else { $title = trim($_POST["title"]); }
    if(empty(trim($_POST["content"]))){ $content_err = "Please enter content."; } else { $content = trim($_POST["content"]); }

    $category = trim($_POST["category"]);
    $tags = trim($_POST["tags"]);
    $current_image = trim($_POST["current_image"]);
    $image_path = $current_image;

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
            $upload_dir = "uploads/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            if(move_uploaded_file($_FILES["image"]["tmp_name"], $upload_dir . $new_filename)){
                if(!empty($current_image) && file_exists("../" . $current_image)){
                    unlink("../" . $current_image);
                }
                $image_path = "uploads/" . $new_filename;
            } else {
                $image_err = "Error uploading your file.";
            }
        } else{
            $image_err = "There was a problem uploading your file."; 
        }
    }

    if(empty($title_err) && empty($content_err) && empty($image_err)){
        
        $sql = "UPDATE blogs SET title=?, content=?, category=?, image=?, tags=? WHERE id=?";
         
        if($stmt = $conn->prepare($sql)){
            $stmt->bind_param("sssssi", $param_title, $param_content, $param_category, $param_image, $param_tags, $param_id);
            
            $param_title = $title;
            $param_content = $content;
            $param_category = $category;
            $param_image = $image_path;
            $param_tags = $tags;
            $param_id = $blog_id;
            
            if($stmt->execute()){
                header("location: blog_list.php");
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
    <h1 class="mt-4">Edit Blog Post</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="blog_list.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Edit Blog</li>
    </ol>
    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-edit me-1"></i> Edit Post Details</div>
        <div class="card-body">
            <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $blog_id; ?>"/>
                <input type="hidden" name="current_image" value="<?php echo $current_image; ?>"/>
                <div class="mb-3"><label for="title" class="form-label">Title</label><input type="text" name="title" id="title" class="form-control <?php echo (!empty($title_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $title; ?>"><span class="invalid-feedback"><?php echo $title_err; ?></span></div>
                <div class="mb-3"><label for="content" class="form-label">Content</label><textarea name="content" id="content" rows="10" class="form-control <?php echo (!empty($content_err)) ? 'is-invalid' : ''; ?>"><?php echo $content; ?></textarea><span class="invalid-feedback"><?php echo $content_err; ?></span></div>
                <div class="mb-3"><label for="category" class="form-label">Category</label><input type="text" name="category" id="category" class="form-control" value="<?php echo $category; ?>"></div>
                <div class="mb-3"><label for="tags" class="form-label">Tags (comma separated)</label><input type="text" name="tags" id="tags" class="form-control" value="<?php echo $tags; ?>"></div>
                <div class="mb-3">
                    <label for="image" class="form-label">Featured Image</label>
                    <input type="file" name="image" id="image" class="form-control <?php echo (!empty($image_err)) ? 'is-invalid' : ''; ?>"><span class="invalid-feedback"><?php echo $image_err; ?></span>
                    <?php if($current_image): ?>
                        <div class="mt-2"><p>Current Image:</p><img src="../<?php echo $current_image; ?>" alt="Current Image" style="max-width: 200px; height: auto;"></div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">Update Post</button>
                <a href="blog_list.php" class="btn btn-secondary">Cancel</a>
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