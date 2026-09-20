<?php

session_start();

require_once "../base de datos/database.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: signin.html");
    exit;
}

$id_usuario = $_SESSION["id_usuario"];

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

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>CulturaActiva Pasto</title>

<style>

* {
    box-sizing: border-box;
}

html,
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background: #eeeeee;
}

/* APP */

.app {
    width: 100%;
    max-width: 485px;
    min-height: 100vh;
    margin: 0 auto;
    background: #51006f;
    padding-bottom: 75px;
}

/* CABECERA */

header {
    width: 100%;
    height: 145px;
    background: white;

    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 15px 25px;
}

.logo {
    width: 175px;
    height: auto;
}

.usuario {
    color: #333;
    text-decoration: none;
}

/* CONTENIDO */

.contenedor {
    width: 100%;
    padding: 0 22px;
}

/* BUSCADOR */

.buscador {
    display: block;

    width: 100%;
    height: 45px;

    margin-top: 22px;

    padding: 0 18px;

    border: none;
    border-radius: 25px;

    font-size: 14px;

    outline: none;
}

/* TITULO */

.titulo {
    display: block;

    color: white;

    font-size: 19px;

    margin-top: 28px;
    margin-bottom: 18px;
}

/* PUBLICAR */

.publicar {
    display: block;

    width: 100%;
    height: 35px;

    background: white;
    color: #4b1f78;

    text-align: center;

    text-decoration: none;

    padding-top: 9px;

    font-size: 13px;

    border-radius: 4px;

    margin-bottom: 25px;
}

/* EVENTOS */

.lista {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.evento {
    width: 100%;

    display: flex;

    min-height: 125px;
}

.imagen {
    width: 42%;
    height: 125px;

    object-fit: cover;
}

.sin-imagen {
    width: 42%;
    height: 125px;

    background: #ddd;

    display: flex;
    align-items: center;
    justify-content: center;

    color: #777;

    font-size: 12px;
}

.info {
    width: 58%;

    color: white;

    padding-left: 8px;
}

.nombre {
    background: #f04444;

    padding: 9px;

    font-size: 15px;
}

.evento:nth-child(2n) .nombre {
    background: #f28b20;
}

.evento:nth-child(3n) .nombre {
    background: #35bd58;
}

.dato {
    font-size: 12px;

    padding: 5px 9px;
}

.descripcion {
    font-size: 12px;

    padding: 5px 9px;
}

.mapa {
    display: inline-block;

    color: white;

    font-size: 12px;

    padding: 5px 9px;

    text-decoration: none;
}

.eliminar {
    margin: 5px 9px;

    padding: 6px 10px;

    border: none;

    border-radius: 5px;

    background: #d62828;

    color: white;

    font-size: 11px;

    cursor: pointer;
}

/* SIN EVENTOS */

.vacio {
    color: white;

    text-align: center;

    padding: 40px 10px;

    font-size: 15px;
}

/* ADMIN */

.admin {
    margin-top: 25px;
}

.admin a {
    display: block;

    background: white;

    color: #4b1f78;

    padding: 10px;

    text-align: center;

    text-decoration: none;

    border-radius: 6px;
}

/* BARRA INFERIOR */

.nav {
    position: fixed;

    left: 50%;
    bottom: 0;

    transform: translateX(-50%);

    width: 100%;
    max-width: 485px;

    height: 65px;

    background: white;

    display: flex;

    justify-content: space-around;

    align-items: center;

    z-index: 9999;

    border-top: 1px solid #ddd;
}

.nav a {
    width: 20%;

    height: 65px;

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: center;

    color: #333;

    text-decoration: none;

    font-size: 10px;

    cursor: pointer;
}

.nav a:hover {
    color: #4b1f78;
}

.icono {
    width: 22px;
    height: 22px;

    margin-bottom: 4px;

    display: flex;

    align-items: center;
    justify-content: center;
}

.icono svg {
    width: 21px;
    height: 21px;

    stroke: currentColor;

    fill: none;

    stroke-width: 1.7;

    stroke-linecap: round;
    stroke-linejoin: round;
}

.activo {
    color: #4b1f78 !important;

    font-weight: bold;
}

</style>

</head>

<body>


<div class="app">


<!-- CABECERA -->

<header>

<img
    src="../imagen/usuario.png"
    class="logo"
    alt="CulturaActiva Pasto"
>

<a
    href="perfil.php"
    class="usuario"
>

<svg
    width="25"
    height="25"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="1.6"
    stroke-linecap="round"
    stroke-linejoin="round"
>

<circle
    cx="12"
    cy="8"
    r="3"
></circle>

<path
    d="M5 21c0-3.5 3-6 7-6s7 2.5 7 6"
></path>

<circle
    cx="12"
    cy="12"
    r="10"
></circle>

</svg>

</a>

</header>


<!-- CONTENIDO -->

<main class="contenedor">


<!-- BUSCADOR -->

<input
    type="text"
    id="buscador"
    class="buscador"
    placeholder="Buscar eventos, artistas, lugares..."
>


<!-- TITULO -->

<h2 class="titulo">
    EVENTOS DESTACADOS
</h2>


<!-- PUBLICAR -->

<a
    href="publicar_evento.php"
    class="publicar"
>
    + Publicar evento
</a>


<!-- EVENTOS -->

<?php if (count($eventos) > 0): ?>

<div class="lista">

<?php foreach ($eventos as $evento): ?>

<article
    class="evento"

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
    src="<?php
        echo htmlspecialchars($evento["imagen"]);
    ?>"
    class="imagen"
    alt="Evento"
>

<?php else: ?>

<div class="sin-imagen">
    Sin imagen
</div>

<?php endif; ?>


<div class="info">


<div class="nombre">

<?php
echo htmlspecialchars($evento["titulo"]);
?>

</div>


<div class="dato">

<?php
echo htmlspecialchars($evento["fecha"]);
?>

</div>


<div class="dato">

<?php
echo htmlspecialchars(
    substr($evento["hora"], 0, 5)
);
?>

</div>


<div class="dato">

<?php
echo htmlspecialchars($evento["lugar"]);
?>

</div>


<?php if (
    $evento["latitud"] !== null &&
    $evento["longitud"] !== null
): ?>

<a
    href="mapa.php?evento=<?php
        echo $evento["id_evento"];
    ?>"
    class="mapa"
>
    Ver ubicación
</a>

<?php endif; ?>


<?php if (
    $evento["id_usuario"] == $id_usuario
): ?>

<form
    action="eliminar_evento.php"
    method="POST"
    onsubmit="return confirm('¿Quieres eliminar tu evento?');"
>

<input
    type="hidden"
    name="id_evento"
    value="<?php
        echo $evento["id_evento"];
    ?>"
>

<button
    type="submit"
    class="eliminar"
>
    Eliminar mi evento
</button>

</form>

<?php endif; ?>


</div>

</article>

<?php endforeach; ?>

</div>


<?php else: ?>

<div class="vacio">
    No hay eventos publicados todavía.
</div>

<?php endif; ?>


<?php if (
    isset($_SESSION["rol"]) &&
    $_SESSION["rol"] === "admin"
): ?>

<div class="admin">

<a href="administrador.php">
    Administrar eventos
</a>

</div>

<?php endif; ?>


</main>


<!-- BARRA -->

<nav class="nav">


<!-- INICIO -->

<a
    href="principal.php"
    class="activo"
>

<span class="icono">

<svg viewBox="0 0 24 24">

<path d="M3 10.5L12 3l9 7.5"></path>

<path d="M5 9.5V21h14V9.5"></path>

<path d="M9 21v-7h6v7"></path>

</svg>

</span>

Inicio

</a>


<!-- FAVORITOS -->

<a href="favoritos.php">

<span class="icono">

<svg viewBox="0 0 24 24">

<path d="M20.8 8.8c0 5.5-8.8 11-8.8 11S3.2 14.3 3.2 8.8C3.2 5.6 5.3 3.5 8.2 3.5c1.7 0 3.1.8 3.8 2.1.7-1.3 2.1-2.1 3.8-2.1 2.9 0 5 2.1 5 5.3z"></path>

</svg>

</span>

Favoritos

</a>


<!-- MAPA -->

<a href="mapa.php">

<span class="icono">

<svg viewBox="0 0 24 24">

<path d="M12 21s7-6.2 7-12a7 7 0 1 0-14 0c0 5.8 7 12 7 12z"></path>

<circle
    cx="12"
    cy="9"
    r="2.5"
></circle>

</svg>

</span>

Mapa

</a>


<!-- NOTIFICACIONES -->

<a href="notificaciones.php">

<span class="icono">

<svg viewBox="0 0 24 24">

<path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>

<path d="M10 21h4"></path>

</svg>

</span>

Notificaciones

</a>


<!-- PERFIL -->

<a href="perfil.php">

<span class="icono">

<svg viewBox="0 0 24 24">

<circle
    cx="12"
    cy="8"
    r="3.2"
></circle>

<path
    d="M5 21c0-3.8 3-6 7-6s7 2.2 7 6"
></path>

</svg>

</span>

Perfil

</a>


</nav>


</div>


<script>

/* BUSCADOR */

const buscador = document.getElementById("buscador");

const eventos = document.querySelectorAll(".evento");

if (buscador) {

    buscador.addEventListener("input", function () {

        const texto = this.value.toLowerCase().trim();

        eventos.forEach(function (evento) {

            const contenido =
                evento.dataset.busqueda || "";

            if (contenido.includes(texto)) {

                evento.style.display = "flex";

            } else {

                evento.style.display = "none";

            }

        });

    });

}

</script>


</body>

</html>