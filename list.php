<?php
// Define as variáveis de conexão com o banco de dados, pegando os valores da URL via GET
$h = $_GET['host'];
$u = $_GET['user'];
$p = $_GET['dbsenha']; // Usando 'dbsenha' como você solicitou

// Pega o nome do banco de dados da URL, se existir, senão define como vazio
$d = isset($_GET['db']) ? $_GET['db'] : '';

// Tenta estabelecer uma nova conexão com o MySQLi
$conn = new mysqli($h, $u, $p, $d);

// Verifica se houve algum erro na conexão
if ($conn->connect_error) {
    die("Erro de Conexão: " . $conn->connect_error);
}

// ---

## Listar Bancos de Dados

echo "<h2>Bancos de Dados:</h2>";

// Executa a query para mostrar todos os bancos de dados
$result = $conn->query("SHOW DATABASES");

// Itera sobre os resultados e exibe cada nome de banco de dados
while ($row = $result->fetch_row()) {
    echo $row[0] . "<br>";
}

// ---

## Listar Tabelas (se um banco de dados foi especificado)

// Se um nome de banco de dados foi fornecido na URL (parâmetro 'db')
if ($d) {
    echo "<h2>Tabelas em {$d}:</h2>";

    // Executa a query para mostrar as tabelas do banco de dados especificado
    // As crases (`) ao redor de $d são importantes para nomes de DB com caracteres especiais
    $result = $conn->query("SHOW TABLES FROM `{$d}`");

    // Itera sobre os resultados e exibe cada nome de tabela
    while ($row = $result->fetch_row()) {
        echo $row[0] . "<br>";
    }
}

// ---

## Listar Colunas de uma Tabela (se um banco de dados e tabela foram especificados)

// Adicionando a funcionalidade de listar colunas se 'table' também for passado via GET
if ($d && isset($_GET['table'])) {
    $t = $_GET['table']; // Pega o nome da tabela
    echo "<h2>Colunas em {$d}.{$t}:</h2>";

    // Consulta para descrever as colunas da tabela especificada
    // É crucial sanitizar $t e $d antes de usar em uma query real para evitar injeção SQL
    $result = $conn->query("DESCRIBE `{$d}`.`{$t}`");

    // Verifica se a query retornou resultados
    if ($result) {
        // Itera sobre os resultados e exibe o nome de cada coluna e seu tipo
        while ($row = $result->fetch_assoc()) {
            echo "Coluna: " . $row['Field'] . " (Tipo: " . $row['Type'] . ")<br>";
        }
    } else {
        echo "Erro ao listar colunas ou tabela não encontrada.<br>";
    }
}

// Fecha a conexão com o banco de dados
$conn->close();

?>
