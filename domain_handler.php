<?php
require_once "config.php";

$domain = htmlspecialchars(stripslashes(trim($_POST["domain_url"])));

if (checkdnsrr($domain, "A")) {

    $sql = "SELECT id FROM domain WHERE name = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $param_domain);
        $param_domain = $domain;
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) == 1) {
                header('Location: ./domain.php?domain_url=' . $domain);
                mysqli_stmt_close($stmt);
                mysqli_close($link);
                die();
            }
            else {
                mysqli_stmt_close($stmt);
                $hashed = hash('sha256', $domain);
               // $date = date("Y-m-d H:i:s");
                $sql = "INSERT INTO domain (hash, name, priority) VALUES (\"$hashed\", \"$domain\", 1)";
            
                if (mysqli_query($link, $sql)) {
                    echo "New record created successfully";
                } else {
                    echo "Error: " . $sql . "<br>" . mysqli_error($link);
                }
            
                mysqli_close($link);
            }
        }
    }
} else {
    header('Location: ./error.php');
    die();
}
