<?php

include_once("getCcsv.php");
?>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script src="//api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
	<script>
    var myMap;
    var clusterer;
    // Дождёмся загрузки API и готовности DOM.
    ymaps.ready(init);
    
    function init () {
        // Создание экземпляра карты и его привязка к контейнеру с
        // заданным id ("map").
        myMap = new ymaps.Map('map', {
            // При инициализации карты обязательно нужно указать
            // её центр и коэффициент масштабирования.
            center: [55.76, 37.64], // Москва
            zoom: 10
    	}),
            clusterer = new ymaps.Clusterer(),

            getPointData = function (index) {
                return {
                    balloonContentHeader: 'Адрес №' + (i + 1),
                    balloonContentBody: getContentBody(i)
                };
            },
            points = [
                <?php 
                    //init('address.csv', 'coord.csv');
                    $ars = getCoord('coord.csv');
                    foreach($ars as $ar){
                        echo $ar.', ';
                    }
                ?>
            ],
            geoObjects = [];
    
        for (var i = 0, len = points.length; i < len; i++) {
            geoObjects[i] = new ymaps.Placemark(points[i], getPointData(i));
        }	
        clusterer.add(geoObjects);
        myMap.geoObjects.add(clusterer);
        };
        
        var placemarkBodies;
        function getContentBody (num) {
            if (!placemarkBodies) {
                placemarkBodies = [
    
                     <?php 
                        $ars = getAddr('coord.csv');
                        foreach($ars as $ar){
                            echo "['$ar'], ";
                        }
                    ?>
                ];
            }
            return placemarkBodies[num];
        }
    </script>
	<style>
        body, html {padding: 0;margin: 0;width: 100%;height: 100%;}
        #map {width: 100%;height: 90%;}
    </style>
</head>
<body>
    <div id="map"></div>
</body>