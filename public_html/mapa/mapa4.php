<?php
//mapa4.php
// Página do mapa com funcionalidades de captura e melhoria de imagem via IA
require_once __DIR__ . '/../header.php';
?>

<!-- ======== ESTILO DO MAPA ========= -->
<style>
  html,
  body {
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
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    border: 1px solid #ddd;
    position: relative;
  }

  /* Mapa */
  #map {
    height: 100%;
    width: 100%;
    border-radius: 9px;
    touch-action: none; /* Desabilitar ações de toque padrão */
  }

  /* Cabeçalho da telinha */
  .map-header {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to bottom, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.7));
    padding: 10px 15px;
    border-bottom: 1px solid #eee;
    z-index: 1000;
    border-radius: 9px 9px 0 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
  }

  .map-title {
    margin: 0 0 10px 0;
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
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    max-width: 300px;
  }

  .info-panel h3 {
    margin: 0 0 10px 0;
    font-size: 1rem;
    color: #333;
  }

  .info-panel p {
    margin: 5px 0;
    font-size: 0.9rem;
    color: #555;
  }

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

  /* Botão de seleção de área - ESTILO AMARELO QUANDO ATIVO */
  #selectAreaBtn {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s;
  }

  #selectAreaBtn.active {
    background-color: #FFEB3B;
    /* Amarelo */
    color: #333;
    /* Texto escuro para contraste */
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
    background-color: rgba(0, 0, 0, 0.8);
    overflow: auto;
  }

  .modal-content {
    background-color: #fefefe;
    margin: 3% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 90%;
    max-width: 1200px;
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
    z-index: 2001;
  }

  .close:hover,
  .close:focus {
    color: black;
    text-decoration: none;
  }

  .modal-title {
    margin-top: 0;
    margin-bottom: 15px;
    text-align: center;
  }

  /* Container para as duas imagens */
  .images-container {
    display: flex;
    flex-direction: row;
    gap: 20px;
    margin-top: 20px;
  }

  .image-column {
    flex: 1;
    text-align: center;
    position: relative;
    /* Adicionado para posicionamento do overlay */
  }

  .image-column h3 {
    margin-top: 0;
    margin-bottom: 10px;
    color: #333;
  }

  .modal-image {
    max-width: 100%;
    height: auto;
    display: block;
    margin: 0 auto;
    border-radius: 5px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }

  .download-section {
    margin-top: 20px;
    text-align: center;
  }

  /* Estimativas Card */
  .estimativas-card {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-top: 20px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  }

  .estimativas-card h3 {
    margin-top: 0;
    margin-bottom: 15px;
    color: #495057;
    text-align: center;
  }

  .estimativas-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
  }

  .estimativa-item {
    background-color: white;
    border-radius: 6px;
    padding: 12px;
    text-align: center;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }

  .estimativa-item .icon {
    font-size: 24px;
    margin-bottom: 8px;
  }

  .estimativa-item .label {
    font-weight: bold;
    color: #495057;
    font-size: 14px;
  }

  .estimativa-item .value {
    color: #28a745;
    font-size: 18px;
    margin-top: 5px;
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
    background-color: rgba(0, 0, 0, 0.8);
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
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }

  /* Modal de seleção de melhorias */
  .improvements-modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    overflow: auto;
  }

  .improvements-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
    border-radius: 10px;
    position: relative;
  }

  /* Estilo para a animação de carregamento */
  .loading-animation {
    text-align: center;
    padding: 30px 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }

  .loading-animation p {
    font-size: 16px;
    line-height: 1.5;
    margin-bottom: 20px;
    color: #333;
  }

  .loading-animation img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  }

  .improvements-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
    margin: 20px 0;
  }

  .improvement-item {
    display: flex;
    align-items: center;
    padding: 10px;
    background-color: #f9f9f9;
    border-radius: 5px;
    transition: background-color 0.2s;
  }

  .improvement-item:hover {
    background-color: #f0f0f0;
  }

  .improvement-item input[type="checkbox"] {
    margin-right: 10px;
    transform: scale(1.2);
  }

  .improvement-item label {
    cursor: pointer;
    font-size: 14px;
  }

  .improvements-actions {
    text-align: center;
    margin-top: 20px;
  }

  .improvements-actions button {
    margin: 0 10px;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
  }

  .improvements-actions .submit-btn {
    background-color: #4CAF50;
    color: white;
  }

  .improvements-actions .submit-btn:hover {
    background-color: #45a049;
  }

  .improvements-actions .cancel-btn {
    background-color: #f44336;
    color: white;
  }

  .improvements-actions .cancel-btn:hover {
    background-color: #d32f2f;
  }

  /* Overlay de carregamento da imagem */
  .image-loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    z-index: 10;
    border-radius: 5px;
  }

  .image-loading-overlay img {
    max-width: 80%;
    max-height: 80%;
    margin-bottom: 15px;
  }

  .image-loading-overlay .loading-text {
    color: white;
    font-size: 16px;
    text-align: center;
    padding: 0 10px;
  }

  @media (max-width: 768px) {
    .map-container {
      width: 95%;
      height: 70vh;
      margin: 10px auto;
    }

    .info-panel {
      bottom: 10px;
      right: 10px;
      padding: 10px;
      max-width: 200px;
    }

    .info-panel h3 {
      font-size: 0.9rem;
    }

    .info-panel p {
      font-size: 0.8rem;
    }

    .modal-content {
      width: 95%;
      margin: 5% auto;
      padding: 15px;
    }

    /* Em dispositivos móveis, as imagens ficam uma abaixo da outra */
    .images-container {
      flex-direction: column;
      gap: 15px;
    }

    .image-column {
      margin-bottom: 15px;
    }

    .estimativas-grid {
      grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    }

    .improvements-grid {
      grid-template-columns: 1fr;
    }
    
    /* Melhorar interação com toque em dispositivos móveis */
    .capture-btn {
      padding: 12px 20px;
      font-size: 16px;
      margin: 5px;
    }
    
    .map-header {
      padding: 15px;
    }
    
    /* Garantir que a área de seleção seja visível em dispositivos móveis */
    .selection-box {
      border-width: 3px;
    }
  }
</style>

<!-- ======== CONTEÚDO PRINCIPAL ========= -->
<div class="container mt-4">
  <h2 class="text-center mb-3">Mapa Satélite – Bairro Santo Antonio </h2>
</div>

<div class="map-container">
  <div class="map-header">
    <h1 class="map-title">Mapa Satélite - Recife</h1>
    <button id="selectAreaBtn" class="capture-btn">Selecionar Área</button>
    <button id="captureBtn" class="capture-btn btn btn-warning" style="background-color: yellow; color: #333; display: none;">Capturar e Melhorar</button>

  </div>

  <div id="map"></div>
  <div id="selectionBox" class="selection-box"></div>

  <div class="info-panel">
    <h3>Informações</h3>
    <p><strong>Localização:</strong> Recife, Pernambuco</p>
    <p><strong>Zoom:</strong> até 23 (forçado)</p>
  </div>
</div>

<!-- Modal de seleção de melhorias -->
<div id="improvementsModal" class="improvements-modal">
  <div class="improvements-content">
    <span class="close">&times;</span>
    <h2 class="modal-title">Potencial</h2>
    
    <!-- Animação de carregamento -->
    <div id="loadingAnimation" class="loading-animation">
      <p>Estamos fazendo o cruzamento de dados com a base de dados da Prefeitura do Recife para Identificar potencial de revitalização
       </p>
      <img src="../images/Prague.gif" alt="Carregando dados...">
    </div>
    
    <!-- Formulário de melhorias (inicialmente oculto) -->
    <div id="improvementsForm" style="display: none;">
      <h3>O cruzamentos de dados identificou os seguinte potencial</h3>
      <div class="improvements-grid" id="improvementsGrid">
        <!-- Itens de melhoria serão adicionados via JavaScript -->
      </div>

      <div class="improvements-actions">
        <button id="cancelImprovementsBtn" class="cancel-btn">Cancelar</button>
        <button id="submitImprovementsBtn" class="submit-btn">Aplicar Potencial</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal único para ambas as imagens -->
<div id="imageModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2 class="modal-title">Comparação de Imagens</h2>

    <div class="images-container">
      <!-- Coluna da imagem original -->
      <div class="image-column">
        <h3>Imagem Original</h3>
        <img id="originalImage" class="modal-image" src="" alt="Imagem original">
      </div>

      <!-- Coluna da imagem melhorada -->
      <div class="image-column">
        <h3>Planejamento Urbano</h3>
        <div class="image-column-container" style="position: relative;">
          <img id="enhancedImage" class="modal-image" style="display:none" alt="Imagem melhorada">
          <div id="imageLoadingOverlay" class="image-loading-overlay" style="display: none;">
            <img src="../images/trabalho.gif" alt="Carregando..." width="500" height="450">
            <div class="loading-text">Estamos trabalhando na sua imagem...</div>
          </div>
        </div>
        <div class="download-section">
          <button id="downloadEnhancedBtn" class="btn btn-success">Baixar Imagem Melhorada</button>
        </div>

        <!-- Card de estimativas -->
        <div class="estimativas-card">
          <h3>Estimativas de Materiais</h3>
          <div class="estimativas-grid" id="estimativasGrid">
            <!-- As estimativas serão inseridas aqui via JavaScript -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Loading -->
<div id="loadingContainer" class="loading-container" style="display: none;">
  <div class="loading-spinner"></div>
  <div class="loading-text">Estamos Trabalhando...</div>
</div>

<!-- ======== LEAFLET MAP JS ========= -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-providers@1.13.0/leaflet-providers.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
  // Lista de melhorias disponíveis
  const improvementOptions = [{
      id: 'arvores',
      label: 'Árvores'
    },
    {
      id: 'grama',
      label: 'Grama'
    },
    {
      id: 'fiacao',
      label: 'Fiação'
    },
    {
      id: 'pintura',
      label: 'Pintura'
    },
    {
      id: 'estacao_bicicleta',
      label: 'Estação de Bicicleta Itaú'
    },
    {
      id: 'postes_led',
      label: 'Postes de LED'
    },
    {
      id: 'placa_solar',
      label: 'Placa Solar'
    },
    {
      id: 'ciclofaixa',
      label: 'Ciclofaixa'
    },
    {
      id: 'camera_seguranca',
      label: 'Câmera de Segurança'
    },
    {
      id: 'estacao_carro_eletrico',
      label: 'Estação de Carro Elétrico'
    },
    {
      id: 'bicicletario',
      label: 'Bicicletário'
    },
    {
      id: 'banco_praca',
      label: 'Banco de Praça'
    },
    {
      id: 'rampa_cadeirante',
      label: 'Rampa para Cadeirante'
    }
  ];

  // Coordenadas de Recife
  const coordenadas = [-8.06469, -34.8806];

  // Inicializar o mapa com zoom aumentado (23 em vez de 20)
  const map = L.map('map', {
    center: coordenadas,
    zoom: 23, // Zoom aumentado
    maxZoom: 23
  });

  // Camadas base
  const osm = L.tileLayer.provider('OpenStreetMap.Mapnik');

  const esriSat = L.tileLayer.provider('Esri.WorldImagery', {
    maxNativeZoom: 18,
    maxZoom: 23
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
    .bindPopup('<b>Recife</b><br>Zoom artificial até 23 (pode perder qualidade).')
    .openPopup();

  // Variáveis para seleção e captura
  let isSelecting = false;
  let startPoint = null;
  let selectionBox = document.getElementById('selectionBox');
  let capturedImageData = null; // Armazenará a imagem capturada
  let selectionDimensions = {
    width: 0,
    height: 0
  }; // Armazenará as dimensões da seleção
  let selectedImprovements = []; // Armazenará as melhorias selecionadas

  // Botões
  const selectAreaBtn = document.getElementById('selectAreaBtn');
  const captureBtn = document.getElementById('captureBtn');

  // Modal de melhorias
  const improvementsModal = document.getElementById('improvementsModal');
  const improvementsGrid = document.getElementById('improvementsGrid');
  const submitImprovementsBtn = document.getElementById('submitImprovementsBtn');
  const cancelImprovementsBtn = document.getElementById('cancelImprovementsBtn');

  // Modal único
  const imageModal = document.getElementById('imageModal');
  const originalImage = document.getElementById('originalImage');
  const enhancedImage = document.getElementById('enhancedImage');
  const downloadEnhancedBtn = document.getElementById('downloadEnhancedBtn');
  const estimativasGrid = document.getElementById('estimativasGrid');
  const imageLoadingOverlay = document.getElementById('imageLoadingOverlay');

  // Loading
  const loadingContainer = document.getElementById('loadingContainer');

  // Função para embaralhar array (algoritmo Fisher-Yates)
  function shuffleArray(array) {
    const newArray = [...array];
    for (let i = newArray.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [newArray[i], newArray[j]] = [newArray[j], newArray[i]];
    }
    return newArray;
  }

  // Função para obter melhorias aleatórias
  function getRandomImprovements() {
    // Embaralhar o array de opções
    const shuffledOptions = shuffleArray(improvementOptions);

    // Sortear um número entre 5 e 8
    const count = Math.floor(Math.random() * 4) + 5; // 5-8

    // Retornar os primeiros 'count' itens
    return shuffledOptions.slice(0, count);
  }

  // Inicializar o grid de melhorias
  function initImprovementsGrid() {
    improvementsGrid.innerHTML = '';

    // Mostrar a animação e esconder o formulário
    document.getElementById('loadingAnimation').style.display = 'flex';
    document.getElementById('improvementsForm').style.display = 'none';

    // Obter melhorias aleatórias
    const randomImprovements = getRandomImprovements();

    randomImprovements.forEach(improvement => {
      const item = document.createElement('div');
      item.className = 'improvement-item';

      const checkbox = document.createElement('input');
      checkbox.type = 'checkbox';
      checkbox.id = improvement.id;
      checkbox.value = improvement.id;

      const label = document.createElement('label');
      label.htmlFor = improvement.id;
      label.textContent = improvement.label;

      item.appendChild(checkbox);
      item.appendChild(label);
      improvementsGrid.appendChild(item);
    });

    // Após 5 segundos, esconder a animação e mostrar o formulário
    setTimeout(() => {
      document.getElementById('loadingAnimation').style.display = 'none';
      document.getElementById('improvementsForm').style.display = 'block';
    }, 5000);
  }

  // Fechar modal de melhorias
  cancelImprovementsBtn.addEventListener('click', function() {
    improvementsModal.style.display = 'none';
    resetSelection();
  });

  // Fechar modal de imagens
  const closeBtns = document.querySelectorAll('.close');
  closeBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      const modal = btn.closest('.modal, .improvements-modal');
      if (modal) {
        modal.style.display = 'none';
      }
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

    // Adicionar classe active para mudar cor do botão para amarelo
    this.classList.add('active');

    selectAreaBtn.style.display = 'none';
    captureBtn.style.display = 'inline-block';

    // Adiciona cursor personalizado
    document.getElementById('map').style.cursor = 'crosshair';
  });

  // Eventos de mouse para seleção (desktop)
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
  });

  // Eventos de toque para seleção (dispositivos móveis)
  map.getContainer().addEventListener('touchstart', function(e) {
    if (!isSelecting) return;
    
    // Impedir o comportamento padrão para evitar rolagem da página
    e.preventDefault();
    
    // Obter as coordenadas do toque
    const touch = e.touches[0];
    const rect = map.getContainer().getBoundingClientRect();
    
    startPoint = {
        x: touch.clientX - rect.left,
        y: touch.clientY - rect.top
    };
    
    selectionBox.style.left = startPoint.x + 'px';
    selectionBox.style.top = startPoint.y + 'px';
    selectionBox.style.width = '0px';
    selectionBox.style.height = '0px';
    selectionBox.style.display = 'block';
  });

  map.getContainer().addEventListener('touchmove', function(e) {
    if (!isSelecting || !startPoint) return;
    
    // Impedir o comportamento padrão para evitar rolagem da página
    e.preventDefault();
    
    // Obter as coordenadas do toque
    const touch = e.touches[0];
    const rect = map.getContainer().getBoundingClientRect();
    
    const currentX = touch.clientX - rect.left;
    const currentY = touch.clientY - rect.top;
    
    const width = Math.abs(currentX - startPoint.x);
    const height = Math.abs(currentY - startPoint.y);
    
    const left = Math.min(currentX, startPoint.x);
    const top = Math.min(currentY, startPoint.y);
    
    selectionBox.style.left = left + 'px';
    selectionBox.style.top = top + 'px';
    selectionBox.style.width = width + 'px';
    selectionBox.style.height = height + 'px';
  });

  map.getContainer().addEventListener('touchend', function(e) {
    if (!isSelecting) return;
    
    // Impedir o comportamento padrão
    e.preventDefault();
    
    startPoint = null;
  });

  // Evento para capturar a área selecionada
  captureBtn.addEventListener('click', function() {
    if (!selectionBox.style.display || selectionBox.style.display === 'none') {
      alert('Por favor, selecione uma área primeiro');
      return;
    }

    // Obter as dimensões da seleção
    const rect = selectionBox.getBoundingClientRect();
    const mapRect = map.getContainer().getBoundingClientRect();

    // Armazenar as dimensões da seleção
    selectionDimensions.width = rect.width;
    selectionDimensions.height = rect.height;

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

      // Resetar seleção
      resetSelection();

      // Mostrar o modal de melhorias
      improvementsModal.style.display = 'block';

      // Inicializar o grid de melhorias
      initImprovementsGrid();
    }).catch(err => {
      console.error('Erro ao capturar imagem:', err);
      alert('Erro ao capturar imagem. Tente novamente.');
    });
  });

  // Evento para submeter as melhorias selecionadas
  submitImprovementsBtn.addEventListener('click', function(e) {
    e.preventDefault(); // Prevenir comportamento padrão
    e.stopPropagation(); // Impedir propagação do evento

    enhancedImage.style.display = 'none';

    // Coletar as melhorias selecionadas
    selectedImprovements = [];
    const checkboxes = improvementsGrid.querySelectorAll('input[type="checkbox"]:checked');

    if (checkboxes.length === 0) {
      alert('Por favor, selecione pelo menos uma melhoria');
      return;
    }

    checkboxes.forEach(checkbox => {
      selectedImprovements.push(checkbox.value);
    });

    // Fechar o modal de melhorias
    improvementsModal.style.display = 'none';

    // Mostrar a imagem capturada no modal
    originalImage.src = capturedImageData;
    imageModal.style.display = 'block';

    // Mostrar o overlay de carregamento na imagem melhorada
    imageLoadingOverlay.style.display = 'contents';
    imageLoadingOverlay.querySelector('img').style.margin = '0 auto';

    // Enviar para a API da OpenAI
    enhanceImage(capturedImageData, selectedImprovements);
  });

  // Função para enviar imagem para a API da OpenAI
  async function enhanceImage(imageData, improvements) {
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
          image_base64: imageData,
          improvements: improvements
        })
      });

      const data = await response.json();

      // Esconder loading
      loadingContainer.style.display = 'none';

      if (data.status === 'ok' && data.enhanced_base64) {
        // Mostrar imagem melhorada no modal
        enhancedImage.src = data.enhanced_base64;

        // Esconder o overlay de carregamento
        imageLoadingOverlay.style.display = 'none';
        enhancedImage.style.display = 'block';

        // Configurar botão de download
        downloadEnhancedBtn.onclick = function() {
          enhancedImage.style.display = 'block';

          const link = document.createElement('a');
          link.download = 'mapa-melhorado.png';
          link.href = data.enhanced_base64;
          link.click();
        };
        calcularEstimativas(selectionDimensions.width, selectionDimensions.height);
      } else {
        console.error('Erro na resposta da API:', data);
        imageLoadingOverlay.style.display = 'none';
        alert('Erro ao processar imagem com IA: ' + (data.error || 'Erro desconhecido'));
      }
    } catch (error) {
      console.error('Erro na requisição:', error);
      loadingContainer.style.display = 'none';
      imageLoadingOverlay.style.display = 'none';
      alert('Erro ao comunicar com o servidor: ' + error.message);
    }
  }

  // Função para calcular estimativas
  function calcularEstimativas(width, height) {
    // Estimativa de área em metros quadrados (suposição: 1 pixel = 0.1m²)
    const areaPixels = width * height;
    const areaM2 = Math.round(areaPixels * 0.1);

    // Cálculo das estimativas
    const estimativas = {
      placasSolares: Math.ceil(areaM2 * 0.05 / 2), // 5% da área, cada placa 2m²
      grama: Math.round(areaM2 * 0.6), // 60% da área
      arvores: Math.ceil(areaM2 * 0.15 / 4), // 15% da área, cada árvore 4m²
      bancos: Math.ceil(areaM2 / 200), // 1 banco a cada 200m²
      tinta: Math.ceil(areaM2 * 0.02 / 10), // 2% da área, 1 litro pinta 10m²
      lampadas: Math.ceil(areaM2 / 100), // 1 lâmpada a cada 100m²
      fiação: Math.ceil(areaM2 / 100 * 15) // 15m de fiação a cada 100m²
    };

    // Limpar grid anterior
    estimativasGrid.innerHTML = '';

    // Adicionar itens de estimativa
    const itens = [{
        icon: '☀️',
        label: 'Placas Solares',
        value: `${(estimativas.placasSolares / 50).toFixed(1)} unidades`
      },
      {
        icon: '🌱',
        label: 'Grama Plantada',
        value: `${(estimativas.grama / 50).toFixed(1)} m²`
      },
      {
        icon: '🌳',
        label: 'Árvores Plantadas',
        value: `${(estimativas.arvores / 50).toFixed(1)} unidades`
      },
      {
        icon: '🪑',
        label: 'Bancos de Jardim',
        value: `${(estimativas.bancos / 50).toFixed(1)} unidades`
      },
      {
        icon: '🎨',
        label: 'Tinta para Pintura',
        value: `${(estimativas.tinta / 50).toFixed(1)} litros`
      },
      {
        icon: '💡',
        label: 'Lâmpadas',
        value: `${(estimativas.lampadas / 50).toFixed(1)} unidades`
      },
      {
        icon: '🔌',
        label: 'Fiação Elétrica',
        value: `${(estimativas.fiação / 50).toFixed(1)} metros`
      }
    ];

    itens.forEach(item => {
      const div = document.createElement('div');
      div.className = 'estimativa-item';
      div.innerHTML = `
        <div class="icon">${item.icon}</div>
        <div class="label">${item.label}</div>
        <div class="value">${item.value}</div>
      `;
      estimativasGrid.appendChild(div);
    });
  }

  // Função para resetar a seleção
  function resetSelection() {
    isSelecting = false;
    selectionBox.style.display = 'none';
    selectAreaBtn.style.display = 'inline-block';
    captureBtn.style.display = 'none';
    
    // Remover classe active do botão
    selectAreaBtn.classList.remove('active');
    
    // Reabilitar controles do mapa
    map.dragging.enable();
    map.touchZoom.enable();
    map.doubleClickZoom.enable();
    map.scrollWheelZoom.enable();
    map.boxZoom.enable();
    map.keyboard.enable();
    
    // Restaurar cursor
    document.getElementById('map').style.cursor = '';
    
    // Garantir que eventos de toque funcionem corretamente após resetar
    map.getContainer().style.touchAction = 'manipulation';
  }

  // Fechar modal ao clicar fora
  window.addEventListener('click', function(event) {
    if (event.target === imageModal) {
      imageModal.style.display = 'none';
    }
    if (event.target === improvementsModal) {
      improvementsModal.style.display = 'none';
      resetSelection();
    }
  });
</script>

<?php
require_once __DIR__ . '/../footer.php';
?>