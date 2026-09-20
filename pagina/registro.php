<?php

require_once "../base de datos/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: registro.html");
    exit;
}

$nombre = trim($_POST["nombre"] ?? "");
$correo = trim($_POST["correo"] ?? "");
$contrasena = trim($_POST["contrasena"] ?? "");
$confirmar = trim($_POST["confirmar"] ?? "");

/* Verificar campos vacios */

if ($nombre === "" || $correo === "" || $contrasena === "" || $confirmar === "") {
    header("Location: registro.html?error=campos_vacios");
    exit;
}


/* Verificar que las dos contraseñas sean iguales */

if ($contrasena !== $confirmar) {
    header("Location: registro.html?error=contrasenas_no_coinciden");
    exit;
}


/* Verificar que el correo no exista */

$sql = "SELECT id_usuario
        FROM usuario
        WHERE correo = $1";

$resultado = pg_query_params(
    $conn_supa,
    $sql,
    [$correo]
);

if (!$resultado) {
    die("Error al consultar la base de datos.");
}

if (pg_num_rows($resultado) > 0) {
    header("Location: registro.html?error=correo_existente");
    exit;
}


/* Crear el usuario */

$sql_insertar = "INSERT INTO usuario
                 (nombre, correo, contrasena)
                 VALUES ($1, $2, $3)";

$insertado = pg_query_params(
    $conn_supa,
    $sql_insertar,
    [$nombre, $correo, $contrasena]
);

if (!$insertado) {
    die("Error al registrar el usuario.");
}


/* Registro correcto */

header("Location: signin.html?registro=exitoso");
exit;

?>