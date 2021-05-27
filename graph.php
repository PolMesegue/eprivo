<?php

require_once "config.php";

$domain = trim($_GET['domain_url']);

$nodes[] = [];
$links[] = [];
$itn = 0;
$itl = 0;

$typetocolor = ["main_frame" => 0, "stylesheet" => 1, "script" => 2, "image" => 3, "other" => 4, "font" => 5,"xmlhttprequest" => 6, "media" => 7 , "sub_frame" => 8, "beacon" => 9, "websocket" => 10, "object" => 11,"csp_report"=>12];

$sql = "SELECT url.id, domain_url.initiator_frame, url.type, mime_type.name, url.server_ip, url.security_info FROM domain JOIN domain_url ON domain.id = domain_url.domain_id JOIN url ON domain_url.url_id = url.id JOIN mime_type ON url.mime_type_id = mime_type.id WHERE domain.name = ? and url.server_ip IS NOT NULL";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $param_domain);

    $param_domain = $domain;

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $id, $initiator, $type, $mime_type, $server_ip, $security_info );
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
mysqli_close($link);

$graph = ["nodes" => $nodes,"links" => $links]; 

$tmpfname = tempnam("/var/www/html/data/", 'graph-');
file_put_contents($tmpfname, json_encode($graph));

$piecesGraph = explode('/data', $tmpfname);

?>

<!DOCTYPE html>
<meta charset="utf-8">
<head>
<link rel="stylesheet" href="style.css">
<script src="https://d3js.org/d3.v4.min.js"></script>
</head>
<body>
<svg id ="svggraph"  width="960" height="600"></svg>

<script>

var svg = d3.select("#svggraph"),
    width = +svg.attr("width"),
    height = +svg.attr("height");

var color = d3.scaleOrdinal(d3.schemeCategory20);


var simulation = d3.forceSimulation()
    .force("link", d3.forceLink().id(function(d) { return d.id; }))
    .force("charge", d3.forceManyBody().distanceMax(200))
    .force("center", d3.forceCenter(width / 2, height / 2));

d3.json(<?php echo "\"/data" . $piecesGraph[1] . "\""; ?>, function(error, graph) {
  if (error) throw error;

  var link = svg.append("g")
      .attr("class", "links")
    .selectAll("line")
    .data(graph.links)
    .enter().append("line")
      .attr("stroke-width", function(d) { return Math.sqrt(2); });

  var node = svg.append("g")
      .attr("class", "nodes")
    .selectAll("g")
    .data(graph.nodes)
    .enter().append("g")
    
  var circles = node.append("circle")
      .attr("r", 5)
      .attr("fill", function(d) { return color(d.type); })
      .call(d3.drag()
          .on("start", dragstarted)
          .on("drag", dragged)
          .on("end", dragended));

 /* var lables = node.append("text")
      .text(function(d) {
        return d.id;
      })
      .attr('x', 6)
      .attr('y', 3);
*/
  node.append("title")
      .text(function(d) { return d.id; });

  simulation
      .nodes(graph.nodes)
      .on("tick", ticked);

  simulation.force("link")
      .links(graph.links);

  function ticked() {
    link
        .attr("x1", function(d) { return d.source.x; })
        .attr("y1", function(d) { return d.source.y; })
        .attr("x2", function(d) { return d.target.x; })
        .attr("y2", function(d) { return d.target.y; });

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

</body>
</html>