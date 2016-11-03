<?php
define('APPLICATION_LOADED', true);
require '../../inc/db.php';

$req = preg_replace("/public\/hash.*/", '', $_SERVER['REQUEST_URI']);
$link = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://' . $_SERVER['HTTP_HOST'] . $req;
if (!isset($_GET['hash']) || $_GET['hash'] == '') {
    header("Location: $link");
}

$con = new mysqli(HOST, USER, PASS, DATABASE);
if ($con->connect_error) {
    throw new Exception("MySQL connection failed: " . $con->connect_error);
    exit;
} else {
    $con->set_charset("utf8");
    $hash = trim(mysqli_real_escape_string($con, $_GET['hash']));
    $result = $con->query("SELECT title, content FROM wiki_pages WHERE hash = '$hash'");
    if ($result->num_rows == 0) {
        echo 'Not valid page!';
        exit;
    } else {
        $arr = $result->fetch_assoc();
        ?>
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="utf8">
                <title><?= $arr['title'] ?></title>
            </head>
            <body>
                <?= $arr['content'] ?>
            </body>
        </html>
        <?php
    }
}
?>