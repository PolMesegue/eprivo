<?php

require_once "config.php";

//$sql = "select domain_name, SUM(count_trackings) AS intrusion_lvl FROM (select tracking.name, domain.name AS domain_name, 0.5 * ((10+(count(tracking.name) * tracking.intrusion_level)) - ABS (10-(count(tracking.name) * tracking.intrusion_level))) AS count_trackings FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON url.id = domain_url.url_id LEFT JOIN url_tracking ON url_tracking.url_id = url.id LEFT JOIN tracking ON tracking.id = url_tracking.tracking_id GROUP BY tracking.name, domain.name, tracking.intrusion_level UNION SELECT tracking.name, domain.name, 0.5 * ((10+(count(tracking.name) * tracking.intrusion_level)) - ABS (10-(count(tracking.name) * tracking.intrusion_level))) AS count_trackings FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON url.id = domain_url.url_id JOIN resource ON resource.id = url.resource_id LEFT JOIN resource_tracking ON resource_tracking.resource_id = resource.id LEFT JOIN tracking ON tracking.id = resource_tracking.tracking_id GROUP BY tracking.name, domain.name, tracking.intrusion_level) AS tracking GROUP BY domain_name ORDER BY intrusion_lvl DESC limit 20";
$sql = "select name, intrusion_level from domain order by intrusion_level desc limit 20";
$result = mysqli_query($link, $sql);

$pos = 1;

echo "<table class=\"table table-hover text-center\" >";
echo "<tr>";
echo "<th style=\"background: #557bce;position: sticky;top: 0px;\">Intrusion Rank</th>";
echo "<th style=\"background: #557bce;position: sticky;top: 0px;\">Domain</th>";
echo "<th style=\"background: #557bce;position: sticky;top: 0px;\"> Intrusion Level</th>";

echo "<th style=\"background: #557bce;position: sticky;top: 0px;\"> </th>";
echo "</tr>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr id = {$row['name']} onclick=\"updateTrackingGrid(this.id); updateWebGrid(this.id);\">";
    echo "<td class=\"align-middle\"> $pos </td>";
    echo "<td id = \"top$pos\" class=\"align-middle\"> {$row['name']}</td>";
    $pos++;
    $rest = $row['intrusion_level'];  
    if ($rest == "0") {
        echo "<td class=\"align-middle\"> <button class=\"btn-green\"> 0 </button></td>";
    }
    elseif ($rest >= "40") {
        echo "<td class=\"align-middle\"> <button class=\"btn-red\"> $rest </button></td>";

    }
    elseif($rest >= "21" ) {
        echo "<td class=\"align-middle\"> <button class=\"btn-orange\"> $rest </button></td>";

    }
    elseif ($rest > "0") {
        echo "<td class=\"align-middle\"> <button class=\"btn-yellow\"> $rest </button></td>";

    }
    
    echo "<td class=\"align-middle\"> <a href=\"domain.php?domain_url={$row['name']}\"><i class=\"far fa-plus-square\"></i></a></td>";
    echo "</tr>";
}



mysqli_free_result($result);

mysqli_close($link);
echo "</table>";
