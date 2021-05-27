<?php
require_once "config.php";
$domain = trim($_GET["domain_url"]);

$codes = json_decode(file_get_contents('http://country.io/iso3.json'), true);

$sql = "SELECT url.country_code AS alpha2, count(url.country_code) AS reps FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON domain_url.url_id = url.id WHERE domain.name = ? AND url.country_code IS NOT NULL GROUP BY url.country_code";

$tmpfname = tempnam("/var/www/html/data/", 'map-');
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



$piecesMap = explode('/data', $tmpfname);




$nodes[] = [];
$links[] = [];
$itn = 0;
$itl = 0;

$typetocolor = ["main_frame" => 0, "stylesheet" => 1, "script" => 2, "image" => 3, "other" => 4, "font" => 5, "xmlhttprequest" => 6, "media" => 7, "sub_frame" => 8, "beacon" => 9, "websocket" => 10, "object" => 11, "csp_report" => 12];

$sql = "SELECT url.id, domain_url.initiator_frame, url.type, mime_type.name, url.server_ip, url.security_info FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON domain_url.url_id = url.id JOIN mime_type ON url.mime_type_id = mime_type.id WHERE domain.name = ? and url.server_ip IS NOT NULL";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $param_domain);

    $param_domain = $domain;

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $id, $initiator, $type, $mime_type, $server_ip, $security_info);
        while (mysqli_stmt_fetch($stmt)) {
            $decoded_json = json_decode($security_info, true);
            $nodes[$itn++] = ['id' => $id, 'type' => $typetocolor[$type], 'mime' => $mime_type, 'ip' => $server_ip, 'state' => $decoded_json["state"]];
            if ($initiator != null) {
                $links[$itl++] = ['source' => $id, 'target' => $initiator];
            }
        }
    }

    mysqli_stmt_close($stmt);
}

$graph = ["nodes" => $nodes, "links" => $links];

$tmpfname = tempnam("/var/www/html/data/", 'graph-');
file_put_contents($tmpfname, json_encode($graph));

$piecesGraph = explode('/data', $tmpfname);

mysqli_close($link);

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
    <script src="https://d3js.org/d3.v4.min.js"></script>
    <script src="https://d3js.org/queue.v1.min.js"></script>
    <script src="https://d3js.org/topojson.v1.min.js"></script>
    <script src="d3-tip.js"></script>

</head>

<body>
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

                    <svg id="svggraph" width="960" height="600"></svg>

                    <script>
                        var svg = d3.select("#svggraph"),
                            width = +svg.attr("width"),
                            height = +svg.attr("height");

                        var color = d3.scaleOrdinal(d3.schemeCategory20);


                        var simulation = d3.forceSimulation()
                            .force("link", d3.forceLink().id(function(d) {
                                return d.id;
                            }))
                            .force("charge", d3.forceManyBody().distanceMax(200))
                            .force("center", d3.forceCenter(width / 2, height / 2));

                        d3.json(<?php echo "\"/data" . $piecesGraph[1] . "\""; ?>, function(error, graph) {
                            if (error) throw error;

                            var link = svg.append("g")
                                .attr("class", "links")
                                .selectAll("line")
                                .data(graph.links)
                                .enter().append("line")
                                .attr("stroke-width", function(d) {
                                    return Math.sqrt(2);
                                });

                            var node = svg.append("g")
                                .attr("class", "nodes")
                                .selectAll("g")
                                .data(graph.nodes)
                                .enter().append("g")

                            var circles = node.append("circle")
                                .attr("r", 5)
                                .attr("fill", function(d) {
                                    return color(d.type);
                                })
                                .call(d3.drag()
                                    .on("start", dragstarted)
                                    .on("drag", dragged)
                                    .on("end", dragended));

                            node.append("title")
                                .text(function(d) {
                                    return d.id;
                                });

                            simulation
                                .nodes(graph.nodes)
                                .on("tick", ticked);

                            simulation.force("link")
                                .links(graph.links);

                            function ticked() {
                                link
                                    .attr("x1", function(d) {
                                        return d.source.x;
                                    })
                                    .attr("y1", function(d) {
                                        return d.source.y;
                                    })
                                    .attr("x2", function(d) {
                                        return d.target.x;
                                    })
                                    .attr("y2", function(d) {
                                        return d.target.y;
                                    });

                                node
                                    .attr("transform", function(d) {
                                        return "translate(" + d.x + "," + d.y + ")";
                                    })
                            }
                        });

                        function dragstarted(d) {
                            if (!d3.event.active) simulation.alphaTarget(0.3).restart();
                            d.fx = d.x;
                            d.fy = d.y;
                            alert(d.mime);

                        }

                        function dragged(d) {
                            d.fx = d3.event.x;
                            d.fy = d3.event.y;


                        }

                        function dragended(d) {
                            if (!d3.event.active) simulation.alphaTarget(0);
                            d.fx = null;
                            d.fy = null;
                        }
                    </script>


                </div>
                <div class="tab-pane fade" id="nav-map" role="tabpanel" aria-labelledby="nav-map-tab">
                    <svg id="svgmap" width="920" height="600"> </svg>

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
                                left: 250
                            },
                            width = 920 - margin.left - margin.right,
                            height = 600 - margin.top - margin.bottom;

                        var color = d3.scaleThreshold()
                            .domain([1, 4, 8, 13, 26, 31])
                            .range(["rgb(247,251,255)", "rgb(222,235,247)", "rgb(198,219,239)", "rgb(158,202,225)", "rgb(107,174,214)", "rgb(66,146,198)", "rgb(33,113,181)"]);

                        var path = d3.geoPath();

                        var svg = d3.select("#svgmap")
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
                            .defer(d3.csv, <?php echo "\"/data" . $piecesMap[1] . "\""; ?>)
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
                    -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>

    <!--
    <script>
        function showGraph() {
            var xhttp = new XMLHttpRequest();
            var str = "<?php echo trim($_GET["domain_url"]); ?>";
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("svggraph").innerHTML =
                        this.responseText;
                }
            };
            xhttp.open("GET", "graph.php?domain_url=" + str, true);
            xhttp.send();
        }
    </script> -->



</body>

</html>