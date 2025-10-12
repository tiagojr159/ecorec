<?php
// Caminhos corretos para incluir header e footer
require_once __DIR__ . '/../header.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mapa do Google - Recife</title>

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

#google-map {
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

/* Mensagem de erro */
.error-message {
  display: none;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.2);
  text-align: center;
  max-width: 400px;
  z-index: 2000;
}

.error-message h3 {
  color: #d32f2f;
  margin: 0 0 10px 0;
}

.error-message p {
  margin: 0 0 15px 0;
  color: #666;
}

.error-message button {
  background: #d32f2f;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
}

@media (max-width: 768px){
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
}
</style>
</head>
<body>

<div class="map-container">
  <div class="map-header">
    <h1 class="map-title">Mapa do Google - Recife</h1>
  </div>
  
  <div id="google-map"></div>
  
  <div class="info-panel">
    <h3>Informações</h3>
    <p><strong>Localização:</strong> Recife, Pernambuco</p>
    <p><strong>Zoom Máximo:</strong> Permitido</p>
    <p><strong>Controles:</strong> Arraste para mover, use o scroll para zoom</p>
  </div>
  
  <!-- Mensagem de erro -->
  <div id="error-message" class="error-message">
    <h3>Erro ao carregar o mapa</h3>
    <p id="error-text">Não foi possível carregar o mapa do Google. Verifique sua conexão com a internet.</p>
    <button onclick="location.reload()">Tentar novamente</button>
  </div>
</div>

<!-- API do Google Maps -->
<script>
// Função para inicializar o mapa
function initMap() {
  try {
    // Coordenadas do centro de Recife
    const recife = { lat: -8.0476, lng: -34.8770 };
    
    // Opções do mapa
    const mapOptions = {
      zoom: 12,                    // Zoom inicial
      center: recife,              // Centralizar em Recife
      mapTypeId: google.maps.MapTypeId.ROADMAP, // Tipo de mapa (ruas)
      zoomControl: true,           // Mostrar controle de zoom
      mapTypeControl: true,        // Mostrar controle de tipo de mapa
      scaleControl: true,          // Mostrar controle de escala
      streetViewControl: true,     // Mostrar controle de Street View
      rotateControl: true,          // Mostrar controle de rotação
      fullscreenControl: true,     // Mostrar controle de tela cheia
      maxZoom: 21,                 // Zoom máximo permitido
      minZoom: 3                   // Zoom mínimo permitido
    };
    
    // Criar o mapa
    const map = new google.maps.Map(document.getElementById("google-map"), mapOptions);
    
    // Adicionar um marcador no centro
    const marker = new google.maps.Marker({
      position: recife,
      map: map,
      title: "Recife, Pernambuco",
      animation: google.maps.Animation.DROP
    });
    
    // Adicionar uma janela de informação
    const infoWindow = new google.maps.InfoWindow({
      content: `
        <div style="padding: 10px;">
          <h3 style="margin: 0 0 10px 0; color: #333;">Recife</h3>
          <p style="margin: 0; font-size: 0.9rem;">Capital de Pernambuco</p>
          <p style="margin: 5px 0 0 0; font-size: 0.9rem;">População: ~1,6 milhão</p>
        </div>
      `
    });
    
    // Abrir a janela de informação quando o marcador for clicado
    marker.addListener("click", () => {
      infoWindow.open(map, marker);
    });
    
    // Adicionar alguns pontos de interesse
    const pontosInteresse = [
      {
        position: { lat: -8.0625, lng: -34.8711 },
        title: "Marco Zero",
        content: "<h3>Marco Zero</h3><p>Local histórico de Recife</p>"
      },
      {
        position: { lat: -8.0399, lng: -34.9040 },
        title: "Instituto Ricardo Brennand",
        content: "<h3>Instituto Ricardo Brennand</h3><p>Museu com acervo cultural</p>"
      },
      {
        position: { lat: -8.0524, lng: -34.9097 },
        title: "Boa Viagem",
        content: "<h3>Boa Viagem</h3><p>Praia popular de Recife</p>"
      }
    ];
    
    // Adicionar marcadores para os pontos de interesse
    pontosInteresse.forEach(ponto => {
      const marker = new google.maps.Marker({
        position: ponto.position,
        map: map,
        title: ponto.title,
        icon: {
          url: "data:image/svg+xml;charset=UTF-8," + encodeURIComponent(`
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 2C8.13 2 5 5.13 5 9C5 14.25 12 22 12 22S19 14.25 19 9C19 5.13 15.87 2 12 2ZM12 11.5C10.62 11.5 9.5 10.38 9.5 9C9.5 7.62 10.62 6.5 12 6.5C13.38 6.5 14.5 7.62 14.5 9C14.5 10.38 13.38 11.5 12 11.5Z" fill="#FF5722"/>
            </svg>
          `),
          scaledSize: new google.maps.Size(24, 24),
          origin: new google.maps.Point(0, 0),
          anchor: new google.maps.Point(12, 24)
        }
      });
      
      const infoWindow = new google.maps.InfoWindow({
        content: ponto.content
      });
      
      marker.addListener("click", () => {
        infoWindow.open(map, marker);
      });
    });
    
    // Evento para quando o mapa for carregado
    google.maps.event.addListenerOnce(map, 'tilesloaded', function() {
      console.log('Mapa do Google carregado com sucesso');
    });
    
  } catch (error) {
    console.error('Erro ao inicializar o mapa:', error);
    showError('Erro ao inicializar o mapa: ' + error.message);
  }
}

// Função para mostrar erro
function showError(message) {
  const errorElement = document.getElementById('error-message');
  const errorText = document.getElementById('error-text');
  
  errorText.textContent = message;
  errorElement.style.display = 'block';
}

// Função para carregar a API do Google Maps
function loadGoogleMapsAPI() {
  // SUBSTITUA A CHAVE DE API ABAIXO PELA SUA
  const apiKey = 'SUA_CHAVE_DE_API_AQUI';
  
  if (apiKey === 'SUA_CHAVE_DE_API_AQUI') {
    showError('Por favor, substitua "SUA_CHAVE_DE_API_AQUI" pela sua chave de API do Google Maps.');
    return;
  }
  
  const script = document.createElement('script');
  script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&callback=initMap`;
  script.async = true;
  script.defer = true;
  
  script.onerror = function() {
    showError('Não foi possível carregar a API do Google Maps. Verifique sua chave de API e conexão com a internet.');
  };
  
  document.head.appendChild(script);
}

// Carregar a API quando a página estiver pronta
document.addEventListener('DOMContentLoaded', function() {
  loadGoogleMapsAPI();
});
</script>

<?php
// Footer correto
require_once __DIR__ . '/../footer.php';
?>
</body>
</html>


