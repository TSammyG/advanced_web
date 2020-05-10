<?php
/* This include calls the code from header.php, letting us concentrate on the code unique to the page */
include("includes/header.php");

/*session_destroy();*/ // This code can be used to log the user out whenever they refresh their page
if (isset($_GET['profile_username'])) {
    $username = $_GET['profile_username'];
    $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
    $user_array = mysqli_fetch_array($user_details_query);

    $num_friends = (substr_count($user_array['friend_array'], ",")) - 1;
}

if (isset($_POST['remove_friend'])) {
    $user = new User($con, $userLoggedIn);
    $user->removeFriend($username);
}

if (isset($_POST['add_friend'])) {
    $user = new User($con, $userLoggedIn);
    $user->sendRequest($username);
}

if (isset($_POST['respond_request'])) {
    header("Location: requests.php");
}
?>

<div class="profile_left">
    <img src="<?php echo $user_array['profile_pic']; ?>"
</div>

<div class="profile_info">
    <p>
    <p><?php echo "Posts: " . $user_array['num_post']; ?><p>
    <p><?php echo "Likes: " . $user_array['num_like']; ?><p>
    <p><?php echo "Friends: " . $num_friends; ?><p>
    </p>
</div>

<form action="<?php echo $username; ?>" method="POST">
    <?php $profile_user_obj = new User($con, $username);
    if ($profile_user_obj->isClosed()) {
        header("Location: user_closed.php");
    }

    $logged_in_user_obj = new User($con, $userLoggedIn);

    if ($userLoggedIn != $username) {
        if ($logged_in_user_obj->isFriend($username)) {
            echo '<input type="submit" name="remove_friend" class="danger" value="Remove Friend"><br>';
        } else if ($logged_in_user_obj->didReceiveRequest($username)) {
            echo '<input type="submit" name="respond_request" class="warning" value="Accept Request"><br>';
        } else if ($logged_in_user_obj->didSendRequest($username)) {
            echo '<input type="submit" name="" class="default" value="Request Sent"><br>';
        } else {
            echo '<input type="submit" name="add_friend" class="success" value="Add Friend"><br>';
        }
    }


    ?>

</form>
<input type="submit" class="deep_blue" data-toggle="modal" data-target="#post_form" value="Create Post">

</div>

<div class="main_column column">
    <div class="posts_area"></div>
    <img id="loading" src="assets/images/icons/loading.gif">


</div>


<!-- Modal -->
<div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">What's on your mind?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p>Posts made here can be viewed by you and your friends!</p>
                <form class="profile_post" action="" method="POST">
                    <div class="form-group">
                        <textarea class="form-control" name="post_body"></textarea>
                        <input type="hidden" name="user_from" value="<?php echo $userLoggedIn; ?>"
                        <input type="hidden" name="user_to" value="<?php echo $username; ?>"

                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" name="post_button" id="submit_profile_post">Post</button>
            </div>
        </div>
    </div>
</div>

<script>
    var userLoggedIn = '<?php echo $userLoggedIn; ?>';
    var profileUsername = '<?php echo $username; ?>';

    $(document).ready(function() {

        $('#loading').show();

        //Original ajax request for loading first posts
        $.ajax({
            url: "includes/handlers/ajax_load_profile_posts.php",
            type: "POST",
            data: "page=1&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
            cache:false,

            success: function(data) {
                $('#loading').hide();
                $('.posts_area').html(data);
            }
        });

        $(window).scroll(function() {
            var height = $('.posts_area').height(); //Div containing posts
            var scroll_top = $(this).scrollTop();
            var page = $('.posts_area').find('.nextPage').val();
            var noMorePosts = $('.posts_area').find('.noMorePosts').val();

            if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
                $('#loading').show();

                var ajaxReq = $.ajax({
                    url: "includes/handlers/ajax_load_profile_posts.php",
                    type: "POST",
                    data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
                    cache:false,

                    success: function(response) {
                        $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage
                        $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage

                        $('#loading').hide();
                        $('.posts_area').append(response);
                    }
                });

            } //End if

            return false;

        }); //End (window).scroll(function())


    });

</script>


</div> <!-- This is opened in header.php -->
</body>

</html>