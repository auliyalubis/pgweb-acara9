<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Leaflet JS with MySQL Data</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
        background-color: #2e3e4a; 
        margin-left: 50px; 
        margin-right: 50px; 
        margin-top: 50px; 
        }

        #map {
            width: 100%;
            height: 600px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background-color: #8da3ba; /* Blue color for header */
            color: white;
            padding: 10px;
            text-align: left;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2; /* Light grey for even rows */
        }

        tr:nth-child(odd) {
            background-color: #cad8e8; /* White for odd rows */
        }

        tr:hover {
            background-color: #d1ecf1; /* Light blue for hover effect */
        }

        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

    </style>
</head>

<body>
    <div class="alert alert-primary text-center" role="alert" text style="background-color: #8da3ba;">
        <h1>Kabupaten Sleman</h1>
        <h4>Daerah Istimewa Yogyakarta</h4>
    </div>

    <div id="map"></div>

    <?php
    // Database configuration
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "pgweb-acara8";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch data from the database
    $sql = "SELECT * FROM 7b";
    $result = $conn->query($sql);

    //Prepare an array to hold coordinates for Leaflet markers
    $coordinates = [];

    if ($result->num_rows > 0) {
        echo "<table><tr>
                <th>Kecamatan</th>
                <th>Longitude</th>
                <th>Latitude</th>
                <th>Luas</th>
                <th>Jumlah Penduduk</th>
                <th>Aksi</th></tr>";

        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>" . $row["kecamatan"] . "</td>
                <td>" . $row["longitude"] . "</td>
                <td>" . $row["latitude"] . "</td>
                <td>" . $row["luas"] . "</td>
                <td align='right'>" . $row["jumlah_penduduk"] . "</td>
                <td>
                    <a href='edit.php?id=" . intval($row["id"]) . "'>Edit</a> |
                    <a href='delete.php?id=" . $row["id"] . "' onclick=\"return confirm('Apakah Anda yakin ingin menghapus data ini?');\">Delete</a>
                </td>
            </tr>";
            
            // Add coordinates to the array
            $coordinates[] = [
                'kecamatan' => $row["kecamatan"],
                'longitude' => $row["longitude"],
                'latitude' => $row["latitude"],
            ];
        }
        echo "</table>";
    } else {
        echo "0 results";
    }

    $conn->close();
    ?>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        // Initialize the map
        var map = L.map("map").setView([-7.8011927,110.3646666], 10);

        // Base Map
        var osm = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution:
                '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        });
        var Esri_WorldImagery = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
        });

        var rupabumiindonesia = L.tileLayer('https://geoservices.big.go.id/rbi/rest/services/BASEMAP/Rupabumi_Indonesia/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Badan Informasi Geospasial'
        });

        osm.addTo(map);

        // JavaScript array holding PHP data
        var locations = <?php echo json_encode($coordinates); ?>;

        // Add markers from the database data
        locations.forEach(function(location) {
            var marker = L.marker([location.latitude, location.longitude]).addTo(map);
            marker.bindPopup("<b>" + location.kecamatan + "</b><br>Longitude: " + location.longitude + "<br>Latitude: " + location.latitude);
        });

        // Control Layer
        var baseMaps = {
            "OpenStreetMap": osm,
            "Esri World Imagery": Esri_WorldImagery,
            "Rupa Bumi Indonesia": rupabumiindonesia,
        };

        var controllayer = L.control.layers(baseMaps, null, {
            collapsed: false,
        });
        controllayer.addTo(map);


    </script>
</body>

</html>