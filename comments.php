<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
//error_reporting(E_ERROR | ~E_WARNING | ~E_PARSE | ~E_NOTICE);
ini_set('display_errors', '1');

define('DB', 'DB');
define('FILE', 'FILE');
define('ASC', 'ASC');
define('DESC', 'DESC');

define('DELETE', 'Delete');

$is_comment_valid = function () {
    return !empty($_POST["email"]) && !empty($_POST["name"])  && !empty($_POST["comment"]);
};

// Get storage type
$storage = 'file';
if (!empty($_GET['storage'])) {
    $storage = strtoupper($_GET['storage']);
}

// Get order type
$order = ASC;
if (!empty($_GET["order"])) {
    if (strtoupper($_GET["order"]) === DESC) {
        $order = DESC;
    }
}

// Get storage functions
if ($storage === DB) {
    include 'comments_db.php';
} else {
    include 'comments_file.php';
}

// Process comment
if (!empty($_POST)) {
    if ($is_comment_valid()) {
        if ($validate_duplicated()) {
            $feedback_message = '<p class="feedback-error">You have already commented on this posting.</p>';
        } else if ($add_comment()) {
            $feedback_message = '<p class="feedback-success">The comment was successfully added.</p>';
        };

        // For Deleting
    } else if (!empty($_POST['deleted_entry'])) {
        if ($delete_comment($_POST['deleted_entry'])) {
            $feedback_message = '<p class="feedback-success">The comment was successfully deleted.</p>';
        };
    } else {
        $feedback_message = '<p class="feedback-error">It is not a valid comment. All fields are required.</p>';
    }
}

$comments = $get_comments();
if ($storage === FILE) {
    $comments = $order_by($comments, $order);
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Comment / Home Page</title>
    <link href='https://fonts.googleapis.com/css?family=Noto Serif' rel='stylesheet'>
    <link rel="stylesheet" href="comments.css">
</head>

<body>
    <div class="container">
        <div class="comments">
            <p class="">
                <a href="/comment/comments_home.php" class="btn">Go Home Page</a>
            </p>
            <hr />
            <h1>Add comments</h1>
            <form action="" method="POST" class="">
                <?php if (!empty($feedback_message)) { ?>
                    <?= $feedback_message ?>
                <?php } ?>
                <p class="">
                    <label for="name" class="">Name:</label>
                    <input name="name" type="text" class="">
                </p>
                <p class="">
                    <label for="email" class="">Email:</label>
                    <input name="email" type="text" class="">
                </p>
                <p class="">
                    <label for="comment" class="">Comment:</label>
                </p>
                <p class="">
                    <textarea name="comment" id="" cols="30" rows="10" class=""></textarea>
                </p>
                <p class="">
                    <input name="storage" class="hide" value="file">
                    <input type="button" class="" value="Cancel">
                    <input type="submit" class="" value="Ok">
                </p>
            </form>
            <hr />
            <h2 id="view-posting-comments" class="view-posting-comments" onclick="const p = document.getElementById('posting-comments'); p.style.display = p.style.display == 'block' ? 'none' : 'block'; this.innerText = (this.innerText == 'Comments') ? 'View posting comments' : 'Comments'; console.log(this.innerText);"> View posting comments</h2>
            <div id="posting-comments" class="hide">
                <p class="">Order by Name <a href="/comment/comments.php?storage=<?= strtolower($storage) ?>&order=asc" class="btn btn-small">Asc</a>&nbsp;&nbsp;<a href="/comment/comments.php?storage=<?= strtolower($storage) ?>&order=desc" class="btn btn-small">Desc</a></p>
                <form action="" method="POST" class="">
                    <p class="">
                        <label for="entry" class="">Entry for deleting:</label>
                        <input id="entry" name="entry" value="">
                        <input id="deleted_entry" name="deleted_entry" class="hide" value="">
                        <input type="submit" class="" value="Delete" onclick="document.getElementById('deleted_entry').value = document.getElementById(document.getElementById('entry').value ).getAttribute('name'); return true;">
                    </p>
                </form>
                <hr />
                <ul class="">
                    <?php foreach ($comments as $index => $comment) {
                    ?>
                        <li id="<?= $index + 1 ?>" name="<?= empty($comment['id']) ? $index + 1 : $comment['id'] ?>" class="">
                            <div class="index-comment">ID:<?= $index + 1 ?></div>
                            <div class="block-comment">
                               Name: <a href="mailto:<?= $comment['email'] ?>"><?= $comment['name'] ?></a><br>
                               Comment:  <span><?= $comment['comment'] ?></span>
                                <span class="timezone">&nbsp;&nbsp;<em><?= $comment['formatted'] ?></em></span>
                            </div>
                        </li>
                    <?php }
                    ?>
                </ul>
            </div>

        </div>
    </div>
</body>

</html>