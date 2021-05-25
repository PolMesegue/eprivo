<?php
require_once "config.php";
$domain = trim($_GET["domain_url"]);

$codes = json_decode(file_get_contents('http://country.io/iso3.json'), true);

$sql = "SELECT url.country_code AS alpha2, count(url.country_code) AS reps FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON domain_url.url_id = url.id WHERE domain.name = ? AND url.country_code IS NOT NULL GROUP BY url.country_code";

$tmpfname = tempnam("/var/www/html/data/", 'orm');
$handle = fopen($tmpfname, "w");
fwrite($handle, "code,reps" . PHP_EOL);


if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $param_domain);

    $param_domain = $domain;

    if (mysqli_stmt_execute($stmt)) {

        mysqli_stmt_bind_result($stmt, $alpha2, $reps);
        while (mysqli_stmt_fetch($stmt)) {
            $alpha3 = $codes[$alpha2];
            fwrite($handle, "$alpha3,$reps" . PHP_EOL);
        }
    }
    mysqli_stmt_close($stmt);
}


fclose($handle);

mysqli_close($link);

$pieces = explode('/data', $tmpfname);

?>

<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ePrivacy Observatory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/682550c010.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">


    <script src="http://d3js.org/d3.v4.js"></script>
    <script src="https://d3js.org/d3-scale-chromatic.v1.min.js"></script>
    <script src="https://d3js.org/d3-geo-projection.v2.min.js"></script>

</head>

<body onload="showGraph()">
    <div class="grid-container">
        <div class="search">
            <div class="row justify-content-center padding">
                <div class="col-md-8 ftco-animate fadeInUp ftco-animated">
                    <form action="domain.php" class="domain-form">
                        <div class="form-group d-md-flex">
                            <input type="text" id="domain_url" name="domain_url" class="form-control px-4" placeholder="Insert domain here...">
                            <input type="submit" class="search-domain btn btn-primary px-5" value="Analize Domain">
                        </div>
                    </form>
                    <!--  <p class="domain-price text-center"><span><small>.com</small>10.75</span> <span><small>.net</small>19.90</span> <span><small>.biz</small>$5.95</span> <span><small>.gov</small>$3.95</span></p> -->
                </div>
            </div>
        </div>
        <div class="tracking">
        </div>
        <div class="web">
        </div>
        <div class="list">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-graph-tab" data-bs-toggle="tab" data-bs-target="#nav-graph" type="button" role="tab" aria-controls="nav-graph" aria-selected="true" onclick="showGraph()">Resources</button>
                    <button class="nav-link" id="nav-map-tab" data-bs-toggle="tab" data-bs-target="#nav-map" type="button" role="tab" aria-controls="nav-map" aria-selected="false">Map</button>

                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-graph" role="tabpanel" aria-labelledby="nav-graph-tab">
                </div>
                <div class="tab-pane fade" id="nav-map" role="tabpanel" aria-labelledby="nav-map-tab">
                    <svg width="600" height="450"></svg>
                    <script>
                        // The svg
                        var svg = d3.select("svg"),
                            width = +svg.attr("width"),
                            height = +svg.attr("height");

                        // Map and projection
                        var path = d3.geoPath();
                        var projection = d3.geoMercator()
                            .scale(70)
                            .center([0, 20])
                            .translate([width / 2, height / 2]);

                        // Data and color scale
                        var data = d3.map();
                        var colorScale = d3.scaleThreshold()
                            .domain([1, 4, 8, 13, 26, 31])
                            .range(d3.schemeBlues[7]);

                        // Load external data and boot
                        d3.queue()
                            .defer(d3.json, "http://enjalot.github.io/wwsd/data/world/world-110m.geojson")
                            .defer(d3.csv, <?php echo "\"/data" . $pieces[1] . "\""; ?>, function(d) {
                                data.set(d.code, +d.reps);
                            })
                            .await(ready);

                        function ready(error, topo) {

                            // Draw the map
                            svg.append("g")
                                .selectAll("path")
                                .data(topo.features)
                                .enter()
                                .append("path")
                                // draw each country
                                .attr("d", d3.geoPath()
                                    .projection(projection)
                                )
                                // set the color of each country
                                .attr("fill", function(d) {
                                    d.total = data.get(d.id) || 0;
                                    return colorScale(d.total);
                                });
                        }
                    </script>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>


    <script>
        function showGraph() {
            var xhttp = new XMLHttpRequest();
            var str = "<?php echo trim($_GET["domain_url"]); ?>";
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("nav-graph").innerHTML =
                        this.responseText;
                }
            };
            xhttp.open("GET", "graph_resources.php?domain_url=" + str, true);
            xhttp.send();
        }
    </script>



</body>

</html>