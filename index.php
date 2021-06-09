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


</head>


<body onload="showTableIntruder(), updateTrackingGrid('google.com'), updateWebGrid('google.com')">

    <div class="topnav">
        <a class="active" href="index.php">Home</a>
        <a href="about.php">About</a>
    </div>

    <h1 style="text-align:center; margin-top:30px;"> ePrivacy Observatory </h1>
    <h2 style="text-align:center;"> Analize a domain with the Online Resource Mapper tool <br> and see how it's vulnerating your right to privacy </h2>


    <div class="grid-container">
        <div class="search">
            <div class="row justify-content-center padding">
                <div class="col-md-8 ftco-animate fadeInUp ftco-animated">
                    <form action="domain_handler.php" class="domain-form" method="post">
                        <div class="form-group d-md-flex">
                            <input type="text" id="domain_url" name="domain_url" class="form-control px-4" placeholder="Insert domain here..." pattern="((?:[a-z\d](?:[a-z\d-]{0,63}[a-z\d])?|\*)\.)+[a-z\d][a-z\d-]{0,63}[a-z\d]" title="Enter a valid domain syntax" required>
                            <input type="submit" class="search-domain btn btn-primary px-5" value="Analize Domain">
                        </div>
                    </form>
                    <p style="text-align:center;">Enter a Domain here, and if it's not analized yet by ORM, it will be queued and analized soon <br> Or you can also browse the following top-lists</p>
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
                </div>
            </nav>
            <div class="tab-content wrapper" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-intruder" role="tabpanel" aria-labelledby="nav-intruder-tab"></div>
                <div class="tab-pane fade" id="nav-popular" role="tabpanel" aria-labelledby="nav-popular-tab"></div>
                <div class="tab-pane fade" id="nav-3rdp" role="tabpanel" aria-labelledby="nav-3rdp-tab"></div>
            </div>
        </div>


    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>

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
                }
            };

            xhttp.open("POST", "tracking_grid.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("domain_url=" + str);


            setTimeout(function() {

                data = JSON.parse(data_textjs);

                d3.selectAll("g > *").remove()
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
    </script>


</body>

</html>