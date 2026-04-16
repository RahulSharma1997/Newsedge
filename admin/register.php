<?php
require_once 'db_connect.php';
session_start();

$errors = [];
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name)) $errors[] = "Name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    if (empty($phone)) $errors[] = "Phone number is required.";
    if (strlen($password) < 6) $errors[] = "Password must have at least 6 characters.";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match.";

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
        $stmt->bind_param("ss", $email, $phone);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email or phone number already registered.";
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $phone, $hashed_password);
        
        if ($stmt->execute()) {
            $success_message = "Registration successful! You can now <a href='login.php'>login</a>.";
        } else {
            $errors[] = "Something went wrong. Please try again later.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Register - SB Admin</title>
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="bg-primary">
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-7">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Create Account</h3></div>
                                    <div class="card-body">
                                        <?php if (!empty($errors)): ?>
                                            <div class="alert alert-danger"><?= implode('<br>', $errors) ?></div>
                                        <?php endif; ?>
                                        <?php if ($success_message): ?>
                                            <div class="alert alert-success"><?= $success_message ?></div>
                                        <?php else: ?>
                                        <form action="register.php" method="post">
                                            <div class="form-floating mb-3"><input class="form-control" id="inputName" name="name" type="text" placeholder="Enter your name" required /><label for="inputName">Full name</label></div>
                                            <div class="form-floating mb-3"><input class="form-control" id="inputEmail" name="email" type="email" placeholder="name@example.com" required /><label for="inputEmail">Email address</label></div>
                                            <div class="form-floating mb-3"><input class="form-control" id="inputPhone" name="phone" type="tel" placeholder="Enter your phone number" required /><label for="inputPhone">Phone Number</label></div>
                                            <div class="row mb-3">
                                                <div class="col-md-6"><div class="form-floating mb-3 mb-md-0"><input class="form-control" id="inputPassword" name="password" type="password" placeholder="Create a password" required /><label for="inputPassword">Password</label></div></div>
                                                <div class="col-md-6"><div class="form-floating mb-3 mb-md-0"><input class="form-control" id="inputPasswordConfirm" name="confirm_password" type="password" placeholder="Confirm password" required /><label for="inputPasswordConfirm">Confirm Password</label></div></div>
                                            </div>
                                            <div class="mt-4 mb-0"><div class="d-grid"><button type="submit" class="btn btn-primary btn-block">Create Account</button></div></div>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-footer text-center py-3">
                                        <div class="small"><a href="login.php">Have an account? Go to login</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
    </body>
</html>