<?php
require_once __DIR__ . '/../header.php';
?>

<div class="container mt-5 mb-5">
    <h2 class="text-center mb-4">Mapas Interativos</h2>
    <p class="text-center text-muted mb-5">Escolha um dos mapas para visualizar os pontos de energia verde e sustentabilidade.</p>

    <div class="services-grid">
        <!-- Mapa 1 -->
        <a href="mapa1.php" class="service-item green">
            <div class="service-icon"><i class="fas fa-solar-panel"></i></div>
            <h3>Mapa 1</h3>
            <p>Distribuição de placas solares</p>
        </a>

        <!-- Mapa 2 -->
        <a href="mapa2.php" class="service-item pink">
            <div class="service-icon"><i class="fas fa-bolt"></i></div>
            <h3>Mapa 2</h3>
            <p>Consumo de energia sustentável</p>
        </a>

        <!-- Mapa 3 -->
        <a href="mapa3.php" class="service-item orange-red">
            <div class="service-icon"><i class="fas fa-leaf"></i></div>
            <h3>Mapa 3</h3>
            <p>Áreas verdes e ecopontos</p>
        </a>

        <!-- Mapa 4 -->
        <a href="mapa4.php" class="service-item purple">
            <div class="service-icon"><i class="fas fa-globe-americas"></i></div>
            <h3>Mapa 4</h3>
            <p>Monitoramento ambiental</p>
        </a>
    </div>
</div>

<?php
require_once __DIR__ . '/../footer.php';
?>
