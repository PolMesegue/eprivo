<?php
require_once "config.php";

$domain = trim($_POST["domain_url"]);

$sql = "select priority from domain where name = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $param_domain);

    $param_domain = $domain;

    if (mysqli_stmt_execute($stmt)) {

        mysqli_stmt_bind_result($stmt, $priority);
        if (mysqli_stmt_fetch($stmt)) {
            echo "$priority";
        }
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($link);


?>