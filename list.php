<?php
$conn = new mysqli($_GET['host'], $_GET['user'], $_GET['pass']);
if ($conn->connect_error) die("Erro de Conexão: " . $conn->connect_error);
$result = $conn->query("SHOW DATABASES");
while ($row = $result->fetch_row()) echo $row[0] . "<br>";
$conn->close();
?>
