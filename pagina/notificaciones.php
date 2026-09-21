<?php

session_start();

require_once "../base de datos/database.php";

// Verificar sesión
if (!isset($_SESSION["id_usuario"])) {
    header("Location: signin.html");
    exit;
}

/*
 * Las notificaciones se generan a partir de los eventos aceptados.
 * Así, cada evento que se publique y sea aceptado aparece automáticamente
 * en esta sección.
 */
$sql = "SELECT
            id_evento,
            titulo,
            descripcion,
            fecha,
            hora,
            lugar,
            imagen
        FROM evento
        WHERE estado = 'aceptado'
        ORDER BY fecha DESC, hora DESC";

$resultado = pg_query($conn_supa, $sql);

if (!$resultado) {
    die("Error al cargar las notificaciones.");
}

$eventos = [];

while ($fila = pg_fetch_assoc($resultado)) {
    $eventos[] = $fila;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Notificaciones - CulturaActiva</title>

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

        .buscador-wrap {
            position: relative;
            width: min(700px, 100%);
            margin: 0 auto;
        }

        .buscador {
            display: block;
            width: 100%;
            height: 48px;
            padding: 0 20px 0 48px;
            border: 1px solid #ddd;
            border-radius: 25px;
            font-size: 14px;
            outline: none;
            background: #fff;
        }

        .icono-busqueda {
            position: absolute;
            left: 17px;
            top: 13px;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }

        .icono-busqueda svg {
            width: 100%;
            height: 100%;
            stroke: #555;
            fill: none;
            stroke-width: 1.7;
            stroke-linecap: round;
        }

        .perfil-superior {
            color: #333;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 8px;
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
            width: min(1180px, 92%);
            margin: 0 auto;
            padding: 35px 0 50px;
        }

        .hero {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 30px;
            margin-bottom: 24px;
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

        /* LISTA DE NOTIFICACIONES */

        .lista-notificaciones {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .notificacion {
            width: 100%;
            min-height: 105px;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 18px rgba(0, 0, 0, .16);
            display: flex;
            align-items: stretch;
        }

        .notificacion-icono {
            width: 84px;
            min-width: 84px;
            background: #6d2788;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notificacion-icono svg {
            width: 30px;
            height: 30px;
            stroke: #fff;
            fill: none;
            stroke-width: 1.7;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .notificacion-info {
            flex: 1;
            padding: 16px 18px;
            min-width: 0;
        }

        .notificacion-titulo {
            color: #4b1f78;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 7px;
        }

        .notificacion-mensaje {
            color: #444;
            font-size: 13px;
            line-height: 1.45;
            margin-bottom: 10px;
        }

        .notificacion-datos {
            display: flex;
            flex-wrap: wrap;
            gap: 8px 18px;
            color: #666;
            font-size: 12px;
        }

        .notificacion-dato {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .notificacion-dato svg {
            width: 15px;
            height: 15px;
            stroke: #4b1f78;
            fill: none;
            stroke-width: 1.6;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .notificacion-imagen {
            width: 150px;
            min-width: 150px;
            height: 105px;
            object-fit: cover;
            background: #ddd;
        }

        .boton-evento {
            display: inline-flex;
            margin-top: 12px;
            background: #4b1f78;
            color: #fff;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
        }

        .sin-notificaciones {
            width: 100%;
            min-height: 180px;
            border: 1px dashed rgba(255, 255, 255, 0.6);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 30px;
            color: #fff;
        }

        .sin-notificaciones svg {
            width: 48px;
            height: 48px;
            margin-bottom: 14px;
            stroke: #fff;
            fill: none;
            stroke-width: 1.5;
        }

        .sin-notificaciones p {
            font-size: 15px;
            margin-bottom: 6px;
        }

        .sin-notificaciones small {
            font-size: 12px;
            color: #ddd;
        }

        .sin-resultados {
            display: none;
            background: #fff;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            color: #555;
            margin-top: 12px;
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

            .notificacion-imagen {
                width: 120px;
                min-width: 120px;
            }

        }

        @media (max-width: 650px) {

            header {
                height: auto;
                min-height: 150px;
                flex-wrap: wrap;
                padding: 14px 20px;
                gap: 10px;
            }

            .logo {
                width: 105px;
            }

            .titulo-app {
                min-width: 0;
                flex: 1;
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

            header .buscador-wrap {
                order: 5;
                flex-basis: 100%;
                width: 100%;
            }

            .contenedor {
                width: 92%;
                padding-top: 28px;
            }

            .hero {
                align-items: flex-start;
                flex-direction: column;
            }

            .hero h1 {
                font-size: 26px;
            }

            .notificacion {
                min-height: 100px;
            }

            .notificacion-icono {
                width: 60px;
                min-width: 60px;
            }

            .notificacion-icono svg {
                width: 24px;
                height: 24px;
            }

            .notificacion-info {
                padding: 13px 12px;
            }

            .notificacion-titulo {
                font-size: 15px;
            }

            .notificacion-mensaje {
                font-size: 11px;
            }

            .notificacion-datos {
                font-size: 10px;
                gap: 5px 10px;
            }

            .notificacion-imagen {
                width: 95px;
                min-width: 95px;
                height: 100px;
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

    <!-- ENCABEZADO -->

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

        <div class="buscador-wrap">

            <span class="icono-busqueda">

                <svg viewBox="0 0 24 24">
                    <circle cx="10.8" cy="10.8" r="6.5"></circle>
                    <path d="M16 16l5 5"></path>
                </svg>

            </span>

            <input
                type="text"
                id="busqueda"
                class="buscador"
                placeholder="Buscar eventos, artistas, lugares..."
            >

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

    <!-- CONTENIDO -->

    <main class="contenedor">

        <section class="hero">

            <div>
                <h1>Notificaciones</h1>
                <p>Aquí aparecen automáticamente los nuevos eventos publicados.</p>
            </div>

        </section>

        <?php if (count($eventos) > 0): ?>

            <div class="lista-notificaciones" id="listaNotificaciones">

                <?php foreach ($eventos as $evento): ?>

                    <article
                        class="notificacion"
                        data-busqueda="<?php
                            echo htmlspecialchars(
                                strtolower(
                                    ($evento["titulo"] ?? "") . " " .
                                    ($evento["descripcion"] ?? "") . " " .
                                    ($evento["lugar"] ?? "")
                                )
                            );
                        ?>"
                    >

                        <div class="notificacion-icono">

                            <svg viewBox="0 0 24 24">

                                <path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>

                                <path d="M10 21h4"></path>

                            </svg>

                        </div>

                        <div class="notificacion-info">

                            <div class="notificacion-titulo">
                                Nuevo evento: <?php echo htmlspecialchars($evento["titulo"]); ?>
                            </div>

                            <div class="notificacion-mensaje">
                                Se ha publicado un nuevo evento cultural en CulturaActiva.
                            </div>

                            <div class="notificacion-datos">

                                <span class="notificacion-dato">

                                    <svg viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9"></circle>
                                        <path d="M12 7v5l3 2"></path>
                                    </svg>

                                    <?php echo htmlspecialchars($evento["fecha"]); ?>
                                    ·
                                    <?php echo htmlspecialchars(substr($evento["hora"], 0, 5)); ?>

                                </span>

                                <span class="notificacion-dato">

                                    <svg viewBox="0 0 24 24">
                                        <path d="M12 21s7-6.2 7-12a7 7 0 1 0-14 0c0 5.8 7 12 7 12z"></path>
                                        <circle cx="12" cy="9" r="2.5"></circle>
                                    </svg>

                                    <?php echo htmlspecialchars($evento["lugar"]); ?>

                                </span>

                            </div>

                            <a
                                href="principal.php"
                                class="boton-evento"
                            >
                                Ver evento
                            </a>

                        </div>

                        <?php if (!empty($evento["imagen"])): ?>

                            <img
                                src="<?php echo htmlspecialchars($evento["imagen"]); ?>"
                                class="notificacion-imagen"
                                alt="<?php echo htmlspecialchars($evento["titulo"]); ?>"
                            >

                        <?php endif; ?>

                    </article>

                <?php endforeach; ?>

            </div>

            <div id="sinResultados" class="sin-resultados">
                No encontramos notificaciones con esa búsqueda.
            </div>

        <?php else: ?>

            <div class="sin-notificaciones">

                <svg viewBox="0 0 24 24">

                    <path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>

                    <path d="M10 21h4"></path>

                </svg>

                <p>
                    No tienes notificaciones.
                </p>

                <small>
                    Cuando se publique y acepte un evento, aparecerá aquí.
                </small>

            </div>

        <?php endif; ?>

    </main>

    <!-- NAVEGACIÓN INFERIOR -->

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

        <a href="notificaciones.php" class="activo">

            <span class="icono">

                <svg viewBox="0 0 24 24">
                    <path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                    <path d="M10 21h4"></path>
                </svg>

            </span>

            Notificaciones

        </a>

        <a href="perfil.php">

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

<script>

    const buscador = document.getElementById("busqueda");
    const notificaciones =
        document.querySelectorAll(".notificacion");
    const sinResultados =
        document.getElementById("sinResultados");

    if (buscador) {

        buscador.addEventListener("input", function() {

            const texto =
                this.value.toLowerCase().trim();

            let encontrados = 0;

            notificaciones.forEach(function(notificacion) {

                const contenido =
                    notificacion.dataset.busqueda || "";

                const coincide =
                    contenido.includes(texto);

                notificacion.style.display =
                    coincide ? "flex" : "flex";

                if (texto === "" || coincide) {
                    notificacion.style.display = "flex";
                    encontrados++;
                } else {
                    notificacion.style.display = "none";
                }

            });

            if (sinResultados) {

                sinResultados.style.display =
                    texto !== "" && encontrados === 0
                        ? "block"
                        : "none";

            }

        });

    }

</script>

</body>

</html>
