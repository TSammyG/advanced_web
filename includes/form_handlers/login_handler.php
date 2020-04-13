<?php
if (isset($_POST['login_button'])) {
    $email = filter_var($_POST['log_email'], FILTER_SANITIZE_EMAIL); //sanitize email

    $_SESSION['log_email'] = $email; //Store email into session variable 
    $password = md5($_POST['log_password']); //Get password

    $query_string_1 = <<<EOQ
    SELECT * 
    FROM users 
        WHERE email='$email' AND password='$password'
    EOQ;
    $check_database_query = mysqli_query($con, $query_string_1);
    $check_login_query = mysqli_num_rows($check_database_query);

    if ($check_login_query == 1) {
        $row = mysqli_fetch_array($check_database_query);
        $username = $row['username'];

        $query_string_2 = <<<EOQ
        SELECT * 
        FROM users 
            WHERE email='$email' AND user_closed='yes'
        EOQ;

        $user_closed_query = mysqli_query($con, $query_string_2);
        if (mysqli_num_rows($user_closed_query) == 1) {
            $query_string_2 = <<<EOQ
            UPDATE users 
            SET user_closed='no' 
                WHERE email='$email'
            EOQ;
            $reopen_account = mysqli_query($con, $query_string_2);
        }

        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit();
    } else {
        array_push($error_array, "Email or password was incorrect<br>");
    }
}
