<?php
session_start();
if (isset($_POST['submit'])) {
    include 'db.inc.php';

    $uid = mysqli_real_escape_string($conn, $_POST['user_uid']);
    $pwd = mysqli_real_escape_string($conn, $_POST['user_pwd']);
    $email = mysqli_real_escape_string($conn, $_POST['user_email']);

    // Error handlers
    // Check if inputs are empty
    if (empty($email) || empty($pwd)) {
        header("Location: ../index.html?login=empty");
        exit();
    } else {
        $sql = "SELECT * FROM users WHERE user_uid='$uid' OR user_email='$uid'";
        $result = mysqli_query($conn, $sql);
        $resultCheck = mysqli_num_rows($result);
        if($resultCheck < 1) {
            header("Location: ../index.html?login=notuid");
            exit();
        } else {
            if ($row = mysqli_fetch_assoc($result)) {
                // De-hasing the password 
                $hashedPwdCheck = password_verify($pwd, $row['user_pwd']);
                if ($hashedPwdCheck == false) {
                    header("Location: ../index.html?login=errorpass");
                    exit();
                } elseif ($hashedPwdCheck == true) {
                    $_SESSION['u_id'] = $row['user_id'];
                    $_SESSION['u_name'] = $row['user_name'];
                    $_SESSION['u_email'] = $row['user_email'];
                    $_SESSION['u_uid'] = $row['user_uid'];
                    header("Location: ../index.html?login=success");
                    exit();
                }
            }
        }
    }
} else {
    header("Location: ../index.html?login=error");
}

?>