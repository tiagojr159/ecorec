<?php
// Caminhos corretos para incluir header e footer
require_once __DIR__ . '/../header.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mapa Interativo - Recife</title>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
html, body { 
  height: 100%; 
  margin: 0;
  padding: 0;
  font-family: Arial, sans-serif;
}

/* Container principal com aparência de telinha */
.map-container {
  width: 90%;
  max-width: 1200px;
  height: 80vh;
  margin: 20px auto;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0,0,0,0.15);
  border: 1px solid #ddd;
  position: relative;
}

#map {
  height: 100%;
  width: 100%;
  border-radius: 9px; /* Um pouco menos que o container para não aparecer borda dupla */
}

/* Painel de controle */
.control-panel {
  position: absolute;
  top: 70px;
  right: 20px;
  background: rgba(255, 255, 255, 0.95);
  padding: 15px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  z-index: 1000;
  min-width: 200px;
}

.control-panel h3 {
  margin: 0 0 12px 0;
  font-size: 1rem;
  color: #333;
  border-bottom: 1px solid #eee;
  padding-bottom: 8px;
}

.toggle-container {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
  cursor: pointer;
}

.toggle-container input[type="checkbox"] {
  margin-right: 10px;
  transform: scale(1.2);
}

.toggle-container label {
  cursor: pointer;
  font-size: 0.95rem;
  color: #333;
  user-select: none;
}

.legend {
position: absolute;
bottom: 20px;
left: 20px;
background: #fff;
padding: 12px 14px;
border-radius: 8px;
box-shadow: 0 0 15px rgba(0,0,0,0.2);
z-index: 1000;
font-size: 0.95rem;
}
.legend h4 { margin-bottom: 8px; color: #333; font-size: 1rem; }
.legend-item { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
.legend-color { width: 18px; height: 18px; border-radius: 4px; }

/* Cabeçalho da telinha */
.map-header {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  background: linear-gradient(to bottom, rgba(255,255,255,0.9), rgba(255,255,255,0.7));
  padding: 10px 15px;
  border-bottom: 1px solid #eee;
  z-index: 1000;
  border-radius: 9px 9px 0 0;
}

.map-title {
  margin: 0;
  font-size: 1.2rem;
  color: #333;
  font-weight: 600;
}

/* Ajuste do z-index do mapa para não ficar por cima do cabeçalho */
.leaflet-map-pane {
  z-index: 1 !important;
}

@media (max-width: 768px){
  .map-container {
    width: 95%;
    height: 70vh;
    margin: 10px auto;
  }
  
  .control-panel {
    top: 60px;
    right: 10px;
    padding: 10px;
    min-width: 160px;
  }
  
  .control-panel h3 {
    font-size: 0.9rem;
    margin-bottom: 8px;
  }
  
  .toggle-container label {
    font-size: 0.85rem;
  }
  
  .legend {
    bottom: 10px;
    left: 10px;
    padding: 8px 10px;
    font-size: 0.85rem;
  }
  
  .legend h4 { font-size: 0.9rem; }
  .legend-color { width: 14px; height: 14px; }
}
</style>
</head>
<body>

<div class="map-container">
  <div class="map-header">
    <h1 class="map-title">Mapa Interativo de Recife</h1>
  </div>
  
  <div id="map"></div>
  
  <!-- Painel de controle -->
  <div class="control-panel">
    <h3>Camadas</h3>
    <div class="toggle-container">
      <input type="checkbox" id="toggle-admin">
      <label for="toggle-admin">Edificações Administrativas</label>
    </div>
  </div>
  
  <div class="legend">
    <h4>Legenda</h4>
    <div class="legend-item">
      <div class="legend-color" style="background:#ff7800;"></div><span>Escolas</span>
    </div>
    <div class="legend-item">
      <div class="legend-color" style="background:#00c853;"></div><span>Parques</span>
    </div>
    <div class="legend-item">
      <div class="legend-color" style="background:#2962ff;"></div><span>Hospitais</span>
    </div>
    <div class="legend-item">
      <div class="legend-color" style="background:#4CAF50;"></div><span>Bairros</span>
    </div>
    <div class="legend-item">
      <div class="legend-color" style="background:#FF5722;"></div><span>Edificações Administrativas</span>
    </div>
  </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// Paleta de cores pré-definida para os bairros
const colorPalette = [
  '#FF6B6B', '#4ECDC4', '#45B7D1', '#FFBE0B', '#FB5607', 
  '#8338EC', '#3A86FF', '#06D6A0', '#118AB2', '#073B4C',
  '#EF476F', '#FFD166', '#06D6A0', '#118AB2', '#073B4C',
  '#7209B7', '#560BAD', '#480CA8', '#3A0CA3', '#3F37C9',
  '#4361EE', '#4CC9F0', '#F72585', '#B5179E', '#7209B7'
];

// Inicializar mapa centrado em Recife
const map = L.map('map').setView([-8.0476, -34.8770], 12);

// Camadas base
const ruas = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
attribution: '&copy; OpenStreetMap contributors'
});
const satelite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
attribution: 'Tiles &copy; Esri'
});
ruas.addTo(map);

// Controle de camadas
L.control.layers({ 'Ruas': ruas, 'Satélite': satelite }).addTo(map);

// Variável para armazenar a camada administrativa
let adminLayer = null;

// Carregar dados dos bairros (GeoJSON)
const bairrosPath = '<?php echo dirname($_SERVER['PHP_SELF']); ?>/dados/bairros.geojson';
const adminPath = '<?php echo dirname($_SERVER['PHP_SELF']); ?>/dados/admin.geojson';

console.log('Tentando carregar GeoJSON de bairros:', bairrosPath);
console.log('Tentando carregar GeoJSON de admin:', adminPath);

// Array para armazenar todas as camadas para ajustar o bounds
const allLayers = [];

// Carregar bairros
fetch(bairrosPath)
  .then(response => {
    console.log('Status da resposta (bairros):', response.status);
    console.log('Content-Type (bairros):', response.headers.get('content-type'));
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    return response.json();
  })
  .then(data => {
    console.log('GeoJSON de bairros carregado com sucesso:', data);
    
    // Adicionar camada GeoJSON ao mapa com cores diferentes para cada bairro
    const bairrosLayer = L.geoJSON(data, {
      style: function(feature) {
        // Selecionar uma cor da paleta com base no índice do bairro
        const bairroIndex = feature.properties.OBJECTID || 0;
        const colorIndex = bairroIndex % colorPalette.length;
        const bairroColor = colorPalette[colorIndex];
        
        return {
          color: "#333",           // Cor da borda (escura para contraste)
          weight: 1,               // Espessura da borda
          opacity: 0.8,             // Opacidade da borda
          fillColor: bairroColor,  // Cor de preenchimento (da paleta)
          fillOpacity: 0.5         // Opacidade do preenchimento
        };
      },
      onEachFeature: function (feature, layer) {
        // Adicionar popup com nome do bairro
        if (feature.properties && feature.properties.EBAIRRNOME) {
          layer.bindPopup(`
            <div style="text-align: center; padding: 5px;">
              <h3 style="margin: 0 0 8px 0; color: #333;">${feature.properties.EBAIRRNOME}</h3>
              <p style="margin: 0; font-size: 0.9rem;">Código: ${feature.properties.CBAIRRCODI || 'N/A'}</p>
            </div>
          `);
        }
      }
    }).addTo(map);
    
    allLayers.push(bairrosLayer);
    
    // Ajustar visualização do mapa para abranger todos os bairros
    if (allLayers.length === 1) { // Se for o primeiro carregado
      map.fitBounds(bairrosLayer.getBounds());
    }
  })
  .catch(error => {
    console.error('Erro ao carregar GeoJSON de bairros:', error);
    // Exibir mensagem de erro no mapa
    L.popup()
      .setLatLng(map.getCenter())
      .setContent(`Erro ao carregar dados dos bairros: ${error.message}`)
      .openOn(map);
  });

// Carregar edificações administrativas (mas não adicionar ao mapa ainda)
fetch(adminPath)
  .then(response => {
    console.log('Status da resposta (admin):', response.status);
    console.log('Content-Type (admin):', response.headers.get('content-type'));
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    return response.json();
  })
  .then(data => {
    console.log('GeoJSON de admin carregado com sucesso:', data);
    
    // Criar camada GeoJSON de edificações administrativas
    adminLayer = L.geoJSON(data, {
      style: {
        color: "#FF5722",       // Cor da borda (laranja/vermelho)
        weight: 3,             // Espessura da borda (mais grossa para destaque)
        opacity: 1,             // Opacidade da borda
        fillColor: "#FF5722",   // Cor de preenchimento
        fillOpacity: 0.3        // Opacidade do preenchimento
      },
      onEachFeature: function (feature, layer) {
        // Adicionar popup com informações da edificação
        if (feature.properties) {
          const dsqfl = feature.properties.DSQFL || 'Endereço não disponível';
          const nmendcomp = feature.properties.NMENDCOMP || 'Nome não disponível';
          
          layer.bindPopup(`
            <div style="padding: 8px; min-width: 250px;">
              <h3 style="margin: 0 0 10px 0; color: #FF5722; border-bottom: 1px solid #eee; padding-bottom: 5px;">Edificação Administrativa</h3>
              <p style="margin: 5px 0; font-size: 0.9rem;"><strong>Endereço:</strong><br>${dsqfl}</p>
              <p style="margin: 5px 0; font-size: 0.9rem;"><strong>Nome Complementar:</strong><br>${nmendcomp}</p>
            </div>
          `);
        }
      }
    });
    
    // Não adiciona ao mapa inicialmente - aguarda o checkbox
    
    console.log('Camada administrativa criada, aguardando ativação pelo usuário');
  })
  .catch(error => {
    console.error('Erro ao carregar GeoJSON de admin:', error);
    // Exibir mensagem de erro no mapa
    L.popup()
      .setLatLng(map.getCenter())
      .setContent(`Erro ao carregar dados administrativos: ${error.message}`)
      .openOn(map);
  });

// Event listener para o checkbox
document.getElementById('toggle-admin').addEventListener('change', function() {
  if (this.checked) {
    if (adminLayer) {
      map.addLayer(adminLayer);
      allLayers.push(adminLayer);
      
      // Ajustar visualização do mapa para incluir todas as camadas
      const group = new L.featureGroup(allLayers);
      map.fitBounds(group.getBounds().pad(0.1));
      
      console.log('Camada administrativa adicionada ao mapa');
    } else {
      console.log('Camada administrativa ainda não foi carregada');
    }
  } else {
    if (adminLayer && map.hasLayer(adminLayer)) {
      map.removeLayer(adminLayer);
      
      // Remover do array de camadas
      const index = allLayers.indexOf(adminLayer);
      if (index > -1) {
        allLayers.splice(index, 1);
      }
      
      // Ajustar visualização para as camadas restantes
      if (allLayers.length > 0) {
        const group = new L.featureGroup(allLayers);
        map.fitBounds(group.getBounds().pad(0.1));
      }
      
      console.log('Camada administrativa removida do mapa');
    }
  }
});

// Exemplo de pontos
const escolas = [
{coords: [-8.0593, -34.8716], name: "Escola A", info: "Ensino Fundamental"},
{coords: [-8.0305, -34.9042], name: "Escola B", info: "Ensino Médio"}
];
const parques = [
{coords: [-8.0500, -34.8870], name: "Parque da Jaqueira", info: "Área de lazer"},
{coords: [-8.0670, -34.8910], name: "Parque de Santana", info: "Área preservada"}
];

escolas.forEach(e => {
L.marker(e.coords).addTo(map)
.bindPopup(`<b>${e.name}</b><br>${e.info}`);
});

parques.forEach(p => {
L.circleMarker(p.coords, {
radius: 8, fillColor: "#00c853", color: "#000",
weight: 1, opacity: 1, fillOpacity: 0.8
}).addTo(map)
.bindPopup(`<b>${p.name}</b><br>${p.info}`);
});
</script>

<?php
// Footer correto
require_once __DIR__ . '/../footer.php';
?>
</body>
</html>