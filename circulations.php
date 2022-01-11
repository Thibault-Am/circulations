<?php

$opts = array('http' => array('proxy'=> 'tcp://www-cache:3128'));
$context = stream_context_create($opts);
//point gps mairie notre dame des landes 47.3803867,-1.7113826

$meteo= file_get_contents("https://data.loire-atlantique.fr/explore/dataset/224400028_info-route-departementale/download?format=json&timezone=Europe/Berlin&use_labels_for_header=false",false,$context);
var_dump($meteo);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Circulations</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
        integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
        crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
        integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
        crossorigin=""></script>
</head>
<style>
    #map {
        height: 500px;
    }
</style>

<body>
<div id="map">
<script type="text/javascript">

            var map = L.map('map').setView([47.3803867, -1.7113826], 14);
            var marker = L.marker([47.3803867,-1.7113826]).addTo(map);
                marker.bindPopup("Mairie de notre dame des landes").openPopup();
            var tiles = L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
                maxZoom: 18,
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, ' +
                    'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                id: 'mapbox/streets-v11',
                tileSize: 512,
                zoomOffset: -1
            }).addTo(map);

        </script>
    </div>
</body>
</html>
