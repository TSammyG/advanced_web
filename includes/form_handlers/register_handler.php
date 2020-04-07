<?php
/* Declaring variables to prevent errors */
$fname = ""; //First Name
$lname = ""; //Last Name
$em = ""; //email
$em2 = ""; //email confirm
$password = ""; //password
$password2 = ""; //password confirm
$date = ""; //Sign up date
$error_array = array(); //Holds error messages
//NB: It's important that the messages in the arrays in the if statement validating the emails are what is stored verbatim in the "needle and haystack" error messages displayed to the user.

/* This means if the button created below has been pressed, start handling the form  */
if (isset($_POST['register_button'])) {

//Registration form values
// Strip tags is a security measure to remove HTML tags.
//This means "store the value in fname that was sent from the POST form"

// First Name
$fname = strip_tags($_POST['reg_fname']); // Removes HTML tags
$fname = str_replace(' ', '', $fname);     //This removes any spaces put accidentally
$fname = ucfirst(strtolower($fname));   //Capitalises the first letter and lower-cases the rest
$_SESSION['reg_fname'] = $fname; // This stores the first name into session variables

// Last Name
$lname = strip_tags($_POST['reg_lname']); // Removes HTML tags
$lname = str_replace(' ', '', $lname);     //This removes any spaces put accidentally
$lname = ucfirst(strtolower($lname));   //Capitalises the first letter and lower-cases the rest
$_SESSION['reg_lname'] = $lname;

// Email
$em = strip_tags($_POST['reg_email']); // Removes HTML tags
$em = str_replace(' ', '', $em);     //This removes any spaces put accidentally
$em = ucfirst(strtolower($em));   //Capitalises the first letter and lower-cases the rest
$_SESSION['reg_email'] = $em;

// Email Confirmation
$em2 = strip_tags($_POST['reg_email2']); // Removes HTML tags
$em2 = str_replace(' ', '', $em2);     //This removes any spaces put accidentally
$em2 = ucfirst(strtolower($em2));   //Capitalises the first letter and lower-cases the rest
$_SESSION['reg_email2'] = $em2;

// Password
// In the case of a password, we obviously don't want to remove spaces or capitalisation. Only HTML tags are removed.
$password = strip_tags($_POST['reg_password']); // Removes HTML tags
$password2 = strip_tags($_POST['reg_password2']); // Removes HTML tags

$date = date("Y-m-d"); //This gets the current date

//If emails are the same
if ($em == $em2) {

//AND if emails are valid format
if (filter_var($em, FILTER_VALIDATE_EMAIL)) {

//Set the email equal to the validated form of the email
$em = filter_var($em, FILTER_VALIDATE_EMAIL);

//Check if email already exists
$e_check = mysqli_query($con, "SELECT email FROM users WHERE email='$em'");

//Count the number of rows returned
$num_rows = mysqli_num_rows($e_check);

// If the number of rows returned is greater than zero, the email is already in use.
if($num_rows > 0) {
array_push($error_array, "This email is already in use<br>");
}


} else {
// Gives message if the format is invalid
array_push($error_array,  "Invalid email format<br>");
}


} else {
// Gives message the emails don't match
array_push($error_array,  "Emails don't match<br>");
}
}

//If number of name characters is more than 25, give error message

if(strlen($fname) > 25 || strlen($fname) < 2) {
array_push($error_array,  "First name must be between 2 and 25 characters<br>");
}

if(strlen($lname) > 25 || strlen($lname) < 2) {
array_push($error_array,  "Last name must be between 2 and 25 characters<br>");
}

// If passwords don't match, let them now
if($password != $password2) {
array_push($error_array,  "Passwords do not match<br>");
}

//Checks to ensure the password only includes letters and numbers
else {
if(preg_match('/[^A-Za-z0-9]/', $password)) {
array_push($error_array,  "Your password can only include uppercase and lowercase letters (a-z, A-Z) and numbers (0-9)<br>");
}
}

//Checks to ensure the password is between 5 and 30 characters
if(strlen($password > 30 || strlen($password) < 5)) {
array_push($error_array,  "Your password must be between 5 and 30 characters<br>");
}

if(empty($error_array)) {
$password = md5($password); //Encrypt password before sending to database

//Generate username by concatenating first name and last name
$username = strtolower($fname . "_" . $lname);
//This checks to see if anyone within our database has this username
$check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");

$i = 0;
//if username exists, add a number to the username
//This lets there be more than 1 "John Smith" on a system by making JohnSmith_1 e.g
while(mysqli_num_rows($check_username_query) !=0) {
$i++; //Add 1 to i
$username = $username . "_" . $i;
$check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");
}

//Profile picture assignment
$rand = rand(1,2); // A random number between 1 and 2
$profile_pic = "assets/images/profile_pics/defaults/default_image.png";
if($rand == 1)
$profile_pic = "assets/images/profile_pics/defaults/default_image.png";
else if($rand == 2)
$profile_pic = "assets/images/profile_pics/defaults/default_image2.png";

$query = mysqli_query($con, "INSERT INTO users VALUES ('', '$fname', '$lname', '$username', '$em', '$password', '$date', '$profile_pic', '0', '0', 'no', ',')");

array_push($error_array, "<span style='color: #14c800;'> Registration complete! You're ready to log in.</span><br>");
//Clear session variables
$_SESSION['reg_fname'] = "";
$_SESSION['reg_lname'] = "";
$_SESSION['reg_email'] = "";
$_SESSION['reg_email2'] = "";
}
?>