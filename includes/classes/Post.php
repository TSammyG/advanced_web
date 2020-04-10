<?php

class Post
{
    private $user_obj;
    private $con;

    public function __construct($con, $user)
    {
        $this->con = $con;
        $this->user_obj = new User($con, $user);
    }

    public function submitPost($body, $user_to)
    {
        $body = strip_tags($body); // Removes HTML tags
        $body = mysqli_real_escape_string($this->con, $body); // Escapes any special characters with an apostrophe
        // that could be mistaken for a single quote, for example

        // For recognising new lines
        $body = str_replace('\r\n', '\n', $body); // Looks for a carried return followed by a new line
        $body = nl2br($body); // nl = new line, 2 = to, br = line break

        $check_empty = preg_replace('/\s+/', '', $body); //Deletes all spaces

        if ($check_empty != "") {
            //Current date and time
            $date_added = date("Y-m-d H:i:s");
            //Get username
            $added_by = $this->user_obj->getUsername();

            //IF user is on own profile, user_to is 'none'
            if ($user_to == $added_by) {
                $user_to = "none";
            }

            //Insert post
            $query = mysqli_query($this->con, "INSERT INTO posts VALUES('', '$body', '$added_by', '$user_to', 
            '$date_added', 'no', 'no', '0')");
            $returned_id = mysqli_insert_id($this->con);

            //Insert notification

            //Update post count for user
            $num_post = $this->user_obj->getNumPosts();
            //Increases post count by 1
            $num_post++;
            $update_query = mysqli_query($this->con, "UPDATE users SET num_post='$num_post' 
            WHERE username='$added_by'");
        }
    }
}

/**/