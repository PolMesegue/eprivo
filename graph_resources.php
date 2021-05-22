<?php
require_once "config.php";
$domain = trim($_GET["domain_url"]);

			$sql="SELECT url.id, url.url, url.type, url.country_code, url.is_EU ,url.resource_id FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON domain_url.url_id = url.id WHERE domain.name = ?";

			if ($stmt = mysqli_prepare($link, $sql)) {
				mysqli_stmt_bind_param($stmt, "s", $param_domain);

				$param_domain = $domain;

				if (mysqli_stmt_execute($stmt)) {
					mysqli_stmt_bind_result($stmt, $id, $url, $type, $country_code, $is_EU ,$resource_id);
					while (mysqli_stmt_fetch($stmt)) { 
						echo "<tr>";
						echo "<td> $id </td>";
						echo "<td> <a href=\"url_tracking.php?url_id=$id\">$url</a></td>";
						echo "<td> $type </td>";
						echo "<td> $country_code </td>";
						echo "<td> $is_EU </td>";
						echo "<td> <a href=\"resource.php?resource_id=$resource_id\">$resource_id</a></td>";
						echo "</tr>";
					}

				}
			}


			mysqli_stmt_close($stmt);

			mysqli_close($link);
?>