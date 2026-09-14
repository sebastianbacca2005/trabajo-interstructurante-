<?php

session_start();

/* Verificar que el usuario haya iniciado sesión */
if (!isset($_SESSION["id_usuario"])) {
    header("Location: signin.html");
    exit;
}

$nombre = $_SESSION["nombre"];

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CulturaActiva Pasto</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #ffffff;
            font-family: Arial, Helvetica, sans-serif;
            color: #222222;
        }

        .contenedor {
            width: 100%;
            max-width: 430px;
            min-height: 100vh;
            margin: 0 auto;
            padding-bottom: 90px;
        }

        /* ENCABEZADO */

        .encabezado {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 25px 10px;
        }

        .logo {
            width: 150px;
            height: 75px;
            object-fit: contain;
        }

        .perfil {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #563b94;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
        }

        /* BIENVENIDA */

        .bienvenida {
            text-align: left;
            padding: 5px 25px 15px;
        }

        .bienvenida h1 {
            margin: 0;
            font-size: 22px;
            color: #28105f;
        }

        .bienvenida p {
            margin-top: 7px;
            font-size: 13px;
            color: #555555;
        }

        /* BUSCADOR */

        .buscador {
            margin: 5px 25px 25px;
            height: 40px;
            display: flex;
            align-items: center;
            background: #eeeeee;
            border: 1px solid #cccccc;
        }

        .buscador span {
            margin-left: 12px;
            font-size: 18px;
        }

        .buscador input {
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            padding: 8px;
            font-size: 13px;
        }

        /* TITULOS */

        .titulo-seccion {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 25px;
            margin-bottom: 12px;
        }

        .titulo-seccion h2 {
            margin: 0;
            font-size: 17px;
            color: #28105f;
        }

        .titulo-seccion a {
            color: #563b94;
            text-decoration: none;
            font-size: 12px;
        }

        /* EVENTOS */

        .eventos {
            display: flex;
            gap: 15px;
            overflow-x: auto;
            padding: 0 25px 10px;
        }

        .evento {
            min-width: 250px;
            border: 1px solid #dddddd;
            background: white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.10);
        }

        .evento-imagen {
            height: 125px;
            background: #dddddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }

        .evento-contenido {
            padding: 12px;
            text-align: left;
        }

        .evento-contenido h3 {
            margin: 0 0 7px;
            font-size: 15px;
        }

        .evento-contenido p {
            margin: 4px 0;
            font-size: 11px;
            color: #666666;
        }

        .boton-evento {
            display: inline-block;
            margin-top: 8px;
            padding: 7px 12px;
            background: #563b94;
            color: white;
            text-decoration: none;
            font-size: 11px;
        }

        /* PROXIMOS */

        .proximos {
            padding: 0 25px;
        }

        .evento-lista {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #dddddd;
            padding: 13px 0;
        }

        .fecha {
            width: 50px;
            height: 50px;
            background: #563b94;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }

        .fecha .dia {
            font-size: 18px;
            font-weight: bold;
        }

        .fecha .mes {
            font-size: 9px;
        }

        .evento-info {
            text-align: left;
            flex: 1;
        }

        .evento-info h3 {
            margin: 0 0 5px;
            font-size: 14px;
        }

        .evento-info p {
            margin: 0;
            font-size: 11px;
            color: #666666;
        }

        .corazon {
            font-size: 20px;
            color: #563b94;
        }

        /* BARRA INFERIOR */

        .barra {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 430px;
            height: 70px;
            background: white;
            border-top: 1px solid #dddddd;
            display: flex;
            justify-content: space-around;
            align-items: center;
            box-shadow: 0 -2px 8px rgba(0,0,0,0.08);
        }

        .nav-item {
            text-decoration: none;
            color: #777777;
            text-align: center;
            font-size: 10px;
        }

        .nav-item span {
            display: block;
            font-size: 21px;
            margin-bottom: 3px;
        }

        .nav-item.activo {
            color: #563b94;
        }

        /* CELULAR */

        @media (max-width: 430px) {

            .contenedor {
                max-width: 100%;
            }

        }

    </style>

</head>

<body>

<div class="contenedor">

    <!-- ENCABEZADO -->

    <div class="encabezado">

        <img
            src="../imagen/usuario.png"
            alt="CulturaActiva Pasto"
            class="logo"
        >

        <div class="perfil">
            👤
        </div>

    </div>


    <!-- BIENVENIDA -->

    <div class="bienvenida">

        <h1>
            ¡Hola, <?php echo htmlspecialchars($nombre); ?>!
        </h1>

        <p>
            Descubre lo que está pasando en Pasto.
        </p>

    </div>


    <!-- BUSCADOR -->

    <div class="buscador">

        <span>🔍</span>

        <input
            type="text"
            placeholder="Buscar eventos..."
        >

    </div>


    <!-- EVENTOS DESTACADOS -->

    <div class="titulo-seccion">

        <h2>Eventos destacados</h2>

        <a href="#">Ver todos</a>

    </div>


    <div class="eventos">

        <div class="evento">

            <div class="evento-imagen">
                🎭
            </div>

            <div class="evento-contenido">

                <h3>
                    Festival Cultural de Pasto
                </h3>

                <p>
                    📅 Próximamente
                </p>

                <p>
                    📍 Pasto, Nariño
                </p>

                <a href="#" class="boton-evento">
                    Ver evento
                </a>

            </div>

        </div>


        <div class="evento">

            <div class="evento-imagen">
                🎶
            </div>

            <div class="evento-contenido">

                <h3>
                    Música y Cultura
                </h3>

                <p>
                    📅 Próximamente
                </p>

                <p>
                    📍 Pasto, Nariño
                </p>

                <a href="#" class="boton-evento">
                    Ver evento
                </a>

            </div>

        </div>

    </div>


    <!-- PROXIMOS EVENTOS -->

    <div class="titulo-seccion" style="margin-top: 25px;">

        <h2>Próximos eventos</h2>

        <a href="#">
            Ver todos
        </a>

    </div>


    <div class="proximos">

        <div class="evento-lista">

            <div class="fecha">

                <div class="dia">
                    15
                </div>

                <div class="mes">
                    SEP
                </div>

            </div>

            <div class="evento-info">

                <h3>
                    Evento cultural
                </h3>

                <p>
                    📍 Centro de Pasto
                </p>

            </div>

            <div class="corazon">
                ♡
            </div>

        </div>


        <div class="evento-lista">

            <div class="fecha">

                <div class="dia">
                    20
                </div>

                <div class="mes">
                    SEP
                </div>

            </div>

            <div class="evento-info">

                <h3>
                    Exposición artística
                </h3>

                <p>
                    📍 Pasto, Nariño
                </p>

            </div>

            <div class="corazon">
                ♡
            </div>

        </div>


        <div class="evento-lista">

            <div class="fecha">

                <div class="dia">
                    25
                </div>

                <div class="mes">
                    SEP
                </div>

            </div>

            <div class="evento-info">

                <h3>
                    Presentación musical
                </h3>

                <p>
                    📍 Pasto, Nariño
                </p>

            </div>

            <div class="corazon">
                ♡
            </div>

        </div>

    </div>

</div>


<!-- BARRA DE NAVEGACIÓN -->

<div class="barra">

    <a href="principal.php" class="nav-item activo">
        <span>🏠</span>
        Inicio
    </a>

    <a href="#" class="nav-item">
        <span>❤️</span>
        Favoritos
    </a>

    <a href="#" class="nav-item">
        <span>🗺️</span>
        Mapa
    </a>

    <a href="#" class="nav-item">
        <span>🔔</span>
        Avisos
    </a>

    <a href="#" class="nav-item">
        <span>👤</span>
        Perfil
    </a>

</div>

</body>
</html>