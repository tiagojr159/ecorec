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
  margin-left: 5px;
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

/* Modal */
.modal {
  display: none;
  position: fixed;
  z-index: 2000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.8);
  overflow: auto;
}

.modal-content {
  background-color: #fefefe;
  margin: 5% auto;
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
  max-width: 800px;
  border-radius: 10px;
  position: relative;
}

.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
  cursor: pointer;
  position: absolute;
  right: 15px;
  top: 10px;
}

.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
}

.modal-image {
  max-width: 100%;
  display: block;
  margin: 0 auto;
  border-radius: 5px;
}

.modal-title {
  margin-top: 0;
  margin-bottom: 15px;
  text-align: center;
}

/* Loading */
.loading-container {
  display: none;
  position: fixed;
  z-index: 3000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.8);
  justify-content: center;
  align-items: center;
  flex-direction: column;
}

.loading-spinner {
  border: 8px solid #f3f3f3;
  border-top: 8px solid #3498db;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  animation: spin 2s linear infinite;
  margin-bottom: 20px;
}

.loading-text {
  color: white;
  font-size: 18px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@media (max-width: 768px){
  .map-container { width: 95%; height: 70vh; margin: 10px auto; }
  .info-panel { bottom: 10px; right: 10px; padding: 10px; max-width: 200px; }
  .info-panel h3 { font-size: 0.9rem; }
  .info-panel p  { font-size: 0.8rem; }
  .modal-content { width: 95%; margin: 10% auto; }
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
      <button id="captureBtn" class="capture-btn" style="display: none;">Capturar e Melhorar</button>
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

<!-- Modal para imagem original -->
<div id="originalModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2 class="modal-title">Imagem Original</h2>
    <img id="originalImage" class="modal-image" src="" alt="Imagem original">
  </div>
</div>

<!-- Modal para imagem melhorada -->
<div id="enhancedModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2 class="modal-title">Imagem Melhorada pela IA</h2>
    <img id="enhancedImage" class="modal-image" src="" alt="Imagem melhorada">
    <div class="text-center mt-3">
      <button id="downloadEnhancedBtn" class="btn btn-success">Baixar Imagem Melhorada</button>
    </div>
  </div>
</div>

<!-- Loading -->
<div id="loadingContainer" class="loading-container">
  <div class="loading-spinner"></div>
  <div class="loading-text">Processando imagem com IA...</div>
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

  // Modais
  const originalModal = document.getElementById('originalModal');
  const enhancedModal = document.getElementById('enhancedModal');
  const originalImage = document.getElementById('originalImage');
  const enhancedImage = document.getElementById('enhancedImage');
  const downloadEnhancedBtn = document.getElementById('downloadEnhancedBtn');

  // Loading
  const loadingContainer = document.getElementById('loadingContainer');

  // Fechar modais
  document.querySelectorAll('.close').forEach(closeBtn => {
    closeBtn.addEventListener('click', function() {
      originalModal.style.display = 'none';
      enhancedModal.style.display = 'none';
    });
  });

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
    statusText.textContent = 'Área selecionada. Clique em "Capturar e Melhorar"';
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
      
      // Mostrar a imagem capturada no primeiro modal
      originalImage.src = imageData;
      originalModal.style.display = 'block';
      
      // Resetar seleção
      resetSelection();
      
      statusText.textContent = 'Imagem capturada. Enviando para IA...';
      
      // Enviar para a API da OpenAI
      enhanceImage(imageData);
    }).catch(err => {
      console.error('Erro ao capturar imagem:', err);
      statusText.textContent = 'Erro ao capturar imagem. Tente novamente.';
    });
  });

  // Função para enviar imagem para a API da OpenAI
  async function enhanceImage(imageData) {
    // Mostrar loading
    loadingContainer.style.display = 'flex';
    
    try {
      // Fazer requisição para a API
      const response = await fetch('chat.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          image_base64: imageData
        })
      });
      
      const data = await response.json();
      
      // Esconder loading
      loadingContainer.style.display = 'none';
      
      if (data.status === 'ok' && data.enhanced_base64) {
        // Mostrar imagem melhorada no segundo modal
        enhancedImage.src = data.enhanced_base64;
        enhancedModal.style.display = 'block';
        statusText.textContent = 'Imagem melhorada com sucesso!';
        
        // Configurar botão de download
        downloadEnhancedBtn.onclick = function() {
          const link = document.createElement('a');
          link.download = 'mapa-melhorado.png';
          link.href = data.enhanced_base64;
          link.click();
        };
      } else {
        console.error('Erro na resposta da API:', data);
        statusText.textContent = 'Erro ao processar imagem com IA.';
        alert('Erro ao processar imagem com IA: ' + (data.error || 'Erro desconhecido'));
      }
    } catch (error) {
      console.error('Erro na requisição:', error);
      loadingContainer.style.display = 'none';
      statusText.textContent = 'Erro ao comunicar com o servidor.';
      alert('Erro ao comunicar com o servidor: ' + error.message);
    }
  }

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

  // Fechar modais ao clicar fora
  window.addEventListener('click', function(event) {
    if (event.target === originalModal) {
      originalModal.style.display = 'none';
    }
    if (event.target === enhancedModal) {
      enhancedModal.style.display = 'none';
    }
  });
</script>

<?php
require_once __DIR__ . '/../footer.php';
?>