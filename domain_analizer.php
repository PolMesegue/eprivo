<?php
require_once "config.php";

$domain = htmlspecialchars(stripslashes(trim($_POST["domain_url"])));
$update_domain = htmlspecialchars(stripslashes(trim($_POST["update_domain"])));

if ($update_domain == "yes") {

    $sql = "update domain set priority = 1 where name = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $param_domain);
        $param_domain = $domain;
        if (mysqli_stmt_execute($stmt)) {
        } else {
            header('Location: ./error.php?code=4');
            mysqli_stmt_close($stmt);
            mysqli_close($link);
            die();
        }
    }
    mysqli_stmt_close($stmt);
    mysqli_close($link);
} else {
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
                } else {
                    mysqli_stmt_close($stmt);
                    $hashed = hash('sha256', $domain);
                    $sql = "INSERT INTO domain (hash, name, priority) VALUES (?, ?, 1)";

                    if ($stmt = mysqli_prepare($link, $sql)) {
                        mysqli_stmt_bind_param($stmt, "ss", $param_hashed, $param_domain);
                        $param_hashed = $hashed;
                        $param_domain = $domain;
                        if (mysqli_stmt_execute($stmt)) {
                        } else {
                            header('Location: ./error.php?code=4');
                            mysqli_stmt_close($stmt);
                            mysqli_close($link);
                            die();
                        }
                        mysqli_stmt_close($stmt);
                        mysqli_close($link);
                    }
                }
            }
        }
    } else {
        header('Location: ./error.php?code=2');
        die();
    }
    mysqli_stmt_close($stmt);
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ePrivacy Observatory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">

</head>

<body style="cursor:wait">
    <div class="topnav">
        <a class="active" href="index.php">Home</a>
        <a href="statistics.php">Statistics</a>
       <!-- <a href="about.php">About</a> -->
    </div>

    <div class="wrapperlvl">
        <h1 style="text-align:center;padding-top:30px;"><?php echo $domain; ?> is being analized by ORM, please wait</h1>
        <h2 style="text-align:center;padding-top:30px;"> The average waiting time is 180 seconds.</h2>

        <p style="text-align:center;padding-top:30px;"><img src="771.svg"></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>

</body>

<script>
    let startTime, endTime, mseconds;
    var str = "<?php echo $domain; ?>";
    startTime = new Date();


    var myvar = setInterval(poll_domain, 5000);


    function poll_domain() {
        var xhttp;

        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                if (this.responseText == "0") {
                    window.location.replace("domain.php?domain_url=" + str);
                } else {
                    endTime = new Date();
                    if ((endTime - startTime) > 300000) {
                        window.location.replace("error.php?code=1");
                    }
                }
            }
        };
        xhttp.open("POST", "bd_poller.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("domain_url=" + str);

    }
</script>

</html>