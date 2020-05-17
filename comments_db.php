<?php
require_once('comments_connect.php');

$add_comment = function () use ($dbConn) {
    $sql = 'INSERT INTO comment(name, email, comment) VALUES(:name, :email, :comment)';

    try {
        $query = $dbConn->prepare($sql);

        $query->bindparam(':name', $_POST['name']);
        $query->bindparam(':email', $_POST['email']);
        $query->bindparam(':comment', $_POST['comment']);
        $query->execute();

        return true;
    } catch (PDOException $e) {
    }

    return false; // error when saving comment
};

$get_comments = function () use ($dbConn, $order) {
    $sql = "SELECT *,
    DATE_FORMAT(CONVERT_TZ(last_visited, 'UTC', 'EST'), '%H:%i %M %d, %Y EST') as formatted
    FROM comment ORDER BY name " . $order;
    $query = $dbConn->prepare($sql);
    $query->execute();
    return $query->fetchAll(\PDO::FETCH_ASSOC);
};


$delete_comment = function ($id) use ($dbConn) {
    $sql = "DELETE FROM comment WHERE id=:id";

    try {
        $query = $dbConn->prepare($sql);
        $query->execute(array(':id' => $id));

        return true;
    } catch (PDOException $e) {
    }

    return false; // error when saving comment
};

$validate_duplicated = function () use ($dbConn) {
    $sql = "SELECT * FROM comment WHERE email=:email OR name=:name OR comment=:comment";

    try {
        $query = $dbConn->prepare($sql);
        $query->execute(array(
            ':name' => $_POST['name'],
            ':email' => $_POST['email'],
            ':comment' => $_POST['comment']
        ));

        $row = $query->fetch(PDO::FETCH_ASSOC);

        if (!empty($row)) {
            return true;
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    return false;
};
