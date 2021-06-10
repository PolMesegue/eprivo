<?php

require_once "config.php";

$sql = "SELECT id FROM domain";
if ($stmt = mysqli_prepare($link, $sql)) {
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);
        $num_domains = mysqli_stmt_num_rows($stmt);
    }    
}

mysqli_stmt_close($stmt);

$sql = "Select count(intrusion_lvl) AS num FROM (SELECT domain_name, SUM(count_trackings) AS intrusion_lvl FROM (select tracking.name, domain.name AS domain_name, count(tracking.name) AS count_trackings FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON url.id = domain_url.url_id LEFT JOIN url_tracking ON url_tracking.url_id = url.id LEFT JOIN tracking ON tracking.id = url_tracking.tracking_id GROUP BY tracking.name, domain.name, tracking.intrusion_level UNION SELECT tracking.name, domain.name, count(tracking.name) AS count_trackings FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON url.id = domain_url.url_id JOIN resource ON resource.id = url.resource_id LEFT JOIN resource_tracking ON resource_tracking.resource_id = resource.id LEFT JOIN tracking ON tracking.id = resource_tracking.tracking_id GROUP BY tracking.name, domain.name, tracking.intrusion_level) AS tracking  GROUP BY domain_name) AS counts WHERE intrusion_lvl = 0";
$result = mysqli_query($link, $sql);
if ($row = mysqli_fetch_assoc($result)) {
    $not_tracking = $row['num'];
}

$tracking = $num_domains - $row['num'];

mysqli_stmt_close($stmt);

mysqli_close($link);

echo "{\"Tracking\": $tracking, \"Not-Tracking\": $not_tracking}";