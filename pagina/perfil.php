<?php

session_start();

require_once "../base de datos/database.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: signin.html");
    exit;
}

$id_usuario = $_SESSION["id_usuario"];

$sql = "SELECT
            id_usuario,
            nombre,
            correo,
            rol
        FROM usuario
        WHERE id_usuario = $1
        LIMIT 1";

$resultado = pg_query_params(
    $conn_supa,
    $sql,
    [$id_usuario]
);

if (!$resultado) {
    die("Error al cargar el perfil.");
}

$usuario = pg_fetch_assoc($resultado);

if (!$usuario) {
    session_destroy();
    header("Location: signin.html");
    exit;
}

$rol = strtolower(trim($usuario["rol"] ?? "usuario"));

$_SESSION["rol"] = $rol;

$es_admin = ($rol === "admin");

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Mi perfil - CulturaActiva</title>

<style>

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

html,
body {
    margin: 0;
    padding: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: #eeeeee;
    color: #222;
}

body {
    min-height: 100vh;
}

.app {
    width: 100%;
    min-height: 100vh;
    background: #51006f;
    padding-bottom: 72px;
}

/* ENCABEZADO IGUAL AL PRINCIPAL */

header {
    width: 100%;
    height: 112px;
    background: #fff;
    display: flex;
    align-items: center;
    border-bottom: 1px solid #ddd;
    padding: 0 5vw;
    gap: 28px;
}

.logo {
    width: 145px;
    height: auto;
    flex-shrink: 0;
}

.titulo-app {
    color: #51006f;
    line-height: 1.05;
    min-width: 150px;
}

.nombre-app {
    font-size: 21px;
    font-weight: 700;
}

.ciudad-app {
    font-size: 15px;
    font-weight: 700;
    margin-top: 2px;
}

.eslogan-app {
    font-size: 8px;
    color: #555;
    margin-top: 4px;
}

.perfil-superior {
    color: #333;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: auto;
    border: 0;
    background: transparent;
    cursor: pointer;
    flex-shrink: 0;
}

.perfil-superior svg {
    width: 28px;
    height: 28px;
}

/* CONTENIDO */

.contenedor {
    width: min(900px, 92%);
    margin: 0 auto;
    padding: 35px 0 50px;
}

.hero {
    margin-bottom: 25px;
}

.hero h1 {
    margin: 0;
    color: #fff;
    font-size: 32px;
}

.hero p {
    margin: 7px 0 0;
    color: #eadcf0;
    font-size: 15px;
}

/* TARJETA DE PERFIL */

.perfil-panel {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 5px 18px rgba(0, 0, 0, .16);
    overflow: hidden;
}

.perfil-cabecera {
    background: #4b1f78;
    color: #fff;
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 18px;
}

.usuario-icono {
    width: 82px;
    height: 82px;
    min-width: 82px;
    border: 2px solid #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.usuario-icono svg {
    width: 48px;
    height: 48px;
    stroke: #fff;
    fill: none;
    stroke-width: 1.4;
}

.perfil-cabecera-info h2 {
    font-size: 24px;
    margin-bottom: 5px;
}

.perfil-cabecera-info p {
    font-size: 13px;
    color: #eadcf0;
}

.datos-perfil {
    padding: 25px;
}

.fila-dato {
    border-bottom: 1px solid #eee;
    padding: 0 0 16px;
    margin-bottom: 18px;
}

.fila-dato:last-child {
    border-bottom: 0;
    margin-bottom: 0;
    padding-bottom: 0;
}

.etiqueta {
    color: #777;
    font-size: 13px;
    margin-bottom: 6px;
}

.valor {
    color: #222;
    font-size: 16px;
    word-break: break-word;
}

.rol-admin {
    color: #50006f;
    font-weight: bold;
}

.acciones-perfil {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-top: 18px;
}

.accion {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 48px;
    border-radius: 7px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 700;
    padding: 10px 14px;
    text-align: center;
}

.admin {
    background: #fff;
    color: #50006f;
    border: 2px solid #50006f;
}

.cerrar {
    background: #e32626;
    color: #fff;
}

/* NAVEGACIÓN INFERIOR IGUAL AL PRINCIPAL */

.nav {
    position: fixed;
    left: 0;
    bottom: 0;
    width: 100%;
    height: 72px;
    background: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 55px;
    border-top: 1px solid #ddd;
    padding: 0 20px;
    z-index: 1000;
}

.nav a {
    min-width: 75px;
    height: 72px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #333;
    text-decoration: none;
    font-size: 11px;
    cursor: pointer;
}

.nav a:hover,
.activo {
    color: #4b1f78 !important;
}

.icono {
    width: 24px;
    height: 24px;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.icono svg {
    width: 22px;
    height: 22px;
    stroke: currentColor;
    fill: none;
    stroke-width: 1.7;
    stroke-linecap: round;
    stroke-linejoin: round;
}

@media (max-width: 900px) {

    header {
        padding: 0 3%;
        gap: 18px;
    }

    .logo {
        width: 120px;
    }

    .titulo-app {
        min-width: 125px;
    }

    .nombre-app {
        font-size: 18px;
    }

    .ciudad-app {
        font-size: 13px;
    }

    .eslogan-app {
        font-size: 7px;
    }

    .nav {
        gap: 25px;
    }

}

@media (max-width: 650px) {

    header {
        height: 100px;
        padding: 12px 20px;
        gap: 12px;
    }

    .logo {
        width: 95px;
    }

    .titulo-app {
        min-width: 0;
        flex: 1;
    }

    .nombre-app {
        font-size: 17px;
    }

    .ciudad-app {
        font-size: 12px;
    }

    .eslogan-app {
        font-size: 6px;
    }

    .perfil-superior {
        margin-left: 0;
    }

    .contenedor {
        width: 92%;
        padding-top: 28px;
    }

    .hero h1 {
        font-size: 26px;
    }

    .perfil-cabecera {
        padding: 18px;
    }

    .usuario-icono {
        width: 68px;
        height: 68px;
        min-width: 68px;
    }

    .usuario-icono svg {
        width: 40px;
        height: 40px;
    }

    .perfil-cabecera-info h2 {
        font-size: 20px;
    }

    .perfil-cabecera-info p {
        font-size: 11px;
    }

    .datos-perfil {
        padding: 20px;
    }

    .acciones-perfil {
        grid-template-columns: 1fr;
    }

    .nav {
        gap: 4px;
        padding: 0 5px;
    }

    .nav a {
        min-width: 55px;
        font-size: 10px;
    }

}

</style>

</head>

<body>

<div class="app">

<header>

    <img
        src="../imagen/usuario.png"
        class="logo"
        alt="CulturaActiva Pasto"
    >

    <div class="titulo-app">

        <div class="nombre-app">
            CulturaActiva
        </div>

        <div class="ciudad-app">
            PASTO
        </div>

        <div class="eslogan-app">
            Conecta con la cultura, vive tu ciudad.
        </div>

    </div>

    <a
        href="perfil.php"
        class="perfil-superior"
        aria-label="Perfil"
    >

        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.6"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <circle cx="12" cy="8" r="3"></circle>
            <path d="M5 21c0-3.5 3-6 7-6s7 2.5 7 6"></path>
            <circle cx="12" cy="12" r="10"></circle>
        </svg>

    </a>

</header>

<main class="contenedor">

    <section class="hero">

        <h1>Mi perfil</h1>

        <p>
            Consulta la información de tu cuenta en CulturaActiva.
        </p>

    </section>

    <section class="perfil-panel">

        <div class="perfil-cabecera">

            <div class="usuario-icono">

                <svg viewBox="0 0 24 24">

                    <circle
                        cx="12"
                        cy="8"
                        r="4"
                    ></circle>

                    <path
                        d="M4 21c0-4.5 3.5-7 8-7s8 2.5 8 7"
                    ></path>

                </svg>

            </div>

            <div class="perfil-cabecera-info">

                <h2>
                    <?php echo htmlspecialchars($usuario["nombre"]); ?>
                </h2>

                <p>
                    <?php echo $es_admin
                        ? "Cuenta de administrador"
                        : "Cuenta de usuario";
                    ?>
                </p>

            </div>

        </div>

        <div class="datos-perfil">

            <div class="fila-dato">

                <div class="etiqueta">
                    Nombre
                </div>

                <div class="valor">
                    <?php
                        echo htmlspecialchars($usuario["nombre"]);
                    ?>
                </div>

            </div>

            <div class="fila-dato">

                <div class="etiqueta">
                    Correo electrónico
                </div>

                <div class="valor">
                    <?php
                        echo htmlspecialchars($usuario["correo"]);
                    ?>
                </div>

            </div>

            <div class="fila-dato">

                <div class="etiqueta">
                    Tipo de cuenta
                </div>

                <div class="valor">

                    <?php

                    if ($es_admin) {

                        echo '<span class="rol-admin">Administrador</span>';

                    } else {

                        echo "Usuario";

                    }

                    ?>

                </div>

            </div>

            <div class="acciones-perfil">

                <?php if ($es_admin): ?>

                    <a
                        href="administrador.php"
                        class="accion admin"
                    >
                        ⚙ Administrar eventos
                    </a>

                <?php endif; ?>

                <a
                    href="cerrar_sesion.php"
                    class="accion cerrar"
                >
                    Cerrar sesión
                </a>

            </div>

        </div>

    </section>

</main>

<nav class="nav">

    <a href="principal.php">

        <span class="icono">

            <svg viewBox="0 0 24 24">
                <path d="M3 10.5L12 3l9 7.5"></path>
                <path d="M5 9.5V21h14V9.5"></path>
                <path d="M9 21v-7h6v7"></path>
            </svg>

        </span>

        Inicio

    </a>

    <a href="favoritos.php">

        <span class="icono">

            <svg viewBox="0 0 24 24">
                <path d="M20.8 8.8c0 5.5-8.8 11-8.8 11S3.2 14.3 3.2 8.8C3.2 5.6 5.3 3.5 8.2 3.5c1.7 0 3.1.8 3.8 2.1.7-1.3 2.1-2.1 3.8-2.1 2.9 0 5 2.1 5 5.3z"></path>
            </svg>

        </span>

        Favoritos

    </a>

    <a href="mapa.php">

        <span class="icono">

            <svg viewBox="0 0 24 24">
                <path d="M12 21s7-6.2 7-12a7 7 0 1 0-14 0c0 5.8 7 12 7 12z"></path>
                <circle cx="12" cy="9" r="2.5"></circle>
            </svg>

        </span>

        Mapa

    </a>

    <a href="notificaciones.php">

        <span class="icono">

            <svg viewBox="0 0 24 24">
                <path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                <path d="M10 21h4"></path>
            </svg>

        </span>

        Notificaciones

    </a>

    <a href="perfil.php" class="activo">

        <span class="icono">

            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="8" r="3.2"></circle>
                <path d="M5 21c0-3.8 3-6 7-6s7 2.2 7 6"></path>
            </svg>

        </span>

        Perfil

    </a>

</nav>

</div>

</body>

</html>
