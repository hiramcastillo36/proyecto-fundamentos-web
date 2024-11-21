<?php
    include_once '../bd/CAD.php';

    if (isset($_POST['email']) && isset($_POST['password'])) {
        $cad = new CAD();
        $result = $cad->signUp($_POST['email'], $_POST['password']);

        if ($result) {
            header('Location: ../index.php');
        }
    }

    unset($_POST['email']);
    unset($_POST['password']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BYTE Y PIXEL - Blog</title>
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=SF+Pro+Display:wght@500&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=New+York+Extra+Large:wght@700&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=SF+Mono:wght@400&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=New+York+Small:wght@400;500&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=SF+Pro+Text:wght@400;500;800&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="script" href="script.js">
</head>
<body>
    <div class="main-container">
    <div class="container-login" id="container">
        <div class="form-container">
            <?php if (isset($result) && !$result) { ?>
                <div>
                    <p>There was an error creating the account</p>
                </div>
            <?php } ?>
            <form action="signup.php" method="POST">
                <h1>Sign Up</h1>
                <input type="email" placeholder="Email" name="email">
                <input type="password" placeholder="Password" name="password">
                <button id="login">Sign up</button>
            </form>
            <p>
                You have an account? <a href="login.html" id="create-account">Sign Up</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>
