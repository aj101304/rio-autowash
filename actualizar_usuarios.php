<?php
require 'config/database.php';

$claveAdmin = 'andres1006';
$clavePaola = 'paolita8';

$hashAdmin = password_hash($claveAdmin, PASSWORD_BCRYPT);
$hashPaola = password_hash($clavePaola, PASSWORD_BCRYPT);

// Actualiza admin
$stmt = $pdo->prepare("UPDATE usuarios SET clave = ? WHERE usuario = 'admin'");
$stmt->execute([$hashAdmin]);
echo "Admin actualizado: " . $stmt->rowCount() . " fila(s)<br>";

// Si no existía admin, lo crea
if($stmt->rowCount() == 0){
    $pdo->prepare("INSERT INTO usuarios (usuario, clave, nombre, rol) VALUES ('admin', ?, 'Andres', 'admin')")->execute([$hashAdmin]);
    echo "Admin creado<br>";
}

// Actualiza cajera -> paola
$stmt = $pdo->prepare("UPDATE usuarios SET usuario = 'paola', clave = ?, nombre='Paola', rol='cajera' WHERE usuario = 'cajera'");
$stmt->execute([$hashPaola]);
echo "Cajera -> paola actualizada: " . $stmt->rowCount() . " fila(s)<br>";

if($stmt->rowCount() == 0){
    // si no existía cajera, intenta actualizar paola si ya existe, o crearla
    $stmt2 = $pdo->prepare("UPDATE usuarios SET clave = ?, nombre='Paola', rol='cajera' WHERE usuario = 'paola'");
    $stmt2->execute([$hashPaola]);
    if($stmt2->rowCount() == 0){
        $pdo->prepare("INSERT INTO usuarios (usuario, clave, nombre, rol) VALUES ('paola', ?, 'Paola', 'cajera')")->execute([$hashPaola]);
        echo "Paola creada<br>";
    } else {
        echo "Paola ya existía, clave actualizada<br>";
    }
}

echo "<hr>Usuarios actuales:<br>";
foreach($pdo->query("SELECT usuario, nombre, rol FROM usuarios") as $r) {
  echo $r['usuario']." - ".$r['nombre']." - ".$r['rol']."<br>";
}
echo "<hr>Listo. Borra este archivo actualizar_usuarios.php por seguridad.";
?>
