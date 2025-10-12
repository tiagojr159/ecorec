<?php
// Detecta a URL base automaticamente
$basePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
?>


<!DOCTYPE html>
<html lang="pt-BR">



<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECO.REC - Energia Verde para Prédios</title>
    <link rel="stylesheet" href="/css/style.css">
<link rel="stylesheet" href="<?php echo $basePath; ?>/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="<?php echo $basePath; ?>/../index.php">
                    <img src="<?php echo $basePath; ?>/images/ecorec_log.png" alt="Logo ECO.REC" class="logo-img">
                </a>
            </div>

            <div class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </div>

            <nav class="main-nav" id="mainNav">
                <ul>
                    <li><a href="cadastro-predio.php">Cadastre seu Prédio</a></li>
                    <li><a href="comprar-energia.php">Compre Energia Verde</a></li>
                    <li><a href="sobre-projeto.php">Sobre o Projeto</a></li>
                    <li><a href="contato.php">Contato / Suporte</a></li>
                    <li><a href="mapa-predios.php">Mapa de Prédios</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <script>
        document.getElementById('menuToggle')?.addEventListener('click', function() {
            document.getElementById('mainNav')?.classList.toggle('active');
        });
    </script>

</body>

</html>