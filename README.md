# **Maxiss Autocenter — Sistema de Gestão Web**

Um sistema web desenvolvido para o autocenter **Maxiss**, permitindo o gerenciamento completo de serviços automotivos, clientes, veículos, ordens de serviço e estoque.
Construído com **PHP**, **HTML**, **CSS**, **JavaScript** e **MySQL**, o projeto oferece uma solução simples, rápida e funcional para oficinas mecânicas.

---

## 🚗 **Funcionalidades Principais**

### **🔧 Gestão de Serviços e Ordens**

* Cadastro, edição e exclusão de serviços
* Emissão de Ordens de Serviço (O.S.)
* Atualização de status: *aberto*, *em andamento*, *finalizado*

### **👤 Controle de Clientes e Veículos**

* Cadastro de clientes
* Registro de veículos vinculados
* Histórico de serviços realizados

### **📦 Controle de Produtos e Estoque**

* Cadastro de produtos
* Gerenciamento de estoque
* Atualização automática ao lançar uma O.S.

### **💰 Financeiro (opcional)**

* Cálculo de valor total da O.S.
* Relatórios simples de faturamento

---

## 🛠️ **Tecnologias Utilizadas**

| Tecnologia     | Uso                         |
| -------------- | --------------------------- |
| **PHP**        | Backend e lógica do sistema |
| **HTML5**      | Estrutura das páginas       |
| **CSS3**       | Estilização e layout        |
| **JavaScript** | Interatividade e validações |
| **MySQL**      | Banco de dados              |
| **PDO**        | Conexão segura com o banco  |

---

## 📁 **Estrutura do Projeto**

```
/maxiss_autocenter
│
├── /config
│   └── database.php
│
├── /models
│   ├── Cliente.php
│   ├── Veiculo.php
│   ├── Servico.php
│   └── Produto.php
│
├── /controllers
│   └── ...arquivos de controle...
│
├── /views
│   ├── clientes.php
│   ├── servicos.php
│   ├── produtos.php
│   └── ordens.php
│
├── /assets
│   ├── css/
│   ├── js/
│   └── imagens/
│
└── index.php
```

---

## 🗄️ **Banco de Dados**

Crie um banco de dados MySQL chamado **`maxiss_autocenter`** e importe as tabelas:

### Tabelas recomendadas:

* `clientes`
* `veiculos`
* `servicos`
* `produtos`
* `ordens_servico`
* `itens_ordem`

### Exemplo de conexão (usando PDO)

```php
$host = 'localhost';
$db   = 'maxiss_autocenter';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

$pdo = new PDO($dsn, $user, $pass, $options);
```

---

## 🏁 **Como Executar o Projeto**

### **1. Clonar o repositório**

```bash
git clone https://github.com/SEU_USUARIO/maxiss-autocenter.git
```

### **2. Mover para o htdocs ou www**

Exemplo (XAMPP):

```
C:/xampp/htdocs/maxiss-autocenter
```

### **3. Iniciar o servidor**

Abra o painel do XAMPP -> Start **Apache** e **MySQL**

### **4. Importar o banco**

* Abrir *phpMyAdmin*
* Criar DB: `maxiss_autocenter`
* Importar o arquivo `.sql` do projeto

### **5. Acessar o sistema**

Abra no navegador:

```
http://localhost/maxiss-autocenter
```

---

## 🎨 **Layout e Estilos**

* Feito com CSS puro
* Responsivo
* Pode ser integrado futuramente com Bootstrap ou Tailwind

---

## 📌 **Recursos Extras (opcionais)**

* Autenticação com sessão (`session_start()`)
* Logs de alterações
* Upload de imagens para produtos e serviços
* Dashboard com gráficos (JS)

---

## 🤝 **Contribuição**

Pull requests são bem-vindos.
Para mudanças significativas, abra uma issue primeiro para discussão.

É só pedir!
