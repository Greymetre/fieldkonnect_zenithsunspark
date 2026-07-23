<!DOCTYPE html>
<html>

<head>
    <title>Track Map</title>
    <style>
        #map {
            height: 700px;
            width: 100%;
        }
    </style>

    <script src="https://unpkg.com/@googlemaps/markerclustererplus/dist/index.min.js"></script>
    <script>
        const locations = @json($coordinates);

        function initMap() {
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 20,
                center: {
                    lat: parseFloat(locations[0].latitude),
                    lng: parseFloat(locations[0].longitude)
                }
            });

            const directionsService = new google.maps.DirectionsService();

            const chunkSize = 25; // max waypoints per request
            let routeChunks = [];

            for (let i = 0; i < locations.length - 1; i += chunkSize) {
                const chunk = locations.slice(i, i + chunkSize + 1);
                if (chunk.length >= 2) {
                    routeChunks.push(chunk);
                }
            }

            routeChunks.forEach((chunk, i) => {
                const directionsRenderer = new google.maps.DirectionsRenderer({
                    map: map,
                    suppressMarkers: true,
                    preserveViewport: true
                });

                const origin = {
                    lat: parseFloat(chunk[0].latitude),
                    lng: parseFloat(chunk[0].longitude)
                };
                const destination = {
                    lat: parseFloat(chunk[chunk.length - 1].latitude),
                    lng: parseFloat(chunk[chunk.length - 1].longitude)
                };
                const waypoints = chunk.slice(1, -1).map(loc => ({
                    location: {
                        lat: parseFloat(loc.latitude),
                        lng: parseFloat(loc.longitude)
                    },
                    stopover: true
                }));

                directionsService.route({
                    origin: origin,
                    destination: destination,
                    waypoints: waypoints,
                    travelMode: google.maps.TravelMode.WALKING
                }, (response, status) => {
                    if (status === 'OK') {
                        directionsRenderer.setDirections(response);
                    } else {
                        console.error('Directions request failed: ' + status);
                    }
                });
            });

            // Tooltip logic
            const tooltip = document.createElement("div");
            tooltip.style.position = "absolute";
            tooltip.style.background = "white";
            tooltip.style.padding = "2px 6px";
            tooltip.style.borderRadius = "4px";
            tooltip.style.fontSize = "12px";
            tooltip.style.boxShadow = "0 1px 3px rgba(0,0,0,0.3)";
            tooltip.style.display = "none";
            tooltip.style.pointerEvents = "none";
            tooltip.style.zIndex = "1000";
            document.body.appendChild(tooltip);

            const markers = locations.map((loc, index) => {
                const marker = new google.maps.Marker({
                    position: {
                        lat: parseFloat(loc.latitude),
                        lng: parseFloat(loc.longitude)
                    },
                    label: `${index + 1}`,
                    title: loc.name,
                    map: map
                });

                marker.addListener("mouseover", (e) => {
                    tooltip.innerText = loc.time;
                    tooltip.style.left = e.domEvent.pageX + 10 + "px";
                    tooltip.style.top = e.domEvent.pageY + 10 + "px";
                    tooltip.style.display = "block";
                });

                marker.addListener("mousemove", (e) => {
                    tooltip.style.left = e.domEvent.pageX + 10 + "px";
                    tooltip.style.top = e.domEvent.pageY + 10 + "px";
                });

                marker.addListener("mouseout", () => {
                    tooltip.style.display = "none";
                });

                return marker;
            });

            // Cluster the markers
            new MarkerClusterer(map, markers, {
                imagePath: "https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m"
            });
        }
    </script>

    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAVSDwHbKULnZa93kYpYINTqX4eaWy9q18&callback=initMap">
    </script>
</head>

<body>
    <div id="map"></div>
</body>

</html>