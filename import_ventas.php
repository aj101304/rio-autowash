<?php
// import_ventas.php - Súbelo a GitHub y abre https://rio-autowash.onrender.com/import_ventas.php
require 'config/database.php';
ini_set('max_execution_time', 300);

echo "<h3>Importando ventas...</h3>";

$sqlFile = __DIR__ . '/Ventas.sql';
if(!file_exists($sqlFile)){
    $sqlFile = __DIR__ . '/Ventas.txt';
}
if(!file_exists($sqlFile)){
    die("No encuentro Ventas.sql ni Ventas.txt en la raiz. Súbelos a GitHub junto a este archivo.");
}

$sql = file_get_contents($sqlFile);

// Limpia comentarios que rompen el exec
$pdo->exec("SET FOREIGN_KEY_CHECKS=0");

try {
    // Divide por ; pero respeta que el dump ya viene listo
    // Usamos exec directo porque el dump es pequeño
    $pdo->exec($sql);
    echo "✅ Importación terminada<br>";
    
    // Verifica
    $c = $pdo->query("SELECT COUNT(*) FROM ventas")->fetchColumn();
    echo "Ventas importadas: $c<br>";
    $c2 = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    echo "Usuarios: $c2<br>";
    echo "<br><b>Ya puedes borrar import_ventas.php y Ventas.sql de GitHub</b><br><a href='index.php'>Ir al inicio</a>";
} catch(Exception $e){
    echo "Error: ".$e->getMessage();
    // Intenta por partes
    echo "<br>Intentando por partes...<br>";
    $queries = explode(";", $sql);
    $ok=0;
    foreach($queries as $q){
        $q=trim($q);
        if(empty($q) || str_starts_with($q, '--') || str_starts_with($q, '/*')) continue;
        try { $pdo->exec($q); $ok++; } catch(Exception $ex){ }
    }
    echo "Ejecutadas $ok consultas<br>";
}
?>
