<?php

session_start();

// ==========================================
// 1. CONFIGURACIÓN Y CONEXIÓN BBDD
// ==========================================
$servidor_db = "localhost";
$usuario_db  = "u908766211_adminviews"; 
$password_db = "Adminviews2526$";     
$nombre_db   = "u908766211_adminviews";

$conexion = null;
$error_db = "";

try {
    $conexion = new mysqli($servidor_db, $usuario_db, $password_db, $nombre_db);
    if ($conexion->connect_error) throw new Exception($conexion->connect_error);
    $conexion->set_charset("utf8");
} catch (Exception $e) {
    die("<div style='color:white; background:red; padding:20px; text-align:center;'>Error crítico de conexión: " . $e->getMessage() . "</div>");
}

// ==========================================
// 2. ENRUTADOR (ROUTER)
// ==========================================
// Determinamos qué "pantalla" ver. Por defecto: login o seleccion.
$page = isset($_GET['page']) ? $_GET['page'] : 'seleccion';

// Si no hay usuario logueado, forzamos ir al login (salvo que sea el proceso de login)
if (!isset($_SESSION['usuario_id']) && $page !== 'login' && $page !== 'auth') {
    header("Location: index.php?page=login");
    exit();
}

// ==========================================
// 3. LÓGICA DE NEGOCIO (CONTROLADORES)
// ==========================================

$mensaje_error = "";
$mensaje_exito = "";

// A) PROCESO DE LOGIN
if ($page === 'auth' && $_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $conexion->real_escape_string($_POST['correo']);
    $pass   = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE correo = '$correo'";
    $res = $conexion->query($sql);

    if ($res && $res->num_rows > 0) {
        $user = $res->fetch_assoc();
        // Nota: En tu código original comparabas texto plano.
        if ($pass === $user['password']) {
            $_SESSION['usuario_id'] = $user['id'];
            header("Location: index.php?page=seleccion");
            exit();
        } else {
            $mensaje_error = "Contraseña incorrecta.";
        }
    } else {
        $mensaje_error = "Usuario no encontrado.";
    }
    // Si falla, volvemos a mostrar login
    $page = 'login';
}

// B) LOGOUT
if ($page === 'logout') {
    session_destroy();
    header("Location: index.php?page=login");
    exit();
}

// C) ACCIONES (BORRAR / MOVER)
if ($page === 'acciones') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $accion = $_GET['accion'] ?? '';
    $redirect_to = $_GET['from'] ?? 'seleccion'; // Para saber a dónde volver
    
    if ($id > 0) {
        if ($accion == 'borrar') {
            $conexion->query("DELETE FROM contenidos WHERE id='$id'");
        } elseif ($accion == 'mover' && isset($_GET['estado'])) {
            $nuevo_estado = $conexion->real_escape_string($_GET['estado']);
            $conexion->query("UPDATE contenidos SET estado='$nuevo_estado' WHERE id='$id'");
        }
    }
    header("Location: index.php?page=" . $redirect_to);
    exit();
}

// D) GUARDAR NUEVO CONTENIDO
if ($page === 'guardar' && $_SERVER["REQUEST_METHOD"] == "POST") {
    $uid = $_SESSION['usuario_id'];
    $titulo = $conexion->real_escape_string($_POST['titulo']);
    $estado = $conexion->real_escape_string($_POST['estado']);
    $tipo = $conexion->real_escape_string($_POST['tipo_contenido']);
    $img = $conexion->real_escape_string($_POST['imagen_url']);
    
    if (empty($img)) $img = "https://via.placeholder.com/150";

    $sql = "INSERT INTO contenidos (usuario_id, titulo, tipo, estado, imagen_url) 
            VALUES ('$uid', '$titulo', '$tipo', '$estado', '$img')";
    
    if ($conexion->query($sql)) {
        header("Location: index.php?page=" . ($tipo == 'pelicula' ? 'peliculas' : 'series'));
        exit();
    } else {
        $mensaje_error = "Error al guardar: " . $conexion->error;
        $page = 'agregar'; // Volvemos al formulario
    }
}

// ==========================================
// 4. VISTA HTML (ESTRUCTURA ÚNICA)
// ==========================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Streaming</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* CSS UNIFICADO Y CENTRADO */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #40c4ff; /* color azul */
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center; /* Esto centra verticalmente */
        }

        /* Contenedor principal para que todo quede centrado y bonito */
        .main-container {
            width: 100%;
            max-width: 1200px;
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        /* --- ESTILOS LOGIN --- */
        .login-wrapper { display: flex; gap: 50px; align-items: center; background: rgba(255,255,255,0.2); padding: 40px; border-radius: 30px; backdrop-filter: blur(5px); }
        .logo-circle { width: 200px; height: 200px; background: #ff7043; border-radius: 50%; display: flex; justify-content: center; align-items: center; box-shadow: -5px 5px 0 rgba(0,0,0,0.1); }
        .play-triangle { width: 0; height: 0; border-top: 40px solid transparent; border-bottom: 40px solid transparent; border-left: 70px solid #cfd8dc; margin-left: 10px; }
        .login-form { display: flex; flex-direction: column; gap: 15px; width: 300px; }
        .login-input { padding: 15px; border-radius: 10px; border: none; outline: none; }
        .btn-login { background: #ffa000; padding: 15px; border: none; border-radius: 25px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-login:hover { background: #ffb300; transform: scale(1.05); }

        /* --- ESTILOS SELECCIÓN --- */
        .cards-container { display: flex; gap: 30px; flex-wrap: wrap; justify-content: center; }
        .card-option { text-decoration: none; width: 220px; height: 300px; background: white; border-radius: 20px; padding: 10px; transition: transform 0.3s; cursor: pointer; display: block; color: black;}
        .card-option:hover { transform: translateY(-10px); }
        .card-inner { background: #afb42b; width: 100%; height: 100%; border-radius: 15px; display: flex; flex-direction: column; justify_content: center; align-items: center; }
        .card-title { font-size: 1.8rem; font-weight: bold; margin-bottom: 10px; }
        .card-icon { font-size: 4rem; }

        /* --- ESTILOS LISTADOS (Peliculas/Series) --- */
        .dashboard { width: 100%; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn-back { width: 50px; height: 50px; background: #ff7043; border-radius: 50%; display: flex; justify-content: center; align-items: center; color: white; text-decoration: none; font-size: 1.5rem; }
        .btn-add { background: #ffb300; padding: 10px 20px; border-radius: 20px; text-decoration: none; color: black; font-weight: bold; }
        
        .columns-wrapper { display: flex; justify-content: center; gap: 20px; flex-wrap: wrap; }
        .column { background: white; padding: 20px; border-radius: 20px; width: 300px; min-height: 400px; display: flex; flex-direction: column; align-items: center; }
        .column h3 { margin-top: 0; }
        
        .item { background: #afb42b; width: 100%; padding: 10px; border-radius: 30px; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; box-sizing: border-box; }
        .item img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 10px; }
        .item-title { flex-grow: 1; font-size: 0.9rem; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .item-actions a { margin-left: 5px; cursor: pointer; }

        /* Mensajes */
        .alert { background: #ffebee; color: #c62828; padding: 10px; border-radius: 5px; text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="main-container">

    <?php if ($page === 'login'): ?>
    <div class="login-wrapper">
        <div class="logo-circle">
            <div class="play-triangle"></div>
        </div>
        <form class="login-form" action="index.php?page=auth" method="POST">
            <h2 style="text-align:center; margin:0;">Bienvenido</h2>
            <?php if ($mensaje_error): ?>
                <div class="alert"><?php echo $mensaje_error; ?></div>
            <?php endif; ?>
            <input type="email" name="correo" class="login-input" placeholder="correo@correo.com" required>
            <input type="password" name="password" class="login-input" placeholder="Contraseña" required>
            <button type="submit" class="btn-login">ENTRAR</button>
        </form>
    </div>
    <?php endif; ?>

    <?php if ($page === 'seleccion'): ?>
    <div style="text-align:center;">
        <h1 style="color:white; margin-bottom:40px;">¿Qué quieres ver hoy?</h1>
        
        <div class="cards-container">
            <a href="index.php?page=peliculas" class="card-option">
                <div class="card-inner">
                    <div class="card-title">Películas</div>
                    <i class="fa-solid fa-clapperboard card-icon"></i>
                </div>
            </a>
            <a href="index.php?page=series" class="card-option">
                <div class="card-inner">
                    <div class="card-title">Series</div>
                    <i class="fa-solid fa-tv card-icon"></i>
                </div>
            </a>
        </div>
        
        <div style="margin-top: 30px;">
            <a href="index.php?page=logout" style="color: white; text-decoration: underline;">Cerrar Sesión</a>
        </div>
    </div>
    <?php endif; ?>

    <?php 
    if ($page === 'peliculas' || $page === 'series'): 
        $tipo_actual = ($page === 'peliculas') ? 'pelicula' : 'serie';
        $uid = $_SESSION['usuario_id'];
        
        // Recuperar datos
        $sql = "SELECT * FROM contenidos WHERE usuario_id = '$uid' AND tipo = '$tipo_actual'";
        $res = $conexion->query($sql);
        
        $vistas = []; $pendientes = []; $viendo = [];
        
        while($fila = $res->fetch_assoc()){
            if($fila['estado']=='vistas') $vistas[] = $fila;
            elseif($fila['estado']=='por_ver') $pendientes[] = $fila;
            elseif($fila['estado']=='viendo') $viendo[] = $fila;
        }
    ?>
    <div class="dashboard">
        <div class="header">
            <a href="index.php?page=seleccion" class="btn-back"><i class="fa-solid fa-arrow-left"></i></a>
            <h2 style="color:white; margin:0; text-transform: uppercase;"><?php echo $page; ?></h2>
            <a href="index.php?page=agregar&tipo=<?php echo $tipo_actual; ?>" class="btn-add">+ Agregar</a>
        </div>

        <div class="columns-wrapper">
            <div class="column">
                <h3>Vistas <i class="fa-solid fa-check-circle" style="color:green"></i></h3>
                <?php foreach($vistas as $item): ?>
                    <div class="item">
                        <img src="<?php echo $item['imagen_url']; ?>">
                        <span class="item-title"><?php echo $item['titulo']; ?></span>
                        <div class="item-actions">
                            <a href="index.php?page=acciones&id=<?php echo $item['id']; ?>&accion=borrar&from=<?php echo $page; ?>" onclick="return confirm('¿Borrar?')" style="color:red"><i class="fa-solid fa-trash"></i></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="column">
                <h3>Por ver <i class="fa-solid fa-clock" style="color:orange"></i></h3>
                <?php foreach($pendientes as $item): ?>
                    <div class="item">
                        <img src="<?php echo $item['imagen_url']; ?>">
                        <span class="item-title"><?php echo $item['titulo']; ?></span>
                        <div class="item-actions">
                            <a href="index.php?page=acciones&id=<?php echo $item['id']; ?>&accion=mover&estado=vistas&from=<?php echo $page; ?>" style="color:green"><i class="fa-solid fa-check"></i></a>
                            <?php if($tipo_actual=='serie'): ?>
                                <a href="index.php?page=acciones&id=<?php echo $item['id']; ?>&accion=mover&estado=viendo&from=<?php echo $page; ?>" style="color:blue"><i class="fa-solid fa-eye"></i></a>
                            <?php endif; ?>
                            <a href="index.php?page=acciones&id=<?php echo $item['id']; ?>&accion=borrar&from=<?php echo $page; ?>" onclick="return confirm('¿Borrar?')" style="color:red"><i class="fa-solid fa-trash"></i></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if($tipo_actual == 'serie'): ?>
            <div class="column">
                <h3>Viendo <i class="fa-solid fa-eye" style="color:blue"></i></h3>
                <?php foreach($viendo as $item): ?>
                    <div class="item">
                        <img src="<?php echo $item['imagen_url']; ?>">
                        <span class="item-title"><?php echo $item['titulo']; ?></span>
                        <div class="item-actions">
                            <a href="index.php?page=acciones&id=<?php echo $item['id']; ?>&accion=mover&estado=vistas&from=<?php echo $page; ?>" style="color:green"><i class="fa-solid fa-check"></i></a>
                            <a href="index.php?page=acciones&id=<?php echo $item['id']; ?>&accion=borrar&from=<?php echo $page; ?>" onclick="return confirm('¿Borrar?')" style="color:red"><i class="fa-solid fa-trash"></i></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($page === 'agregar'): 
        $tipo_form = $_GET['tipo'] ?? 'pelicula';
    ?>
    <div class="column" style="width: 300px; margin: 0 auto;">
        <h2>Nueva <?php echo ucfirst($tipo_form); ?></h2>
        <?php if ($mensaje_error) echo "<p style='color:red'>$mensaje_error</p>"; ?>
        
        <form action="index.php?page=guardar" method="POST" style="width:100%">
            <input type="hidden" name="tipo_contenido" value="<?php echo $tipo_form; ?>">
            
            <label>Título:</label>
            <input type="text" name="titulo" class="login-input" style="width:90%; margin-bottom:10px; border:1px solid #ccc" required>
            
            <label>Imagen URL:</label>
            <input type="text" name="imagen_url" class="login-input" style="width:90%; margin-bottom:10px; border:1px solid #ccc">
            
            <label>Estado:</label>
            <select name="estado" class="login-input" style="width:100%; margin-bottom:20px; border:1px solid #ccc">
                <option value="por_ver">Por ver</option>
                <option value="vistas">Vista</option>
                <?php if($tipo_form == 'serie'): ?><option value="viendo">Viendo</option><?php endif; ?>
            </select>

            <button type="submit" class="btn-add" style="width:100%; border:none; cursor:pointer;">Guardar</button>
            <br><br>
            <a href="index.php?page=<?php echo ($tipo_form=='pelicula'?'peliculas':'series'); ?>" style="display:block; text-align:center;">Cancelar</a>
        </form>
    </div>
    <?php endif; ?>

</div> <script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('btnCheck');
        if(btn) {
            btn.addEventListener('click', () => {
                alert('¡Sistema verificado! JavaScript funciona correctamente.');
            });
        }
    });
</script>

</body>
</html>
