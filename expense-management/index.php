<?php
include 'includes/db.php';
session_start();

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $res = $conn->query($sql);
    if($res->num_rows > 0){
        $user = $res->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        header('Location: dashboard.php');
    } else {
        $error = "Invalid Credentials!";
    }
}
include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card p-4 shadow">
            <h3 class="mb-3 text-center">Login</h3>
            <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <form method="POST">
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button name="login" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
