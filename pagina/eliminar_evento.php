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

if (!ctype_digit((string)$id_evento)) {
    header("Location: administrador.php");
    exit;
}

$sql = "DELETE FROM evento
        WHERE id_evento = $1";

$resultado = pg_query_params(
    $conn_supa,
    $sql,
    [(int)$id_evento]
);

if (!$resultado) {
    die("Error al eliminar el evento.");
}

header("Location: administrador.php");
exit;
?>