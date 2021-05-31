<!DOCTYPE html>
<meta charset="utf-8">
<style>
    body {
        margin: 15px;
        background-color: #F1F3F3
    }

    .bar {
        fill: #6F257F;
    }

    .axis path,
    .axis line {
        fill: none;
        stroke: #D4D8DA;
        stroke-width: 1px;
        shape-rendering: crispEdges;
    }

    .x path {
        display: none;
    }

    .toolTip {
        position: absolute;
        display: none;
        min-width: 80px;
        height: auto;
        background: none repeat scroll 0 0 #ffffff;
        border: 1px solid #6F257F;
        padding: 14px;
        text-align: center;
    }
</style>
<svg width="960" height="500"></svg>
<script src="https://d3js.org/d3.v4.min.js"></script>


    


<script>
    var xhttp = new XMLHttpRequest();
    var data_textjs;

    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            data_textjs = this.responseText;
            //console.log(JSON.parse(data_textjs));
            //document.write(JSON.parse(data_textjs));

        }
    };


    xhttp.open("GET", "tracking_grid.php?domain_url=google.com", true);
    xhttp.send();


    setTimeout(function() {
        //document.write(data_textjs);
        //var data2 = JSON.parse(data_textjs);
        //var data = data_textjs;
       // var data = '[{"area": "Very long-living cookies ", "value": 1},{"area": "JavaScript cookies ", "value": 2}]';
        data = JSON.parse(data_textjs);
       // document.write(JSON.parse(data));
       // document.write(data);

        var svg = d3.select("svg"),
            margin = {
                top: 20,
                right: 20,
                bottom: 30,
                left: 80
            },
            width = +svg.attr("width") - margin.left - margin.right,
            height = +svg.attr("height") - margin.top - margin.bottom;

        var tooltip = d3.select("body").append("div").attr("class", "toolTip");

        var x = d3.scaleLinear().range([0, width]);
        var y = d3.scaleBand().range([height, 0]);

        var g = svg.append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");



        data.sort(function(a, b) {
            return a.value - b.value;
        });

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
            .attr("x", 0)
            .attr("height", y.bandwidth())
            .attr("y", function(d) {
                return y(d.area);
            })
            .attr("width", function(d) {
                return x(d.value);
            })
            .on("mousemove", function(d) {
                tooltip
                    .style("left", d3.event.pageX - 50 + "px")
                    .style("top", d3.event.pageY - 70 + "px")
                    .style("display", "inline-block")
                    .html((d.area) + "<br>" + "Elements: " + (d.value));
            })
            .on("mouseout", function(d) {
                tooltip.style("display", "none");
            });

    }, 3000);
</script>