<?php

$code = $_GET['code'];

include 'config.php';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    echo "Connection failed";
}

// get the json from the post request
$sql = "SELECT json FROM `geohopper`.`traces` WHERE code='$code';";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    $json = $result->fetch_assoc()["json"];
} else {
    echo "0 results";
}

?>

<head>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Geohopper Overview</title>
  <meta content="Geohopper" property="og:title">
  <meta content="A Revolutionary 3D Traceroute Software" property="og:description">
  <meta content="https://geohopper.net/" property="og:url">
  <meta content="/geohopper.png" property="og:image">
  <meta content="#43B581" data-react-helmet="true" name="theme-color">
  <link rel="apple-touch-icon" sizes="180x180" href="/geohopper.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/geohopper.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/geohopper.png">
  <!-- import style sheet -->
  <link rel="stylesheet" href="./style.css" />
  <script src="//unpkg.com/globe.gl"></script>
  <!-- jquery -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  <script>
    // Raw data with hop information
    let rawData = <?php echo $json; ?>;
    // Format the data for globe.gl
    let locations = rawData.map(data => {
      let [lat, lng] = data.coordinates.split(',').map(Number);
      return {
        coords: [lng, lat],
        name: `Hop: ${data.hop}<br>IP: ${data.ip}<br>Location: ${data.location}<br>ASN: ${data.asn_name}<br>Latency: ${data.average_latency} ms`
      };
    });
    let lastHopIP = locations[locations.length - 1].name.split('<br>')[1].replace('IP: ', '');

    // Create a list of hops
    $(document).ready(function() {
      locations.forEach(location => {
        let locationDetails = location.name.split('<br>');
        let ipInfoLink = locationDetails[1].replace(/(IP: )(.*)/, `$1<a href="https://ipinfo.io/$2" target="_blank">$2</a>`);
        locationDetails[1] = ipInfoLink;
        let formattedDetails = locationDetails.join('\n');
        $('#hopsList').append(`<div style="direction: ltr;"><pre>${formattedDetails}</pre><hr></div>`);
      });
      $('#title').append(`<h5>Trace route to: ${lastHopIP}</h5>`);

    });
  </script>
</head>

<body>
<div id="title">
  <h1><a href="https://geohopper.net" style="text-decoration: none; color: inherit;">Geohopper</a></h1>
</div>
<div id="globeViz"></div>
<div id="hopsList"></div>

  <script>
    const globe = Globe()
      .globeImageUrl('nasa-1.jpg')
      .bumpImageUrl('//unpkg.com/three-globe/example/img/earth-topology.png')
      .backgroundImageUrl('//unpkg.com/three-globe/example/img/night-sky.png')
      (document.getElementById('globeViz'));

    // Define the tracer route points
    const tracerRoutes = [
      {
        coords: locations.map(c => c.coords),
        properties: { color: 'red' }
      }
    ];

    globe
      .pointsData(locations)
      .pointLat(p => p.coords[1])
      .pointLng(p => p.coords[0])
      .pointColor(() => 'red')
      .pointAltitude(0)
      .pointRadius(0.5)

      .pathsData(tracerRoutes)
      .pathPoints('coords')
      .pathPointLat(p => p[1])
      .pathPointLng(p => p[0])
      .pathColor(path => path.properties.color)
      .pathDashLength(0.02)
      .pathDashGap(0.05)
      .pathDashAnimateTime(8000)

  </script>
</body>
