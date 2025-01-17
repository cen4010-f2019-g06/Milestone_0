<?php

include '/home/cen4010fal19_g06/public_html/checkLogin.php';
include_once '/home/cen4010fal19_g06/public_html/DBConnection.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/~cen4010fal19_g06/assets/stylesheets/main.css">
    <link rel="stylesheet" href="https://use.typekit.net/xkf2xga.css">
    <title>Event</title>
    
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-153783349-1"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-153783349-1');
    </script>

</head>

<body>
<?php
if(isset($_SESSION['adminId']))
{
    include ("/home/cen4010fal19_g06/public_html/assets/pages/admin-nav.php");
    echo'
        <header class="header-bar">
            <a href="/~cen4010fal19_g06/index.php"><img class="logo" src="/~cen4010fal19_g06/assets/images/icons/logo-admin.png"></a>
            <img src="/~cen4010fal19_g06/assets/images/buttons/admin-menu-collapsed.svg" class="menu-bttn" id="menu-closed" onclick="openAdminNav()">
        ';
}
else
{
    include ("/home/cen4010fal19_g06/public_html/assets/pages/nav.php");
    echo'
    <header class="header-bar">
        <a href="/~cen4010fal19_g06/index.php"><img class="logo" src="/~cen4010fal19_g06/assets/images/icons/logo-user.png"></a>
        <img src="/~cen4010fal19_g06/assets/images/buttons/menu-collapsed.svg" class="menu-bttn" id="menu-closed" onclick="openNav()">
        ';
}
?>

    <?php include ("/home/cen4010fal19_g06/public_html/assets/pages/searchbar.php"); ?>

</header>
<div class="horizontal-line"></div>

<?php

//postId must be set in the URL querystring
//for example: http://lamp.cse.fau.edu/~cen4010fal19_g06/viewPostTest.php?postId=5
//$_GET['postId']=5
if(isset($_GET['postId']))
{
    $postId = $_GET['postId'];
    $event = $db->getEventById($postId);
    $content = $event->getContent();
    $title = $event->getTitle();
    $time = $event->getTime();
    $eventDate = $event->getEventDate();
    $location = $event->getLocation();

    $statusIcon = $db->queryStatusIcon($event->getStatus());

    //Insert a new comment into DB
    if(isset($_POST['commentContent']))
    {
        $commentSectionId = $db->queryCommentSectionId($postId, "event");
        $comment = new Comment($_POST['commentContent'], $_SESSION['userId'], $commentSectionId);
        $db->insertComment($comment);
    }

    //Get comment section for the post
    $commentSection = $db->getCommentSection($postId, "event");
    $commentCount = $commentSection->getCount();

    echo '
<div class="center-element">

    <div class="user-container">
        <img class="icon" src="/~cen4010fal19_g06/assets/images/icons/default-user-icon.svg">
    </div>

    <div class="main-post-container">
            <span id="watch-msg" style="font-size: 14px; color: var(--success-green); display: inline-block; float: left; visibility: hidden;">
                You are now watching this post</span><br>

        <img class="status-line" src=' . $statusIcon . '>

        <div class="tile-block">
            <h3>'. $title. '</h3>
        </div>



        <div class="indicators">
            <time class="post-date">'. $time .'</time>
            <input class="post-watch" id ="watch-icon" type="image" src="/~cen4010fal19_g06/assets/images/icons/indicators/not-watching.svg" name="watching" onfocus="nowWatching(watch-icon, watch-msg)">
            <input class="post-flag" type="image" src="/~cen4010fal19_g06/assets/images/icons/flag.svg" name="flag">
        </div>

        <div class="scroll-container">
            <div class="scroll-area">
                <div class="post-content">
                    <p>' . $content . '</p>
                </div>
                <br>
                <div class="post-content">
                    <p>Event Date: ' . $eventDate . '</p> <br>
                    <p>Event Location: ' . $location .' </p>
                </div>
            </div>
            <div class="scrollbar-track">
                <img class="down-arrow" src="/~cen4010fal19_g06/assets/images/icons/scroll-arrow-down.svg">
                <div class="scrollbar-thumb"></div>
                <img class="up-arrow" src="/~cen4010fal19_g06/assets/images/icons/scroll-arrow-up.svg">
            </div>
        </div>
    </div>';

    echo '
        <section class="comment-container">
        <form action="" method="post">
            <input name="commentContent" class="post-comment" placeholder="Add a public comment...">
            <img class="comment-user-icon" src="/~cen4010fal19_g06/assets/images/icons/default-user-icon.svg">
            <input type="submit" value="Add Comment">
        </form>
        <h4 class="comment-header">Comments (' . $commentCount . ')</h4>
        
        <div class="comment-box__small">
        ';

    foreach($commentSection->getComments() as $comment)
    {
        $user = $db->getUserById($comment->getPostedByUserId());
        echo '
            <p class="comment-user"><span style="color: var(--faded-navy)">' . $user['username'] . '</span> says:</p>
            <p class="comment-content__small">' . $comment->getContent() . '</p>
            <time class="comment-date">'. $comment->getTime() .'</time>
            ';
    }
    echo'
        </div>
    </section>

</div>
';
}
?>



<!-- include google's jquery hosted library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="/~cen4010fal19_g06/assets/scripts/main.js"></script>
</body></html>