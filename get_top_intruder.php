<?php


require_once "config.php";


$sql = "select alexa_rank, name, update_timestamp from domain where id <= 20";
$result = mysqli_query($link, $sql);

echo "<table class=\"table table-hover text-center\">";
echo "<tr>";
echo "<th style=\"background: #557bce;position: sticky;top: 0px;\">#</th>";
echo "<th style=\"background: #557bce;position: sticky;top: 0px;\">Domain</th>";
echo "<th style=\"background: #557bce;position: sticky;top: 0px;\">Intrusion Level</th>";
echo "<th style=\"background: #557bce;position: sticky;top: 0px;\"> </th>";
echo "</tr>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td> {$row['alexa_rank']} </td>";
    echo "<td> <a href=\"domain.php?domain_url={$row['name']}\">{$row['name']}</a></td>";
    echo "<td> {$row['update_timestamp']} </td>";
    echo "<td><i class=\"far fa-plus-square\"></i> </td>";
    echo "</tr>";
}

mysqli_free_result($result);

mysqli_close($link);
echo "</table>";

?>