<?php

require_once "../base de datos/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: recuperar.html");
    exit;
}

$correo = trim($_POST["correo"] ?? "");

/* Verificar que el correo no esté vacío */
if ($correo === "") {
    header("Location: recuperar.html?error=correo_vacio");
    exit;
}

/* Buscar el correo en la tabla usuario */
$sql = "SELECT id_usuario, nombre, correo
        FROM usuario
        WHERE correo = $1";

$resultado = pg_query_params(
    $conn_supa,
    $sql,
    [$correo]
);

if (!$resultado) {
    header("Location: recuperar.html?error=conexion");
    exit;
}

/* Comprobar si existe */
$usuario = pg_fetch_assoc($resultado);

if (!$usuario) {
    header("Location: recuperar.html?error=correo_no_encontrado");
    exit;
}

/* Generar código de recuperación */
$codigo = str_pad(
    random_int(0, 999999),
    6,
    "0",
    STR_PAD_LEFT
);

/* El código dura 10 minutos */
$fecha_expiracion = date(
    "Y-m-d H:i:s",
    time() + 600
);

/* Guardar código en la base de datos */
$sql_codigo = "INSERT INTO recuperacion_contrasena
               (id_usuario, codigo, fecha_expiracion)
               VALUES ($1, $2, $3)";

$guardado = pg_query_params(
    $conn_supa,
    $sql_codigo,
    [
        $usuario["id_usuario"],
        $codigo,
        $fecha_expiracion
    ]
);

if (!$guardado) {
    header("Location: recuperar.html?error=registro_codigo");
    exit;
}

/* Si todo salió bien */
header("Location: recuperar.html?enviado=1");
exit;

?>