<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <title>My Calendar</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <h1><b>My Calendar</b></h1>
    <nav>
        <a href="calendar.php" style="text-decoration:none">My Calendar</a> &nbsp; &nbsp; &nbsp;
        <a href="form.php" style="text-decoration:none">Form Input</a>
    </nav>

    <table>
        <?php
        require_once "utils.php";
        $events = read_data();
        if (empty($events)) {
            echo "<p class='alert'>Calender has no events. Please use the input page to enter some events.</p>";
        } else {
            $events = events_every_day($events);
            $mapping = array(1 => "Mon", 2 => "Tue", 3 => "Wed", 4 => "Thu", 5 => "Fri");

            for ($i = 1; $i <= 5; $i++) {
                $day = $mapping[$i];
                $events_day = $events[$i-1];
                echo "<tr><th><span>$day</span></th>";
                foreach ($events_day as $event) {
                    $event_name = $event["event_name"];
                    $start_time = $event["start_time"];
                    $end_time = $event["end_time"];
                    $long = $event["long"];
                    $lati = $event["lati"];
                    $location = $event["location"];
                    echo "
                <td>
                    <div class=\"fontstyle\">$start_time - $end_time</div>
                    <br/> $event_name - $location <br/><br/>
                </td>
            ";
                }
                echo "</tr>";
            }
        }
        ?>
    </table>

    <!--<div class="floatright">
        <a class="twitter-timeline" href="https://twitter.com/hashtag/UMN" data-widget-id="920441589192896512">#UMN
            Tweets</a>
        <script>!function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
                if (!d.getElementById(id)) {
                    js = d.createElement(s);
                    js.id = id;
                    js.src = p + "://platform.twitter.com/widgets.js";
                    fjs.parentNode.insertBefore(js, fjs);
                }
            }(document, "script", "twitter-wjs");
        </script>
    </div>-->

    <div class="center">
        <!--<form name="cal">
            <div class="calendar_form">
                <p>Radius:
                    <input id="input-radius" type="number" name="radius">
                    <button type="button" onclick="findRestaurant()">Find Nearby Resturants</button>
                    <br/>
                </p>

                <p>Destination:
                    <input id="input-destination" name="destination" type="text">
                    <button type="button" onclick="findDestination()">Get Directions</button>
                </p>
            </div>
            <div id="type-selector" class="calendar_form" onchange="calculateAndDisplayRoute();">
                <br/>
                Walking<input id="changemode-walking" type="radio" name="trip mode" value="WALKING"> &nbsp;
                Driving<input id="changemode-driving" type="radio" name="trip mode" value="DRIVING"> &nbsp;
                Transit<input id="changemode-transit" type="radio" name="trip mode" value="TRANSIT"> &nbsp;
                Bicycling<input id="changemode-bicycling" type="radio" name="trip mode" value="BICYCLING">
            </div>
        </form>-->
        <div id="map"></div>
        <div id="right-panel"></div>
    </div>

    <script
        type="text/javascript">/* Reference: https://www.codexworld.com/google-maps-with-multiple-markers-using-javascript-api/ */
        var map;
        var infowindow;
        var marker, i;

        function markedBuilding() {
            var events = <?php echo read_data_str() ?>;

            var myCenter = new google.maps.LatLng(44.974, -93.234);
            var mapOptions = {
                center: myCenter,
                zoom: 14,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            map = new google.maps.Map(document.getElementById("map"), mapOptions);
            var bounds = new google.maps.LatLngBounds();
            infoWindow = new google.maps.InfoWindow();

            for (i = 0; i < events.length; i++) {
                var e = events[i];
                var position = new google.maps.LatLng(e["lati"], e["long"]);
                bounds.extend(position);
                marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    title: e["event_name"],
                    animation: google.maps.Animation.BOUNCE
                });
                marker.setMap(map);
                google.maps.event.addListener(marker, 'click', (function (marker, i) {
                    return function () {
                        infoWindow.setContent('<div class="info_content"><p>' + e["location"] + '</p><p></p>' + e["event_name"] + '</p>');
                        infoWindow.open(map, marker);
                    }
                })(marker, i));
                map.fitBounds(bounds);
            }
            var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function (event) {
                this.setZoom(15);
                google.maps.event.removeListener(boundsListener);
            });
        }


        function findRestaurant() {
            var myCenter = {lat: 44.974, lng: -93.234};

            map = new google.maps.Map(document.getElementById('map'), {
                center: myCenter,
                zoom: 14
            });

            var getRadius = document.forms["cal"]["radius"].value;

            var service = new google.maps.places.PlacesService(map);
            console.log(getRadius);
            service.nearbySearch({
                location: myCenter,
                radius: getRadius,
                type: ['restaurant']
            }, callback);


            function callback(results, status) {
                if (status === google.maps.places.PlacesServiceStatus.OK) {
                    for (var i = 0; i < results.length; i++) {
                        createMarker(results[i]);
                    }
                }
            }

            function createMarker(place) {
                var marker = new google.maps.Marker({
                    map: map,
                    position: place.geometry.location,
                    animation: google.maps.Animation.BOUNCE
                });

                google.maps.event.addListener(marker, 'click', function () {
                    infowindow.setContent(place.name);
                    infowindow.open(map, this);
                });
            }
        }

        function findDestination() {

            var myCenter = new google.maps.LatLng(44.974, -93.234);
            var directionsService = new google.maps.DirectionsService;
            var directionsDisplay = new google.maps.DirectionsRenderer;
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 14,
                center: myCenter
            });
            directionsDisplay.setMap(map);
            directionsDisplay.setPanel(document.getElementById("right-panel"));

            calculateAndDisplayRoute(directionsService, directionsDisplay)
            var onChangeHandler = function () {
                calculateAndDisplayRoute(directionsService, directionsDisplay);
            };

            document.getElementById('type-selector').addEventListener('change', onChangeHandler);
        }

        function calculateAndDisplayRoute(directionsService, directionsDisplay) {
            var selectedMode = document.forms["cal"]["trip mode"].value;
            directionsService.route({
                origin: {lat: 44.974, lng: -93.234},
                destination: document.getElementById('input-destination').value,
                travelMode: selectedMode
            }, function find(response, status) {
                if (status === 'OK') {
                    directionsDisplay.setDirections(response);
                }
            });
        }
        function initMap() {
            markedBuilding();
        }

    </script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBaVfWggow-ynHdHCzbcZlqNtlUZ04VlCU&libraries=places&callback=initMap"
        async defer></script>
</body>
</html>


<?php
    function cmp($x, $y) {
        return ($x["start_time"] < $y["start_time"]) ? -1 : 1;
    }

    function events_every_day($events) {
        $ret = array(array(), array(), array(), array(), array());
        foreach ($events as $event) {
            $ret[$event["day"] - 1][] = $event;
        }

        $res = array();
        foreach ($ret as $es) {
            usort($es, "cmp");
            $res[] = $es;
        }

        return $res;
    }
?>