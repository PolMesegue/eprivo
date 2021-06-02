<html>
<head>
<style>
/* HTML styles */
html{ width: 100%; }
body{ 
  width: 100%; 
  margin: 0; padding: 0; 
  display: flex; 
  font-family: sans-serif; font-size: 75%; }
.controls {
  flex-basis: 200px;
  padding: 0 5px;
}
.controls .force {
  background-color:#eee;
  border-radius: 3px;
  padding: 5px;
  margin: 5px 0;
}
.controls .force p label { margin-right: .5em; font-size: 120%; font-weight: bold;}
.controls .force p { margin-top: 0;}
.controls .force label { display: inline-block; }
.controls input[type="checkbox"] { transform: scale(1.2, 1.2); }
.controls input[type="range"] { margin: 0 5% 0.5em 5%; width: 90%; }
/* alpha viewer */
.controls .alpha p { margin-bottom: .25em; }
.controls .alpha .alpha_bar { height: .5em; border: 1px #777 solid; border-radius: 2px; padding: 1px; display: flex; }
.controls .alpha .alpha_bar #alpha_value { background-color: #555; border-radius: 1px; flex-basis: 100% }
.controls .alpha .alpha_bar:hover { border-width: 2px; margin:-1px; }
.controls .alpha .alpha_bar:active #alpha_value { background-color: #222 }

/* SVG styles */
svg {
  flex-basis: 100%;
  min-width: 200px;
}
.links line {
  stroke: #aaa;
}
.nodes circle {
  pointer-events: all;
}

</style>
</head>
<body>
<div class="controls">
  <div class="force alpha">
    <p><label>alpha</label> Simulation activity</p>
    <div class="alpha_bar" onclick="updateAll();"><div id="alpha_value"></div></div>
  </div>
  <div class="force">
    <p><label>center</label> Shifts the view, so the graph is centered at this location.</p>
    <label>
      x
      <output id="center_XSliderOutput">.21</output>
      <input type="range" min="0" max="1" value=".21" step="0.01" oninput="d3.select('#center_XSliderOutput').text(value); forceProperties.center.x=value; updateAll();">
    </label>
    <label>
      y
      <output id="center_YSliderOutput">.5</output>
      <input type="range" min="0" max="1" value=".5" step="0.01" oninput="d3.select('#center_YSliderOutput').text(value); forceProperties.center.y=value; updateAll();">
    </label>
  </div>

  <div class="force">
    <p><label><input type="checkbox" checked onchange="forceProperties.charge.enabled = this.checked; updateAll();"> charge</label> Attracts (+) or repels (-) nodes to/from each other.</p>
    <label title="Negative strength repels nodes. Positive strength attracts nodes.">
      strength
      <output id="charge_StrengthSliderOutput">-50</output>
      <input type="range" min="-200" max="50" value="-50" step=".1" oninput="d3.select('#charge_StrengthSliderOutput').text(value); forceProperties.charge.strength=value; updateAll();">
    </label>
    <label title="Minimum distance where force is applied">
      distanceMin
      <output id="charge_distanceMinSliderOutput">1</output>
      <input type="range" min="0" max="50" value="1" step=".1" oninput="d3.select('#charge_distanceMinSliderOutput').text(value); forceProperties.charge.distanceMin=value; updateAll();">
    </label>
    <label title="Maximum distance where force is applied">
      distanceMax
      <output id="charge_distanceMaxSliderOutput">2000</output>
      <input type="range" min="0" max="2000" value="2000" step=".1" oninput="d3.select('#charge_distanceMaxSliderOutput').text(value); forceProperties.charge.distanceMax=value; updateAll();">
    </label>
  </div>

  <div class="force">
    <p><label><input type="checkbox" checked onchange="forceProperties.collide.enabled = this.checked; updateAll();"> collide</label> Prevents nodes from overlapping</p>
    <label>
      strength
      <output id="collide_StrengthSliderOutput">.7</output>
      <input type="range" min="0" max="2" value=".7" step=".1" oninput="d3.select('#collide_StrengthSliderOutput').text(value); forceProperties.collide.strength=value; updateAll();">
    </label>
    <label title="Size of nodes">
      radius
      <output id="collide_radiusSliderOutput">5</output>
      <input type="range" min="0" max="100" value="5" step="1" oninput="d3.select('#collide_radiusSliderOutput').text(value); forceProperties.collide.radius=value; updateAll();">
    </label>
    <label title="Higher values increase rigidity of the nodes (WARNING: high values are computationally expensive)">
      iterations
      <output id="collide_iterationsSliderOutput">1</output>
      <input type="range" min="1" max="10" value="1" step="1" oninput="d3.select('#collide_iterationsSliderOutput').text(value); forceProperties.collide.iterations=value; updateAll();">
    </label>
  </div>

  <div class="force">
    <p><label><input type="checkbox" onchange="forceProperties.forceX.enabled = this.checked; updateAll();"> forceX</label> Acts like gravity. Pulls all points towards an X location.</p>
    <label>
      strength
      <output id="forceX_StrengthSliderOutput">.1</output>
      <input type="range" min="0" max="1" value=".1" step="0.01" oninput="d3.select('#forceX_StrengthSliderOutput').text(value); forceProperties.forceX.strength=value; updateAll();">
    </label>
    <label title="The X location that the force will push the nodes to (NOTE: This demo multiplies by the svg width)">
      x
      <output id="forceX_XSliderOutput">.5</output>
      <input type="range" min="0" max="1" value=".5" step="0.01" oninput="d3.select('#forceX_XSliderOutput').text(value); forceProperties.forceX.x=value; updateAll();">
    </label>
  </div>

  <div class="force">
    <p><label><input type="checkbox" onchange="forceProperties.forceY.enabled = this.checked; updateAll();"> forceY</label> Acts like gravity. Pulls all points towards a Y location.</p>
    <label>
      strength
      <output id="forceY_StrengthSliderOutput">.1</output>
      <input type="range" min="0" max="1" value=".1" step="0.01" oninput="d3.select('#forceY_StrengthSliderOutput').text(value); forceProperties.forceY.strength=value; updateAll();">
    </label>
    <label title="The Y location that the force will push the nodes to (NOTE: This demo multiplies by the svg height)">
      y
      <output id="forceY_YSliderOutput">.5</output>
      <input type="range" min="0" max="1" value=".5" step="0.01" oninput="d3.select('#forceY_YSliderOutput').text(value); forceProperties.forceY.y=value; updateAll();">
    </label>
  </div>

  <div class="force">
    <p><label><input type="checkbox" checked onchange="forceProperties.link.enabled = this.checked; updateAll();"> link</label> Sets link length</p>
    <label title="The force will push/pull nodes to make links this long">
      distance
      <output id="link_DistanceSliderOutput">30</output>
      <input type="range" min="0" max="100" value="30" step="1" oninput="d3.select('#link_DistanceSliderOutput').text(value); forceProperties.link.distance=value; updateAll();">
    </label>
    <label title="Higher values increase rigidity of the links (WARNING: high values are computationally expensive)">
      iterations
      <output id="link_IterationsSliderOutput">1</output>
      <input type="range" min="1" max="10" value="1" step="1" oninput="d3.select('#link_IterationsSliderOutput').text(value); forceProperties.link.iterations=value; updateAll();">
    </label>
  </div>
</div>
<svg></svg>
<script src="https://d3js.org/d3.v4.min.js"></script>
<script src="code.js"></script>
</body>
</html>