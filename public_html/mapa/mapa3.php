<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Mapa com Zoom Forçado (até 21)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <style>
    #map { height: 100vh; width: 100%; }
  </style>
</head>
<body>

<div id="map"></div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-providers@1.13.0/leaflet-providers.js"></script>

<script>
  const coordenadas = [-7.8305, -34.9011]; // Igarassu

  const map = L.map('map', {
    center: coordenadas,
    zoom: 20,
    maxZoom: 21 // permite até zoom 21
  });

  const osm = L.tileLayer.provider('OpenStreetMap.Mapnik');

  const esriSat = L.tileLayer.provider('Esri.WorldImagery', {
    maxNativeZoom: 18, // qualidade máxima real
    maxZoom: 21        // zoom forçado (imagem será esticada)
  }).addTo(map);

  const baseMaps = {
    "Mapa Comum": osm,
    "Satélite (Zoom Forçado)": esriSat
  };
  L.control.layers(baseMaps).addTo(map);

  L.marker(coordenadas)
    .addTo(map)
    .bindPopup('Zoom artificial até 21 (pode perder qualidade)')
    .openPopup();
</script>

</body>
</html>
