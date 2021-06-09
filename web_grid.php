<?php

require_once "config.php";
$domain = trim($_POST['domain_url']);

echo "<table class=\"table table-hover table-bordered\" style=\"table-layout: fixed; width: 100%;\">";
echo "<tr><td>Domain</td><td>" . $domain . "</td></tr>";

$sql = "SELECT url.server_ip, url.country_code, url.certificate_id, url.security_info FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON domain_url.url_id = url.id WHERE domain.name = ? AND url.server_ip IS NOT NULL AND url.type =\"main_frame\"";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $param_domain);

    $param_domain = $domain;

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $server_ip, $country_code, $certificate_id, $security_info);
        while (mysqli_stmt_fetch($stmt)) {
        }
        $decoded_json = json_decode($security_info, true);
        if ($decoded_json["state"] == "secure") {
            echo "<tr><td>Using HTTPS</td><td> Yes </td></tr>";
        }
        else {
            echo "<tr><td>Using HTTPS</td><td> No </td></tr>";
        }

        echo "<tr><td>IP Address</td><td>$server_ip</td></tr>";
        echo "<tr><td>Host Country</td><td>$country_code</td></tr>";
    }

    mysqli_stmt_close($stmt);
}

$sql = "SELECT count(*) FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON domain_url.url_id = url.id WHERE domain.name = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $param_domain);

    $param_domain = $domain;

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $norows);
        while (mysqli_stmt_fetch($stmt)) {
        }
        echo "<tr><td>No. Loaded Elements</td><td>$norows</td></tr>";
    }
    mysqli_stmt_close($stmt);
}

$sql = "SELECT COUNT(domain_url.third_party) FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON domain_url.url_id = url.id WHERE domain.name = ? AND domain_url.third_party = 1";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $param_domain);

    $param_domain = $domain;

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $norows3p);
        while (mysqli_stmt_fetch($stmt)) {
        }
        echo "<tr><td>No. Third Party Elements</td><td>$norows3p</td></tr>";
    }

    mysqli_stmt_close($stmt);
}

/*
$sql = "SELECT json FROM certificate WHERE id = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $cert_id);

    $cert_id = $certificate_id;

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $json);
        while (mysqli_stmt_fetch($stmt)) {
        }
        $decoded_json = json_decode($json, true);

        #  echo "Certificate:  $json <br>";
    }

    mysqli_stmt_close($stmt);
}
*/
$sql = "SELECT url.type, count(url.type) FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON domain_url.url_id = url.id WHERE domain.name = ? GROUP BY url.type";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $param_domain);

    $param_domain = $domain;

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $type, $reps);
        while (mysqli_stmt_fetch($stmt)) {
            if ($type == "stylesheet" || $type == "script" || $type == "image" || $type == "sub_frame" ) {
                echo "<tr><td>No. $type</td><td>$reps</td></tr>";
            }
        }
    }

    mysqli_stmt_close($stmt);
}




mysqli_close($link);
echo "</table>";

?>