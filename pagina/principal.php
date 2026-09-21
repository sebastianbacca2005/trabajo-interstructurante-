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

<style>
*{box-sizing:border-box}
html,body{margin:0;padding:0;font-family:Arial,sans-serif;background:#eeeeee;color:#222}
body{min-height:100vh}
.app{width:100%;min-height:100vh;background:#51006f;padding-bottom:40px}
header{width:100%;height:112px;background:#fff;display:flex;align-items:center;border-bottom:1px solid #ddd;padding:0 5vw;gap:28px}
.logo{width:145px;height:auto;flex-shrink:0}
.titulo-app{color:#51006f;line-height:1.05;min-width:150px}
.nombre-app{font-size:21px;font-weight:700}
.ciudad-app{font-size:15px;font-weight:700;margin-top:2px}
.eslogan-app{font-size:8px;color:#555;margin-top:4px}
.perfil-superior{color:#333;text-decoration:none;display:flex;align-items:center;justify-content:center;margin-left:8px}
.perfil-superior svg{width:28px;height:28px}
header .buscador-wrap{width:min(700px,100%);margin:0 auto;position:relative}
.contenedor{width:min(1180px,92%);margin:0 auto;padding:35px 0 70px}
.hero{display:flex;justify-content:space-between;align-items:end;gap:30px;margin-bottom:28px}
.hero h1{margin:0;color:#fff;font-size:32px}
.hero p{margin:7px 0 0;color:#eadcf0;font-size:15px}
.publicar{display:inline-flex;align-items:center;justify-content:center;background:#fff;color:#4b1f78;text-decoration:none;padding:11px 20px;border-radius:7px;font-size:14px;font-weight:700;white-space:nowrap}
.buscador-wrap{position:relative}
.buscador{display:block;width:100%;height:48px;padding:0 20px 0 48px;border:1px solid #ddd;border-radius:25px;font-size:14px;outline:none;background:#fff}
.icono-busqueda{position:absolute;left:17px;top:13px;width:22px;height:22px}
.icono-busqueda svg{width:100%;height:100%;stroke:#555;fill:none;stroke-width:1.7;stroke-linecap:round}
.titulo{color:#fff;font-size:21px;margin:0 0 18px}
.lista{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:22px}
.evento{background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 5px 18px rgba(0,0,0,.16);cursor:pointer;transition:transform .18s,box-shadow .18s;display:flex;flex-direction:column;min-width:0}
.evento:hover{transform:translateY(-3px);box-shadow:0 9px 25px rgba(0,0,0,.22)}
.imagen,.sin-imagen{width:100%;height:205px;object-fit:cover}
.sin-imagen{background:#ddd;display:flex;align-items:center;justify-content:center;color:#777;font-size:13px}
.info{padding:0 17px 17px;color:#333}
.nombre{color:#fff;padding:12px 14px;margin:0 -17px 10px;background:#f04444;font-size:17px;font-weight:700}
.evento:nth-child(2n) .nombre{background:#f28b20}
.evento:nth-child(3n) .nombre{background:#35bd58}
.dato{font-size:13px;padding:5px 0;color:#444}
.descripcion{font-size:13px;padding:5px 0;color:#555;line-height:1.45;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
.evento-acciones{display:flex;gap:8px;align-items:center;margin-top:10px}
.mapa{display:inline-flex;color:#4b1f78;font-size:12px;padding:7px 10px;text-decoration:none;border:1px solid #4b1f78;border-radius:6px}
.eliminar{margin:0;padding:7px 10px;border:0;border-radius:6px;background:#d62828;color:#fff;font-size:11px;cursor:pointer}
.vacio{color:white;text-align:center;padding:50px 10px;font-size:15px}
.admin{margin-top:30px}
.admin a{display:inline-block;background:#fff;color:#4b1f78;padding:11px 18px;text-align:center;text-decoration:none;border-radius:7px;font-weight:700}
.nav{width:100%;height:72px;background:#fff;display:flex;justify-content:center;align-items:center;gap:55px;border-top:1px solid #ddd;padding:0 20px}
.nav a{min-width:75px;height:72px;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#333;text-decoration:none;font-size:11px;cursor:pointer}
.nav a:hover,.activo{color:#4b1f78!important}
.icono{width:24px;height:24px;margin-bottom:5px;display:flex;align-items:center;justify-content:center}
.icono svg{width:22px;height:22px;stroke:currentColor;fill:none;stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round}
.modal{position:fixed;inset:0;background:rgba(0,0,0,.62);display:none;align-items:center;justify-content:center;padding:25px;z-index:10000}
.modal.abierto{display:flex}
.detalle{width:min(820px,100%);max-height:92vh;overflow:auto;background:#fff;border-radius:14px;box-shadow:0 15px 50px rgba(0,0,0,.35);position:relative}
.detalle-imagen{width:100%;height:330px;object-fit:cover}
.detalle-contenido{padding:25px 30px 30px}
.detalle-titulo{margin:0 0 18px;color:#4b1f78;font-size:28px}
.detalle-dato{font-size:14px;margin:9px 0;color:#444}
.detalle-descripcion{font-size:15px;line-height:1.6;color:#444;margin:18px 0}
.detalle-acciones{display:flex;gap:10px;flex-wrap:wrap;margin-top:22px}
.btn{border:0;border-radius:7px;padding:11px 16px;cursor:pointer;text-decoration:none;font-size:13px;font-weight:700}
.btn-favorito{background:#4b1f78;color:#fff}
.btn-mapa{background:#eee;color:#4b1f78}
.cerrar-modal{position:absolute;right:15px;top:15px;width:36px;height:36px;border:0;border-radius:50%;background:rgba(0,0,0,.55);color:#fff;font-size:22px;cursor:pointer;z-index:2}
.sin-resultados{display:none;background:#fff;border-radius:10px;padding:30px;text-align:center;color:#555}
@media(max-width:900px){
 .lista{grid-template-columns:repeat(2,minmax(0,1fr))}
 header{padding:0 3%;gap:20px}
 .header-centro{gap:16px}
 .header-centro a{font-size:12px}
 .nav{gap:25px}
}
@media(max-width:650px){
 header{height:auto;min-height:90px;flex-wrap:wrap;padding:14px 20px}
 .logo{width:145px}
 .header-centro{order:3;flex-basis:100%;justify-content:center;padding-bottom:5px}
 .hero{align-items:flex-start;flex-direction:column}
 .hero h1{font-size:26px}
 .lista{grid-template-columns:1fr}
 .nav{gap:4px}
 .nav a{min-width:55px}
 .detalle-imagen{height:230px}
 .detalle-contenido{padding:20px}
 .detalle-titulo{font-size:23px}
}
</style>

<body>
<div class="app">

<header>
    <img src="../imagen/usuario.png" class="logo" alt="CulturaActiva Pasto">

    <div class="titulo-app">
        <div class="nombre-app">CulturaActiva</div>
        <div class="ciudad-app">PASTO</div>
        <div class="eslogan-app">Conecta con la cultura, vive tu ciudad.</div>
    </div>

    <div class="buscador-wrap">
        <span class="icono-busqueda">
            <svg viewBox="0 0 24 24">
                <circle cx="10.8" cy="10.8" r="6.5"></circle>
                <path d="M16 16l5 5"></path>
            </svg>
        </span>
        <input type="text" id="buscador" class="buscador" placeholder="Buscar eventos, artistas, lugares...">
    </div>

    <a href="perfil.php" class="perfil-superior" aria-label="Perfil">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="8" r="3"></circle>
            <path d="M5 21c0-3.5 3-6 7-6s7 2.5 7 6"></path>
            <circle cx="12" cy="12" r="10"></circle>
        </svg>
    </a>
</header>

<main class="contenedor">

    <section class="hero">
        <div>
            <h1>CulturaActiva Pasto</h1>
            <p>Conecta con la cultura, vive tu ciudad.</p>
        </div>

        <a href="publicar_evento.php" class="publicar">+ Publicar evento</a>
    </section>

    <h2 class="titulo">EVENTOS DESTACADOS</h2>

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
            data-id="<?php echo (int)$evento["id_evento"]; ?>"
            data-titulo="<?php echo htmlspecialchars($evento["titulo"], ENT_QUOTES); ?>"
            data-descripcion="<?php echo htmlspecialchars($evento["descripcion"] ?? "", ENT_QUOTES); ?>"
            data-fecha="<?php echo htmlspecialchars($evento["fecha"], ENT_QUOTES); ?>"
            data-hora="<?php echo htmlspecialchars(substr($evento["hora"], 0, 5), ENT_QUOTES); ?>"
            data-lugar="<?php echo htmlspecialchars($evento["lugar"], ENT_QUOTES); ?>"
            data-imagen="<?php echo htmlspecialchars($evento["imagen"] ?? "", ENT_QUOTES); ?>"
            data-latitud="<?php echo htmlspecialchars($evento["latitud"] ?? "", ENT_QUOTES); ?>"
            data-longitud="<?php echo htmlspecialchars($evento["longitud"] ?? "", ENT_QUOTES); ?>"
        >

            <?php if (!empty($evento["imagen"])): ?>
                <img src="<?php echo htmlspecialchars($evento["imagen"]); ?>" class="imagen" alt="Evento">
            <?php else: ?>
                <div class="sin-imagen">Sin imagen</div>
            <?php endif; ?>

            <div class="info">
                <div class="nombre"><?php echo htmlspecialchars($evento["titulo"]); ?></div>

                <div class="dato"><strong>Fecha:</strong> <?php echo htmlspecialchars($evento["fecha"]); ?></div>
                <div class="dato"><strong>Hora:</strong> <?php echo htmlspecialchars(substr($evento["hora"], 0, 5)); ?></div>
                <div class="dato"><strong>Lugar:</strong> <?php echo htmlspecialchars($evento["lugar"]); ?></div>

                <div class="descripcion">
                    <?php echo htmlspecialchars($evento["descripcion"] ?? "Sin descripción."); ?>
                </div>

                <div class="evento-acciones" onclick="event.stopPropagation();">

                    <?php if ($evento["latitud"] !== null && $evento["longitud"] !== null): ?>
                        <a
                            href="mapa.php?evento=<?php echo (int)$evento["id_evento"]; ?>"
                            class="mapa"
                        >
                            Ver ubicación
                        </a>
                    <?php endif; ?>

                    <?php if ($evento["id_usuario"] == $id_usuario): ?>
                        <form
                            action="eliminar_evento.php"
                            method="POST"
                            onsubmit="return confirm('¿Quieres eliminar tu evento?');"
                        >
                            <input type="hidden" name="id_evento" value="<?php echo (int)$evento["id_evento"]; ?>">
                            <button type="submit" class="eliminar">Eliminar mi evento</button>
                        </form>
                    <?php endif; ?>

                </div>
            </div>

        </article>

    <?php endforeach; ?>

    </div>

    <div id="sinResultados" class="sin-resultados">
        No encontramos eventos con esa búsqueda.
    </div>

    <?php else: ?>

        <div class="vacio">No hay eventos publicados todavía.</div>

    <?php endif; ?>

    <?php if (isset($_SESSION["rol"]) && $_SESSION["rol"] === "admin"): ?>
        <div class="admin">
            <a href="administrador.php">Administrar eventos</a>
        </div>
    <?php endif; ?>

</main>

<!-- DETALLE DEL EVENTO -->
<div class="modal" id="modalEvento" aria-hidden="true">

    <div class="detalle" role="dialog" aria-modal="true">

        <button type="button" class="cerrar-modal" id="cerrarModal" aria-label="Cerrar">×</button>

        <img id="detalleImagen" class="detalle-imagen" src="" alt="Imagen del evento">

        <div class="detalle-contenido">

            <h2 id="detalleTitulo" class="detalle-titulo"></h2>

            <div id="detalleFecha" class="detalle-dato"></div>
            <div id="detalleHora" class="detalle-dato"></div>
            <div id="detalleLugar" class="detalle-dato"></div>

            <div id="detalleDescripcion" class="detalle-descripcion"></div>

            <div class="detalle-acciones">

                <button type="button" id="btnFavorito" class="btn btn-favorito">
                    ♡ Agregar a favoritos
                </button>

                <a id="btnMapa" href="#" class="btn btn-mapa" style="display:none;">
                    Ver ubicación
                </a>

            </div>

        </div>
    </div>
</div>

<!-- BARRA DE NAVEGACIÓN CON LOS MISMOS ICONOS -->
<nav class="nav">

    <a href="principal.php" class="activo">
        <span class="icono">
            <svg viewBox="0 0 24 24">
                <path d="M3 10.5L12 3l9 7.5"></path>
                <path d="M5 9.5V21h14V9.5"></path>
                <path d="M9 21v-7h6v7"></path>
            </svg>
        </span>
        Inicio
    </a>

    <a href="favoritos.php">
        <span class="icono">
            <svg viewBox="0 0 24 24">
                <path d="M20.8 8.8c0 5.5-8.8 11-8.8 11S3.2 14.3 3.2 8.8C3.2 5.6 5.3 3.5 8.2 3.5c1.7 0 3.1.8 3.8 2.1.7-1.3 2.1-2.1 3.8-2.1 2.9 0 5 2.1 5 5.3z"></path>
            </svg>
        </span>
        Favoritos
    </a>

    <a href="mapa.php">
        <span class="icono">
            <svg viewBox="0 0 24 24">
                <path d="M12 21s7-6.2 7-12a7 7 0 1 0-14 0c0 5.8 7 12 7 12z"></path>
                <circle cx="12" cy="9" r="2.5"></circle>
            </svg>
        </span>
        Mapa
    </a>

    <a href="notificaciones.php">
        <span class="icono">
            <svg viewBox="0 0 24 24">
                <path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                <path d="M10 21h4"></path>
            </svg>
        </span>
        Notificaciones
    </a>

    <a href="perfil.php">
        <span class="icono">
            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="8" r="3.2"></circle>
                <path d="M5 21c0-3.8 3-6 7-6s7 2.2 7 6"></path>
            </svg>
        </span>
        Perfil
    </a>

</nav>

</div>

<script>
const buscador = document.getElementById("buscador");
const eventos = document.querySelectorAll(".evento");
const sinResultados = document.getElementById("sinResultados");

if (buscador) {
    buscador.addEventListener("input", function () {

        const texto = this.value.toLowerCase().trim();
        let encontrados = 0;

        eventos.forEach(function (evento) {

            const contenido = evento.dataset.busqueda || "";
            const coincide = contenido.includes(texto);

            evento.style.display = coincide ? "flex" : "none";

            if (coincide) {
                encontrados++;
            }
        });

        if (sinResultados) {
            sinResultados.style.display =
                encontrados === 0 ? "block" : "none";
        }
    });
}

/* ================================
   DETALLE DEL EVENTO
================================ */

const modal = document.getElementById("modalEvento");
const cerrarModal = document.getElementById("cerrarModal");

const detalleImagen = document.getElementById("detalleImagen");
const detalleTitulo = document.getElementById("detalleTitulo");
const detalleFecha = document.getElementById("detalleFecha");
const detalleHora = document.getElementById("detalleHora");
const detalleLugar = document.getElementById("detalleLugar");
const detalleDescripcion = document.getElementById("detalleDescripcion");
const btnMapa = document.getElementById("btnMapa");
const btnFavorito = document.getElementById("btnFavorito");

let eventoActual = null;

function abrirDetalle(evento) {

    eventoActual = evento;

    const titulo = evento.dataset.titulo || "";
    const descripcion = evento.dataset.descripcion || "Sin descripción.";
    const fecha = evento.dataset.fecha || "";
    const hora = evento.dataset.hora || "";
    const lugar = evento.dataset.lugar || "";
    const imagen = evento.dataset.imagen || "";
    const id = evento.dataset.id || "";
    const latitud = evento.dataset.latitud || "";
    const longitud = evento.dataset.longitud || "";

    detalleTitulo.textContent = titulo;
    detalleFecha.textContent = "Fecha: " + fecha;
    detalleHora.textContent = "Hora: " + hora;
    detalleLugar.textContent = "Lugar: " + lugar;
    detalleDescripcion.textContent = descripcion;

    if (imagen !== "") {
        detalleImagen.src = imagen;
        detalleImagen.style.display = "block";
    } else {
        detalleImagen.removeAttribute("src");
        detalleImagen.style.display = "none";
    }

    if (latitud !== "" && longitud !== "") {
        btnMapa.href = "mapa.php?evento=" + encodeURIComponent(id);
        btnMapa.style.display = "inline-flex";
    } else {
        btnMapa.style.display = "none";
    }

    actualizarBotonFavorito(id);

    modal.classList.add("abierto");
    modal.setAttribute("aria-hidden", "false");
    document.body.style.overflow = "hidden";
}

function cerrarDetalle() {
    modal.classList.remove("abierto");
    modal.setAttribute("aria-hidden", "true");
    document.body.style.overflow = "";
    eventoActual = null;
}

eventos.forEach(function(evento) {
    evento.addEventListener("click", function() {
        abrirDetalle(evento);
    });
});

cerrarModal.addEventListener("click", cerrarDetalle);

modal.addEventListener("click", function(e) {
    if (e.target === modal) {
        cerrarDetalle();
    }
});

document.addEventListener("keydown", function(e) {
    if (e.key === "Escape" && modal.classList.contains("abierto")) {
        cerrarDetalle();
    }
});

/* ==========================================
   FAVORITOS
   Se guardan temporalmente en el navegador.
   La conexión con la tabla de favoritos se
   puede hacer después sin cambiar este diseño.
========================================== */

function obtenerFavoritos() {

    try {
        return JSON.parse(localStorage.getItem("culturaactiva_favoritos") || "[]");
    } catch (e) {
        return [];
    }
}

function guardarFavoritos(lista) {
    localStorage.setItem(
        "culturaactiva_favoritos",
        JSON.stringify(lista)
    );
}

function actualizarBotonFavorito(id) {

    const favoritos = obtenerFavoritos();
    const estaGuardado = favoritos.includes(String(id));

    if (estaGuardado) {
        btnFavorito.textContent = "♥ En favoritos";
    } else {
        btnFavorito.textContent = "♡ Agregar a favoritos";
    }
}

btnFavorito.addEventListener("click", function() {

    if (!eventoActual) {
        return;
    }

    const id = String(eventoActual.dataset.id);
    let favoritos = obtenerFavoritos();

    if (favoritos.includes(id)) {

        favoritos = favoritos.filter(function(item) {
            return item !== id;
        });

        btnFavorito.textContent = "♡ Agregar a favoritos";

    } else {

        favoritos.push(id);

        btnFavorito.textContent = "♥ En favoritos";
    }

    guardarFavoritos(favoritos);
});
</script>

</body>
</html>
