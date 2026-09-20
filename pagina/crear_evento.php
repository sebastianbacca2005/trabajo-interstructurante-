<?php

session_start();

require_once "../base de datos/database.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: signin.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: crear_evento.html");
    exit;
}

$id_usuario = $_SESSION["id_usuario"];

$titulo = trim($_POST["titulo"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");
$fecha = trim($_POST["fecha"] ?? "");
$hora = trim($_POST["hora"] ?? "");
$latitud = trim($_POST["latitud"] ?? "");
$longitud = trim($_POST["longitud"] ?? "");

if (
    $titulo === "" ||
    $fecha === "" ||
    $hora === "" ||
    $latitud === "" ||
    $longitud === ""
) {
    header("Location: crear_evento.html?error=campos_vacios");
    exit;
}

if (
    !is_numeric($latitud) ||
    !is_numeric($longitud)
) {
    header("Location: crear_evento.html?error=ubicacion");
    exit;
}

$imagen = null;

if (
    isset($_FILES["imagen"]) &&
    $_FILES["imagen"]["error"] === UPLOAD_ERR_OK
) {

    $carpeta = "uploads/eventos/";

    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0777, true);
    }

    $extension = strtolower(
        pathinfo(
            $_FILES["imagen"]["name"],
            PATHINFO_EXTENSION
        )
    );

    $permitidas = [
        "jpg",
        "jpeg",
        "png",
        "webp"
    ];

    if (!in_array($extension, $permitidas)) {
        header("Location: crear_evento.html?error=imagen");
        exit;
    }

    $nombre_archivo =
        uniqid("evento_", true) .
        "." .
        $extension;

    $imagen =
        $carpeta .
        $nombre_archivo;

    if (
        !move_uploaded_file(
            $_FILES["imagen"]["tmp_name"],
            $imagen
        )
    ) {
        header("Location: crear_evento.html?error=imagen");
        exit;
    }
}

$lugar = "Ubicación seleccionada en el mapa";

$sql = "INSERT INTO evento
        (
            id_usuario,
            titulo,
            descripcion,
            fecha,
            hora,
            lugar,
            imagen,
            latitud,
            longitud,
            estado
        )
        VALUES
        (
            $1,
            $2,
            $3,
            $4,
            $5,
            $6,
            $7,
            $8,
            $9,
            'pendiente'
        )";

$resultado = pg_query_params(
    $conn_supa,
    $sql,
    [
        $id_usuario,
        $titulo,
        $descripcion,
        $fecha,
        $hora,
        $lugar,
        $imagen,
        $latitud,
        $longitud
    ]
);

if (!$resultado) {
    die("Error al guardar el evento.");
}

header("Location: principal.php?evento=pendiente");
exit;

?>