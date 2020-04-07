<?php
/* This include calls the code from header.php, letting us concentrate on the code unique to the page */
include("includes/header.php");
/*session_destroy();*/ // This code can be used to log the user out whenever they refresh their page
?>
<div class="user_details column">
    <!-- By separating them with a space we can have 2 classes -->
    <a href="#">
        <img src="<?php echo $user['profile_pic']; ?>" />
    </a>
    <div class="user_details_left_right">
        <a href="#">
            <?php
            echo $user['first_name'] . " " . $user['last_name'];
            ?>
            <br>
        </a>
        <?php
        echo "Posts: " . $user['num_post'] . "<br>";
        echo "Likes: " . $user['num_like'];
        ?>
    </div>
</div>
<div class="main_column column">
    <form class="post_form" action="index.php" method="POST">
        <textarea name="post_text" id="post_text" placeholder="What's on your mind?"></textarea>
        <input type="submit" name="post" id="post_button" value="Post">
        <hr>
    </form>
</div>


</div> <!-- This is opened in header.php -->
</body>

</html>