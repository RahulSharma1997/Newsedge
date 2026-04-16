<?php include 'db_connect.php'; ?>
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
    <h1 class="mt-4">Blog Posts</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Blog List</li>
    </ol>
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            All Blog Posts
            <a href="add_blog.php" class="btn btn-primary btn-sm float-end">Add New Blog</a>
        </div>
        <div class="card-body">
            <table id="datatablesSimple">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT id, title, category, author_name, created_at FROM blogs ORDER BY id DESC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td>" . htmlspecialchars($row["title"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["category"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["author_name"]) . "</td>";
                            echo "<td>" . $row["created_at"] . "</td>";
                            echo "<td>";
                            echo "<a href='edit_blog.php?id=" . $row["id"] . "' class='btn btn-info btn-sm'>Edit</a> ";
                            echo "<a href='delete_blog.php?id=" . $row["id"] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this post?\")'>Delete</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
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