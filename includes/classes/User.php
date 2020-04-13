<?php

/**
 * An object representing a user on the website
 */
class User
{
    private $_user;
    private $_con;

    /**
     * Constructor
     * 
     * @param mysqli|false $con  The connection to the database
     * @param string       $user A string containing the username of the user
     */
    public function __construct($con, $user)
    {
        $this->_con = $con;
        $query_string = <<<EOQ
        SELECT * 
        FROM users 
            WHERE username='$user'
        EOQ;

        $user_details_query = mysqli_query($con, $query_string);
        $this->_user = mysqli_fetch_array($user_details_query);
    }

    /**
     * Gets the username of the user
     * 
     * @return string
     */
    public function getUsername()
    {
        return $this->_user['username'];
    }

    /**
     * Gets the number of posts made by the user
     * 
     * @return int
     */
    public function getNumPosts()
    {
        $username = $this->_user['username'];
        $query_string = <<<EOQ
        SELECT num_posts 
        FROM users 
            WHERE username='$username'
        EOQ;
        $query = mysqli_query($this->_con, $query_string);
        $row = mysqli_fetch_array($query);

        return $row['num_posts'];
    }

    /**
     * Gets the full name of the user
     * 
     * @return string
     */
    public function getFirstAndLastName()
    {
        $username = $this->_user['username'];
        $query_string = <<<EOQ
            SELECT first_name, last_name 
            FROM users 
                WHERE username='$username'
        EOQ;
        $query = mysqli_query($this->_con, $query_string);
        $row = mysqli_fetch_array($query);

        return $row['first_name'] . " " . $row['last_name'];
    }

    /**
     * Checks if the user account has been closed
     * 
     * @return bool
     */
    public function isClosed()
    {
        $username = $this->_user['username'];
        $query_string = <<<EOQ
            SELECT user_closed
            FROM users
                WHERE username='$username'
        EOQ;
        $query = mysqli_query($this->_con, $query_string);
        $row = mysqli_fetch_array($query);

        return $row['user_closed'] == 'yes';
    }
}
