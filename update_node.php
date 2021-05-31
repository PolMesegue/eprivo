<?php
require_once "config.php";

$url_id = trim($_POST["url_id"]);

$sql = "";


?>

<table class="table table-hover">
                    <tr>
                        <td> Id </td>
                        <td id="idnode" style="text-align: left;"> </td>
                    </tr>
                    <tr>
                        <td>Security State</td>
                        <td id="ss" style="text-align: left;"></td>
                    </tr>
                    <tr>
                        <td>IP Address</td>
                        <td id="ipadd" style="text-align: left;"></td>
                    </tr>
                    <tr>
                        <td>Mime Type</td>
                        <td id="mimetype" style="text-align: left;"></td>
                    </tr>
                </table>