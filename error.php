<?php

$err = htmlspecialchars(stripslashes(trim($_GET["code"])));

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">

</head>

<body">

    <div class="topnav">
        <a class="active" href="index.php">Home</a>
        <a href="about.php">About</a>
    </div>
    <h1 style="text-align:center;padding-top:30px;"> Error Page </h1>
    <?php 

        if ($err == "1") {
            echo "<h2 style=\"text-align:center;padding-top:30px;\"> ORM Timed Out, try again later.</h1>";
        }
        elseif ($err == "2"){
            echo "<h2 style=\"text-align:center;padding-top:30px;\"> The introduced domain does not have a valid A record.</h1>";
        }
        elseif ($err == "3"){
            echo "<h2 style=\"text-align:center;padding-top:30px;\"> The introduced domain does not exist in our database.</h1>";
        }
        else {
            echo $err;
            echo "<h2 style=\"text-align:center;padding-top:30px;\"> Oops Something went wrong.</h1>";
        }
    ?>
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>

</body>

</html>