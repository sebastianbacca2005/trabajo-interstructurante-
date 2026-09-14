<?php

session_start();

require_once "../base de datos/database.php";

/* Verificar que el formulario sea enviado por POST */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: signin.html");
    exit;
}

/* Recibir datos del formulario */
$correo = trim($_POST["correo"] ?? "");
$contrasena = trim($_POST["contrasena"] ?? "");

/* Verificar campos */
if ($correo === "" || $contrasena === "") {
    header("Location: signin.html?error=campos_vacios");
    exit;
}

/* Buscar el usuario en la nueva tabla */
$sql = "SELECT id_usuario, nombre, correo, contrasena
        FROM usuario
        WHERE correo = $1
        LIMIT 1";

$resultado = pg_query_params(
    $conn_supa,
    $sql,
    [$correo]
);

/* Verificar consulta */
if (!$resultado) {
    header("Location: signin.html?error=conexion");
    exit;
}

/* Obtener usuario */
$usuario = pg_fetch_assoc($resultado);

/* Verificar si el usuario existe */
if (!$usuario) {
    header("Location: signin.html?error=usuario_no_encontrado");
    exit;
}

/* Verificar contraseña */
if ($contrasena !== $usuario["contrasena"]) {
    header("Location: signin.html?error=contrasena_incorrecta");
    exit;
}

/* Guardar información del usuario */
$_SESSION["id_usuario"] = $usuario["id_usuario"];
$_SESSION["nombre"] = $usuario["nombre"];
$_SESSION["correo"] = $usuario["correo"];

/* Inicio de sesión correcto */
header("Location: principal.php");
exit;

?>