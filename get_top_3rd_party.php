<?php


require_once "config.php";


$sql = "SELECT domain_tracks AS domain_name, num, intrusion_lvl  FROM  (select domain_name AS domain_tracks, SUM(count_trackings) AS intrusion_lvl FROM (select tracking.name, domain.name AS domain_name, 0.5 * ((10+(count(tracking.name) * tracking.intrusion_level)) - ABS (10-(count(tracking.name) * tracking.intrusion_level))) AS count_trackings FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON url.id = domain_url.url_id LEFT JOIN url_tracking ON url_tracking.url_id = url.id LEFT JOIN tracking ON tracking.id = url_tracking.tracking_id GROUP BY tracking.name, domain.name, tracking.intrusion_level UNION SELECT tracking.name, domain.name, 0.5 * ((10+(count(tracking.name) * tracking.intrusion_level)) - ABS (10-(count(tracking.name) * tracking.intrusion_level))) AS count_trackings FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON url.id = domain_url.url_id JOIN resource ON resource.id = url.resource_id LEFT JOIN resource_tracking ON resource_tracking.resource_id = resource.id LEFT JOIN tracking ON tracking.id = resource_tracking.tracking_id GROUP BY tracking.name, domain.name, tracking.intrusion_level) AS tracking GROUP BY domain_tracks) AS num_trackings  JOIN    (SELECT domain.name AS domain_third, COUNT(domain_url.third_party) AS num FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON domain_url.url_id = url.id WHERE domain_url.third_party = 1 GROUP BY domain_third) AS num_thirds  WHERE  num_trackings.domain_tracks = num_thirds.domain_third ORDER BY num DESC limit 20";
$result = mysqli_query($link, $sql);

echo "<table class=\"table table-hover text-center\">";
echo "<tr>";
echo "<th style=\"background: #557bce;position: sticky;top: 0px;\">Third Parties</th>";
echo "<th style=\"background: #557bce;position: sticky;top: 0px;\">Domain</th>";
echo "<th style=\"background: #557bce;position: sticky;top: 0px;\">Intrusion Level</th>";
echo "<th style=\"background: #557bce;position: sticky;top: 0px;\"> </th>";
echo "</tr>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr id = {$row['domain_name']} onclick=\"updateTrackingGrid(this.id); updateWebGrid(this.id);\">";
    echo "<td class=\"align-middle\"> {$row['num']} </td>";
    echo "<td class=\"align-middle\"> {$row['domain_name']}</td>";
    $rest = substr($row['intrusion_lvl'], 0, -2);  
    if ($rest >= "40") {
        echo "<td class=\"align-middle\"> <button class=\"btn-red\" disabled> $rest </button></td>";

    }
    elseif($rest >= "21" ) {
        echo "<td class=\"align-middle\"> <button class=\"btn-orange\" disabled> $rest </button></td>";

    }
    elseif ($rest > "0") {
        echo "<td class=\"align-middle\"> <button class=\"btn-yellow\" disabled> $rest </button></td>";

    }
    elseif ($row['intrusion_lvl'] == NULL) {
        echo "<td class=\"align-middle\"> <button class=\"btn-green\" disabled> 0 </button></td>";
    }
    echo "<td class=\"align-middle\"> <a href=\"domain.php?domain_url={$row['domain_name']}\"><i class=\"far fa-plus-square\"></i></a></td>";
    echo "</tr>";
}

mysqli_free_result($result);

mysqli_close($link);
echo "</table>";

?>

