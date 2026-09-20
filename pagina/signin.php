<?php

session_start();

require_once "../base de datos/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: signin.html");
    exit;
}

$correo = trim($_POST["correo"] ?? "");
$contrasena = trim($_POST["contrasena"] ?? "");

if ($correo === "" || $contrasena === "") {
    header("Location: signin.html?error=campos_vacios");
    exit;
}

/* AHORA TAMBIÉN TRAEMOS EL ROL */
$sql = "SELECT
            id_usuario,
            nombre,
            correo,
            contrasena,
            rol
        FROM usuario
        WHERE correo = $1
        LIMIT 1";

$resultado = pg_query_params(
    $conn_supa,
    $sql,
    [$correo]
);

if (!$resultado) {
    header("Location: signin.html?error=conexion");
    exit;
}

$usuario = pg_fetch_assoc($resultado);

if (!$usuario) {
    header("Location: signin.html?error=usuario_no_encontrado");
    exit;
}

if ($contrasena !== $usuario["contrasena"]) {
    header("Location: signin.html?error=contrasena_incorrecta");
    exit;
}

/* GUARDAR TODA LA INFORMACIÓN EN LA SESIÓN */

$_SESSION["id_usuario"] = $usuario["id_usuario"];
$_SESSION["nombre"] = $usuario["nombre"];
$_SESSION["correo"] = $usuario["correo"];
$_SESSION["rol"] = $usuario["rol"];

/* Entrar al sistema */

header("Location: principal.php");
exit;

?>