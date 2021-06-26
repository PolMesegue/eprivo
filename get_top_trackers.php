<?php

require_once "config.php";

$sql = "select url.url AS name, count(domain_url.url_id) AS num from domain_url JOIN url ON url.id = domain_url.url_id GROUP BY url_id ORDER BY num DESC limit 20;";
$result = mysqli_query($link, $sql);

$pos = 1;

echo "<table class=\"table table-hover text-center\" >";
echo "<tr>";
echo "<th style=\"background: #557bce;position: sticky;top: 0px;\">Rank</th>";
echo "<th style=\"background: #557bce;position: sticky;top: 0px;\">URL</th>";
echo "<th style=\"background: #557bce;position: sticky;top: 0px;\">Appearances</th>";

echo "<th style=\"background: #557bce;position: sticky;top: 0px;\"> </th>";
echo "</tr>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td class=\"align-middle\"> $pos </td>";
    echo "<td class=\"align-middle\"> {$row['name']} </td>";
    echo "<td class=\"align-middle\"> {$row['num']} </td>";
    $pos++;
    echo "</tr>";
}



mysqli_free_result($result);

mysqli_close($link);
echo "</table>";
