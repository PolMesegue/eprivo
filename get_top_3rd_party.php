<?php


require_once "config.php";


$sql = "SELECT domain.name, COUNT(domain_url.third_party) AS num, domain.update_timestamp FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON domain_url.url_id = url.id WHERE domain_url.third_party = 1 GROUP BY domain.id ORDER BY num DESC limit 20";
$result = mysqli_query($link, $sql);

echo "<table class=\"table table-hover text-center\">";
echo "<tr>";
echo "<th style=\"background: #557bce;position: sticky;top: 0px;\">Third Parties</th>";
echo "<th style=\"background: #557bce;position: sticky;top: 0px;\">Domain</th>";
echo "<th style=\"background: #557bce;position: sticky;top: 0px;\">Intrusion Level</th>";
echo "<th style=\"background: #557bce;position: sticky;top: 0px;\"> </th>";
echo "</tr>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr id = {$row['name']} onclick=\"updateTrackingGrid(this.id); updateWebGrid(this.id);\">";
    echo "<td> {$row['num']} </td>";
    echo "<td> {$row['name']}</td>";
    echo "<td> {$row['update_timestamp']} </td>";
    echo "<td> <a href=\"domain.php?domain_url={$row['name']}\"><i class=\"far fa-plus-square\"></i></a></td>";
    echo "</tr>";
}

mysqli_free_result($result);

mysqli_close($link);
echo "</table>";

?>

