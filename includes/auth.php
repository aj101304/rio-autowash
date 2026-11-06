<?php
// Sesión persistente 24h aunque se cierre el navegador
if(session_status()===PHP_SESSION_NONE){
    ini_set('session.gc_maxlifetime', 86400);
    ini_set('session.cookie_lifetime', 86400);
    session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/',
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}
if(!isset($_SESSION['usuario_id'])){ header('Location: login.php'); exit; }

// Si paola está bloqueada y ya tiene sesión abierta, la sacamos
if(isset($_SESSION['nombre']) && strtolower($_SESSION['nombre'])=='paola' || (isset($_SESSION['usuario']) && strtolower($_SESSION['usuario'])=='paola')){
    if(file_exists(__DIR__.'/../paola_bloqueada.txt')){
        session_destroy();
        header('Location: login.php?bloqueada=1'); exit;
    }
}
if(isset($_SESSION['usuario_id'])){
    if(file_exists(__DIR__.'/../paola_bloqueada.txt')){
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
