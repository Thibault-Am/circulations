<?php

//$opts = array('http' => array('proxy'=> 'tcp://www-cache:3128'));
//$context = stream_context_create($opts);
//point gps mairie notre dame des landes 47.3803867,-1.7113826

$trafics= file_get_contents("https://data.loire-atlantique.fr/explore/dataset/224400028_info-route-departementale/download?format=json&timezone=Europe/Berlin&use_labels_for_header=false",false);
$trafics = json_decode($trafics);
//var_dump($trafics);



$data_dep=file_get_contents("https://geo.api.gouv.fr/departements",false);
$data_dep = json_decode($data_dep);


if (isset($_GET['dep'])){
    $code=$_GET['dep'];
}else{
    $code=01;
}



chargerData($code);
function chargerData($code){
    global $hosp_covid,$date_covid,$reas_covid;
    $hosp_covid=[];
    $date_covid=[];
    $reas_covid=[];
    $data_covid=file("https://static.data.gouv.fr/resources/synthese-des-indicateurs-de-suivi-de-lepidemie-covid-19/20220111-191009/table-indicateurs-open-data-dep-2022-01-11-19h10.csv");
    //var_dump($data_covid);
    $i=0;
    foreach($data_covid as $ligne){
        //echo $ligne."\n<br/>";
        if ($i==0){
            $i++;
        }else{
            $res=explode(',',$ligne);
            if($res[0]!=$code){
    
            }else{
                
                array_push($hosp_covid,$res[9]);
                array_push($date_covid,$res[1]);
                array_push($reas_covid,$res[10]);
            }
           
        }
    }
}

//var_dump($date_covid);
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
    #map {
        height: 500px;
    }
</style>

<body>
<h1>Infos trafics: Loire-Atlantique</h1>

<div id="map">
    <script type="text/javascript">
        
        var map = L.map('map').setView([47.3803867, -1.7113826], 10);
        var marker = L.marker([47.3803867,-1.7113826]).addTo(map);
        marker.bindPopup("Mairie de notre dame des landes").openPopup();
        <?php
            foreach($trafics as $trafic){
                $latitude= $trafic->fields->localisation['0'];
                $longitude = $trafic->fields->localisation['1'];
                $nature = $trafic->fields->nature;
                $date = $trafic->fields->ligne4;
                $route = $trafic->fields->ligne2;
                ?>var marker = L.marker([<?php echo $latitude?>,<?php echo $longitude?>]).addTo(map);
                marker.bindPopup("<h3>Route : <?php echo $route?></h3><ul><li>Nature : <?php echo $nature?></li> <li>Date : <?php echo $date?> </li> ").openPopup();<?php
            }
        ?>
        
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

       
        

         <h1>Infos sur la pandémie Covid-19 par département</h1>
    <header>
        <form>

            <div id="dept">
                <label for="dep">Département : </label>
                <select name="dep" id="dep">
                <?php
                    $compteur=0;
                    foreach($data_dep as $dep){
                        $name= $dep->nom;
                        $code = $dep->code;
                        if ($code==01){
                            ?><option selected value='<?php echo $code?>'><?php echo $code?> - <?php echo $name?></option><?php

                        }else{
                            ?><option value='<?php echo $code?>'><?php echo $code?> - <?php echo $name?></option><?php

                            }
                    }
                ?>
            </div>
            <div>
                <input type="submit" onclick="chargerData(<?php $code?>)" value="Mettre à jour">
            </div>
        </form>
    </header>

    <main style="display: flex; flex-wrap: wrap; justify-content: space-around;">
        <div style="width: 700px; height: 400px; flex-basis : 45%; margin : 6em 1em">
            <h1>Hospitalisations</h1>
            <canvas id="hosps"></canvas>
        </div>
        <div style="width: 700px; height: 400px; flex-basis : 45%; margin : 6em 1em">
            <h1>Reanimations</h1>
            <canvas id="reas"></canvas>
        </div>
    </main>

    <?php
$compteur=0;
$tab_len= count($hosp_covid);

     echo '<script>
            var labels=[];
            var hosps = [];
            var reas = [];
        </script>';
     while($compteur<$tab_len){            
        //echo $tab_covid[$compteur];   
        
            echo "<script>hosps.push($hosp_covid[$compteur])</script>";
            echo "<script>labels.push(\"$date_covid[$compteur]\")</script>";
            echo "<script>reas.push(\"$reas_covid[$compteur]\")</script>";
                 
       
        $compteur++;
        
    };
    
   
    
?>
       
        <script>
            console.log(labels);
        //document.write("zfubhzaioufa")
        // Hospitalisations
        
        const data_hosps = {
            labels: labels,
            datasets: [{
                label: 'Hospitalisations',
                backgroundColor: 'lightblue',
                borderColor: '#005291',
                fill: true,
                data: hosps
            }]
        };

        const config_hosps = {
            type: 'line',
            data: data_hosps,
            options: {}
        };

        const myChart_hosps = new Chart(
            document.getElementById('hosps'),
            config_hosps
        );

        // Reanimations
        const data_reas = {
            labels: labels,
            datasets: [{
                label: 'Reanimations',
                backgroundColor: 'rgba(204, 204, 204, 0.6)',
                borderColor: '#005291',
                fill: true,
                data: reas
            }]
        };

        const config_reas = {
            type: 'line',
            data: data_reas,
            options: {}
        };

        const myChart_reas = new Chart(
            document.getElementById('reas'),
            config_reas
        );

    </script>




    <div>
        <h1>Sources :</h1>
        <ul>
            <li><a href="https://data.loire-atlantique.fr/explore/dataset/224400028_info-route-departementale/download?format=json&timezone=Europe/Berlin&use_labels_for_header=false">API Mairie Notre Dame des Landes + Infos trafic loire atlantique</a></li>
            <li><a href="https://static.data.gouv.fr/resources/synthese-des-indicateurs-de-suivi-de-lepidemie-covid-19/20220111-191009/table-indicateurs-open-data-dep-2022-01-11-19h10.csv">Data Covid-19</a></li>
            <li><a href="https://geo.api.gouv.fr/departements">Liste départements</a></li>
        </ul>
    </div>
    
</body>
</html>