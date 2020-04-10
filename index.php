<?php
/* This include calls the code from header.php, letting us concentrate on the code unique to the page */
include("includes/header.php");
include("includes/classes/User.php");
include("includes/classes/Post.php");
/*session_destroy();*/ // This code can be used to log the user out whenever they refresh their page


if (isset($_POST['post'])) {
    $post = new Post($con, $userLoggedIn);
    $post->submitPost($_POST['post_text'], 'none');
    header("Location: index.php"); // If a post has been made, stops it from being posted twice if page is refreshed
}

?>
<div class="user_details column">
    <!-- By separating them with a space we can have 2 classes -->
    <a href="<?php echo $userLoggedIn; ?>">
        <img src="<?php echo $user['profile_pic']; ?>" />
    </a>
    <div class="user_details_left_right">
        <a href="<?php echo $userLoggedIn; ?>">
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

    <?php


    ?>

</div>


</div> <!-- This is opened in header.php -->
</body>

</html>