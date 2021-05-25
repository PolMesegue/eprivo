<?php
require_once "config.php";
$domain = trim($_GET["domain_url"]);

$codes = json_decode(file_get_contents('http://country.io/iso3.json'), true);

$sql = "SELECT url.country_code AS alpha2, count(url.country_code) AS reps FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON domain_url.url_id = url.id WHERE domain.name = ? AND url.country_code IS NOT NULL GROUP BY url.country_code";

$tmpfname = tempnam("/var/www/html/data/", 'orm');
$handle = fopen($tmpfname, "w");
fwrite($handle, "code,reps".PHP_EOL);

#echo $tmpfname;

if ($stmt = mysqli_prepare($link, $sql)) {
	mysqli_stmt_bind_param($stmt, "s", $param_domain);

	$param_domain = $domain;

	if (mysqli_stmt_execute($stmt)) {

		mysqli_stmt_bind_result($stmt, $alpha2, $reps);
		while (mysqli_stmt_fetch($stmt)) {
            $alpha3 = $codes[$alpha2];
            fwrite($handle, "$alpha3,$reps".PHP_EOL);
            
		}
	} 
    mysqli_stmt_close($stmt);
}


fclose($handle);

mysqli_close($link);

$pieces = explode('/data', $tmpfname);

?>

<?php echo "\"/data" . $pieces[1] . "\""; ?>

<!DOCTYPE html>
<meta charset="utf-8">
<style>
  .countries {
    fill: none;
    stroke: #fff;
    stroke-linejoin: round;
  }
  .legendThreshold {
      font-size: 12px;
      font-family: sans-serif;
  }
  .caption {
      fill: #000;
      text-anchor: start;
      font-weight: bold;
  }
</style>
<svg width="960" height="600"></svg>
<script src="https://d3js.org/d3.v4.min.js"></script>
<script src="https://d3js.org/d3-scale-chromatic.v1.min.js"></script>
<script src="https://d3js.org/d3-geo-projection.v2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3-legend/2.24.0/d3-legend.js"></script>


<script>
// The svg
var svg = d3.select("svg"),
    width = +svg.attr("width"),
    height = +svg.attr("height");

// Map and projection
var path = d3.geoPath();
var projection = d3.geoNaturalEarth()
    .scale(width / 2 / Math.PI)
    .translate([width / 2, height / 2])
var path = d3.geoPath()
    .projection(projection);

// Data and color scale
var data = d3.map();
var colorScheme = d3.schemeBlues[6];
colorScheme.unshift("#eee")
var colorScale = d3.scaleThreshold()
    .domain([1, 4, 8, 13, 26, 31])
    .range(colorScheme);

// Legend
var g = svg.append("g")
    .attr("class", "legendThreshold")
    .attr("transform", "translate(20,20)");
g.append("text")
    .attr("class", "caption")
    .attr("x", 0)
    .attr("y", -6)
    .text("Resources");
var labels = ['0', '1-3', '4-7', '8-12', '13-25', '26-30', '> 30'];
var legend = d3.legendColor()
    .labels(function (d) { return labels[d.i]; })
    .shapePadding(4)
    .scale(colorScale);
svg.select(".legendThreshold")
    .call(legend);

// Load external data and boot

d3.queue()
    .defer(d3.json, "http://enjalot.github.io/wwsd/data/world/world-110m.geojson")
    .defer(d3.csv, <?php echo "\"/data" . $pieces[1] . "\""; ?> , function(d) { data.set(d.code, +d.reps); })
    .await(ready);

function ready(error, topo) {
    if (error) throw error;

    // Draw the map
    svg.append("g")
        .attr("class", "countries")
        .selectAll("path")
        .data(topo.features)
        .enter().append("path")
            .attr("fill", function (d){
                // Pull data for this country
                d.total = data.get(d.id) || 0;
                // Set the color
                return colorScale(d.total);
            })
            .attr("d", path);
}
</script>


<?php

#unlink($tmpfname);

?>