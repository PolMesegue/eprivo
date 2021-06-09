<?php
require_once "config.php";
$domain = htmlspecialchars(stripslashes(trim($_GET["domain_url"])));

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
$tmp_id = -1;
$typetocolor = ["main_frame" => 0, "stylesheet" => 1, "script" => 2, "image" => 3, "other" => 4, "font" => 5, "xmlhttprequest" => 6, "media" => 7, "sub_frame" => 8, "beacon" => 9, "websocket" => 10, "object" => 11, "csp_report" => 12];
$trackings = [];

$sql = "select url.id, url.url ,domain_url.initiator_frame, url.type, url.server_ip, url.security_info, tracking.name, domain_url.third_party FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON url.id = domain_url.url_id LEFT JOIN resource ON resource.id = url.resource_id LEFT JOIN url_tracking ON url_tracking.url_id = url.id LEFT JOIN tracking ON tracking.id = url_tracking.tracking_id WHERE domain.name = ? UNION SELECT url.id, url.url ,domain_url.initiator_frame, url.type, url.server_ip, url.security_info, tracking.name, domain_url.third_party FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON url.id = domain_url.url_id LEFT JOIN resource ON resource.id = url.resource_id LEFT JOIN resource_tracking ON resource_tracking.resource_id = resource.id LEFT JOIN tracking ON tracking.id = resource_tracking.tracking_id WHERE domain.name = ? ORDER BY id";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "ss", $param_domain, $param_domain);

    $param_domain = $domain;

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $id, $name, $initiator, $type, $server_ip, $security_info, $tracking_name, $third_party);
        while (mysqli_stmt_fetch($stmt)) {

            if ($tmp_id == $id) {
                if (!is_null($tracking_name)) {
                    $trackings[] = " $tracking_name";
                }
            } elseif ($tmp_id == -1) {

                $tmp_initiator = $initiator;
                $tmp_name = $name;
                if (is_null($type)) {
                    $tmp_type = "no-type";
                } else {
                    $tmp_type = $type;
                }
                if (is_null($server_ip)) {
                    $tmp_server_ip = "no-ip";
                } else {
                    $tmp_server_ip = $server_ip;
                }
                if (is_null($security_info)) {
                    $tmp_security_info = "no-security_info";
                } else {
                    $tmp_security_info = $security_info;
                }
                $tmp_id = $id;
                if (!is_null($tracking_name)) {
                    $trackings[] = " $tracking_name";
                }
                $tmp_third_party = $third_party;
            } else {

                if (!is_null($tmp_security_info)) {
                    $decoded_json = json_decode($tmp_security_info, true);
                    try {
                        $security_state = $decoded_json['state'];
                    } catch (Exception $e) {
                        $security_state = 'undefined';
                    }
                } else {
                    $security_state = 'undefined';
                }

                $is_third = ($tmp_third_party == 0) ? "First-Party" : "Third-Party";
                $count_trackings = (count($trackings) == 0) ? "Not-Tracking" : "Tracking";
                if ($count_trackings == "Not-Tracking") {
                    $trackings[] = "Not-Tracking";
                }
                $nodes[$itn++] = ['id' => $tmp_id, 'name' => $tmp_name, 'type' => $tmp_type, 'color_type' => $typetocolor[$tmp_type], 'ip' => $tmp_server_ip, 'state' => $security_state, 'tracking_type' => $trackings, 'is_tracking' => $count_trackings, 'is_third' => $is_third];

                if ($tmp_initiator != null) {
                    $links[$itl++] = ['source' => $tmp_id, 'target' => $tmp_initiator];
                }

                $tmp_name = $name;
                $tmp_initiator = $initiator;

                if (is_null($type)) {
                    $tmp_type = "no-type";
                } else {
                    $tmp_type = $type;
                }
                if (is_null($server_ip)) {
                    $tmp_server_ip = "no-ip";
                } else {
                    $tmp_server_ip = $server_ip;
                }
                if (is_null($security_info)) {
                    $tmp_security_info = "no-security_info";
                } else {
                    $tmp_security_info = $security_info;
                }
                $tmp_third_party = $third_party;
                $tmp_id = $id;
                $trackings = [];
                if (!is_null($tracking_name)) {
                    $trackings[] = " $tracking_name";
                }
            }
        }

        if (!is_null($tmp_security_info)) {
            $decoded_json = json_decode($tmp_security_info, true);
            try {
                $security_state = $decoded_json['state'];
            } catch (Exception $e) {
                $security_state = 'undefined';
            }
        } else {
            $security_state = 'undefined';
        }

        $is_third = ($tmp_third_party == 0) ? "First-Party" : "Third-Party";
        $count_trackings = (count($trackings) == 0) ? "Not-Tracking" : "Tracking";
        if ($count_trackings == "Not-Tracking") {
            $trackings[] = "Not-Tracking";
        }
        $nodes[$itn++] = ['id' => $tmp_id, 'name' => $tmp_name, 'type' => $tmp_type, 'color_type' => $typetocolor[$tmp_type], 'ip' => $tmp_server_ip, 'state' => $decoded_json["state"], 'tracking_type' => $trackings, 'is_tracking' => $count_trackings, 'is_third' => $is_third];

        if ($tmp_initiator != null) {
            $links[$itl++] = ['source' => $tmp_id, 'target' => $tmp_initiator];
        }
    }

    mysqli_stmt_close($stmt);
}

$graph = ["nodes" => $nodes, "links" => $links];

$tmpfname = tempnam("/var/www/html/data/", 'graph-');
file_put_contents($tmpfname, json_encode($graph));

$piecesGraph = explode('/data', $tmpfname);

$sql = "select SUM(count_trackings) AS intr_lvl FROM (select tracking.name, domain.name AS domain_name, 0.5 * ((10+(count(tracking.name) * tracking.intrusion_level)) - ABS (10-(count(tracking.name) * tracking.intrusion_level))) AS count_trackings FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON url.id = domain_url.url_id LEFT JOIN url_tracking ON url_tracking.url_id = url.id LEFT JOIN tracking ON tracking.id = url_tracking.tracking_id WHERE domain.name = ? GROUP BY tracking.name, domain.name, tracking.intrusion_level UNION SELECT tracking.name, domain.name, 0.5 * ((10+(count(tracking.name) * tracking.intrusion_level)) - ABS (10-(count(tracking.name) * tracking.intrusion_level))) AS count_trackings FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON url.id = domain_url.url_id JOIN resource ON resource.id = url.resource_id LEFT JOIN resource_tracking ON resource_tracking.resource_id = resource.id LEFT JOIN tracking ON tracking.id = resource_tracking.tracking_id WHERE domain.name = ? GROUP BY tracking.name, domain.name, tracking.intrusion_level) AS tracking GROUP BY domain_name";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "ss", $param_domain, $param_domain);

    $param_domain = $domain;

    if (mysqli_stmt_execute($stmt)) {

        mysqli_stmt_bind_result($stmt, $intr_lvl);
        while (mysqli_stmt_fetch($stmt)) {
        }
    }
    mysqli_stmt_close($stmt);
}




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
    <script src="https://d3js.org/d3-scale-chromatic.v1.min.js"></script>

</head>

<body onload="updateTrackingGrid('<?php echo $domain; ?>'), reloadGraph('is_tracking')">
    <div class="topnav">
        <a class="active" href="index.php">Home</a>
        <a href="about.php">About</a>
    </div>

    <div class="wrapperlvl">
        <?php
        $rest = substr($intr_lvl, 0, -2);
        if ($rest >= "40") {
            echo "<h2 style=\"text-align:center;padding-top:30px;\">Intrusion Level for $domain: <button class=\"btn-red\" disabled> $rest </button></h2>";
        } elseif ($rest >= "21") {
            echo "<h2 style=\"text-align:center;padding-top:30px;\">Intrusion Level for $domain: <button class=\"btn-orange\" disabled> $rest </button></h2>";
        } elseif ($rest > "0") {
            echo "<h2 style=\"text-align:center;padding-top:30px;\">Intrusion Level for $domain: <button class=\"btn-yellow\" disabled> $rest </button></h2>";
        } elseif ($rest == NULL) {
            echo "<h2 style=\"text-align:center;padding-top:30px;\">Intrusion Level for $domain: <button class=\"btn-green\" disabled> 0 </button></h2>";
        }

        ?>
    </div>

    <div class="grid-container-domain">

        <div class="tracking wrappertrack">
            <div id="tracking-grid">
                <svg id="svgtracking" width="650" height="255"></svg>


            </div>
        </div>
        <div class="web wrapperweb">
            <div id="web-map-grid"></div>
            <div id="web-grid">

                <table class="table table-hover table-bordered" style="table-layout: fixed; width: 100%;">
                    <th colspan="2" style="text-align: center;">Node Info <br>
                        <p id="clicknode">Click on a node to obtain information</p>
                    </th>
                    <tr>
                        <td>Url Name</td>
                        <td id="nodename" style="text-align: left;"></td>
                    </tr>>
                    <tr>
                        <td>Type</td>
                        <td id="type" style="text-align: left;"></td>
                    </tr>

                    <tr>
                        <td>Tracking</td>
                        <td id="tracking_type" style="text-align: left;"></td>
                    </tr>

                    <tr>
                        <td>Security State</td>
                        <td id="ss" style="text-align: left;"></td>
                    </tr>

                    <tr>
                        <td>IP Address</td>
                        <td id="ipadd" style="text-align: left;"></td>
                    </tr>

                </table>


            </div>
        </div>
        <div class="list">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-graph-tab" data-bs-toggle="tab" data-bs-target="#nav-graph" type="button" role="tab" aria-controls="nav-graph" aria-selected="true" onclick="showGraph()">Resources</button>
                    <button class="nav-link" id="nav-map-tab" data-bs-toggle="tab" data-bs-target="#nav-map" type="button" role="tab" aria-controls="nav-map" aria-selected="false" onclick="updateWebGrid('<?php echo $domain; ?>')">Map</button>

                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-graph" role="tabpanel" aria-labelledby="nav-graph-tab">

                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-bs-toggle="dropdown" aria-expanded="false">
                            Color By:
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                            <li><button class="dropdown-item" type="button" onclick="reloadGraph('is_tracking')">Is Tracking</button></li>
                            <li><button class="dropdown-item" type="button" onclick="reloadGraph('is_third')">Is Third Party</button></li>
                            <li><button class="dropdown-item" type="button" onclick="reloadGraph('type')">Type</button></li>

                        </ul>
                    </div>

                    <svg id="svggraph" width="960" height="600"></svg>




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
                                .attr("class", "names")
                                .attr("d", path);
                        }
                    </script>

                </div>
            </div>
        </div>
    </div>

    <script>
        function updateTrackingGrid(str) {
            var xhttp = new XMLHttpRequest();
            var data_textjs;

            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    data_textjs = this.responseText;
                }
            };

            xhttp.open("POST", "tracking_grid.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("domain_url=" + str);


            setTimeout(function() {

                data = JSON.parse(data_textjs);


                var svg = d3.select("#svgtracking"),
                    margin = {
                        top: 20,
                        right: 10,
                        bottom: 30,
                        left: 170
                    },
                    width = +svg.attr("width") - margin.left - margin.right,
                    height = +svg.attr("height") - margin.top - margin.bottom;

                var tip = d3.tip()
                    .attr('class', 'd3-tip')
                    .offset([-10, 0])
                    .html(function(d) {
                        return "<strong>Type: </strong><span class='details'>" + d.area + "<br></span>" + "<strong>Ocurrences: </strong><span class='details'>" + d.value + "</span>";
                    })

                var x = d3.scaleLinear().range([0, width]);
                var y = d3.scaleBand().range([height, 0]);

                var g = svg.append("g")
                    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

                svg.call(tip);

                x.domain([0, d3.max(data, function(d) {
                    return d.value;
                })]);
                y.domain(data.map(function(d) {
                    return d.area;
                })).padding(0.1);

                g.append("g")
                    .attr("class", "x axis")
                    .attr("transform", "translate(0," + height + ")")
                    .call(d3.axisBottom(x).ticks(2).tickFormat(function(d) {
                        return parseInt(d);
                    }).tickSizeInner([-height]));

                g.append("g")
                    .attr("class", "y axis")
                    .call(d3.axisLeft(y));

                g.selectAll(".bar")
                    .data(data)
                    .enter().append("rect")
                    .attr("class", "bar")
                    .attr("style", function(d) {
                        if (d.area == "Session cookies" || d.area == "Long-living cookies") {
                            return "fill:yellow";
                        } else if (d.area == "Mouse fingerprinting" || d.area == "Canvas fingerprinting (big)") {
                            return "fill:red";
                        } else {
                            return "fill:orange";
                        }
                    })
                    .attr("x", 0)
                    .attr("height", y.bandwidth())
                    .attr("y", function(d) {
                        return y(d.area);
                    })
                    .attr("width", function(d) {
                        return x(d.value);
                    })
                    .on("mousemove", function(d) {
                        tip.show(d);
                    })

                    .on("mouseout", function(d) {
                        tip.hide(d);
                    });

            }, 500);
        }
    </script>

    <script>
        function reloadGraph(str) {

            d3.selectAll("#svggraph > *").remove();

            var svggraph = d3.select("#svggraph"),
                width = +svggraph.attr("width"),
                height = +svggraph.attr("height");

            // var colorgraph = d3.scaleOrdinal(d3.schemeCategory20);


            var simulation = d3.forceSimulation()
                .force("link", d3.forceLink().id(function(d) {
                    return d.id;
                }))
                .force("charge", d3.forceManyBody().distanceMax(200))
                .force("center", d3.forceCenter(width / 3, height / 2));

            d3.json(<?php echo "\"/data" . $piecesGraph[1] . "\""; ?>, function(error, graph) {
                if (error) throw error;

                var link = svggraph.append("g")
                    .attr("class", "links")
                    .selectAll("line")
                    .data(graph.links)
                    .enter().append("line")
                    .attr("stroke-width", function(d) {
                        return Math.sqrt(2);
                    });

                var node = svggraph.append("g")
                    .attr("class", "nodes")
                    .selectAll("g")
                    .data(graph.nodes)
                    .enter().append("g")

                if (str == 'type') {


                    var svglegend = d3.select("#svggraph")

                    // create a list of keys
                    var keys = ["main_frame", "image", "stylesheet", "beacon", "script", "font", "xmlhttprequest", "websocket", "sub_frame", "object", "media", "other", "csp_report"];

                    // Usually you have a color scale in your chart already
                    var color = d3.scaleOrdinal()
                        .domain(keys)
                        .range(d3.schemeCategory20);

                    // Add one dot in the legend for each name.
                    svglegend.selectAll("mydots")
                        .data(keys)
                        .enter()
                        .append("circle")
                        .attr("cx", 10)
                        .attr("cy", function(d, i) {
                            return 35 + i * 25
                        }) // 100 is where the first dot appears. 25 is the distance between dots
                        .attr("r", 7)
                        .style("fill", function(d) {
                            return color(d)
                        })

                    // Add one dot in the legend for each name.
                    svglegend.selectAll("mylabels")
                        .data(keys)
                        .enter()
                        .append("text")
                        .attr("x", 30)
                        .attr("y", function(d, i) {
                            return 40 + i * 25
                        }) // 100 is where the first dot appears. 25 is the distance between dots
                        .style("fill", function(d) {
                            return color(d)
                        })
                        .text(function(d) {
                            return d
                        })
                        .attr("text-anchor", "left")
                        .style("alignment-baseline", "middle");

                    var circles = node.append("circle")
                        .attr("r", 5)
                        .attr("fill", function(d) {
                            return color(d.type);
                        })
                        .call(d3.drag()
                            .on("start", dragstarted)
                            .on("drag", dragged)
                            .on("end", dragended));
                } else if (str == 'is_tracking') {


                    var svglegend = d3.select("#svggraph")

                    // create a list of keys
                    var keys = ["Not-Tracking", "Tracking"];

                    // Usually you have a color scale in your chart already
                    var color = d3.scaleOrdinal()
                        .domain(keys)
                        .range(["rgb(80, 185, 255)", "rgb(255, 60, 60)"]);



                    // Add one dot in the legend for each name.
                    svglegend.selectAll("mydots")
                        .data(keys)
                        .enter()
                        .append("circle")
                        .attr("cx", 10)
                        .attr("cy", function(d, i) {
                            return 35 + i * 25
                        }) // 100 is where the first dot appears. 25 is the distance between dots
                        .attr("r", 7)
                        .style("fill", function(d) {
                            return color(d)
                        })

                    // Add one dot in the legend for each name.
                    svglegend.selectAll("mylabels")
                        .data(keys)
                        .enter()
                        .append("text")
                        .attr("x", 30)
                        .attr("y", function(d, i) {
                            return 40 + i * 25
                        }) // 100 is where the first dot appears. 25 is the distance between dots
                        .style("fill", function(d) {
                            return color(d)
                        })
                        .text(function(d) {
                            return d
                        })
                        .attr("text-anchor", "left")
                        .style("alignment-baseline", "middle");

                    var circles = node.append("circle")
                        .attr("r", 5)
                        .attr("fill", function(d) {
                            return color(d.is_tracking);
                        })
                        .call(d3.drag()
                            .on("start", dragstarted)
                            .on("drag", dragged)
                            .on("end", dragended));
                } else {

                    var svglegend = d3.select("#svggraph")

                    // create a list of keys
                    var keys = ["First-Party", "Third-Party"];

                    // Usually you have a color scale in your chart already
                    var color = d3.scaleOrdinal()
                        .domain(keys)
                        .range(["rgb(80, 185, 255)", "rgb(255, 60, 60)"]);



                    // Add one dot in the legend for each name.
                    svglegend.selectAll("mydots")
                        .data(keys)
                        .enter()
                        .append("circle")
                        .attr("cx", 10)
                        .attr("cy", function(d, i) {
                            return 35 + i * 25
                        }) // 100 is where the first dot appears. 25 is the distance between dots
                        .attr("r", 7)
                        .style("fill", function(d) {
                            return color(d)
                        })

                    // Add one dot in the legend for each name.
                    svglegend.selectAll("mylabels")
                        .data(keys)
                        .enter()
                        .append("text")
                        .attr("x", 30)
                        .attr("y", function(d, i) {
                            return 40 + i * 25
                        }) // 100 is where the first dot appears. 25 is the distance between dots
                        .style("fill", function(d) {
                            return color(d)
                        })
                        .text(function(d) {
                            return d
                        })
                        .attr("text-anchor", "left")
                        .style("alignment-baseline", "middle");

                    var circles = node.append("circle")
                        .attr("r", 5)
                        .attr("fill", function(d) {
                            return color(d.is_third);
                        })
                        .call(d3.drag()
                            .on("start", dragstarted)
                            .on("drag", dragged)
                            .on("end", dragended));

                }

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

                document.getElementById("clicknode").style.display = "none";
                document.getElementById("nodename").innerHTML = d.name;
                document.getElementById("ss").innerHTML = d.state;
                document.getElementById("ipadd").innerHTML = d.ip;
                document.getElementById("type").innerHTML = d.type;
                document.getElementById("tracking_type").innerHTML = d.tracking_type;
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
        }

        function showGraph() {
            document.getElementById("web-map-grid").style.display = "none";
            document.getElementById("web-grid").style.display = "block";
        }

        function updateWebGrid(str) {
            var xhttp;

            xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("web-map-grid").style.display = "block";
                    document.getElementById("web-map-grid").innerHTML = this.responseText;
                    document.getElementById("web-grid").style.display = "none";
                }
            };
            xhttp.open("POST", "web_grid.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("domain_url=" + str);

        }
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>

</body>

</html>