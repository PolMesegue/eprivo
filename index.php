<?php


require_once "config.php";


$sql = "select alexa_rank, name, update_timestamp from domain where id < 50";
$result = mysqli_query($link, $sql);

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ORM</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

    <style>
        .grid-container {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            grid-template-rows: 1fr 1fr 1fr 1fr 1fr;
            gap: 0px 0px;
            grid-template-areas:
                "search search search search"
                "list list tracking tracking"
                "list list tracking tracking"
                "list list web web"
                "list list web web";
        }

        .search {
            grid-area: search;
        }

        .tracking {
            grid-area: tracking;
        }

        .web {
            grid-area: web;
        }

        .list {
            grid-area: list;
            margin: auto;
			width: 90%;
			height: 450px;
			overflow: auto;
        }

        body {
            font: 14px sans-serif;
            text-align: center;
            background-image: url(wp.jpg);
            background-color: #cccccc;
        }

        table.tbl {
            background: rgba(255, 255, 255, 0.9);
        }
    </style>
</head>

<body>
    <div class="grid-container">
        <div class="search">
            <form action="/domain_handler.php">
                <label for="domain_url">Seach</label><br>
                <input type="text" id="domain_url" name="domain_url"><br><br>
                <input type="submit" value="Submit">
            </form>
        </div>
        <div class="tracking">
            hello tracking</div>
        <div class="web">
            hello web </div>
        <div class="list">
            <table class="tbl w3-table w3-striped w3-large w3-centered">
                <tr>
                    <th>#</th>
                    <th>Domain</th>
                    <th>Intrusion Level</th>
                </tr>
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td> {$row['alexa_rank']} </td>";
                    echo "<td> <a href=\"domain.php?domain_url={$row['name']}\">{$row['name']}</a></td>";
                    echo "<td> {$row['update_timestamp']} </td>";
                    echo "</tr>";
                }

                mysqli_free_result($result);

                mysqli_close($link);
                ?>
            </table>
        </div>
    </div>
</body>
</html>