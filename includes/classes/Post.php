<?php

class Post
{
    private $_user_obj;
    private $_con;

    /**
     * Constructor
     *
     * @param mysqli|false $con The connection to the database
     * @param string $user The details of the user as a string
     */
    public function __construct($con, $user)
    {
        $this->_con = $con;
        $this->_user_obj = new User($con, $user);
    }

    /**
     * Creates a post
     *
     * @param string $body The body of the post
     * @param string $user_to The user who will receive the post
     * @return void
     */
    public function submitPost($body, $user_to)
    {
        $body = strip_tags($body); //Removes HTML tags
        $body = mysqli_real_escape_string($this->_con, $body);
        $check_empty = preg_replace('/\s+/', '', $body); //Deletes all spaces

        if ($check_empty != "") {
            //Current date and time
            $date_added = date("Y-m-d H:i:s");
            //Get username
            $added_by = $this->_user_obj->getUsername();



            //If user is on own profile, user_to is 'none'

            if ($user_to == $added_by) {
                $user_to = "none";
            } else {
                $user_to = "someone";
            }



            //insert post
            $query_string = <<<EOQ
            INSERT INTO posts 
            VALUES(
                '', '$body', '$added_by', '$user_to', '$date_added', 
                'no', 'no', '0'
            )
            EOQ;
            mysqli_query($this->_con, $query_string);
            mysqli_insert_id($this->_con);

            //Insert notification

            //Update post count for user
            $num_post = $this->_user_obj->getNumPosts();
            $num_post++;
            $query_string = <<<EOQ
            UPDATE users 
            SET num_post='$num_post' 
                WHERE username='$added_by'
            EOQ;
            mysqli_query($this->_con, $query_string);
        }
    }

    /**
     * Loads posts from all friends of user
     *
     * @param int $limit Page limit
     * @return void
     * @throws Exception
     */
    public function loadPostsFriends($limit)
    {
        $page = $_REQUEST['page'];
        //$userLoggedIn = $this->user_obj->getUsername();


        if ($page == 1) {
            $start = 0;
        } else {

            $start = ($page - 1) * $limit;

        }

        $post_body = ""; //String to return
        $user_query_string = <<<EOQ
        SELECT * 
        FROM posts 
            WHERE deleted='no' 
        ORDER BY id DESC
        EOQ;
        $data_query = mysqli_query($this->_con, $user_query_string);

        if (mysqli_num_rows($data_query) > 0) {
            // Number of results checked (not necessarily posted)
            $num_iterations = 0;
            $count = 1;

            while ($row = mysqli_fetch_array($data_query)) {
                $id = $row['id'];
                $body = $row['body'];
                $added_by = $row['added_by'];
                $date_time = $row['date_added'];

                // Prepare user_to string so it can be included
                // even if not posted to a user
                if ($row['user_to'] == "none") {
                    $user_to = "";
                } else {
                    $user_to_obj = new User($this->_con, $row['user_to']);
                    $user_to_name = $user_to_obj->getFirstAndLastName();
                    $user_to = "to <a href='{$row['user_to']}'>$user_to_name</a>";
                }

                //Check if user who posted, has their account closed
                $added_by_obj = new User($this->_con, $added_by);
                if ($added_by_obj->isClosed()) {
                    continue;
                }

                if ($num_iterations++ < $start) {
                    continue;
                }

                //Once 10 posts have been loaded, break
                if ($count > $limit) {
                    break;
                }
                $count++;

                //$userLoggedIn = $added_by();

                //if($userLoggedIn == $added_by)
                    $delete_button = "<button class='delete_button btn-danger' id='post'$id>X</button>";
                //else
                  //  $delete_button = "";

                $user_query_string = <<<EOQ
                SELECT first_name, last_name, profile_pic 
                FROM users 
                    WHERE username='$added_by'
                EOQ;
                $user_details_query = mysqli_query($this->_con, $user_query_string);
                $user_row = mysqli_fetch_array($user_details_query);
                $first_name = $user_row['first_name'];
                $last_name = $user_row['last_name'];
                $profile_pic = $user_row['profile_pic'];


                ?>
                <script>

                    function toggle<?php echo $id; ?>() {

                        const target = $(event.target);
                        if (!target.is("a")) {
                            const element = document.getElementById("toggleComment<?php echo $id?>");

                            if (element.style.display === "block")
                                element.style.display = "none";
                            else
                                element.style.display = "block";
                        }


                    }

                </script>
                <?php

                $comments_check = mysqli_query($this->_con, "SELECT * FROM comments WHERE post_id='$id'");
                $comments_check_num = mysqli_num_rows($comments_check);


                //Timeframe
                $date_time_now = date("Y-m-d H:i:s");
                $start_date = new DateTime($date_time); //Time of post
                $end_date = new DateTime($date_time_now); //Current time
                $interval = $start_date->diff($end_date); //Difference between dates
                $time_message = $this->createTimeMessage($interval);

                $post_body .= <<<EOL


<div class='status_post' onClick='toggle$id()'> 
    <div class='post_profile_pic'>
        <img src='$profile_pic' width='50'>
    </div>  
    <div class='posted_by' style='color:#ACACAC;'>
        <a href='$added_by'>
            $first_name $last_name 
        </a> 
        $user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
      $delete_button
    </div>
    <div id='post_body'>
        $body
        <br>
    </div>
</div>

<div class="newsfeedPostOptions">
    Comments($comments_check_num)&nbsp;
    
</div>
<br>




<div class="newsfeedPostOptions2">
<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
</div>



<div class='post_comment' id='toggleComment$id' style='display:none;'>
    <iframe src="comment_frame.php?post_id=$id" id="comment_iframe" frameborder="0">
    
    </iframe>
</div>


<hr>

EOL;
                ?>
                <script>

                    $.(document).ready(function () {

                     $('#post<?php echo $id; ?>').on('click', function() {
                    bootbox.confirm("Are you sure you want to delete this post?"function(result)  {

                        $.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

                            if(result)
                                location.reload();

                         });
                     });

                    });
                </script>
                <?php


            } //End while loop

            if ($count > $limit) {
                $new_page = $page + 1;
                $post_body .= <<<EOL
                    <input type='hidden' class='nextPage' value='{$new_page}'>
                    <input type='hidden' class='noMorePosts' value='false'>
                EOL;
            } else {
                $post_body .= <<<EOL
                    <input type='hidden' class='noMorePosts' value='true'>
                    <p style='text-align: center;'>No more posts to show!</p>
                EOL;
            }
        }

        echo $post_body;
    }

    public function loadProfilePosts($data, $limit)
    {
        $page = $_REQUEST['page'];
        $profileUsername = $data['profileUsername'];
        //$userLoggedIn = $this->user_obj->getUsername();


        if ($page == 1) {
            $start = 0;
        } else {

            $start = ($page - 1) * $limit;

        }

        $post_body = ""; //String to return
        $user_query_string = <<<EOQ
        SELECT * 
        FROM posts 
            WHERE deleted='no' 
        AND ((added_by='$profileUsername' AND user_to='none') OR user_to='$profileUsername')
        ORDER BY id DESC
        EOQ;
        $data_query = mysqli_query($this->_con, $user_query_string);

        if (mysqli_num_rows($data_query) > 0) {
            // Number of results checked (not necessarily posted)
            $num_iterations = 0;
            $count = 1;

            while ($row = mysqli_fetch_array($data_query)) {
                $id = $row['id'];
                $body = $row['body'];
                $added_by = $row['added_by'];
                $date_time = $row['date_added'];





                if ($num_iterations++ < $start) {
                    continue;
                }

                //Once 10 posts have been loaded, break
                if ($count > $limit) {
                    break;
                }
                $count++;

                //$userLoggedIn = $added_by();

                //if($userLoggedIn == $added_by)
                $delete_button = "<button class='delete_button btn-danger' id='post'$id>X</button>";
                //else
                //  $delete_button = "";

                $user_query_string = <<<EOQ
                SELECT first_name, last_name, profile_pic 
                FROM users 
                    WHERE username='$added_by'
                EOQ;
                $user_details_query = mysqli_query($this->_con, $user_query_string);
                $user_row = mysqli_fetch_array($user_details_query);
                $first_name = $user_row['first_name'];
                $last_name = $user_row['last_name'];
                $profile_pic = $user_row['profile_pic'];


                ?>
                <script>

                    function toggle<?php echo $id; ?>() {

                        const target = $(event.target);
                        if (!target.is("a")) {
                            const element = document.getElementById("toggleComment<?php echo $id?>");

                            if (element.style.display === "block")
                                element.style.display = "none";
                            else
                                element.style.display = "block";
                        }


                    }

                </script>
                <?php

                $comments_check = mysqli_query($this->_con, "SELECT * FROM comments WHERE post_id='$id'");
                $comments_check_num = mysqli_num_rows($comments_check);


                //Timeframe
                $date_time_now = date("Y-m-d H:i:s");
                $start_date = new DateTime($date_time); //Time of post
                $end_date = new DateTime($date_time_now); //Current time
                $interval = $start_date->diff($end_date); //Difference between dates
                $time_message = $this->createTimeMessage($interval);

                $post_body .= <<<EOL


<div class='status_post' onClick='toggle$id()'> 
    <div class='post_profile_pic'>
        <img src='$profile_pic' width='50'>
    </div>  
    <div class='posted_by' style='color:#ACACAC;'>
        <a href='$added_by'>
            $first_name $last_name 
        </a> 
        &nbsp;&nbsp;&nbsp;&nbsp;$time_message
      $delete_button
    </div>
    <div id='post_body'>
        $body
        <br>
    </div>
</div>

<div class="newsfeedPostOptions">
    Comments($comments_check_num)&nbsp;
    
</div>
<br>




<div class="newsfeedPostOptions2">
<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
</div>



<div class='post_comment' id='toggleComment$id' style='display:none;'>
    <iframe src="comment_frame.php?post_id=$id" id="comment_iframe" frameborder="0">
    
    </iframe>
</div>


<hr>

EOL;
                ?>
                <script>

                    $.(document).ready(function () {

                        $('#post<?php echo $id; ?>').on('click', function() {
                            bootbox.confirm("Are you sure you want to delete this post?"function(result)  {

                                $.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

                                if(result)
                                    location.reload();

                            });
                        });

                    });
                </script>
                <?php


            } //End while loop

            if ($count > $limit) {
                $new_page = $page + 1;
                $post_body .= <<<EOL
                    <input type='hidden' class='nextPage' value='{$new_page}'>
                    <input type='hidden' class='noMorePosts' value='false'>
                EOL;
            } else {
                $post_body .= <<<EOL
                    <input type='hidden' class='noMorePosts' value='true'>
                    <p style='text-align: center;'>No more posts to show!</p>
                EOL;
            }
        }

        echo $post_body;
    }

    /* Testing testing */
    /**
     * @param DateInterval $interval
     * @return string
     */
    private function createTimeMessage($interval)
    {
        if ($interval->y > 0) {
            $year = "year";
            if ($interval->y > 1) {
                $year .= "s";
            }
            $time_message = "{$interval->m} $year ago";
        } elseif ($interval->m > 0) {
            if ($interval->d == 0) {
                $days = "ago";
            } else if ($interval->d == 1) {
                $days = $interval->d . "day ago";
            } else {
                $days = $interval->d . "days ago";
            }

            $month = "month";
            if ($interval->m > 1) {
                $month .= "s";
            }
            $time_message = "{$interval->m} {$month} {$days}";
        } elseif ($interval->d > 0) {
            if ($interval->d > 1) {
                $time_message = "{$interval->d} days ago";
            } else {
                $time_message = "Yesterday";
            }
        } elseif ($interval->h > 0) {
            $hour = "hour";
            if ($interval->h > 1) {
                $hour .= "s";
            }
            $time_message = "{$interval->h} $hour ago";
        } elseif ($interval->i > 0) {
            $minute = "minute";
            if ($interval->i > 1) {
                $minute .= "s";
            }
            $time_message = "{$interval->i} $minute ago";
        } elseif ($interval->s >= 30) {
            $time_message = "{$interval->s} seconds ago";
        } else {
            $time_message = "Just now";
        }
        return $time_message;
    }

}
