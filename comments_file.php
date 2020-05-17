<?php
$filename = 'comments.txt';

$add_comment = function () use ($filename) {
    $content = file_get_contents($filename);

    $datetime = new DateTime();
    $NY_time = new DateTimeZone('America/New_York');
    $NY_time = new DateTimeZone('EST');
    $datetime->setTimezone($NY_time);
    $last_visited = $datetime->format('H:i M d, Y') . ' EST';

    $content .= $_POST['email'] . '|' . $_POST['name'] . '|' . $_POST['comment'] . '|' . $last_visited . "\n";

    if (file_put_contents($filename, $content)) {
        return true;
    }

    return false; // error when saving comment
};

$get_comments = function () use ($filename) {
    $lines = file($filename, FILE_IGNORE_NEW_LINES);
    $comments = [];
    foreach ($lines as $index => $line) {
        $values = explode('|', $line);
        $comments[] = [
            "email" => $values[0],
            "name" => $values[1],
            "comment" => $values[2],
            "formatted" => $values[3]
        ];
    }

    return $comments;
};

$order_by = function ($comments, $order) {
    $custom_sort_asc = function ($a, $b) {
        return $a['name'] > $b['name'];
    };

    $custom_sort_desc = function ($a, $b) {
        return $a['name'] < $b['name'];
    };

    // Sort the multidimensional array
    $custom_sort = 'custom_sort_asc';
    if ($order === DESC) {
        $custom_sort = 'custom_sort_desc';
    }

    usort($comments, $$custom_sort);

    // Define the custom sort function

    return $comments;
};

$delete_comment = function ($entry) use ($order, $get_comments, $order_by, $filename) {
    $comments = $get_comments();
    $comments = $order_by($comments, $order);

    // Delete
    unset($comments[$entry - 1]);

    $content = '';
    foreach ($comments as $index => $comment) {
        $content .= $comment['email'] . '|' . $comment['name'] . '|' . $comment['comment'] . '|' . $comment['formatted'] . "\n";
    }

    if (file_put_contents($filename, $content)) {
        return true;
    }

    return false; // error when saving comment
};

$validate_duplicated = function () use ($get_comments) {
    $comments = $get_comments();

    $duplicated = false;
    foreach ($comments as $key => $comment) {
        if (
            $comment['email'] === $_POST['email'] ||
            $comment['name'] === $_POST['name']  ||
            $comment['comment'] === $_POST['comment']
        ) {
            $duplicated = true;
            break;
        }
    }

    return $duplicated;
};
