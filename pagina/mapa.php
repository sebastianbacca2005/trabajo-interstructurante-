<?php

session_start();

// Verificar sesion
if (!isset($_SESSION["id_usuario"])) {
    header("Location: signin.html");
    exit;
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


    <!-- Leaflet -->

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

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #eeeeee;
        }


        /* Contenedor */

        .app {
            width: 100%;
            max-width: 430px;
            min-height: 100vh;
            margin: auto;
            background: #50006f;
            padding-bottom: 70px;
        }


        /* Encabezado */

        .header {
            height: 126px;
            background: white;
            position: relative;
            padding: 8px 15px;
        }


        /* Logo */

        .logo {
            width: 105px;
            height: auto;
            display: block;
            margin-top: 2px;
        }


        /* Titulo */

        .titulo-app {
            position: absolute;
            left: 126px;
            top: 10px;
            color: #50006f;
        }

        .nombre-app {
            font-size: 23px;
            font-weight: bold;
            line-height: 25px;
        }

        .ciudad-app {
            font-size: 16px;
            font-weight: bold;
            line-height: 18px;
        }

        .eslogan-app {
            font-size: 8px;
            color: #555;
            margin-top: 2px;
        }


        /* Perfil */

        .perfil-superior {
            position: absolute;
            right: 18px;
            top: 28px;
            width: 22px;
            height: 22px;
            border: none;
            background: transparent;
            cursor: pointer;
        }

        .perfil-superior svg {
            width: 100%;
            height: 100%;
            stroke: #333;
            fill: none;
            stroke-width: 1.5;
        }


        /* Buscador */

        .buscador {
            position: absolute;
            top: 77px;
            left: 126px;
            right: 15px;
            height: 33px;
            background: white;
            border: 1px solid #222;
            display: flex;
            align-items: center;
        }

        .icono-busqueda {
            width: 30px;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icono-busqueda svg {
            width: 16px;
            height: 16px;
            stroke: #111;
            fill: none;
            stroke-width: 1.8;
        }

        .buscador input {
            width: 100%;
            height: 100%;
            border: none;
            outline: none;
            font-size: 11px;
            padding-right: 5px;
        }


        /* Contenido */

        .contenido {
            padding: 22px 10px 15px 10px;
            color: white;
        }


        /* Mapa */

        #mapa {
            width: 100%;
            height: 420px;
            border: none;
        }


        /* Tarjeta del evento */

        .evento {
            width: 100%;
            min-height: 110px;
            display: flex;
            margin-top: 10px;
            background: #50006f;
        }


        .evento-imagen {
            width: 130px;
            min-width: 130px;
            height: 110px;
            background: #6d2788;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 11px;
            text-align: center;
        }


        .evento-info {
            flex: 1;
            padding-left: 8px;
        }


        .evento-titulo {
            min-height: 28px;
            background: #ff4054;
            display: flex;
            align-items: center;
            padding: 5px 8px;
            font-size: 13px;
            margin-bottom: 5px;
        }


        .evento-dato {
            display: flex;
            align-items: center;
            min-height: 30px;
            font-size: 11px;
        }


        .evento-dato svg {
            width: 17px;
            height: 17px;
            margin-right: 7px;
            stroke: white;
            fill: none;
            stroke-width: 1.5;
            flex-shrink: 0;
        }


        /* Sin eventos */

        .sin-eventos {
            width: 100%;
            min-height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            border: 1px dashed rgba(255,255,255,0.5);
            font-size: 12px;
            padding: 15px;
            margin-top: 10px;
        }


        /* Menu */

        .menu-inferior {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 430px;
            height: 69px;
            background: white;
            border-top: 1px solid #ddd;
            display: flex;
            z-index: 100;
        }

        .menu-item {
            flex: 1;
            border: none;
            background: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            color: #111;
            font-size: 9px;
            cursor: pointer;
        }

        .menu-item svg {
            width: 21px;
            height: 21px;
            stroke: #222;
            fill: none;
            stroke-width: 1.7;
        }

        .menu-item.activo {
            color: #4d0870;
            font-weight: bold;
        }

        .menu-item.activo svg {
            fill: #4d0870;
            stroke: #4d0870;
        }

    </style>

</head>


<body>

<div class="app">


    <!-- Encabezado -->

    <header class="header">


        <!-- Logo -->

        <img
            src="../imagen/usuario.png"
            alt="CulturaActiva Pasto"
            class="logo"
        >


        <!-- Nombre -->

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


        <!-- Perfil -->

        <button
            class="perfil-superior"
            onclick="irPerfil()"
        >

            <svg viewBox="0 0 24 24">

                <circle
                    cx="12"
                    cy="12"
                    r="9"
                ></circle>

                <circle
                    cx="12"
                    cy="9"
                    r="3"
                ></circle>

                <path
                    d="M6.5 19c1.5-3 9.5-3 11 0"
                ></path>

            </svg>

        </button>


        <!-- Buscador -->

        <div class="buscador">

            <div class="icono-busqueda">

                <svg viewBox="0 0 24 24">

                    <circle
                        cx="10.5"
                        cy="10.5"
                        r="6.5"
                    ></circle>

                    <line
                        x1="15.5"
                        y1="15.5"
                        x2="21"
                        y2="21"
                    ></line>

                </svg>

            </div>

            <input
                type="text"
                placeholder="Buscar eventos, artistas, lugares..."
            >

        </div>

    </header>


    <!-- Contenido -->

    <main class="contenido">


        <!-- Mapa real de Pasto -->

        <div id="mapa"></div>


        <!-- Evento -->

        <div class="sin-eventos">

            Los eventos aparecerán aquí cuando sean registrados.

        </div>

    </main>


    <!-- Menu inferior -->

    <nav class="menu-inferior">


        <!-- Inicio -->

        <button
            class="menu-item"
            onclick="irInicio()"
        >

            <svg viewBox="0 0 24 24">

                <path
                    d="M3 10.5L12 3l9 7.5v9a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"
                ></path>

            </svg>

            <span>Inicio</span>

        </button>


        <!-- Favoritos -->

        <button
            class="menu-item"
            onclick="irFavoritos()"
        >

            <svg viewBox="0 0 24 24">

                <path
                    d="M20.8 8.8c0 5.5-8.8 11-8.8 11S3.2 14.3 3.2 8.8A4.8 4.8 0 0 1 8 4c1.7 0 3.2.9 4 2.2C12.8 4.9 14.3 4 16 4a4.8 4.8 0 0 1 4.8 4.8z"
                ></path>

            </svg>

            <span>Favoritos</span>

        </button>


        <!-- Mapa -->

        <button
            class="menu-item activo"
        >

            <svg viewBox="0 0 24 24">

                <path
                    d="M12 21s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12z"
                ></path>

                <circle
                    cx="12"
                    cy="9"
                    r="2"
                ></circle>

            </svg>

            <span>Mapa</span>

        </button>


        <!-- Notificaciones -->

        <button
            class="menu-item"
            onclick="irNotificaciones()"
        >

            <svg viewBox="0 0 24 24">

                <path
                    d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"
                ></path>

                <path
                    d="M10 21h4"
                ></path>

            </svg>

            <span>Notificaciones</span>

        </button>


        <!-- Perfil -->

        <button
            class="menu-item"
            onclick="irPerfil()"
        >

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

            <span>Perfil</span>

        </button>

    </nav>

</div>


<!-- Leaflet -->

<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
></script>


<script>

    // Crear mapa centrado en Pasto

    const mapa = L.map("mapa").setView(
        [1.2136, -77.2811],
        13
    );


    // Cargar mapa

    L.tileLayer(
        "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
        {
            maxZoom: 19,
            attribution: "&copy; OpenStreetMap"
        }
    ).addTo(mapa);


    // Marcador de Pasto

    L.marker(
        [1.2136, -77.2811]
    )
    .addTo(mapa)
    .bindPopup(
        "<b>Pasto, Nariño</b>"
    );


    // Inicio

    function irInicio() {
        window.location.href = "principal.php";
    }


    // Favoritos

    function irFavoritos() {
        window.location.href = "favoritos.php";
    }


    // Notificaciones

    function irNotificaciones() {
        window.location.href = "notificaciones.php";
    }


    // Perfil

    function irPerfil() {
        window.location.href = "perfil.php";
    }

</script>


</body>

</html>