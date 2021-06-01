<?php
require_once "config.php";

$domain = trim($_GET['domain_url']);

$sql = "select tracking.id, count(tracking.id), tracking.name from tracking JOIN url_tracking ON tracking.id = url_tracking.tracking_id JOIN url ON url.id = url_tracking.url_id JOIN domain_url ON domain_url.url_id = url.id JOIN domain ON domain.id = domain_url.domain_id WHERE domain.name = ? GROUP BY tracking.id";

//echo "<h3> Tracking information for $domain</h3>";

$data_text = '[{"area": "Session cookies", "value": 0},{"area": "Long-living cookies", "value": 0}, {"area": "Very long-living cookies", "value": 0}, {"area": "JavaScript cookies", "value": 0}, {"area": "Third-party cookies", "value": 0}, {"area": "Tracking cookies", "value": 0}, {"area": "Font fingerprinting", "value": 0}, {"area": "Canvas fingerprinting (small)", "value": 0}, {"area": "Canvas fingerprinting (big)", "value": 0}, {"area": "Mouse fingerprinting", "value": 0}] ';
$data_text = json_decode($data_text, true);
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $param_domain);

    $param_domain = $domain;

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $id, $count, $name);
        while (mysqli_stmt_fetch($stmt)) {
            foreach($data_text as $key => $value) { 
                if($value["area"] == $name) {
                    $data_text[$key]["value"] = $count;
                   
                }
                
            }
            
        }
    }

    mysqli_stmt_close($stmt);
}


mysqli_close($link);

//echo $data_text;
echo json_encode($data_text);
