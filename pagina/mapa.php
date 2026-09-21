<?php

session_start();

require_once "../base de datos/database.php";

// Verificar sesión
if (!isset($_SESSION["id_usuario"])) {
    header("Location: signin.html");
    exit;
}

// Evento que se desea marcar
$id_evento_seleccionado = isset($_GET["evento"])
    ? (int) $_GET["evento"]
    : 0;

// Traer todos los eventos aceptados para mostrar sus marcadores
$sql = "SELECT
            id_evento,
            id_usuario,
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
          AND latitud IS NOT NULL
          AND longitud IS NOT NULL
        ORDER BY fecha ASC, hora ASC";

$resultado = pg_query($conn_supa, $sql);

if (!$resultado) {
    die("Error al cargar los eventos.");
}

$eventos = [];

while ($fila = pg_fetch_assoc($resultado)) {
    $eventos[] = $fila;
}

// Evento seleccionado para la tarjeta inferior
$evento_seleccionado = null;

foreach ($eventos as $evento) {
    if ((int)$evento["id_evento"] === $id_evento_seleccionado) {
        $evento_seleccionado = $evento;
        break;
    }
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

    <title>Mapa - CulturaActiva</title>

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    >

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

        /* MAPA */

        .mapa-contenedor {
            width: 100%;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 18px rgba(0, 0, 0, .16);
        }

        #mapa {
            width: 100%;
            height: 500px;
        }

        /* EVENTO MARCADO */

        .evento-marcado {
            margin-top: 22px;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 18px rgba(0, 0, 0, .16);
        }

        .evento-marcado-cabecera {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 13px 18px;
            color: #fff;
            background: #f04444;
        }

        .evento-marcado-cabecera h2 {
            font-size: 19px;
            margin: 0;
        }

        .marcado {
            font-size: 11px;
            white-space: nowrap;
        }

        .evento-marcado-contenido {
            display: flex;
            gap: 0;
            min-height: 170px;
        }

        .evento-marcado-imagen {
            width: 220px;
            min-width: 220px;
            height: 170px;
            object-fit: cover;
            background: #ddd;
        }

        .evento-marcado-sin-imagen {
            width: 220px;
            min-width: 220px;
            height: 170px;
            background: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #777;
            font-size: 13px;
        }

        .evento-marcado-info {
            padding: 18px 20px;
            color: #333;
            flex: 1;
        }

        .evento-marcado-dato {
            font-size: 14px;
            margin: 0 0 9px;
            color: #444;
        }

        .evento-marcado-dato strong {
            color: #4b1f78;
        }

        .evento-marcado-descripcion {
            font-size: 14px;
            line-height: 1.5;
            color: #555;
            margin-top: 13px;
        }

        .boton-ruta {
            display: inline-flex;
            margin-top: 15px;
            background: #4b1f78;
            color: #fff;
            text-decoration: none;
            padding: 9px 13px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
        }

        .sin-evento-seleccionado {
            margin-top: 22px;
            padding: 30px;
            border: 1px dashed rgba(255,255,255,.6);
            text-align: center;
            color: #fff;
            font-size: 14px;
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

        .popup-evento {
            font-family: Arial, Helvetica, sans-serif;
            min-width: 180px;
        }

        .popup-evento strong {
            color: #4b1f78;
        }

        .popup-evento small {
            display: block;
            margin-top: 4px;
            color: #555;
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

            .evento-marcado-imagen,
            .evento-marcado-sin-imagen {
                width: 190px;
                min-width: 190px;
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

            #mapa {
                height: 420px;
            }

            .evento-marcado-contenido {
                flex-direction: column;
            }

            .evento-marcado-imagen,
            .evento-marcado-sin-imagen {
                width: 100%;
                min-width: 0;
                height: 210px;
            }

            .evento-marcado-info {
                padding: 17px;
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
                id="buscador"
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
                <h1>Mapa de eventos</h1>
                <p>Encuentra la ubicación de los eventos culturales en Pasto.</p>
            </div>

        </section>

        <div class="mapa-contenedor">

            <div id="mapa"></div>

        </div>

        <?php if ($evento_seleccionado): ?>

            <section class="evento-marcado" id="eventoMarcado">

                <div class="evento-marcado-cabecera">

                    <h2>
                        <?php echo htmlspecialchars($evento_seleccionado["titulo"]); ?>
                    </h2>

                    <span class="marcado">
                        ● Evento marcado
                    </span>

                </div>

                <div class="evento-marcado-contenido">

                    <?php if (!empty($evento_seleccionado["imagen"])): ?>

                        <img
                            src="<?php echo htmlspecialchars($evento_seleccionado["imagen"]); ?>"
                            class="evento-marcado-imagen"
                            alt="<?php echo htmlspecialchars($evento_seleccionado["titulo"]); ?>"
                        >

                    <?php else: ?>

                        <div class="evento-marcado-sin-imagen">
                            Sin imagen
                        </div>

                    <?php endif; ?>

                    <div class="evento-marcado-info">

                        <div class="evento-marcado-dato">
                            <strong>Dirección:</strong>
                            <?php echo htmlspecialchars($evento_seleccionado["lugar"] ?: "No registrada"); ?>
                        </div>

                        <div class="evento-marcado-dato">
                            <strong>Fecha:</strong>
                            <?php echo htmlspecialchars($evento_seleccionado["fecha"]); ?>
                        </div>

                        <div class="evento-marcado-dato">
                            <strong>Hora:</strong>
                            <?php echo htmlspecialchars(substr($evento_seleccionado["hora"], 0, 5)); ?>
                        </div>

                        <div class="evento-marcado-descripcion">
                            <?php
                                echo htmlspecialchars(
                                    $evento_seleccionado["descripcion"] ?: "Sin descripción."
                                );
                            ?>
                        </div>

                        <a
                            class="boton-ruta"
                            target="_blank"
                            rel="noopener noreferrer"
                            href="https://www.google.com/maps/dir/?api=1&destination=<?php
                                echo urlencode(
                                    $evento_seleccionado["latitud"] . "," .
                                    $evento_seleccionado["longitud"]
                                );
                            ?>"
                        >
                            Cómo llegar
                        </a>

                    </div>

                </div>

            </section>

        <?php else: ?>

            <div class="sin-evento-seleccionado">
                Selecciona un marcador en el mapa para ver el evento y su dirección aquí.
            </div>

        <?php endif; ?>

    </main>

    <!-- NAVEGACIÓN -->

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

        <a href="mapa.php" class="activo">

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

<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
></script>

<script>

    const eventos = <?php echo json_encode(
        $eventos,
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES |
        JSON_HEX_TAG |
        JSON_HEX_AMP |
        JSON_HEX_APOS |
        JSON_HEX_QUOT
    ); ?>;

    const eventoSeleccionado = <?php echo json_encode(
        $id_evento_seleccionado
    ); ?>;

    // Crear mapa centrado en Pasto
    const mapa = L.map("mapa").setView(
        [1.2136, -77.2811],
        13
    );

    L.tileLayer(
        "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
        {
            maxZoom: 19,
            attribution: "&copy; OpenStreetMap"
        }
    ).addTo(mapa);

    const marcadores = {};

    eventos.forEach(function(evento) {

        const lat = parseFloat(evento.latitud);
        const lng = parseFloat(evento.longitud);

        if (Number.isNaN(lat) || Number.isNaN(lng)) {
            return;
        }

        const popup = `
            <div class="popup-evento">
                <strong>${escapeHtml(evento.titulo || "Evento")}</strong>
                <small>${escapeHtml(evento.lugar || "Dirección no registrada")}</small>
                <small>${escapeHtml(evento.fecha || "")} · ${escapeHtml((evento.hora || "").substring(0, 5))}</small>
                <small>
                    <a href="mapa.php?evento=${encodeURIComponent(evento.id_evento)}">
                        Ver evento
                    </a>
                </small>
            </div>
        `;

        const marcador = L.marker([lat, lng])
            .addTo(mapa)
            .bindPopup(popup);

        marcadores[String(evento.id_evento)] = marcador;

        marcador.on("click", function() {
            mostrarEventoSeleccionado(evento);
        });

    });

    function mostrarEventoSeleccionado(evento) {

        const panel = document.getElementById("eventoMarcado");

        if (!panel) {
            return;
        }

        panel.querySelector(".evento-marcado-cabecera h2").textContent =
            evento.titulo || "Evento";

        const imagen = panel.querySelector(".evento-marcado-imagen");
        const sinImagen = panel.querySelector(".evento-marcado-sin-imagen");

        if (imagen) {
            if (evento.imagen) {
                imagen.src = evento.imagen;
                imagen.alt = evento.titulo || "Evento";
                imagen.style.display = "block";
            } else {
                imagen.style.display = "none";
            }
        }

        if (sinImagen) {
            sinImagen.style.display = evento.imagen ? "none" : "flex";
        }

        const datos = panel.querySelectorAll(".evento-marcado-dato");

        if (datos[0]) {
            datos[0].innerHTML =
                "<strong>Dirección:</strong> " +
                escapeHtml(evento.lugar || "No registrada");
        }

        if (datos[1]) {
            datos[1].innerHTML =
                "<strong>Fecha:</strong> " +
                escapeHtml(evento.fecha || "");
        }

        if (datos[2]) {
            datos[2].innerHTML =
                "<strong>Hora:</strong> " +
                escapeHtml((evento.hora || "").substring(0, 5));
        }

        const descripcion =
            panel.querySelector(".evento-marcado-descripcion");

        if (descripcion) {
            descripcion.textContent =
                evento.descripcion || "Sin descripción.";
        }

        const botonRuta =
            panel.querySelector(".boton-ruta");

        if (botonRuta) {
            botonRuta.href =
                "https://www.google.com/maps/dir/?api=1&destination=" +
                encodeURIComponent(
                    evento.latitud + "," + evento.longitud
                );
        }

        panel.scrollIntoView({
            behavior: "smooth",
            block: "start"
        });
    }

    function escapeHtml(valor) {

        return String(valor)
            .replaceAll("&", "&amp;")
            .replaceAll("<", "&lt;")
            .replaceAll(">", "&gt;")
            .replaceAll('"', "&quot;")
            .replaceAll("'", "&#039;");

    }

    // Marcar y centrar automáticamente el evento enviado por GET
    if (
        eventoSeleccionado &&
        marcadores[String(eventoSeleccionado)]
    ) {

        const marcadorSeleccionado =
            marcadores[String(eventoSeleccionado)];

        marcadorSeleccionado.openPopup();

        mapa.setView(
            marcadorSeleccionado.getLatLng(),
            16,
            {
                animate: true
            }
        );

    }

    // Buscador: al escribir, ir al principal con la búsqueda.
    const buscador = document.getElementById("buscador");

    if (buscador) {

        buscador.addEventListener("keydown", function(e) {

            if (e.key === "Enter") {

                const texto = this.value.trim();

                if (texto !== "") {
                    window.location.href =
                        "principal.php?busqueda=" +
                        encodeURIComponent(texto);
                }

            }

        });

    }

</script>

</body>

</html>
