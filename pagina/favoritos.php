<?php

session_start();

require_once "../base de datos/database.php";

// Verificar sesión
if (!isset($_SESSION["id_usuario"])) {
    header("Location: signin.html");
    exit;
}

// Traer todos los eventos aceptados.
// JavaScript mostrará únicamente los que estén guardados en favoritos.
$sql = "SELECT
            id_evento,
            titulo,
            descripcion,
            fecha,
            hora,
            lugar,
            imagen,
            latitud,
            longitud
        FROM evento
        WHERE estado = 'aceptado'
        ORDER BY fecha ASC, hora ASC";

$resultado = pg_query($conn_supa, $sql);

if (!$resultado) {
    die("Error al cargar los eventos.");
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

    <title>Favoritos - CulturaActiva</title>

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
            padding: 35px 0 70px;
        }

        .hero {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 30px;
            margin-bottom: 28px;
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

        .publicar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            color: #4b1f78;
            text-decoration: none;
            padding: 11px 20px;
            border-radius: 7px;
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
        }

        .titulo {
            color: #fff;
            font-size: 21px;
            margin: 0 0 18px;
        }

        .lista {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
        }

        .evento-favorito {
            display: none;
            background: #fff;
            color: #222;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 18px rgba(0, 0, 0, .16);
            min-width: 0;
            flex-direction: column;
        }

        .evento-favorito.visible {
            display: flex;
        }

        .evento-imagen,
        .sin-imagen {
            width: 100%;
            height: 205px;
            object-fit: cover;
        }

        .sin-imagen {
            background: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #777;
            font-size: 13px;
        }

        .evento-info {
            padding: 0 17px 17px;
            color: #333;
        }

        .evento-titulo {
            color: #fff;
            padding: 12px 14px;
            margin: 0 -17px 10px;
            background: #f04444;
            font-size: 17px;
            font-weight: 700;
        }

        .evento-favorito:nth-child(2n) .evento-titulo {
            background: #f28b20;
        }

        .evento-favorito:nth-child(3n) .evento-titulo {
            background: #35bd58;
        }

        .evento-dato {
            font-size: 13px;
            padding: 5px 0;
            color: #444;
        }

        .evento-descripcion {
            font-size: 13px;
            padding: 5px 0;
            color: #555;
            line-height: 1.45;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .acciones {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .boton-mapa,
        .quitar-favorito {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            padding: 7px 10px;
            font-size: 11px;
            cursor: pointer;
            text-decoration: none;
        }

        .boton-mapa {
            color: #4b1f78;
            background: #fff;
            border: 1px solid #4b1f78;
        }

        .quitar-favorito {
            margin: 0;
            border: 0;
            background: #d62828;
            color: #fff;
        }

        .sin-favoritos {
            display: flex;
            width: 100%;
            min-height: 180px;
            border: 1px dashed rgba(255, 255, 255, .6);
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
            padding: 30px;
            color: #fff;
        }

        .sin-favoritos svg {
            width: 48px;
            height: 48px;
            margin-bottom: 14px;
            stroke: #fff;
            fill: none;
            stroke-width: 1.5;
        }

        .sin-favoritos p {
            font-size: 15px;
            margin-bottom: 6px;
        }

        .sin-favoritos small {
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
            margin-top: 10px;
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

        /* RESPONSIVE */

        @media (max-width: 900px) {

            .lista {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

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
                padding-top: 28px;
            }

            .hero {
                align-items: flex-start;
                flex-direction: column;
            }

            .hero h1 {
                font-size: 26px;
            }

            .lista {
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
            <div class="nombre-app">CulturaActiva</div>
            <div class="ciudad-app">PASTO</div>
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

    <main class="contenedor">

        <section class="hero">

            <div>
                <h1>Mis favoritos</h1>
                <p>Los eventos que guardaste para no perderte.</p>
            </div>

        </section>

        <?php if (count($eventos) > 0): ?>

            <div id="listaFavoritos" class="lista">

                <?php foreach ($eventos as $evento): ?>

                    <article
                        class="evento-favorito"
                        data-id="<?php echo (int)$evento["id_evento"]; ?>"
                        data-busqueda="<?php
                            echo htmlspecialchars(
                                strtolower(
                                    $evento["titulo"] . " " .
                                    ($evento["descripcion"] ?? "") . " " .
                                    $evento["lugar"]
                                )
                            );
                        ?>"
                    >

                        <?php if (!empty($evento["imagen"])): ?>

                            <img
                                src="<?php echo htmlspecialchars($evento["imagen"]); ?>"
                                class="evento-imagen"
                                alt="<?php echo htmlspecialchars($evento["titulo"]); ?>"
                            >

                        <?php else: ?>

                            <div class="sin-imagen">
                                Sin imagen
                            </div>

                        <?php endif; ?>

                        <div class="evento-info">

                            <div class="evento-titulo">
                                <?php echo htmlspecialchars($evento["titulo"]); ?>
                            </div>

                            <div class="evento-dato">
                                <strong>Fecha:</strong>
                                <?php echo htmlspecialchars($evento["fecha"]); ?>
                            </div>

                            <div class="evento-dato">
                                <strong>Hora:</strong>
                                <?php echo htmlspecialchars(substr($evento["hora"], 0, 5)); ?>
                            </div>

                            <div class="evento-dato">
                                <strong>Lugar:</strong>
                                <?php echo htmlspecialchars($evento["lugar"]); ?>
                            </div>

                            <div class="evento-descripcion">
                                <?php
                                    echo htmlspecialchars(
                                        $evento["descripcion"] ?? "Sin descripción."
                                    );
                                ?>
                            </div>

                            <div class="acciones">

                                <?php if (
                                    $evento["latitud"] !== null &&
                                    $evento["longitud"] !== null
                                ): ?>

                                    <a
                                        href="mapa.php?evento=<?php echo (int)$evento["id_evento"]; ?>"
                                        class="boton-mapa"
                                    >
                                        Ver ubicación
                                    </a>

                                <?php endif; ?>

                                <button
                                    type="button"
                                    class="quitar-favorito"
                                    data-quitar="<?php echo (int)$evento["id_evento"]; ?>"
                                >
                                    Quitar favorito
                                </button>

                            </div>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

            <div id="sinFavoritos" class="sin-favoritos">

                <svg viewBox="0 0 24 24">
                    <path d="M20.8 8.8c0 5.5-8.8 11-8.8 11S3.2 14.3 3.2 8.8A4.8 4.8 0 0 1 8 4c1.7 0 3.2.9 4 2.2C12.8 4.9 14.3 4 16 4a4.8 4.8 0 0 1 4.8 4.8z"></path>
                </svg>

                <p>
                    No tienes eventos favoritos.
                </p>

                <small>
                    Cuando marques un evento como favorito aparecerá aquí.
                </small>

            </div>

            <div id="sinResultados" class="sin-resultados">
                No se encontraron favoritos con esa búsqueda.
            </div>

        <?php else: ?>

            <div class="sin-favoritos">

                <svg viewBox="0 0 24 24">
                    <path d="M20.8 8.8c0 5.5-8.8 11-8.8 11S3.2 14.3 3.2 8.8A4.8 4.8 0 0 1 8 4c1.7 0 3.2.9 4 2.2C12.8 4.9 14.3 4 16 4a4.8 4.8 0 0 1 4.8 4.8z"></path>
                </svg>

                <p>
                    No hay eventos aceptados todavía.
                </p>

                <small>
                    Los eventos que marques como favoritos aparecerán aquí.
                </small>

            </div>

        <?php endif; ?>

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

        <a href="favoritos.php" class="activo">

            <span class="icono">

                <svg viewBox="0 0 24 24">
                    <path d="M20.8 8.8c0 5.5-8.8 11-8.8 11S3.2 8.8 3.2 8.8C3.2 5.6 5.3 3.5 8.2 3.5c1.7 0 3.1.8 3.8 2.1.7-1.3 2.1-2.1 3.8-2.1 2.9 0 5 2.1 5 5.3z"></path>
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

    const CLAVE_FAVORITOS = "culturaactiva_favoritos";

    const buscador = document.getElementById("busqueda");

    function obtenerFavoritos() {

        try {

            const datos = JSON.parse(
                localStorage.getItem(CLAVE_FAVORITOS) || "[]"
            );

            return Array.isArray(datos)
                ? datos.map(String)
                : [];

        } catch (error) {

            return [];

        }

    }

    function guardarFavoritos(favoritos) {

        localStorage.setItem(
            CLAVE_FAVORITOS,
            JSON.stringify(favoritos)
        );

    }

    function actualizarFavoritos() {

        const favoritos = obtenerFavoritos();

        const tarjetas = document.querySelectorAll(".evento-favorito");

        const texto = buscador
            ? buscador.value.toLowerCase().trim()
            : "";

        let visibles = 0;
        let coincidencias = 0;

        tarjetas.forEach(function(tarjeta) {

            const id = String(tarjeta.dataset.id);

            const contenido =
                (tarjeta.dataset.busqueda || "").toLowerCase();

            const esFavorito = favoritos.includes(id);

            const coincide = contenido.includes(texto);

            if (esFavorito) {

                visibles++;

                if (coincide) {

                    tarjeta.classList.add("visible");
                    coincidencias++;

                } else {

                    tarjeta.classList.remove("visible");

                }

            } else {

                tarjeta.classList.remove("visible");

            }

        });

        const sinFavoritos =
            document.getElementById("sinFavoritos");

        const sinResultados =
            document.getElementById("sinResultados");

        if (sinFavoritos) {

            sinFavoritos.style.display =
                visibles === 0 && texto === ""
                    ? "flex"
                    : "none";

        }

        if (sinResultados) {

            sinResultados.style.display =
                texto !== "" &&
                favoritos.length > 0 &&
                coincidencias === 0
                    ? "block"
                    : "none";

        }

    }

    document
        .querySelectorAll(".quitar-favorito")
        .forEach(function(boton) {

            boton.addEventListener("click", function() {

                const id = String(this.dataset.quitar);

                let favoritos = obtenerFavoritos();

                favoritos = favoritos.filter(function(item) {
                    return item !== id;
                });

                guardarFavoritos(favoritos);

                actualizarFavoritos();

            });

        });

    if (buscador) {

        buscador.addEventListener("input", actualizarFavoritos);

    }

    actualizarFavoritos();

</script>

</body>

</html>
