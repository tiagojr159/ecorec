<?php require_once __DIR__ . '/header.php'; ?>

<style>
    .form-container {
        max-width: 600px;
        background: #f8f9fa;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        margin: auto;
    }

    .form-container h2 {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .btn-custom {
        background: #5f4b8b;
        color: #fff;
        border: none;
    }

    .btn-custom:hover {
        background: #4e3c71;
    }

    .predio-card {
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
        background-color: #ffffff;
    }

    @media (max-width: 576px) {
        .form-container {
            padding: 1.2rem;
        }

        .predio-card {
            font-size: 0.9rem;
        }
    }
</style>

<div class="container mt-5 mb-5">
    <div class="form-container">
        <h2>Cadastro de Prédios</h2>
        <form id="formPredio">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome do Prédio</label>
                <input type="text" class="form-control" id="nome" required>
            </div>
            <div class="mb-3">
                <label for="endereco" class="form-label">Endereço</label>
                <input type="text" class="form-control" id="endereco" required>
            </div>
            <div class="mb-3">
                <label for="responsavel" class="form-label">Responsável</label>
                <input type="text" class="form-control" id="responsavel">
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-custom btn-lg">Cadastrar Prédio</button>
            </div>
        </form>
    </div>

    <div class="mt-5">
        <h3 class="text-center mb-4">Prédios Cadastrados</h3>
        <div id="listaPredios" class="row justify-content-center"></div>
    </div>
</div>

<script>
const STORAGE_KEY = 'predios_json_list';

function carregarPredios() {
    const lista = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
    const container = document.getElementById('listaPredios');
    container.innerHTML = '';

    if (lista.length === 0) {
        container.innerHTML = '<p class="text-muted text-center">Nenhum prédio cadastrado ainda.</p>';
        return;
    }

    lista.forEach((predio, index) => {
        const card = document.createElement('div');
        card.className = 'col-md-6 col-lg-4 predio-card';

        card.innerHTML = `
            <h5>${predio.nome}</h5>
            <p><strong>Endereço:</strong> ${predio.endereco}</p>
            <p><strong>Responsável:</strong> ${predio.responsavel}</p>
            <button class="btn btn-sm btn-danger" onclick="removerPredio(${index})">Remover</button>
        `;
        container.appendChild(card);
    });
}

document.getElementById('formPredio').addEventListener('submit', function (e) {
    e.preventDefault();

    const nome = document.getElementById('nome').value;
    const endereco = document.getElementById('endereco').value;
    const responsavel = document.getElementById('responsavel').value;

    const novo = { nome, endereco, responsavel };
    const lista = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];

    lista.push(novo);
    localStorage.setItem(STORAGE_KEY, JSON.stringify(lista));

    this.reset();
    carregarPredios();
});

function removerPredio(index) {
    const lista = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
    lista.splice(index, 1);
    localStorage.setItem(STORAGE_KEY, JSON.stringify(lista));
    carregarPredios();
}

window.addEventListener('DOMContentLoaded', carregarPredios);
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
