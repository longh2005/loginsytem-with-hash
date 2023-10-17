<?php

// check data back-end
if (isset($_POST['submit'])) {
    // connect to database
    include_once 'db.inc.php';

    // except error special char 
    $name = mysqli_real_escape_string($conn, $_POST['user_name']);
    $email = mysqli_real_escape_string($conn, $_POST['user_email']);
    $uid = mysqli_real_escape_string($conn, $_POST['user_uid']);
    $pwd = mysqli_real_escape_string($conn, $_POST['user_pwd']);

    // Error handlers
    // check for empty fields
    if (empty($name) || empty($email) || empty($uid) || empty($pwd)) {
        header("Location: ../signup.html?signup=empty");
        exit();
    } else {
        // check if input characters are valid
        // if (!preg_match("/^[a-zA-Z]*$/", $name)) {
        //     header("Location: ../signup.html?signup=invalid");
        //     exit();
        // } else {
            // check if eamil is valid
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                header("Location: ../signup.html?signup=email");
                exit();
            } else {
                // check exist account
                $sql = "SELECT * FROM users WHERE user_uid='$uid'";
                $result = mysqli_query($conn, $sql);
                $resultCheck = mysqli_num_rows($result);

                if ($resultCheck > 0) {
                    header("Location: ../signup.html?signup=usertaken");
                    exit();
                } else {
                    // Hashing the password 
                    $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
                    // Insert the user into the database
                    $sql = "INSERT INTO users (user_name, user_email, user_uid, user_pwd) VALUES ('$name', '$email', '$uid', '$hashedPwd');";
                    mysqli_query($conn, $sql);

                    // GET data user just register
                    $sql = "SELECT * FROM users WHERE user_uid='$uid' AND user_name='$name'";
                    $result = mysqli_query($conn, $sql);
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $userid = $row['user_id'];
                            // $sql = "INSERT INTO profileimg (userid, status) VALUES ('$userid', 1);";
                            // mysqli_query($conn, $sql);
                        }
                    } else {
                        echo "You have an error!";
                    }


                    header("Location: ../index.html?signup=success");
                    exit();
                }
            }
        }
} else {
    header("Location: ../signup.html?signup=error");
}

?>