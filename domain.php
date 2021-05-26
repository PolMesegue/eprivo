<?php
require_once "config.php";
$domain = trim($_GET["domain_url"]);

$codes = json_decode(file_get_contents('http://country.io/iso3.json'), true);

$sql = "SELECT url.country_code AS alpha2, count(url.country_code) AS reps FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON domain_url.url_id = url.id WHERE domain.name = ? AND url.country_code IS NOT NULL GROUP BY url.country_code";

$tmpfname = tempnam("/var/www/html/data/", 'orm');
$handle = fopen($tmpfname, "w");
fwrite($handle, "id,reps" . PHP_EOL);


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

<style>
    .names {
        fill: none;
        stroke: #fff;
        stroke-linejoin: round;
    }

    /* Tooltip CSS */
    .d3-tip {
        line-height: 1.5;
        font-weight: 400;
        font-family: "avenir next", Arial, sans-serif;
        padding: 6px;
        background: rgba(0, 0, 0, 0.6);
        color: #FFA500;
        border-radius: 1px;
        pointer-events: none;
    }

    /* Creates a small triangle extender for the tooltip */
    .d3-tip:after {
        box-sizing: border-box;
        display: inline;
        font-size: 8px;
        width: 100%;
        line-height: 1.5;
        color: rgba(0, 0, 0, 0.6);
        position: absolute;
        pointer-events: none;

    }

    /* Northward tooltips */
    .d3-tip.n:after {
        content: "\25BC";
        margin: -1px 0 0 0;
        top: 100%;
        left: 0;
        text-align: center;
    }

    /* Eastward tooltips */
    .d3-tip.e:after {
        content: "\25C0";
        margin: -4px 0 0 0;
        top: 50%;
        left: -8px;
    }

    /* Southward tooltips */
    .d3-tip.s:after {
        content: "\25B2";
        margin: 0 0 1px 0;
        top: -8px;
        left: 0;
        text-align: center;
    }

    /* Westward tooltips */
    .d3-tip.w:after {
        content: "\25B6";
        margin: -4px 0 0 -1px;
        top: 50%;
        left: 100%;
    }

    /*    text{
      pointer-events:none;
    }*/

    .details {
        color: white;
    }
</style>


<head>
    <meta charset="UTF-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ePrivacy Observatory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/682550c010.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <script src="https://d3js.org/d3.v4.min.js"></script>
    <script src="https://d3js.org/queue.v1.min.js"></script>
    <script src="https://d3js.org/topojson.v1.min.js"></script>
    <script src="d3-tip.js"></script>
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
                    <svg width="920" height="600"> </svg>

                    <script>
                        var format = d3.format(",");

                        // Set tooltips
                        var tip = d3.tip()
                            .attr('class', 'd3-tip')
                            .offset([-10, 0])
                            .html(function(d) {
                                return "<strong>Country: </strong><span class='details'>" + d.properties.name + "<br></span>" + "<strong>Reps: </strong><span class='details'>" + format(d.reps) + "</span>";
                            })

                        var margin = {
                                top: 50,
                                right: 0,
                                bottom: 0,
                                left: 200
                            },
                            width = 920 - margin.left - margin.right,
                            height = 600 - margin.top - margin.bottom;

                        var color = d3.scaleThreshold()
                            .domain([1, 4, 8, 13, 26, 31])
                            .range(["rgb(247,251,255)", "rgb(222,235,247)", "rgb(198,219,239)", "rgb(158,202,225)", "rgb(107,174,214)", "rgb(66,146,198)", "rgb(33,113,181)"]);

                        var path = d3.geoPath();

                        var svg = d3.select("svg")
                            //.append("svg")
                            .attr("width", width)
                            .attr("height", height)
                            .append('g')
                            .attr('class', 'map');

                        var projection = d3.geoMercator()
                            .scale(100)
                            //.center([0, 20])
                            .translate([width / 2, height / 1.5]);

                        var path = d3.geoPath().projection(projection);

                        svg.call(tip);

                        queue()
                            .defer(d3.json, "http://enjalot.github.io/wwsd/data/world/world-110m.geojson")
                            .defer(d3.csv, <?php echo "\"/data" . $pieces[1] . "\""; ?>)
                            .await(ready);

                        function ready(error, data, reps) {
                            var populationById = {};

                            reps.forEach(function(d) {
                                populationById[d.id] = +d.reps;
                            });
                            data.features.forEach(function(d) {

                                d.reps = populationById[d.id]

                                if (d.reps === undefined) {
                                    d.reps = 0;
                                }

                            });

                            svg.append("g")
                                .attr("class", "countries")
                                .selectAll("path")
                                .data(data.features)
                                .enter().append("path")
                                .attr("d", path)
                                .style("fill", function(d) {
                                    return color(populationById[d.id]);
                                })
                                .style('stroke', 'white')
                                .style('stroke-width', 1.5)
                                .style("opacity", 0.8)
                                // tooltips
                                .style("stroke", "white")
                                .style('stroke-width', 0.3)
                                .on('mouseover', function(d) {
                                    tip.show(d);

                                    d3.select(this)
                                        .style("opacity", 1)
                                        .style("stroke", "white")
                                        .style("stroke-width", 3);
                                })
                                .on('mouseout', function(d) {
                                    tip.hide(d);

                                    d3.select(this)
                                        .style("opacity", 0.8)
                                        .style("stroke", "white")
                                        .style("stroke-width", 0.3);
                                });

                            svg.append("path")
                                .datum(topojson.mesh(data.features, function(a, b) {
                                    return a.id !== b.id;
                                }))
                                // .datum(topojson.mesh(data.features, function(a, b) { return a !== b; }))
                                .attr("class", "names")
                                .attr("d", path);
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