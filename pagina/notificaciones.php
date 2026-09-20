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

    <title>Notificaciones - CulturaActiva</title>

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
            padding: 31px 10px 25px 10px;
            color: white;
        }

        .titulo-seccion {
            font-size: 14px;
            font-weight: normal;
            margin: 0 0 20px 9px;
        }


        /* Lista de notificaciones */

        .lista-notificaciones {
            width: 100%;
        }


        /* Notificacion */

        .notificacion {
            width: 100%;
            min-height: 72px;
            background: #6d2788;
            margin-bottom: 9px;
            display: flex;
            align-items: center;
            padding: 10px;
        }


        /* Icono */

        .icono-notificacion {
            width: 42px;
            min-width: 42px;
            height: 42px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
        }

        .icono-notificacion svg {
            width: 22px;
            height: 22px;
            stroke: #50006f;
            fill: none;
            stroke-width: 1.7;
        }


        /* Texto */

        .texto-notificacion {
            flex: 1;
        }

        .texto-notificacion strong {
            display: block;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .texto-notificacion span {
            display: block;
            font-size: 10px;
            color: #eeeeee;
            line-height: 14px;
        }


        /* Sin notificaciones */

        .sin-notificaciones {
            width: 100%;
            min-height: 150px;
            border: 1px dashed rgba(255, 255, 255, 0.6);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
        }

        .sin-notificaciones svg {
            width: 42px;
            height: 42px;
            margin-bottom: 12px;
            stroke: white;
            fill: none;
            stroke-width: 1.5;
        }

        .sin-notificaciones p {
            font-size: 13px;
            margin-bottom: 5px;
        }

        .sin-notificaciones small {
            font-size: 10px;
            color: #dddddd;
        }


        /* Menu inferior */

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
                id="busqueda"
                placeholder="Buscar eventos, artistas, lugares..."
            >

        </div>

    </header>


    <!-- Contenido -->

    <main class="contenido">


        <!-- Titulo -->

        <h2 class="titulo-seccion">
            NOTIFICACIONES
        </h2>


        <!-- Sin notificaciones -->

        <div class="sin-notificaciones">

            <svg viewBox="0 0 24 24">

                <path
                    d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"
                ></path>

                <path
                    d="M10 21h4"
                ></path>

            </svg>

            <p>
                No tienes notificaciones.
            </p>

            <small>
                Aquí aparecerán las novedades de tus eventos.
            </small>

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
                    d="M20.8 8.8c0 5.5-8.8 11-8.8 11S3.2 14.3 3.2 8.8A4.8 4.8 0 0 1 8 4c1.7 0 3.2 0.9 4 2.2C12.8 4.9 14.3 4 16 4a4.8 4.8 0 0 1 4.8 4.8z"
                ></path>

            </svg>

            <span>Favoritos</span>

        </button>


        <!-- Mapa -->

        <button
            class="menu-item"
            onclick="irMapa()"
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
            class="menu-item activo"
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


<script>

    // Inicio

    function irInicio() {
        window.location.href = "principal.php";
    }


    // Favoritos

    function irFavoritos() {
        window.location.href = "favoritos.php";
    }


    // Mapa

    function irMapa() {
        window.location.href = "mapa.php";
    }


    // Perfil

    function irPerfil() {
        window.location.href = "perfil.php";
    }

</script>


</body>

</html>