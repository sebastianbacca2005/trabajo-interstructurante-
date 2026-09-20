<?php

session_start();

require_once "../base de datos/database.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: signin.html");
    exit;
}

if (
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] !== "admin"
) {
    header("Location: principal.php");
    exit;
}

$sql = "SELECT
            e.id_evento,
            e.titulo,
            e.descripcion,
            e.fecha,
            e.hora,
            e.lugar,
            e.imagen,
            e.latitud,
            e.longitud,
            e.estado,
            u.nombre,
            u.correo
        FROM evento e
        INNER JOIN usuario u
        ON e.id_usuario = u.id_usuario
        ORDER BY
            CASE
                WHEN e.estado = 'pendiente' THEN 1
                WHEN e.estado = 'aceptado' THEN 2
                ELSE 3
            END,
            e.fecha_creacion DESC";

$resultado = pg_query(
    $conn_supa,
    $sql
);

if (!$resultado) {
    die("Error al cargar los eventos.");
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

<title>Administrar eventos - CulturaActiva</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: #51006f;
    font-family: Arial, sans-serif;
}

.contenedor {
    width: 92%;
    max-width: 900px;
    margin: 25px auto;
}

.volver {
    color: white;
    text-decoration: none;
    font-size: 15px;
}

h1 {
    color: white;
    font-size: 24px;
    margin-top: 25px;
}

.subtitulo {
    color: white;
    font-size: 17px;
    margin-top: 30px;
}

.evento {
    background: white;
    padding: 15px;
    margin: 15px 0;
    border-radius: 10px;
    overflow: hidden;
}

.imagen {
    width: 150px;
    height: 110px;
    object-fit: cover;
    float: left;
    margin-right: 15px;
    border-radius: 5px;
}

.info {
    min-height: 120px;
}

.titulo {
    margin-top: 0;
    color: #4b1f78;
}

.dato {
    font-size: 13px;
    margin: 6px 0;
}

.estado {
    display: inline-block;
    padding: 5px 9px;
    border-radius: 5px;
    font-size: 12px;
    margin-top: 5px;
}

.pendiente {
    background: #ffe08a;
}

.aceptado {
    background: #9be7ad;
}

.rechazado {
    background: #ffaaaa;
}

.botones {
    margin-top: 15px;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

button {
    border: none;
    padding: 9px 14px;
    border-radius: 5px;
    color: white;
    cursor: pointer;
}

.aceptar {
    background: #299447;
}

.rechazar {
    background: #d77a18;
}

.eliminar {
    background: #d62828;
}

.mapa {
    color: #4b1f78;
    text-decoration: none;
    font-size: 13px;
}

.vacio {
    background: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
}

</style>

</head>

<body>

<div class="contenedor">

<a
    href="principal.php"
    class="volver"
>
    ← Volver al inicio
</a>

<h1>
    Administrar eventos
</h1>

<?php

$hay_eventos = false;
$pendientes = false;

while ($evento = pg_fetch_assoc($resultado)):

    $hay_eventos = true;

    if ($evento["estado"] === "pendiente") {
        $pendientes = true;
    }

?>

<div class="evento">

<?php if (!empty($evento["imagen"])): ?>

<img
    src="<?php
        echo htmlspecialchars(
            $evento["imagen"]
        );
    ?>"
    class="imagen"
    alt="Evento"
>

<?php endif; ?>

<div class="info">

<h2 class="titulo">

<?php
echo htmlspecialchars(
    $evento["titulo"]
);
?>

</h2>

<div class="dato">

<strong>Descripción:</strong>

<?php
echo htmlspecialchars(
    $evento["descripcion"] ?? ""
);
?>

</div>

<div class="dato">

<strong>Fecha:</strong>

<?php
echo htmlspecialchars(
    $evento["fecha"]
);
?>

</div>

<div class="dato">

<strong>Hora:</strong>

<?php
echo htmlspecialchars(
    substr($evento["hora"], 0, 5)
);
?>

</div>

<div class="dato">

<strong>Publicado por:</strong>

<?php
echo htmlspecialchars(
    $evento["nombre"]
);
?>

</div>

<div class="dato">

<strong>Correo:</strong>

<?php
echo htmlspecialchars(
    $evento["correo"]
);
?>

</div>

<span class="estado <?php
    echo htmlspecialchars(
        $evento["estado"]
    );
?>">

<?php

if ($evento["estado"] === "pendiente") {
    echo "PENDIENTE";
}

if ($evento["estado"] === "aceptado") {
    echo "ACEPTADO";
}

if ($evento["estado"] === "rechazado") {
    echo "RECHAZADO";
}

?>

</span>

<br><br>

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
    📍 Ver ubicación
</a>

<?php endif; ?>

<div class="botones">

<?php if ($evento["estado"] === "pendiente"): ?>

<form
    action="cambiar_estado_evento.php"
    method="POST"
>

<input
    type="hidden"
    name="id_evento"
    value="<?php
        echo $evento["id_evento"];
    ?>"
>

<input
    type="hidden"
    name="estado"
    value="aceptado"
>

<button
    type="submit"
    class="aceptar"
>

✓ Aceptar

</button>

</form>

<form
    action="cambiar_estado_evento.php"
    method="POST"
>

<input
    type="hidden"
    name="id_evento"
    value="<?php
        echo $evento["id_evento"];
    ?>"
>

<input
    type="hidden"
    name="estado"
    value="rechazado"
>

<button
    type="submit"
    class="rechazar"
>

✕ Rechazar

</button>

</form>

<?php endif; ?>

<form
    action="eliminar_evento.php"
    method="POST"
    onsubmit="return confirm('¿Seguro que quieres eliminar este evento?');"
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

🗑 Eliminar

</button>

</form>

</div>

</div>

</div>

<?php endwhile; ?>

<?php if (!$hay_eventos): ?>

<div class="vacio">

No hay eventos para gestionar.

</div>

<?php endif; ?>

</div>

</body>

</html>