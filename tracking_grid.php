<?php
require_once "config.php";

$domain = trim($_POST['domain_url']);

$sql = "select tracking.id, count(tracking.id), tracking.name from tracking JOIN url_tracking ON tracking.id = url_tracking.tracking_id JOIN url ON url.id = url_tracking.url_id JOIN domain_url ON domain_url.url_id = url.id JOIN domain ON domain.id = domain_url.domain_id WHERE domain.name = ? GROUP BY tracking.id";

//echo "<h3> Tracking information for $domain</h3>";

$data_text = '[';

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $param_domain);

    $param_domain = $domain;

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $id, $count, $name);
        while (mysqli_stmt_fetch($stmt)) {
            $data_text .= "{\"area\": \"$name \", \"value\": $count},";
            
        }
    }

    mysqli_stmt_close($stmt);
}
$data_text = substr($data_text, 0, -1);  
$data_text .= ']';

mysqli_close($link);

//echo $data_text;
echo $data_text;

?>

