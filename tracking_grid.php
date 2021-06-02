<?php
require_once "config.php";

$domain = trim($_POST['domain_url']);

//$sql = "select tracking.id, count(tracking.id), tracking.name from tracking JOIN url_tracking ON tracking.id = url_tracking.tracking_id JOIN url ON url.id = url_tracking.url_id JOIN domain_url ON domain_url.url_id = url.id JOIN domain ON domain.id = domain_url.domain_id WHERE domain.name = ? GROUP BY tracking.id";

$sql = "select tracking.id, tracking.name AS tracking_name, count(tracking.name) FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON url.id = domain_url.url_id LEFT JOIN url_tracking ON url_tracking.url_id = url.id LEFT JOIN tracking ON tracking.id = url_tracking.tracking_id WHERE domain.name = ? AND tracking.id IS NOT NULL GROUP BY tracking.name, tracking.id UNION SELECT tracking.id, tracking.name AS tracking_name, count(tracking.name) FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON url.id = domain_url.url_id JOIN resource ON resource.id = url.resource_id LEFT JOIN resource_tracking ON resource_tracking.resource_id = resource.id LEFT JOIN tracking ON tracking.id = resource_tracking.tracking_id WHERE domain.name = ? AND tracking.id IS NOT NULL GROUP BY tracking.name, tracking.id";

//echo "<h3> Tracking information for $domain</h3>";

$data_text = '[{"area": "Mouse fingerprinting", "value": 0},{"area": "Canvas fingerprinting (big)", "value": 0}, {"area": "Canvas fingerprinting (small)", "value": 0}, {"area": "Font fingerprinting", "value": 0}, {"area": "Tracking cookies", "value": 0}, {"area": "Third-party cookies", "value": 0}, {"area": "JavaScript cookies", "value": 0}, {"area": "Very long-living cookies", "value": 0}, {"area": "Long-living cookies", "value": 0}, {"area": "Session cookies", "value": 0}] ';

$data_text = json_decode($data_text, true);
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "ss", $param_domain, $param_domain);

    $param_domain = $domain;

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $id, $name, $count);
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

?>

