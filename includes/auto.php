<?php
if(session_status()===PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['usuario_id'])){ header('Location: login.php'); exit; }

// Si paola está bloqueada y ya tiene sesión abierta, la sacamos
if(isset($_SESSION['nombre']) && strtolower($_SESSION['nombre'])=='paola' || (isset($_SESSION['usuario']) && strtolower($_SESSION['usuario'])=='paola')){
    if(file_exists(__DIR__.'/../paola_bloqueada.txt')){
        session_destroy();
        header('Location: login.php?bloqueada=1'); exit;
    }
}
// También por usuario
if(isset($_SESSION['usuario_id'])){
    // verifica si el usuario logueado es paola y existe el flag
    if(file_exists(__DIR__.'/../paola_bloqueada.txt')){
        // chequear en BD si el id corresponde a paola para no sacar al admin
        require_once __DIR__.'/../config/database.php';
        try {
            $stmt = $pdo->prepare("SELECT usuario FROM usuarios WHERE id=?");
            $stmt->execute([$_SESSION['usuario_id']]);
            $u = $stmt->fetch();
            if($u && strtolower($u['usuario'])=='paola'){
                session_destroy();
                header('Location: login.php?bloqueada=1'); exit;
            }
        } catch(Exception $e){}
    }
}
?>
