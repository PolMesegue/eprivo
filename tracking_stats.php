<?php

require_once "config.php";

$sql = "select name, SUM(count_trackings) AS count FROM (select tracking.name, domain.name AS domain_name, count(tracking.name) AS count_trackings FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON url.id = domain_url.url_id LEFT JOIN url_tracking ON url_tracking.url_id = url.id LEFT JOIN tracking ON tracking.id = url_tracking.tracking_id GROUP BY tracking.name, domain.name, tracking.intrusion_level UNION SELECT tracking.name, domain.name, count(tracking.name) AS count_trackings FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON url.id = domain_url.url_id JOIN resource ON resource.id = url.resource_id LEFT JOIN resource_tracking ON resource_tracking.resource_id = resource.id LEFT JOIN tracking ON tracking.id = resource_tracking.tracking_id GROUP BY tracking.name, domain.name, tracking.intrusion_level) AS tracking WHERE name IS NOT NULL GROUP BY name ORDER BY count ASC";
$result = mysqli_query($link, $sql);


$data = '{"Very long-living cookies": 861, "Mouse fingerprinting": 28, "Third-party cookies": 512, "WebGL fingerprinting": 61, "Long-living cookies": 302, "Canvas fingerprinting (big)": 183, "JavaScript cookies": 5653, "Canvas fingerprinting (small)": 106, "Tracking cookies": 436 , "Font fingerprinting": 153, "Session cookies": 806}';
$data = json_decode($data, true);
while ($row = mysqli_fetch_assoc($result)) {
    $name = $row['name'];
    $count = $row['count'];
    $data[$name] = $count;
}





echo json_encode($data);
