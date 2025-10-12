<?php require_once __DIR__ . '/header.php'; ?>

<style>
    body {
        background-color: #f5f8f6;
    }

    .ponto-card {
        border-radius: 14px;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        margin-bottom: 1.5rem;
        background-color: #fff;
        transition: 0.3s ease;
        border-left: 6px solid #00b37a;
    }

    .ponto-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    }

    .ponto-card h5 {
        color: #007e50;
        font-weight: 600;
    }

    .badge-status {
        font-size: 0.8rem;
        padding: 5px 10px;
        border-radius: 20px;
    }

    .badge-ativo {
        background-color: #28a745;
        color: #fff;
    }

    .badge-manutencao {
        background-color: #ffc107;
        color: #000;
    }

    .badge-publico {
        background-color: #007bff;
        color: #fff;
    }

    .badge-comercial {
        background-color: #6f42c1;
        color: #fff;
    }

    .badge-residencial {
        background-color: #17a2b8;
        color: #fff;
    }
</style>

<div class="container mt-5 mb-5">
    <h2 class="text-center mb-4">Prédios com Energia Solar – Bairro de Santo Antônio ☀️</h2>
    <p class="text-center text-muted mb-5">
        Confira os principais prédios e instituições do bairro de Santo Antônio, Recife-PE, que já utilizam energia limpa e sustentável.
    </p>

    <div class="row">

        <!-- 1 -->
        <div class="col-md-6 col-lg-4">
            <div class="ponto-card p-4">
                <h5>Edifício Paço Municipal do Recife</h5>
                <p><strong>Endereço:</strong> Praça do Arsenal, nº 111 – Santo Antônio</p>
                <p><strong>Capacidade:</strong> 150 kWp</p>
                <p><strong>Tipo:</strong> <span class="badge-publico">Público</span></p>
                <span class="badge-status badge-ativo">Ativo</span>
            </div>
        </div>

        <!-- 2 -->
        <div class="col-md-6 col-lg-4">
            <div class="ponto-card p-4">
                <h5>Colégio Sagrado Coração</h5>
                <p><strong>Endereço:</strong> Rua do Imperador Dom Pedro II, nº 210 – Santo Antônio</p>
                <p><strong>Capacidade:</strong> 60 kWp</p>
                <p><strong>Tipo:</strong> <span class="badge-publico">Educacional</span></p>
                <span class="badge-status badge-ativo">Ativo</span>
            </div>
        </div>

        <!-- 3 -->
        <div class="col-md-6 col-lg-4">
            <div class="ponto-card p-4">
                <h5>Edifício Solar Imperial</h5>
                <p><strong>Endereço:</strong> Rua da Aurora, nº 333 – Santo Antônio</p>
                <p><strong>Capacidade:</strong> 48 kWp</p>
                <p><strong>Tipo:</strong> <span class="badge-residencial">Residencial</span></p>
                <span class="badge-status badge-ativo">Ativo</span>
            </div>
        </div>

        <!-- 4 -->
        <div class="col-md-6 col-lg-4">
            <div class="ponto-card p-4">
                <h5>Centro Cultural da Justiça Federal</h5>
                <p><strong>Endereço:</strong> Av. Dantas Barreto, nº 147 – Santo Antônio</p>
                <p><strong>Capacidade:</strong> 85 kWp</p>
                <p><strong>Tipo:</strong> <span class="badge-publico">Público</span></p>
                <span class="badge-status badge-manutencao">Em Manutenção</span>
            </div>
        </div>

        <!-- 5 -->
        <div class="col-md-6 col-lg-4">
            <div class="ponto-card p-4">
                <h5>Shopping Santo Antônio Solar</h5>
                <p><strong>Endereço:</strong> Rua da Concórdia, nº 587 – Santo Antônio</p>
                <p><strong>Capacidade:</strong> 120 kWp</p>
                <p><strong>Tipo:</strong> <span class="badge-comercial">Comercial</span></p>
                <span class="badge-status badge-ativo">Ativo</span>
            </div>
        </div>

        <!-- 6 -->
        <div class="col-md-6 col-lg-4">
            <div class="ponto-card p-4">
                <h5>Residencial Solar Boa Esperança</h5>
                <p><strong>Endereço:</strong> Rua do Sol, nº 59 – Santo Antônio</p>
                <p><strong>Capacidade:</strong> 40 kWp</p>
                <p><strong>Tipo:</strong> <span class="badge-residencial">Residencial</span></p>
                <span class="badge-status badge-ativo">Ativo</span>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
