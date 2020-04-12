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
        $check_empty = preg_replace('/\s+/', '', $body); //Deletes all spaces

        // For recognising new lines
        $body = str_replace('\r\n', '\n', $body); // Looks for a carried return followed by a new line
        $body = nl2br($body); // nl = new line, 2 = to, br = line break


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
            mysqli_query($this->con, "INSERT INTO posts VALUES('', '$body', '$added_by', '$user_to', 
                '$date_added', 'no', 'no', '0')");
            mysqli_insert_id($this->con);

            //Insert notification

            //Update post count for user
            $num_post = $this->user_obj->getNumPosts();
            //Increases post count by 1
            $num_post++;
            mysqli_query($this->con, "UPDATE users SET num_post='$num_post' 
                WHERE username='$added_by'");
        }
    }

    private function pluraliseMessage($interval, $unit_of_time)
    {
        $pluralisedMessage = $unit_of_time;

        if ($interval != 1) {
            $pluralisedMessage .= 's';
        }

        return $pluralisedMessage;
    }

    private function createSingleTimeMessage($interval, $unit_of_time)
    {
        return "{$interval} {$this->pluraliseMessage($interval,$unit_of_time)} ago";
    }

    private function getTimeMessage($interval)
    {
        $time_message = "";
        if ($interval->y > 0) {
            $time_message = $this->createSingleTimeMessage($interval->y, "year");
        } else if ($interval->m > 0) {
            $days = $this->createSingleTimeMessage($interval->d, "day");
            $time_message = $this->createSingleTimeMessage($interval->m, "month") . $days;
        } else if ($interval->d > 0) {
            $time_message = $interval->d == 1 ? "Yesterday" : $this->createSingleTimeMessage($interval->d, "day");
        } else if ($interval->h > 0) {
            $time_message = $this->createSingleTimeMessage($interval->h, "hour");
        } else if ($interval->i > 0) {
            $time_message = $this->createSingleTimeMessage($interval->i, "minute");
        } else if ($interval->s > 0) {
            $time_message = $this->createSingleTimeMessage($interval->s, "second");
        } else {
            $time_message = "Just now";
        }
        return $time_message;
    }

    public function loadPostsFriends($data, $limit)
    {

        $page = $data['page'];
        $userLoggedIn = $this->user_obj->getUsername();

        if ($page == 1)
            $start = 0;
        else
            $start = ($page - 1) * $limit;

        $post_str = ""; // String to return
        $data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC");

        if (mysqli_num_rows($data_query) > 0) {

            $num_iterations = 0; // No. of results checked, though not necessarily posted
            $count = 1;

            while ($row = mysqli_fetch_array($data_query)) {
                $id = $row['id'];
                $body = $row['body'];
                $added_by = $row['added_by'];
                $date_time = $row['date_added'];

                //Prepare user_to string so it can be included even if not posted to a user

                if ($row['user_to'] == "none") {
                    $user_to = "";
                } else {
                    $user_to_obj = new User($this->con, $row['user_to']);
                    $user_to_name = $user_to_obj->getFirstAndLastName();
                    $user_to = "to <a href='{$row['user_to']}'>$user_to_name</a>";
                }

                //Check if user who posted, has their account closed
                $added_by_obj = new User($this->con, $row['added_by']);
                if ($added_by_obj->isClosed()) {
                    continue;
                }

                if ($num_iterations++ < $start)
                    continue;


                //Once 10 posts have been loaded, break
                if ($count > $limit) {
                    break;
                } else {
                    $count++;
                }


                $user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic 
                                                                FROM users WHERE username='$added_by'");
                $user_row = mysqli_fetch_array($user_details_query);

                $first_name = $user_row['first_name'];
                $last_name = $user_row['last_name'];
                $profile_pic = $user_row['profile_pic'];

                //Timeframe
                $date_time_now = date("Y-m-d H:i:s");
                $start_date = new DateTime($date_time); //Time of post
                $end_date = new DateTime($date_time_now); //Current time
                $interval = $start_date->diff($end_date); //Difference between dates
                $time_message = $this->getTimeMessage($interval);

                $post_str .= "<div class='status_post'>
                                <div class='post_profile_pic'>
                                    <img src='$profile_pic' width='50'>
                                </div>

                                <div class='posted_by' style='color:#ACACAC;'>
                                    <a href='$added_by'> 
                                        $first_name $last_name 
                                    </a> 
                                    $user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
                                </div>

                                <div id='post_body'>
                                    $body
                                    <br>
                                </div>
                            </div>
                            </hr>";
            }

            if ($count > $limit)
                $post_str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type='hidden' class='noMorePosts' value='false'>";
            else
                $post_str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: centre;'> No more posts to show! </p>";
        }
        echo $post_str;
    }
}
