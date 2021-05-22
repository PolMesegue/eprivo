<?php


require_once "config.php";


$sql = "select alexa_rank, name, update_timestamp from domain where id <= 10";
$result = mysqli_query($link, $sql);

echo "<table class=\"table table-striped table-hover text-center\">";
echo "<tr>";
echo "<th>#</th>";
echo "<th>Domain</th>";
echo "<th>Intrusion Level</th>";
echo "<th> </th>";
echo "</tr>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td> {$row['alexa_rank']} </td>";
    echo "<td> {$row['name']}</td>";
    echo "<td> {$row['update_timestamp']} </td>";
    echo "<td> <a href=\"domain.php?domain_url={$row['name']}\"><i class=\"far fa-plus-square\"></i></a></td>";
    echo "</tr>";
}

mysqli_free_result($result);

mysqli_close($link);
echo "</table>";

?>