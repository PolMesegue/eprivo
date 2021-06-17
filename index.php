<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ePrivacy Observatory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/682550c010.js" crossorigin="anonymous"></script>
    <script src="https://d3js.org/d3.v4.min.js"></script>
    <script src="d3-tip.js"></script>
    <script src="https://d3js.org/d3-scale-chromatic.v1.min.js"></script>

</head>


<body onload="showTableIntruder(),showTable3rdp(),showTablePopular(), get_use_of_tracking_stats(), get_tracking_stats()">

    <div class="topnav">
        <a class="active" href="index.php">Home</a>
        <a href="about.php">About</a>
    </div>

    <h1 style="text-align:center; margin-top:30px;"> ePrivacy Observatory </h1>
    <div class="charts">
        <div id="carouselExampleCaptions" class="carousel carousel-dark slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>

            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="donuts">
                        <svg id="donutIsTracking" width="1000" height="500"></svg>
                    </div>

                    <div class="carousel-caption d-none d-md-block">
                        <h5>Percentage of Domains using Tracking</h5>
                        <p> <span id="percentage">98</span>% of the <span id="total_domains"></span> analized Domains use atleast one tracking method</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="donuts">
                        <svg id="donutTrackingStats" width="1000" height="500"></svg>
                    </div>
                    <div class="carousel-caption d-none d-md-block">
                        <h5 style="color:black;">Tracking Methods Analized</h5>
                        <p style="color:black;"> Proportion of Tracking methods used</p>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>

    <h2 style="text-align:center;"> Analize a domain with the ePrivacy Observatory tool <br> and see how it's vulnerating your right to privacy </h2>

    <div class="grid-container">
        <div class="search">
            <div class="row justify-content-center padding">
                <div class="col-md-8 ftco-animate fadeInUp ftco-animated">
                    <form action="domain_analizer.php" class="domain-form" method="post">
                        <div class="form-group d-md-flex">
                            <input type="text" id="domain_url" name="domain_url" class="form-control px-4" placeholder="Insert domain here..." pattern="((?:[a-z\d](?:[a-z\d-]{0,63}[a-z\d])?|\*)\.)+[a-z\d][a-z\d-]{0,63}[a-z\d]" title="Enter a valid domain syntax" required>
                            <input type="hidden" id="update_domain" name="update_domain" value="no">
                            <input type="submit" class="search-domain btn btn-primary px-5" value="Analize Domain">
                        </div>
                    </form>
                    <p style="text-align:center;">Enter a Domain here, and if it's not analized yet, it will be queued and analized soon <br> Or you can also browse the following top-lists</p>
                </div>
            </div>
        </div>
        <div class="tracking wrappertrack">

            <svg id="svgtracking" width="650" height="255"></svg>

        </div>
        <div class="web wrapperweb">
            <div id="web-grid"></div>
        </div>
        <div class="list">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-intruder-tab" data-bs-toggle="tab" data-bs-target="#nav-intruder" type="button" role="tab" aria-controls="nav-intruder" aria-selected="true" onclick="showTableIntruder()">Top Intruder</button>
                    <button class="nav-link" id="nav-popular-tab" data-bs-toggle="tab" data-bs-target="#nav-popular" type="button" role="tab" aria-controls="nav-popular" aria-selected="false" onclick="showTablePopular()">Top Popular</button>
                    <button class="nav-link" id="nav-3rdp-tab" data-bs-toggle="tab" data-bs-target="#nav-3rdp" type="button" role="tab" aria-controls="nav-3rdp" aria-selected="false" onclick="showTable3rdp()">Top 3rd Parties</button>
                   <!-- <button class="nav-link" id="nav-3rdp-tab" data-bs-toggle="tab" data-bs-target="#nav-trackers" type="button" role="tab" aria-controls="nav-trackers" aria-selected="false" onclick="showTableTrackers()">Top Trackers</button> -->

                </div>
            </nav>
            <div class="tab-content wrapper" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-intruder" role="tabpanel" aria-labelledby="nav-intruder-tab"></div>
                <div class="tab-pane fade" id="nav-popular" role="tabpanel" aria-labelledby="nav-popular-tab"></div>
                <div class="tab-pane fade" id="nav-3rdp" role="tabpanel" aria-labelledby="nav-3rdp-tab"></div>
              <!--  <div class="tab-pane fade" id="nav-trackers" role="tabpanel" aria-labelledby="nav-trackers-tab"></div> -->
            </div>
        </div>


    </div>

    <script>
        function showTablePopular() {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("nav-popular").innerHTML =
                        this.responseText;
                }
            };
            xhttp.open("GET", "get_top_popular.php", true);
            xhttp.send();
        }

        function showTableIntruder() {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("nav-intruder").innerHTML =
                        this.responseText;
                    updateTrackingGrid(document.getElementById("top1").innerHTML);
                    updateWebGrid(document.getElementById("top1").innerHTML);
                }
            };
            xhttp.open("GET", "get_top_intruder.php", true);
            xhttp.send();
        }

        function showTable3rdp() {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("nav-3rdp").innerHTML =
                        this.responseText;
                }
            };
            xhttp.open("GET", "get_top_3rd_party.php", true);
            xhttp.send();
        }

        function updateTrackingGrid(str) {

            var xhttp = new XMLHttpRequest();
            var data_textjs;

            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    data_textjs = this.responseText;
                    update_bars(data_textjs)
                }
            };

            xhttp.open("POST", "tracking_grid.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("domain_url=" + str);
        }


        function update_bars(data_textjs) {

            data = JSON.parse(data_textjs);

            d3.selectAll("#svgtracking > *").remove()
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
                    } else if (d.area == "Mouse fingerprinting" || d.area == "Canvas fingerprinting (big)" || d.area == "WebGL fingerprinting" || d.area == "Canvas fingerprinting (small)" || d.area == "Tracking cookies") {
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

        }


        function updateWebGrid(str) {
            var xhttp;

            xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("web-grid").innerHTML = this.responseText;
                }
            };
            xhttp.open("POST", "web_grid.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("domain_url=" + str);

        }

        function get_use_of_tracking_stats() {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    data = this.responseText;
                    stats(data, "1");
                }
            };
            xhttp.open("GET", "use_of_tracking.php", true);
            xhttp.send();
        }

        function get_tracking_stats() {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    data = this.responseText;
                    stats(data, "2");
                }
            };
            xhttp.open("GET", "tracking_stats.php", true);
            xhttp.send();
        }

        function stats(data1, str) {
            var width = 1000
            height = 400
            margin = 40

            // The radius of the pieplot is half the width or half the height (smallest one). I subtract a bit of margin.
            var radius = Math.min(width, height) / 2 - margin

            // append the svg object to the div called 'my_dataviz'
            if (str == "1") {
                var svg = d3.select("#donutIsTracking")
                    .append("svg")
                    .attr("width", width)
                    .attr("height", height)
                    .append("g")
                    .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");


                var color = d3.scaleOrdinal()
                    .domain(["Not-Tracking", "Tracking"])
                    .range(["rgb(80, 185, 255)", "rgb(255, 60, 60)"]);
            } else {
                var svg = d3.select("#donutTrackingStats")
                    .append("svg")
                    .attr("width", width)
                    .attr("height", height)
                    .append("g")
                    .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

                var color = d3.scaleOrdinal()
                    .range(d3.schemeCategory20);
            }

            data = JSON.parse(data1);

            // Compute the position of each group on the pie:
            var pie = d3.pie()
                .sort(null) // Do not sort group by size
                .value(function(d) {
                    return d.value;
                })
            var data_ready = pie(d3.entries(data))

            // The arc generator
            var arc = d3.arc()
                .innerRadius(radius * 0.5) // This is the size of the donut hole
                .outerRadius(radius * 0.8)

            // Another arc that won't be drawn. Just for labels positioning
            var outerArc = d3.arc()
                .innerRadius(radius * 0.9)
                .outerRadius(radius * 0.9)

            // Build the pie chart: Basically, each part of the pie is a path that we build using the arc function.
            svg
                .selectAll('allSlices')
                .data(data_ready)
                .enter()
                .append('path')
                .attr('d', arc)
                .attr('fill', function(d) {
                    return (color(d.data.key))
                })
                .attr("stroke", "white")
                .style("stroke-width", "2px")
                .style("opacity", 0.7)

            // Add the polylines between chart and labels:
            svg
                .selectAll('allPolylines')
                .data(data_ready)
                .enter()
                .append('polyline')
                .attr("stroke", "black")
                .style("fill", "none")
                .attr("stroke-width", 1)
                .attr('points', function(d) {
                    var posA = arc.centroid(d) // line insertion in the slice
                    var posB = outerArc.centroid(d) // line break: we use the other arc generator that has been built only for that
                    var posC = outerArc.centroid(d); // Label position = almost the same as posB
                    var midangle = d.startAngle + (d.endAngle - d.startAngle) / 2 // we need the angle to see if the X position will be at the extreme right or extreme left
                    posC[0] = radius * 0.95 * (midangle < Math.PI ? 1 : -1); // multiply by 1 or -1 to put it on the right or on the left
                    return [posA, posB, posC]
                })



            let track, notrack, total;
            svg
                .selectAll('allLabels')
                .data(data_ready)
                .enter()
                .append('text')
                .text(function(d) {
                    console.log(d.data.key);
                    if (str == "1") {
                        if (d.data.key == "Not-Tracking") {
                            notrack = d.data.value;
                            notrack = parseFloat(notrack);
                        } else {
                            track = d.data.value;
                            track = parseFloat(track);
                        }
                    }
                    return d.data.key
                })
                .attr('style', "margin:auto")
                .attr('transform', function(d) {
                    var pos = outerArc.centroid(d);
                    var midangle = d.startAngle + (d.endAngle - d.startAngle) / 2
                    pos[0] = radius * 0.99 * (midangle < Math.PI ? 1 : -1);
                    return 'translate(' + pos + ')';
                })
                .style('text-anchor', function(d) {
                    var midangle = d.startAngle + (d.endAngle - d.startAngle) / 2
                    return (midangle < Math.PI ? 'start' : 'end')
                })
            if (str == "1") {

                total = track + notrack;
                let percent;
                percent = (track / total) * 100;

                document.getElementById("percentage").innerHTML = percent.toFixed(2);
                document.getElementById("total_domains").innerHTML = total;
            }



        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>


</body>

</html>