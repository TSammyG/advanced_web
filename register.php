<?php

// NB: ensure the order here is correct. In this case, register must be before login
// because login will be accessing the $error_array variable before it's reached register_handler.php
require 'config/config.php';
require 'includes/form_handlers/register_handler.php';
require 'includes/form_handlers/login_handler.php';


?>

<html>
<head>
    <title>Welcome!</title>
    <link rel="stylesheet" type="text/css" href="assets/css/register_style.css"> <!-- CSS being called here -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script> <!-- Jquery has been added here -->
    <script src="assets/js/register.js"></script> <!-- Jquery is being called here -->


</head>

<body>

<?php


if(isset($_POST['register_button'])) {
    echo '
    <script>
    
    $(document).ready(function()
    {
       $("#first").hide(); 
       $("#second").show(); 
    });
    
    </script>
    ';
}

?>
<!-- Importing font -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,600;1,400;1,600&display=swap%27');
</style>

<div class="wrapper">


    <div class="login_box">
        <div class="login_header">
            <h1>Entente</h1>
            Below you may register or login!
        </div>

        <div id="first">
            <form action="register.php" method="POST">
                <input type="email" name="log_email" placeholder="Email Address" value="<?php
                if (isset($_SESSION['log_email'])) {
                    echo $_SESSION['log_email'];
                }
                ?>" required><br>
                <input type="password" name="log_password" placeholder="Password"><br>
                <input type="submit" name="login_button" value="Login">
                <?php if (in_array("Email or password was incorrect<br>", $error_array)) echo "Email or password was incorrect<br>"; ?>
                <br>
                <a href="#" id="signup" class="signup"> Need an account? Register here!</a>
            </form>
        </div>

        <!--The form that will send off user data. Action is the page that the data from this form will be sent to. -->
        <div id="second">
            <form action="register.php" method="POST">
                <!--name is the reference, placeholder is what will display in the box initially. Required means the text field must
                be filled.
                -->
                <!-- NB: PHP Session tags here save text already input into boxes if there's an error, e.g wrong password -->
                <input type="text" name="reg_fname" placeholder="First Name" value="<?php
                if (isset($_SESSION['reg_fname'])) {
                    echo $_SESSION['reg_fname'];
                }
                ?>" required>
                <br>
                <?php

                if (in_array("First name must be between 2 and 25 characters<br>", $error_array)) echo "First name must be between 2 and 25 characters<br>";

                ?>


                <input type="text" name="reg_lname" placeholder="Last Name" value="<?php
                if (isset($_SESSION['reg_lname'])) {
                    echo $_SESSION['reg_lname'];
                }
                ?>" required>
                <br>
                <?php

                if (in_array("Last name must be between 2 and 25 characters<br>", $error_array)) echo "Last name must be between 2 and 25 characters<br>";

                ?>

                <input type="email" name="reg_email" placeholder="Email" value="<?php
                if (isset($_SESSION['reg_email'])) {
                    echo $_SESSION['reg_email'];
                }
                ?>" required>
                <br>

                <input type="email" name="reg_email2" placeholder="Confirm Email" value="<?php
                if (isset($_SESSION['reg_email2'])) {
                    echo $_SESSION['reg_email2'];
                }
                ?>" required>
                <br>
                <?php if (in_array("This email is already in use<br>", $error_array)) echo "This email is already in use<br>";
                else if (in_array("Invalid email format<br>", $error_array)) echo "Invalid email format<br>";
                else if (in_array("Emails don't match<br>", $error_array)) echo "Emails don't match<br>"; ?>


                <input type="password" name="reg_password" placeholder="Password" required>
                <br>
                <input type="password" name="reg_password2" placeholder="Confirm Password" required>
                <br>
                <?php if (in_array("Passwords do not match<br>", $error_array)) echo "Passwords do not match<br>";
                else if (in_array("Your password can only include uppercase and lowercase letters (a-z, A-Z) and numbers (0-9)<br>", $error_array)) echo "Your password can only include uppercase and lowercase letters (a-z, A-Z) and numbers (0-9)<br>";
                else if (in_array("Your password must be between 5 and 30 characters<br>", $error_array)) echo "Your password must be between 5 and 30 characters<br>"; ?>

                <!--The button that will send off the information -->
                <input type="submit" name="register_button" value="Register">

                <?php if (in_array("<span style='color: #14c800;'> Registration complete! You're ready to log in.</span><br>", $error_array)) echo "<span style='color: #14c800;'> Registration complete! You're ready to log in.</span><br>"; ?>
                <br>
                <a href="#" id="signin" class="signin"> Already have an account? Log in here!</a>


            </form>
        </div>


    </div>


</div>

</body>
</html>
