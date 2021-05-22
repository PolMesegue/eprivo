<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ePrivacy Observatory</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/682550c010.js" crossorigin="anonymous"></script>

</head>

<body onload="showTableIntruder()">
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
            <div id="tracking-grid"></div>
        </div>
        <div class="web">
        </div>
        <div class="list">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-intruder-tab" data-bs-toggle="tab" data-bs-target="#nav-intruder" type="button" role="tab" aria-controls="nav-intruder" aria-selected="true" onclick="showTableIntruder()">Top Intruder</button>
                    <button class="nav-link" id="nav-popular-tab" data-bs-toggle="tab" data-bs-target="#nav-popular" type="button" role="tab" aria-controls="nav-popular" aria-selected="false" onclick="showTablePopular()">Top Popular</button>
                    <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Top ?</button>
                </div>
            </nav>
            <div class="tab-content wrapper" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-intruder" role="tabpanel" aria-labelledby="nav-intruder-tab">
                </div>
                <div class="tab-pane fade" id="nav-popular" role="tabpanel" aria-labelledby="nav-popular-tab">

                </div>
                <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                    <table class="table table-striped table-hover text-center">
                        <tr>
                            <th>#</th>
                            <th>Domain</th>
                            <th>Intrusion Level</th>
                        </tr>

                    </table>
                </div>
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

        function updateTrackingGrid(str) {
            var xhttp;
            if (str == "") {
                document.getElementById("txtHint").innerHTML = "";
                return;
            }
            xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("tracking-grid").innerHTML = this.responseText;
                }
            };
            xhttp.open("POST", "tracking_grid.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("domain_url="+str);
        }
    </script>


</body>

</html>