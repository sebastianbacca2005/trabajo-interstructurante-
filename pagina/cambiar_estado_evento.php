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

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: administrador.php");
    exit;
}

$id_evento = $_POST["id_evento"] ?? "";
$estado = $_POST["estado"] ?? "";

if (!ctype_digit((string)$id_evento)) {
    header("Location: administrador.php");
    exit;
}

if (
    $estado !== "aceptado" &&
    $estado !== "rechazado"
) {
    header("Location: administrador.php");
    exit;
}

$sql = "UPDATE evento
        SET estado = $1
        WHERE id_evento = $2";

$resultado = pg_query_params(
    $conn_supa,
    $sql,
    [
        $estado,
        (int)$id_evento
    ]
);

if (!$resultado) {
    die("Error al cambiar el estado del evento.");
}

header("Location: administrador.php");
exit;
?>