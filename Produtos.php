<?php
// api/produtos.php
header('Content-Type: application/json');
require_once '../config.php';

$pdo = getConnection();

// Obter parâmetros
$categoria = isset($_GET['categoria']) ? sanitize($_GET['categoria']) : 'all';
$busca = isset($_GET['busca']) ? sanitize($_GET['busca']) : '';
$ordenar = isset($_GET['ordenar']) ? sanitize($_GET['ordenar']) : 'padrao';

// Construir query
$sql = "SELECT p.*, c.nome as categoria_nome, c.slug as categoria_slug 
        FROM produtos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.ativo = 1";

// Filtrar por categoria
if ($categoria !== 'all') {
    $sql .= " AND c.slug = :categoria";
}

// Filtrar por busca
if (!empty($busca)) {
    $sql .= " AND (p.nome LIKE :busca OR p.descricao LIKE :busca)";
}

// Ordenação
switch ($ordenar) {
    case 'menor-preco':
        $sql .= " ORDER BY p.preco ASC";
        break;
    case 'maior-preco':
        $sql .= " ORDER BY p.preco DESC";
        break;
    case 'nome':
        $sql .= " ORDER BY p.nome ASC";
        break;
    default:
        $sql .= " ORDER BY p.destaque DESC, p.criado_em DESC";
}

try {
    $stmt = $pdo->prepare($sql);
    
    if ($categoria !== 'all') {
        $stmt->bindParam(':categoria', $categoria);
    }
    
    if (!empty($busca)) {
        $buscaParam = "%$busca%";
        $stmt->bindParam(':busca', $buscaParam);
    }
    
    $stmt->execute();
    $produtos = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $produtos,
        'total' => count($produtos)
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao buscar produtos'
    ]);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title>Produtos - Maxiss AutoCenter</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        window.CarrinhoGlobal = {
            STORAGE_KEY: 'maxiss_carrinho_temp',
           
            salvar: function(carrinho) {
                sessionStorage.setItem(this.STORAGE_KEY, JSON.stringify(carrinho));
            },
           
            carregar: function() {
                const data = sessionStorage.getItem(this.STORAGE_KEY);
                return data ? JSON.parse(data) : [];
            },
           
            adicionar: function(nome, preco) {
                const carrinho = this.carregar();
                carrinho.push({ nome, preco });
                this.salvar(carrinho);
                return carrinho;
            },
           
            remover: function(index) {
                const carrinho = this.carregar();
                carrinho.splice(index, 1);
                this.salvar(carrinho);
                return carrinho;
            },
           
            limpar: function() {
                this.salvar([]);
                return [];
            }
        };
    </script>
</head>

<body>
    <!-- Header -->
    <header>
        <div class="container-header">
            <div class="logo">
                <h1>Maxiss AutoCenter</h1>
            </div>
            <nav class="nav-menu">
                <ul>
                    <li><a href="./homepage.html">Início</a></li>
                    <li><a href="./Produtos.html" class="active">Produtos</a></li>
                    <li><a href="./Sobre.html">Sobre</a></li>
                    <li><a href="./Contato.html">Contato</a></li>
                    <li><a href="./login.html" class="btn-login">Login</a></li>
                </ul>
            </nav>
            <a href="./Finalizar.html">
                <div class="carrinho-icon">
                    🛒 <span class="carrinho-count">0</span>
                </div>
            </a>
            <div class="menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </header>

    <!-- Categorias e Filtros -->
    <section class="filtros-section">
        <div class="container-filtros">
            <div class="busca-wrapper">
                <input type="text" id="busca-produto" placeholder="Buscar produtos..." />
                <button class="btn-busca">🔍</button>
            </div>
            
            <nav class="categorias-nav">
                <button class="categoria-btn active" data-category="all">
                    <span class="icon"></span> Todos
                </button>
                <button class="categoria-btn" data-category="motor">
                    <span class="icon"></span> Motor
                </button>
                <button class="categoria-btn" data-category="pneus">
                    <span class="icon"></span> Pneus
                </button>
                <button class="categoria-btn" data-category="suspensao">
                    <span class="icon"></span> Suspensão
                </button>
                <button class="categoria-btn" data-category="eletrica">
                    <span class="icon"></span> Elétrica
                </button>
                <button class="categoria-btn" data-category="acessorios">
                    <span class="icon"></span> Acessórios
                </button>
            </nav>

            <div class="ordenacao">
                <label for="ordenar">Ordenar por:</label>
                <select id="ordenar">
                    <option value="padrao">Padrão</option>
                    <option value="menor-preco">Menor Preço</option>
                    <option value="maior-preco">Maior Preço</option>
                    <option value="nome">Nome A-Z</option>
                </select>
            </div>
        </div>
    </section>

    <!-- Produtos -->
    <main>
        <div class="produtos-container" id="produtos-container">
            <!-- Motor -->
            <div class="produto" data-category="motor" data-nome="Óleo de Motor 5W30" data-preco="59.90">
                <div class="produto-badge">Novo</div>
                <img src="./Oleo.PNG" alt="Óleo de Motor" />
                <h3>Óleo de Motor 5W30</h3>
                <p class="descricao">Lubrificante sintético de alta performance</p>
                <p class="preco">R$ 59,90</p>
                <button class="btn-adicionar" onclick="adicionarAoCarrinho('Óleo de Motor 5W30', 59.90)">
                    Adicionar ao Carrinho
                </button>
            </div>

            <div class="produto" data-category="motor" data-nome="Filtro de Óleo" data-preco="35.00">
                <img src="./Filtro-removebg-preview.png" alt="Filtro de Óleo" />
                <h3>Filtro de Óleo</h3>
                <p class="descricao">Filtro de alta eficiência</p>
                <p class="preco">R$ 35,00</p>
                <button class="btn-adicionar" onclick="adicionarAoCarrinho('Filtro de Óleo', 35.00)">
                    Adicionar ao Carrinho
                </button>
            </div>

            <div class="produto" data-category="motor" data-nome="Correia Dentada" data-preco="120.00">
                <img src="./Dentada.PNG" alt="Correia Dentada" />
                <h3>Correia Dentada</h3>
                <p class="descricao">Correia resistente e durável</p>
                <p class="preco">R$ 120,00</p>
                <button class="btn-adicionar" onclick="adicionarAoCarrinho('Correia Dentada', 120.00)">
                    Adicionar ao Carrinho
                </button>
            </div>

            <div class="produto" data-category="motor" data-nome="Radiador Automotivo" data-preco="350.00">
                <img src="./Radiador-removebg-preview.png" alt="Radiador" />
                <h3>Radiador Automotivo</h3>
                <p class="descricao">Sistema de arrefecimento eficiente</p>
                <p class="preco">R$ 350,00</p>
                <button class="btn-adicionar" onclick="adicionarAoCarrinho('Radiador Automotivo', 350.00)">
                    Adicionar ao Carrinho
                </button>
            </div>

            <!-- Pneus -->
            <div class="produto" data-category="pneus" data-nome="Pneu Aro 15" data-preco="420.00">
                <div class="produto-badge destaque">Destaque</div>
                <img src="./Pneu.PNG" alt="Pneu Aro 15" />
                <h3>Pneu Aro 15</h3>
                <p class="descricao">Pneu de alta durabilidade</p>
                <p class="preco">R$ 420,00</p>
                <button class="btn-adicionar" onclick="adicionarAoCarrinho('Pneu Aro 15', 420.00)">
                    Adicionar ao Carrinho
                </button>
            </div>

            <div class="produto" data-category="pneus" data-nome="Pneu de Moto" data-preco="250.00">
                <img src="./pneu de moto.PNG" alt="Pneu Moto" />
                <h3>Pneu de Moto</h3>
                <p class="descricao">Aderência superior em qualquer terreno</p>
                <p class="preco">R$ 250,00</p>
                <button class="btn-adicionar" onclick="adicionarAoCarrinho('Pneu de Moto', 250.00)">
                    Adicionar ao Carrinho
                </button>
            </div>

            <div class="produto" data-category="pneus" data-nome="Pneu SUV" data-preco="550.00">
                <img src="./Pneu suv.PNG" alt="Pneu SUV" />
                <h3>Pneu SUV</h3>
                <p class="descricao">Performance off-road e on-road</p>
                <p class="preco">R$ 550,00</p>
                <button class="btn-adicionar" onclick="adicionarAoCarrinho('Pneu SUV', 550.00)">
                    Adicionar ao Carrinho
                </button>
            </div>

            <!-- Suspensão -->
            <div class="produto" data-category="suspensao" data-nome="Amortecedor Dianteiro" data-preco="320.00">
                <img src="./Amortecedor-removebg-preview.png" alt="Amortecedor Dianteiro" />
                <h3>Amortecedor Dianteiro</h3>
                <p class="descricao">Conforto e estabilidade</p>
                <p class="preco">R$ 320,00</p>
                <button class="btn-adicionar" onclick="adicionarAoCarrinho('Amortecedor Dianteiro', 320.00)">
                    Adicionar ao Carrinho
                </button>
            </div>

            <div class="produto" data-category="suspensao" data-nome="Mola de Suspensão" data-preco="200.00">
                <img src="./Suspensao mola.PNG" alt="Mola de Suspensão" />
                <h3>Mola de Suspensão</h3>
                <p class="descricao">Mola reforçada e resistente</p>
                <p class="preco">R$ 200,00</p>
                <button class="btn-adicionar" onclick="adicionarAoCarrinho('Mola de Suspensão', 200.00)">
                    Adicionar ao Carrinho
                </button>
            </div>

            <div class="produto" data-category="suspensao" data-nome="Braço de Suspensão" data-preco="180.00">
                <img src="./Bracos de suspensao.PNG" alt="Braço de Suspensão" />
                <h3>Braço de Suspensão</h3>
                <p class="descricao">Peça original de fábrica</p>
                <p class="preco">R$ 180,00</p>
                <button class="btn-adicionar" onclick="adicionarAoCarrinho('Braço de Suspensão', 180.00)">
                    Adicionar ao Carrinho
                </button>
            </div>

            <!-- Elétrica -->
            <div class="produto" data-category="eletrica" data-nome="Bateria 60Ah" data-preco="480.00">
                <div class="produto-badge destaque">Destaque</div>
                <img src="./Moura.PNG" alt="Bateria 60Ah" />
                <h3>Bateria 60Ah</h3>
                <p class="descricao">Bateria selada livre de manutenção</p>
                <p class="preco">R$ 480,00</p>
                <button class="btn-adicionar" onclick="adicionarAoCarrinho('Bateria 60Ah', 480.00)">
                    Adicionar ao Carrinho
                </button>
            </div>

            <div class="produto" data-category="eletrica" data-nome="Farol LED" data-preco="150.00">
                <img src="./Farol-removebg-preview.png" alt="Farol LED" />
                <h3>Farol LED</h3>
                <p class="descricao">Iluminação potente e econômica</p>
                <p class="preco">R$ 150,00</p>
                <button class="btn-adicionar" onclick="adicionarAoCarrinho('Farol LED', 150.00)">
                    Adicionar ao Carrinho
                </button>
            </div>

            <div class="produto" data-category="eletrica" data-nome="Alternador" data-preco="650.00">
                <img src="./Alternador-removebg-preview.png" alt="Alternador" />
                <h3>Alternador</h3>
                <p class="descricao">Alta capacidade de carga</p>
                <p class="preco">R$ 650,00</p>
                <button class="btn-adicionar" onclick="adicionarAoCarrinho('Alternador', 650.00)">
                    Adicionar ao Carrinho
                </button>
            </div>

            <!-- Acessórios -->
            <div class="produto" data-category="acessorios" data-nome="Tapete Automotivo" data-preco="90.00">
                <img src="./Tapete.PNG" alt="Tapete Automotivo" />
                <h3>Tapete Automotivo</h3>
                <p class="descricao">Proteção e estilo para seu carro</p>
                <p class="preco">R$ 90,00</p>
                <button class="btn-adicionar" onclick="adicionarAoCarrinho('Tapete Automotivo', 90.00)">
                    Adicionar ao Carrinho
                </button>
            </div>

            <div class="produto" data-category="acessorios" data-nome="Capa para Banco" data-preco="150.00">
                <img src="./Capa Banco.jpg" alt="Capa para Banco" />
                <h3>Capa para Banco</h3>
                <p class="descricao">Conforto e proteção premium</p>
                <p class="preco">R$ 150,00</p>
                <button class="btn-adicionar" onclick="adicionarAoCarrinho('Capa para Banco', 150.00)">
                    Adicionar ao Carrinho
                </button>
            </div>

            <div class="produto" data-category="acessorios" data-nome="Limpador de Para-brisa" data-preco="40.00">
                <img src="./Limpador.jpg" alt="Limpador de Para-brisa" />
                <h3>Limpador de Para-brisa</h3>
                <p class="descricao">Limpeza eficiente em qualquer clima</p>
                <p class="preco">R$ 40,00</p>
                <button class="btn-adicionar" onclick="adicionarAoCarrinho('Limpador de Para-brisa', 40.00)">
                    Adicionar ao Carrinho
                </button>
            </div>

            <div class="produto" data-category="acessorios" data-nome="Capa de Volante" data-preco="40.90">
                <img src="./Volante.PNG" alt="Capa de Volante" />
                <h3>Capa de Volante</h3>
                <p class="descricao">Conforto e aderência superior</p>
                <p class="preco">R$ 40,90</p>
                <button class="btn-adicionar" onclick="adicionarAoCarrinho('Capa de Volante', 40.90)">
                    Adicionar ao Carrinho
                </button>
            </div>
        </div>

        <div class="sem-resultados" id="sem-resultados" style="display: none;">
            <p> Nenhum produto encontrado...</p>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Maxiss AutoCenter — Todos os direitos reservados.</p>
    </footer>   

    <!-- CSS -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
             font-family: 'Old school', sans-serif;;
            background: #f5f5f5;
        }

        /* ========== HEADER ========== */
        header {
            background-color: #000;
            color: #fff;
            padding: 15px 30px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
        }

        .container-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: auto;
            gap: 20px;
        }

        .logo h1 {
            color: #FFD700;
            font-size: 1.8rem;
        }

        .nav-menu ul {
            list-style: none;
            display: flex;
            gap: 25px;
            align-items: center;
        }

        .nav-menu ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
        }

        .nav-menu ul li a:hover,
        .nav-menu ul li a.active {
            color: #FFD700;
        }

        .btn-login {
            background-color: #FFD700;
            color: #000 !important;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: 600;
        }

        .btn-login:hover {
            background-color: #e6c200;
        }

        .carrinho-icon {
            position: relative;
            font-size: 1.5rem;
            cursor: pointer;
            transition: 0.3s;
        }

        .carrinho-icon:hover {
            transform: scale(1.1);
        }

        .carrinho-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #FFD700;
            color: #000;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 50%;
            min-width: 18px;
            text-align: center;
        }

        .menu-toggle {
            display: none;
            flex-direction: column;
            cursor: pointer;
            gap: 5px;
        }

        .menu-toggle span {
            width: 25px;
            height: 3px;
            background: #FFD700;
            display: block;
            border-radius: 2px;
        }

        /* ========== FILTROS ========== */
        .filtros-section {
            background: #fff;
            padding: 20px;
            margin-top: 80px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 70px;
            z-index: 100;
        }

        .container-filtros {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            align-items: center;
        }

        .busca-wrapper {
            display: flex;
            flex: 1;
            min-width: 250px;
        }

        #busca-produto {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px 0 0 6px;
            font-size: 0.95rem;
            font-family: 'Poppins', sans-serif;
        }

        .btn-busca {
            padding: 10px 15px;
            background: #FFD700;
            border: none;
            border-radius: 0 6px 6px 0;
            cursor: pointer;
            font-size: 1.2rem;
            transition: 0.3s;
        }

        .btn-busca:hover {
            background: #e6c200;
        }

        .categorias-nav {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .categoria-btn {
            background: #f0f0f0;
            border: 2px solid transparent;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .categoria-btn:hover {
            background: #fff9e6;
            border-color: #FFD700;
        }

        .categoria-btn.active {
            background: #FFD700;
            color: #000;
        }

        .ordenacao {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ordenacao label {
            font-size: 0.9rem;
            font-weight: 600;
        }

        .ordenacao select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
        }

        /* ========== PRODUTOS ========== */
        main {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .produtos-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }

        .produto {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            transition: 0.3s;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .produto:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .produto-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #4CAF50;
            color: #fff;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .produto-badge.destaque {
            background: #FF6B6B;
        }

        .produto img {
            width: 100%;
            max-width: 180px;
            height: 180px;
            object-fit: contain;
            margin: 0 auto 20px;
        }

        .produto h3 {
            font-size: 1.2rem;
            margin-bottom: 8px;
            color: #111;
            min-height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .produto .descricao {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 15px;
            min-height: 40px;
        }

        .produto .preco {
            font-size: 1.6rem;
            font-weight: 700;
            color: #FFD700;
            margin-bottom: 15px;
        }

        .btn-adicionar {
            background: #000;
            color: #FFD700;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            font-size: 0.9rem;
            margin-top: auto;
        }

        .btn-adicionar:hover {
            background: #FFD700;
            color: #000;
            transform: scale(1.02);
        }

        .btn-adicionar:active {
            transform: scale(0.98);
        }

        .sem-resultados {
            text-align: center;
            padding: 60px 20px;
            font-size: 1.2rem;
            color: #666;
        }

        /* ========== FOOTER ========== */
        footer {
            background: #000;
            color: #fff;
            text-align: center;
            padding: 30px 20px;
            margin-top: 60px;
        }

        footer p {
            font-size: 0.9rem;
        }

        /* ========== RESPONSIVO ========== */
        @media (max-width: 992px) {
            .categorias-nav {
                width: 100%;
                justify-content: center;
            }

            .ordenacao {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .nav-menu {
                display: none;
                position: absolute;
                top: 60px;
                right: 0;
                background: #000;
                width: 100%;
                text-align: center;
                padding: 20px 0;
                flex-direction: column;
            }

            .nav-menu.active {
                display: flex;
            }

            .nav-menu ul {
                flex-direction: column;
                gap: 15px;
            }

            .menu-toggle {
                display: flex;
            }

            .produtos-container {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }

            .filtros-section {
                top: 60px;
            }
        }

        @media (max-width: 480px) {
            .logo h1 {
                font-size: 1.4rem;
            }

            .produtos-container {
                grid-template-columns: 1fr;
            }

            .categoria-btn {
                font-size: 0.8rem;
                padding: 6px 12px;
            }
        }
    </style>

    <!-- JavaScript -->
    <script>
        // Toggle menu mobile
        const toggle = document.querySelector('.menu-toggle');
        const menu = document.querySelector('.nav-menu');

        if (toggle) {
            toggle.addEventListener('click', () => {
                menu.classList.toggle('active');
            });
        }

        // Adicionar ao carrinho usando o CarrinhoGlobal
        function adicionarAoCarrinho(nome, preco) {
            CarrinhoGlobal.adicionar(nome, preco);
            atualizarContadorCarrinho();
            
            // Feedback visual
            const btn = event.target;
            const textoOriginal = btn.textContent;
            btn.textContent = '✓ Adicionado!';
            btn.style.background = '#4CAF50';
            btn.style.color = '#fff';
            
            setTimeout(() => {
                btn.textContent = textoOriginal;
                btn.style.background = '';
                btn.style.color = '';
            }, 1500);
        }

        // Atualizar contador do carrinho
        function atualizarContadorCarrinho() {
            const contador = document.querySelector('.carrinho-count');
            if (contador) {
                const carrinho = CarrinhoGlobal.carregar();
                contador.textContent = carrinho.length;
            }
        }

        // Filtro por categoria
        const categoriaBtns = document.querySelectorAll('.categoria-btn');
        const produtos = document.querySelectorAll('.produto');

        categoriaBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const categoria = btn.dataset.category;
                
                // Atualizar botão ativo
                categoriaBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                // Filtrar produtos
                let visibleCount = 0;
                produtos.forEach(produto => {
                    if (categoria === 'all' || produto.dataset.category === categoria) {
                        produto.style.display = 'flex';
                        visibleCount++;
                    } else {
                        produto.style.display = 'none';
                    }
                });
                
                // Mostrar mensagem se não houver produtos
                document.getElementById('sem-resultados').style.display = 
                    visibleCount === 0 ? 'block' : 'none';
            });
        });

        // Busca de produtos
        const buscaInput = document.getElementById('busca-produto');
        const btnBusca = document.querySelector('.btn-busca');

        function buscarProdutos() {
            const termo = buscaInput.value.toLowerCase();
            let visibleCount = 0;
            
            produtos.forEach(produto => {
                const nome = produto.dataset.nome.toLowerCase();
                if (nome.includes(termo)) {
                    produto.style.display = 'flex';
                    visibleCount++;
                } else {
                    produto.style.display = 'none';
                }
            });
            
            document.getElementById('sem-resultados').style.display = 
                visibleCount === 0 ? 'block' : 'none';
                
            // Resetar filtro de categoria
            categoriaBtns.forEach(b => b.classList.remove('active'));
            document.querySelector('[data-category="all"]').classList.add('active');
        }

        buscaInput.addEventListener('input', buscarProdutos);
        btnBusca.addEventListener('click', buscarProdutos);

        // Ordenação
        const selectOrdenar = document.getElementById('ordenar');
        const container = document.getElementById('produtos-container');

        selectOrdenar.addEventListener('change', () => {
            const produtosArray = Array.from(produtos);
            const valor = selectOrdenar.value;
            
            produtosArray.sort((a, b) => {
                if (valor === 'menor-preco') {
                    return parseFloat(a.dataset.preco) - parseFloat(b.dataset.preco);
                } else if (valor === 'maior-preco') {
                    return parseFloat(b.dataset.preco) - parseFloat(a.dataset.preco);
                } else if (valor === 'nome') {
                    return a.dataset.nome.localeCompare(b.dataset.nome);
                }
                return 0;
            });
            
            // Reordenar no DOM
            produtosArray.forEach(produto => container.appendChild(produto));
        });

        // Atualizar contador ao carregar a página
        window.onload = atualizarContadorCarrinho;
    </script>
</body>
</html>