<?php include 'header.php'; ?>

<main class="main-content">
    <div class="container">
        <h1>Bem-vindo ao ECO.REC</h1>
        <p>Soluções sustentáveis de energia verde para prédios inteligentes. Junte-se a nós na revolução energética!</p>
        
        <!-- Seção de Serviços com Paleta de Cores do PDF -->
        <section class="services">
    <div class="services-grid">
        <!-- Cadastre seu Prédio - Verde -->
        <a href="cadastro-predio.php" class="service-item green">
            <div class="service-icon">
                <img src="images/eco_rec_.png" alt="Ícone" style="width: 280px; height: auto; margin-bottom: 5px;">
                <i class="bi bi-building"></i>
            </div>
            <h3>Cadastre seu Prédio</h3>
            <p>Registre seu imóvel e participe do programa de energia sustentável</p>
        </a>

        <!-- Compre Energia Verde - Rosa -->
        <a href="comprar-energia.php" class="service-item pink">
            <div class="service-icon">
                <img src="images/eco_rec_.png" alt="Ícone" style="width: 280px; height: auto; margin-bottom: 5px;">
                <i class="bi bi-lightning-charge"></i>
            </div>
            <h3>Compre Energia Verde</h3>
            <p>Adquira energia limpa e renovável para seu prédio</p>
        </a>

        <!-- Sobre o Projeto - Laranja-Avermelhado -->
        <a href="sobre-projeto.php" class="service-item orange-red">
            <div class="service-icon">
                <img src="images/eco_rec_.png" alt="Ícone" style="width: 280px; height: auto; margin-bottom: 5px;">
                <i class="bi bi-info-circle"></i>
            </div>
            <h3>Sobre o Projeto</h3>
            <p>Conheça nossa missão e visão para um futuro sustentável</p>
        </a>

        <!-- Contato / Suporte - Roxo -->
        <a href="contato.php" class="service-item purple">
            <div class="service-icon">
                <img src="images/eco_rec_.png" alt="Ícone" style="width: 280px; height: auto; margin-bottom: 5px;">
                <i class="bi bi-headset"></i>
            </div>
            <h3>Contato / Suporte</h3>
            <p>Fale conosco para tirar dúvidas e receber assistência</p>
        </a>

        <!-- Mapa de Prédios - Amarelo Claro -->
        <a href="mapa-predios.php" class="service-item light-yellow">
            <div class="service-icon">
                <img src="images/eco_rec_.png" alt="Ícone" style="width: 280px; height: auto; margin-bottom: 5px;">
                <i class="bi bi-map"></i>
            </div>
            <h3>Mapa de Prédios</h3>
            <p>Visualize todos os prédios participantes no mapa interativo</p>
        </a>
    </div>
</section>

        
        <!-- Botão principal estendido até o limite direito -->
        <a href="cadastro-predio.php" class="btn">
            <i class="bi bi-building"></i> Cadastre seu Prédio
        </a>
    </div>
</main>

<?php include 'footer.php'; ?>