<?php
require_once "config.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo "{$_GET["domain_url"]}" ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/682550c010.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">

</head>
<body>

	<form action="/domain_updater.php">
		<label for="domain_url">Update this domain</label><br>
		<input type="hidden" id="domain_url" name="domain_url" value=<?php echo "{$_GET["domain_url"]}" ?> ><br><br>
		<input type="submit" value="Submit">
	</form> 

	<br><br>

	<div class="tabl">
		<table>
			<tr>
				<th>#id</th>
				<th>Url</th>
				<th>type</th>
				<th>Country</th>
				<th>is_EU</th>
				<th>resource_id</th>

			</tr>

			<?php

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

		</table>
	</div>
</body>
</html>