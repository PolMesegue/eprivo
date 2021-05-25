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
                    <button class="nav-link" id="nav-map-tab" data-bs-toggle="tab" data-bs-target="#nav-map" type="button" role="tab" aria-controls="nav-map" aria-selected="false" onclick="showMap()">Map</button>

                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-graph" role="tabpanel" aria-labelledby="nav-graph-tab">
                </div>
                <div class="tab-pane fade" id="nav-map" role="tabpanel" aria-labelledby="nav-map-tab">

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
            xhttp.open("GET", "graph_resources.php?domain_url="+str, true);
            xhttp.send();
        }

        function showMap() {
            var xhttp = new XMLHttpRequest();
            var str = "<?php echo trim($_GET["domain_url"]); ?>";
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("nav-map").innerHTML =
                        this.responseText;
                }
            };
            xhttp.open("GET", "map_resources.php?domain_url="+str, true);
            xhttp.send();
        }
    </script>



</body>

</html>