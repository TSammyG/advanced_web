<?php
ob_start(); // Activates output buffering. Here, PHP is loaded to the broswer in sections usually. Here, it'll be passed all at once?
session_start();

// Sets the timezone
$timezone = date_default_timezone_set("Europe/London");

/* Our connection variable to the database */
$con = mysqli_connect("localhost", "root", "", "social");

/* This returns an error if it fails to connect */
if (mysqli_connect_errno()) {
    echo "Failed to connect" . mysqli_connect_errno();
}
