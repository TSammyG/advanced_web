<?php

if (isset($_POST['login_button'])) {

    $email = filter_var($_POST['log_email'], FILTER_SANITIZE_EMAIL); // This sanitises the email

    $_SESSION['log_email'] = $email; //Store email into session variable
    $password = md5($_POST['log_password']); //Get password

    // Checks the users table for an email and password that matches. If there's a match, you've typed in the right details
    $check_database_query = mysqli_query($con, "SELECT * FROM users WHERE email='$email' AND password='$password'");
    $check_login_query = mysqli_num_rows($check_database_query);

    // If there is 1 row returned, they've logged in successfully
    if ($check_login_query == 1) {
        // This allows us to access the query's results
        $row = mysqli_fetch_array($check_database_query);
        // By accessing this row, we can see the aforementioned query's results
        $username = $row['username'];

        // Find the email they entered and see if it is closed
        $user_closed_query = mysqli_query($con, "SELECT * FROM users WHERE email='$email' AND user_closed = 'yes'");
        // If the email is found i.e 1 result is found
        if (mysqli_num_rows($user_closed_query) == 1) {
            //Close the account
            $reopen_account = mysqli_query($con, "UPDATE users SET user_closed='no' WHERE email='$email'");
        }


        // This creates a new session variable called username, setting it to the value of the user's username
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit();
    } else {
        array_push($error_array, "Email or password was incorrect<br>");
    }
}
