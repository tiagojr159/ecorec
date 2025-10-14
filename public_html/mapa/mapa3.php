<?php
require_once __DIR__ . '/../header.php';
?>

<!-- ======== ESTILO DO MAPA ========= -->
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

/* Mapa */
#map {
  height: 100%;
  width: 100%;
  border-radius: 9px;
}

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
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.map-title {
  margin: 0;
  font-size: 1.2rem;
  color: #333;
  font-weight: 600;
}

/* Painel de informações */
.info-panel {
  position: absolute;
  bottom: 20px;
  right: 20px;
  background: rgba(255, 255, 255, 0.95);
  padding: 15px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  z-index: 1000;
  max-width: 300px;
}

.info-panel h3 { margin: 0 0 10px 0; font-size: 1rem; color: #333; }
.info-panel p  { margin: 5px 0; font-size: 0.9rem; color: #555; }

/* Botão de captura */
.capture-btn {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  transition: background-color 0.3s;
}

.capture-btn:hover {
  background-color: #45a049;
}

/* Área de seleção */
.selection-box {
  position: absolute;
  border: 2px dashed #FF0000;
  background-color: rgba(255, 0, 0, 0.1);
  pointer-events: none;
  z-index: 1001;
  display: none;
}

@media (max-width: 768px){
  .map-container { width: 95%; height: 70vh; margin: 10px auto; }
  .info-panel { bottom: 10px; right: 10px; padding: 10px; max-width: 200px; }
  .info-panel h3 { font-size: 0.9rem; }
  .info-panel p  { font-size: 0.8rem; }
}
</style>

<!-- ======== CONTEÚDO PRINCIPAL ========= -->
<div class="container mt-4">
  <h2 class="text-center mb-3">Mapa Satélite – Igarassu (Zoom Forçado até 21)</h2>
</div>

<div class="map-container">
  <div class="map-header">
    <h1 class="map-title">Mapa Satélite - Igarassu</h1>
    <div>
      <button id="selectAreaBtn" class="capture-btn">Selecionar Área</button>
      <button id="captureBtn" class="capture-btn" style="display: none;">Capturar Seleção</button>
    </div>
  </div>

  <div id="map"></div>
  <div id="selectionBox" class="selection-box"></div>

  <div class="info-panel">
    <h3>Informações</h3>
    <p><strong>Localização:</strong> Igarassu, Pernambuco</p>
    <p><strong>Zoom:</strong> até 21 (forçado)</p>
    <p><strong>Mapa:</strong> Satélite e Ruas</p>
    <p id="captureStatus" style="display: none;"><strong>Status:</strong> <span id="statusText">Aguardando seleção</span></p>
  </div>
</div>

<!-- ======== LEAFLET MAP JS ========= -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-providers@1.13.0/leaflet-providers.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
  // Coordenadas de Igarassu
  const coordenadas = [-8.06469, -34.8806];

  // Inicializar o mapa
  const map = L.map('map', {
    center: coordenadas,
    zoom: 20,
    maxZoom: 21
  });

  // Camadas base
  const osm = L.tileLayer.provider('OpenStreetMap.Mapnik');

  const esriSat = L.tileLayer.provider('Esri.WorldImagery', {
    maxNativeZoom: 18,
    maxZoom: 21
  }).addTo(map);

  // Controle de camadas
  const baseMaps = {
    "Mapa Comum": osm,
    "Satélite (Zoom Forçado)": esriSat
  };
  L.control.layers(baseMaps).addTo(map);

  // Marcador principal
  L.marker(coordenadas)
    .addTo(map)
    .bindPopup('<b>Igarassu</b><br>Zoom artificial até 21 (pode perder qualidade).')
    .openPopup();

  // Variáveis para seleção e captura
  let isSelecting = false;
  let startPoint = null;
  let selectionBox = document.getElementById('selectionBox');
  let capturedImageData = null; // Armazenará a imagem capturada

  // Botões
  const selectAreaBtn = document.getElementById('selectAreaBtn');
  const captureBtn = document.getElementById('captureBtn');
  const captureStatus = document.getElementById('captureStatus');
  const statusText = document.getElementById('statusText');

  // Evento para iniciar a seleção
  selectAreaBtn.addEventListener('click', function() {
    isSelecting = true;
    map.dragging.disable();
    map.touchZoom.disable();
    map.doubleClickZoom.disable();
    map.scrollWheelZoom.disable();
    map.boxZoom.disable();
    map.keyboard.disable();
    
    selectAreaBtn.style.display = 'none';
    captureBtn.style.display = 'inline-block';
    captureStatus.style.display = 'block';
    statusText.textContent = 'Clique e arraste para selecionar a área';
    
    // Adiciona cursor personalizado
    document.getElementById('map').style.cursor = 'crosshair';
  });

  // Eventos do mouse para seleção
  map.getContainer().addEventListener('mousedown', function(e) {
    if (!isSelecting) return;
    
    startPoint = {
      x: e.offsetX,
      y: e.offsetY
    };
    
    selectionBox.style.left = startPoint.x + 'px';
    selectionBox.style.top = startPoint.y + 'px';
    selectionBox.style.width = '0px';
    selectionBox.style.height = '0px';
    selectionBox.style.display = 'block';
  });

  map.getContainer().addEventListener('mousemove', function(e) {
    if (!isSelecting || !startPoint) return;
    
    const currentX = e.offsetX;
    const currentY = e.offsetY;
    
    const width = Math.abs(currentX - startPoint.x);
    const height = Math.abs(currentY - startPoint.y);
    
    const left = Math.min(currentX, startPoint.x);
    const top = Math.min(currentY, startPoint.y);
    
    selectionBox.style.left = left + 'px';
    selectionBox.style.top = top + 'px';
    selectionBox.style.width = width + 'px';
    selectionBox.style.height = height + 'px';
  });

  map.getContainer().addEventListener('mouseup', function() {
    if (!isSelecting) return;
    
    startPoint = null;
    statusText.textContent = 'Área selecionada. Clique em "Capturar Seleção"';
  });

  // Evento para capturar a área selecionada
  captureBtn.addEventListener('click', function() {
    if (!selectionBox.style.display || selectionBox.style.display === 'none') {
      alert('Por favor, selecione uma área primeiro');
      return;
    }
    
    statusText.textContent = 'Capturando imagem...';
    
    // Obter as dimensões da seleção
    const rect = selectionBox.getBoundingClientRect();
    const mapRect = map.getContainer().getBoundingClientRect();
    
    // Calcular posição relativa ao mapa
    const left = rect.left - mapRect.left;
    const top = rect.top - mapRect.top;
    
    // Usar html2canvas para capturar apenas a área selecionada
    html2canvas(map.getContainer(), {
      x: left,
      y: top,
      width: rect.width,
      height: rect.height,
      useCORS: true,
      allowTaint: true
    }).then(canvas => {
      // Converter para imagem base64
      const imageData = canvas.toDataURL('image/png');
      
      // Armazenar em memória
      capturedImageData = imageData;
      
      // Criar link para download
      const link = document.createElement('a');
      link.download = 'mapa-capturado.png';
      link.href = imageData;
      link.click();
      
      // Resetar seleção
      resetSelection();
      
      statusText.textContent = 'Imagem capturada e salva com sucesso!';
      
      // Mostrar a imagem capturada em um modal
      showCapturedImage(imageData);
    }).catch(err => {
      console.error('Erro ao capturar imagem:', err);
      statusText.textContent = 'Erro ao capturar imagem. Tente novamente.';
    });
  });

  // Função para resetar a seleção
  function resetSelection() {
    isSelecting = false;
    selectionBox.style.display = 'none';
    selectAreaBtn.style.display = 'inline-block';
    captureBtn.style.display = 'none';
    
    // Reabilitar controles do mapa
    map.dragging.enable();
    map.touchZoom.enable();
    map.doubleClickZoom.enable();
    map.scrollWheelZoom.enable();
    map.boxZoom.enable();
    map.keyboard.enable();
    
    // Restaurar cursor
    document.getElementById('map').style.cursor = '';
  }

  // Função para mostrar a imagem capturada em um modal
  function showCapturedImage(imageData) {
    // Criar modal
    const modal = document.createElement('div');
    modal.style.position = 'fixed';
    modal.style.top = '0';
    modal.style.left = '0';
    modal.style.width = '100%';
    modal.style.height = '100%';
    modal.style.backgroundColor = 'rgba(0,0,0,0.8)';
    modal.style.display = 'flex';
    modal.style.justifyContent = 'center';
    modal.style.alignItems = 'center';
    modal.style.zIndex = '2000';
    
    // Criar imagem
    const img = document.createElement('img');
    img.src = imageData;
    img.style.maxWidth = '90%';
    img.style.maxHeight = '90%';
    img.style.border = '5px solid white';
    img.style.boxShadow = '0 0 20px rgba(0,0,0,0.5)';
    
    // Adicionar imagem ao modal
    modal.appendChild(img);
    
    // Adicionar modal ao body
    document.body.appendChild(modal);
    
    // Fechar modal ao clicar
    modal.addEventListener('click', function() {
      document.body.removeChild(modal);
    });
    
    // Adicionar botão de fechar
    const closeBtn = document.createElement('button');
    closeBtn.textContent = 'Fechar (X)';
    closeBtn.style.position = 'absolute';
    closeBtn.style.top = '20px';
    closeBtn.style.right = '20px';
    closeBtn.style.padding = '10px';
    closeBtn.style.backgroundColor = '#f44336';
    closeBtn.style.color = 'white';
    closeBtn.style.border = 'none';
    closeBtn.style.borderRadius = '4px';
    closeBtn.style.cursor = 'pointer';
    closeBtn.style.fontSize = '16px';
    
    closeBtn.addEventListener('click', function() {
      document.body.removeChild(modal);
    });
    
    modal.appendChild(closeBtn);
  }
</script>

<?php
require_once __DIR__ . '/../footer.php';
?>