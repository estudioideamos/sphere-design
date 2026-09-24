<?php
/**
 * Plugin Name: Sphere Design — Manual del sitio
 * Description: Guía de edición y publicación para el equipo de Sphere Design.
 * Author: Estudio Ideamos
 * Author URI: https://ideamos.com.ar/
 * Version: 1.1.0
 */
if (!defined("ABSPATH")) {
    exit();
}
add_action("admin_menu", function () {
    add_menu_page(
        "Manual de Sphere Design",
        "Manual del sitio",
        "edit_posts",
        "sphere-manual",
        "sphere_manual_page",
        "dashicons-welcome-learn-more",
        3,
    );
});
add_action("admin_enqueue_scripts", function ($hook) {
    if ($hook !== "toplevel_page_sphere-manual") {
        return;
    }
    wp_enqueue_style(
        "sphere-manual",
        plugins_url("sphere-manual.css", __FILE__),
        [],
        filemtime(__DIR__ . "/sphere-manual.css"),
    );
    wp_enqueue_script(
        "sphere-manual",
        plugins_url("sphere-manual.js", __FILE__),
        [],
        filemtime(__DIR__ . "/sphere-manual.js"),
        true,
    );
});
function sphere_manual_page()
{
    if (!current_user_can("edit_posts")) {
        return;
    }
    $links = [
        "post" => admin_url("post-new.php"),
        "posts" => admin_url("edit.php"),
        "project" => admin_url("post-new.php?post_type=sphere_project"),
        "projects" => admin_url("edit.php?post_type=sphere_project"),
        "media" => admin_url("upload.php"),
        "pages" => admin_url("edit.php?post_type=page"),
    ];
    ?>
<div class="sphere-guide">
 <header class="sg-hero"><div><span class="sg-kicker">SPHERE DESIGN / CENTRO DE AYUDA</span><h1>Tu sitio,<br><em>en tus manos.</em></h1><p>Una guía práctica para publicar ideas, presentar proyectos y mantener el contenido al día.</p></div><div class="sg-stamp"><span class="dashicons dashicons-welcome-learn-more" aria-hidden="true"></span><span>Manual de administración</span><strong>Simple. Claro. Paso a paso.</strong></div></header>
 <div class="sg-actions"><a href="<?php echo esc_url(
     $links["post"],
 ); ?>"><span class="dashicons dashicons-edit-page" aria-hidden="true"></span><strong>Crear un Insight</strong><span>Escribir una nueva entrada →</span></a><a href="<?php echo esc_url(
    $links["project"],
); ?>"><span class="dashicons dashicons-format-gallery" aria-hidden="true"></span><strong>Agregar un proyecto</strong><span>Actualizar el portfolio →</span></a><a href="<?php echo esc_url(
    home_url("/"),
); ?>" target="_blank" rel="noopener"><span class="dashicons dashicons-external" aria-hidden="true"></span><strong>Ver la web</strong><span>Abrir en otra pestaña →</span></a></div>
 <div class="sg-layout"><aside class="sg-nav"><label for="sg-search">¿Qué querés hacer?</label><input id="sg-search" type="search" placeholder="Buscar: imagen, borrar, video…"><nav aria-label="Temas del manual"><a href="#sg-basics">Antes de empezar</a><a href="#sg-insights">Insights / Blog</a><a href="#sg-portfolio">Portfolio</a><a href="#sg-media">Imágenes y videos</a><a href="#sg-pages">Datos de contacto</a><a href="#sg-check">Antes de publicar</a></nav><p id="sg-search-status" aria-live="polite"></p></aside>
 <main class="sg-content">
 <section id="sg-basics" class="sg-section"><span class="sg-kicker">01 / LO ESENCIAL</span><h2>Elegí dónde trabajar</h2><p>El panel está organizado para editar <strong>Insights, Portfolio y datos de contacto</strong>. Las demás páginas mantienen su composición fija. Para los artículos se utiliza el <strong>editor clásico de WordPress</strong>. En Insights vas a encontrar las pestañas Visual y Texto, una barra de formato y el cuadro Publicar.</p><div class="sg-table"><div><strong>Entradas → Insights</strong><p>Artículos del blog: título, texto, extracto e imagen destacada.</p></div><div><strong>Portfolio</strong><p>Imágenes, películas y panoramas: nombre, categoría, imagen y orden.</p></div><div><strong>Medios</strong><p>Biblioteca de imágenes. Un archivo puede estar usado en más de un lugar.</p></div><div><strong>Datos de contacto</strong><p>Correo, teléfono, ubicación y enlaces de redes sociales.</p></div></div><div class="sg-note"><strong>Guardar no siempre significa publicar.</strong> Un borrador queda solo en el panel. Al publicar o actualizar, el cambio puede verse en la web.</div></section>
 <section id="sg-insights" class="sg-section"><span class="sg-kicker">02 / INSIGHTS</span><h2>Ideas que se convierten en entradas</h2>
 <details open><summary>Crear y publicar un artículo</summary><ol><li>Entrá en <strong>Entradas → Añadir nueva</strong>, o usá el acceso de arriba.</li><li>Escribí el título. La web lo presenta automáticamente como encabezado principal; no lo repitas dentro del texto.</li><li>Escribí el contenido en la pestaña <strong>Visual</strong> del editor clásico. Usá el desplegable de formato para elegir <strong>Título 2 (H2)</strong> para los apartados: aparecerán en el índice lateral del artículo. Podés usar H3 para subtítulos internos.</li><li>En el panel de la derecha, cargá la <strong>Imagen destacada</strong>: es la portada de Insights y del artículo.</li><li>Completá el <strong>Extracto</strong> con una introducción breve. Se muestra en la tarjeta y debajo del título. Si no encontrás el campo, abrí <strong>Opciones de pantalla</strong>, arriba a la derecha, y activá <strong>Extracto</strong>.</li><li>Revisá la dirección o enlace permanente antes de publicar. Elegí un nombre breve y descriptivo.</li><li>Guardá como borrador y usá <strong>Vista previa</strong>. Si está listo, elegí <strong>Publicar</strong>. Las entradas publicadas aparecen automáticamente en Insights.</li></ol><p class="sg-tip">Las entradas se ordenan por fecha, de más reciente a más antigua. Si programás una fecha futura, WordPress la publicará cuando se ejecute la tarea programada del sitio.</p></details>
 <details><summary>¿Cómo se define “In this story”?</summary><p>Es el índice automático del artículo. Se arma con los encabezados <strong>Título 2 (H2)</strong> del contenido, en el mismo orden en que aparecen.</p><ol><li>Escribí el nombre de un apartado en una línea propia.</li><li>Seleccioná esa línea en la pestaña Visual.</li><li>En el desplegable que normalmente dice Párrafo, elegí <strong>Título 2</strong>.</li><li>Repetí con cada apartado y actualizá la entrada.</li></ol><p>Ejemplo: “The challenge”, “Our approach” y “The result” como Título 2 generan tres enlaces en el índice. Al cambiar, mover o quitar un H2, el índice se actualiza al abrir el artículo.</p><p>No hace falta escribir el índice a mano. Una frase en negrita no cuenta como encabezado. Los H3 sirven para subdivisiones y no se agregan a este índice.</p></details><details><summary>Título y descripción para Google</summary><p>Al editar una entrada, el cuadro <strong>Presentación en Google y redes</strong> permite escribir un título SEO y una descripción breve. Es opcional: si se dejan vacíos, se usan el título del artículo y su extracto.</p><p>Escribí un título claro que describa el tema y una descripción útil de unas 140–160 letras. No repitas palabras clave. Google puede elegir otro fragmento para sus resultados. La imagen destacada también acompaña el enlace al compartirlo.</p><p>La demo permanece fuera de los buscadores. La indexación se activa al publicar en el dominio final; una puntuación de auditoría no garantiza posiciones en Google.</p></details><details><summary>Editar un artículo existente</summary><ol><li>Entrá en <strong>Entradas → Todas las entradas</strong>.</li><li>Buscá el título y elegí <strong>Editar</strong>.</li><li>Modificá el texto, extracto o imagen destacada. Usá la vista previa para revisar el resultado.</li><li>Presioná <strong>Actualizar</strong> en el cuadro Publicar, a la derecha.</li></ol><p class="sg-tip">Evitá cambiar la dirección de un artículo ya compartido. Para recuperar texto anterior, revisá las revisiones disponibles en el editor.</p></details>
 <details><summary>Quitar, borrar o recuperar un artículo</summary><ul><li><strong>Ocultarlo sin borrarlo:</strong> cambiá su estado a Borrador y guardá.</li><li><strong>Quitar de la web:</strong> en la lista de entradas, elegí Papelera.</li><li><strong>Recuperarlo:</strong> abrí Papelera y elegí Restaurar; revisá su estado antes de volver a publicarlo.</li><li><strong>Borrarlo definitivamente:</strong> hacelo desde la Papelera solo cuando estés segura. Esa acción no tiene restauración directa.</li></ul><p class="sg-tip">Eliminar una entrada no elimina automáticamente sus imágenes de la biblioteca.</p></details>
 <a class="sg-link" href="<?php echo esc_url(
     $links["posts"],
 ); ?>">Ir a las entradas →</a></section>
 <section id="sg-portfolio" class="sg-section"><span class="sg-kicker">03 / PORTFOLIO</span><h2>Mostrá el trabajo con claridad</h2><p class="sg-intro">Cada proyecto reúne su imagen, nombre y categoría. Desde <strong>Portfolio</strong> podés crear, editar, ordenar y quitar proyectos del catálogo.</p><div class="sg-choice-grid"><a href="<?php echo esc_url(
     admin_url("post-new.php?post_type=sphere_project"),
 ); ?>"><span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span><strong>Nuevo proyecto</strong><span>Prepará la imagen y completá nombre, categoría y orden.</span><b>Crear proyecto →</b></a><a href="<?php echo esc_url(
    admin_url("edit.php?post_type=sphere_project"),
); ?>"><span class="dashicons dashicons-format-gallery" aria-hidden="true"></span><strong>Proyectos existentes</strong><span>Buscá un proyecto para cambiar su imagen, editarlo o enviarlo a la papelera.</span><b>Administrar Portfolio →</b></a></div><div class="sg-project-map"><figure><img src="<?php echo esc_url(
    get_template_directory_uri() . "/assets/interior2.webp",
); ?>" alt="Ejemplo de una imagen de proyecto" loading="lazy"><figcaption><strong>Nombre del proyecto</strong><span>Residential</span></figcaption></figure><div><span class="sg-kicker">ASÍ SE COMPONE UN PROYECTO</span><dl><dt><b>1</b> Imagen destacada</dt><dd>Es la imagen de la tarjeta y la que se abre en el visor.</dd><dt><b>2</b> Nombre</dt><dd>Se escribe en el título del proyecto.</dd><dt><b>3</b> Categoría</dt><dd>Define el filtro en el que aparece: Residential, Hospitality, Animations…</dd><dt><b>4</b> Orden</dt><dd>Los números menores aparecen primero. Usá 10, 20, 30 para dejar espacio entre proyectos.</dd></dl></div></div><div class="sg-task-strip"><span><b>01</b> Preparar imagen</span><span><b>02</b> Completar proyecto</span><span><b>03</b> Publicar y revisar</span></div>
 <details open><summary>Agregar una imagen o un nuevo proyecto</summary><ol><li>Entrá en <strong>Portfolio → Añadir proyecto</strong>.</li><li>Escribí un título descriptivo y corto.</li><li>Elegí una <strong>Imagen destacada</strong>. Es la imagen que se ve en la grilla y se amplía al abrir la tarjeta.</li><li>Asigná <strong>una categoría</strong>, por ejemplo Residential, Hospitality, Sports o Corporate. Esa categoría determina el filtro donde aparece.</li><li>En <strong>Atributos → Orden</strong>, indicá un número: los valores menores aparecen primero. Si hay empate, se usa el orden de creación.</li><li>Publicá y revisá el resultado en Portfolio, tanto en All como en su categoría.</li></ol><p class="sg-tip">La grilla completa del portfolio es administrable. La selección de imágenes de la Home es una composición independiente; agregar un proyecto no lo agrega automáticamente a la Home.</p></details>
 <details><summary>Agregar una película o un recorrido 360°</summary><p><strong>Películas:</strong> seleccioná la categoría <strong>Animations</strong>, elegí un fotograma atractivo como imagen destacada y pegá el enlace de YouTube en <strong>Medios del proyecto / Video de YouTube</strong>. El video debe permitir reproducción incorporada. No hace falta subir el archivo pesado a WordPress.</p><p><strong>Recorridos:</strong> seleccioná <strong>VR 360°</strong> y usá como imagen destacada un panorama equirectangular completo, con proporción <strong>2:1</strong>. Una fotografía común no produce un recorrido 360° correcto.</p><p class="sg-tip">Revisá siempre el video publicado: una restricción de privacidad, derechos o inserción en YouTube puede impedir la reproducción.</p></details>
 <details><summary>Editar, reordenar o borrar proyectos</summary><ol><li>Entrá en <strong>Portfolio → Todos los proyectos</strong> y abrí el proyecto.</li><li>Para cambiar la imagen, reemplazá la <strong>Imagen destacada</strong>. Para cambiar el grupo, modificá la categoría.</li><li>Para reordenar, ajustá <strong>Orden</strong> y guardá.</li><li>Elegí <strong>Actualizar</strong> y revisá la grilla y el visor.</li></ol><p>Para retirarlo temporalmente, pasalo a Borrador. Para quitarlo, usá Papelera. Podés restaurarlo desde la Papelera mientras no lo hayas eliminado definitivamente.</p></details>
 <a class="sg-link" href="<?php echo esc_url(
     $links["projects"],
 ); ?>">Administrar el portfolio →</a></section>
 <section id="sg-media" class="sg-section"><span class="sg-kicker">04 / MEDIOS</span><h2>Buena imagen. Carga liviana.</h2><p>La tabla indica <strong>objetivos recomendados</strong> para conservar nitidez y cargar rápido. Además, este sitio bloquea imágenes de más de <strong>3 MB o 4096 px por lado</strong>, otros archivos de más de <strong>5 MB</strong> y subidas de videos. El límite que muestre el servidor puede ser mayor; estas reglas del sitio siguen aplicándose.</p>
<div class="sg-media-table"><table><caption>Guía rápida de formatos, dimensiones y peso</caption><thead><tr><th scope="col">Uso</th><th scope="col">Formato</th><th scope="col">Tamaño sugerido</th><th scope="col">Peso orientativo</th></tr></thead><tbody>
<tr><th scope="row">Imagen de portfolio</th><td>WebP o JPEG</td><td>2400–2880 px en el lado mayor. Conservar la proporción original.</td><td>300 KB–1 MB. Un render muy detallado puede necesitar hasta 2 MB.</td></tr>
<tr><th scope="row">Portada de Insights</th><td>WebP o JPEG</td><td>1600–2000 px de ancho. Preferentemente horizontal, 16:9 o 4:3.</td><td>150–500 KB.</td></tr>
<tr><th scope="row">Imagen dentro de un artículo</th><td>WebP o JPEG</td><td>1200–1600 px de ancho; elegir tamaño Grande al insertarla.</td><td>150–500 KB por imagen.</td></tr>
<tr><th scope="row">Panorama VR 360°</th><td>JPEG o WebP</td><td>Equirectangular, exactamente 2:1. Ejemplo: 4096 × 2048 px.</td><td>1–3 MB, según detalle. Revisar nitidez dentro del visor.</td></tr>
<tr><th scope="row">Logo o gráfico con transparencia</th><td>PNG o WebP transparente</td><td>500–1000 px suele ser suficiente. No usar un fondo blanco si debe ser transparente.</td><td>Preferentemente menos de 150 KB.</td></tr>
<tr><th scope="row">Documento descargable</th><td>PDF</td><td>Texto seleccionable, páginas al tamaño final y fotos optimizadas.</td><td>Preferentemente menos de 3 MB; hasta 5 MB para un dossier con imágenes.</td></tr>
<tr><th scope="row">Película del portfolio</th><td>Enlace de YouTube</td><td>Video en 1080p o superior, con inserción permitida. Subir también un fotograma como imagen destacada.</td><td>El archivo de video no se sube a WordPress.</td></tr>
</tbody></table></div>
<details><summary>Cómo preparar el texto de un Insight</summary><ul><li>Escribí o pegá el texto en el editor clásico. Si viene de Word o Google Docs, usá <strong>Pegar como texto</strong> para evitar estilos extraños y luego aplicá el formato desde WordPress.</li><li>Usá párrafos cortos, títulos H2 y, si hace falta, subtítulos H3. No simules un título agrandando letras ni repitas el H1.</li><li>Escribí un extracto de aproximadamente <strong>30–50 palabras</strong>. Es la introducción que acompaña la tarjeta y el artículo.</li><li>Un archivo DOCX o PDF <strong>no se convierte automáticamente en una entrada</strong>. El texto principal debe estar escrito en el editor.</li><li>Para insertar una imagen, usá <strong>Añadir medios</strong>, completá el texto alternativo y elegí un tamaño acorde. Evitá pegar imágenes directamente desde un documento de Word.</li></ul></details>
<details><summary>Videos: cuándo subir un archivo y cuándo usar un enlace</summary><p><strong>Para el portfolio:</strong> subí la película a YouTube y pegá su enlace en el proyecto. Usá visibilidad Pública o No listado y habilitá la inserción. Un video Privado no funcionará para todos los visitantes.</p><p>Para exportar un archivo destinado a YouTube, una base compatible es <strong>MP4 con video H.264, 1920 × 1080 px</strong>, manteniendo la frecuencia de cuadros del original. Conservá una copia de buena calidad; YouTube se encarga de las versiones de reproducción.</p><p><strong>Para un futuro video de fondo del hero:</strong> coordiná el reemplazo con Ideamos. Como objetivo de carga, conviene un loop de 10–20 segundos, MP4 H.264, sin pista de audio y versiones separadas para escritorio y móvil: aproximadamente 3–6 MB y 1–3 MB respectivamente. Son referencias para preparar futuros archivos, no un cambio automático del video actual.</p><p class="sg-tip">No subas películas pesadas directamente a Medios para usarlas en una tarjeta: el portfolio está preparado para enlaces de YouTube.</p></details>
<details><summary>Documentos, nombres de archivo y formatos a evitar</summary><ul><li>Para compartir un dossier, usá PDF. Subilo en Medios y enlazalo desde un texto descriptivo, por ejemplo “Descargar presentación del proyecto (PDF)”.</li><li>Usá nombres simples y descriptivos: <strong>residencia-miami-salon.webp</strong> o <strong>presentacion-sphere.pdf</strong>. Evitá nombres como IMG_1234 o cadenas largas con símbolos.</li><li>No uses TIFF, PSD o archivos de cámara RAW como imágenes de la web. Exportalos a WebP o JPEG. Convertí HEIC antes de subirlo.</li><li>SVG no está habilitado para subida directa en esta instalación. Para logos usá PNG o WebP; si necesitás un SVG, pedí su revisión técnica.</li><li>Eliminá datos privados de los documentos antes de publicarlos: todo archivo publicado y enlazado puede descargarse.</li></ul></details>
<details><summary>Elegir y preparar imágenes</summary><ul><li>Usá archivos nítidos, preferentemente <strong>WebP o JPEG</strong>. Evitá capturas de pantalla de renders.</li><li>Para el portfolio, una dimensión mayor de <strong>2400–2880 px</strong> suele ser una buena base. Para la portada de Insights, <strong>1600–2000 px</strong>. No agrandes una imagen pequeña: no recupera detalle.</li><li>Como objetivo práctico, buscá <strong>200–600 KB</strong> para portadas y menos de <strong>1 MB</strong> para la mayoría de los renders. Algunas imágenes muy detalladas o panoramas pueden necesitar más peso; priorizá un equilibrio visual.</li><li>Completá el <strong>Texto alternativo</strong> describiendo lo que muestra la imagen. Ayuda a quienes usan lectores de pantalla.</li><li>Revisá que no se corten marcas de agua o partes importantes en las miniaturas.</li></ul></details><details><summary>Reemplazar y eliminar archivos sin romper imágenes</summary><p>Primero cargá la nueva imagen y seleccionála en el artículo o proyecto correspondiente. Revisá la web antes de quitar la anterior.</p><p><strong>Eliminar permanentemente desde Medios puede dejar imágenes rotas</strong> si el archivo se usa en otra página. El indicador “Sin adjuntar” no garantiza que esté sin uso. Ante la duda, conservá el archivo y consultá.</p></details><a class="sg-link" href="<?php echo esc_url(
    $links["media"],
); ?>">Abrir biblioteca de medios →</a></section>
 <section id="sg-pages" class="sg-section"><span class="sg-kicker">05 / CONTACTO</span><h2>Los datos que conectan con tus clientes</h2><p>El menú <strong>Datos de contacto</strong> reúne el correo, teléfono, ubicación y redes sociales. Está disponible para administradores.</p><details open><summary>Cambiar correo, teléfono o redes</summary><ol><li>Entrá en <strong>Datos de contacto</strong>.</li><li>Modificá los campos necesarios. Incluí el código de país en el teléfono y la dirección completa en cada red social.</li><li>Presioná <strong>Guardar datos</strong>.</li><li>Abrí <strong>Ver Contacto</strong> para revisar lo publicado.</li></ol><p>El correo también recibe las consultas del formulario. Cambiarlo modifica el destino de los mensajes nuevos.</p></details><details><summary>¿Dónde se reciben las consultas?</summary><p>Las consultas llegan a la casilla configurada. Se responden desde ese correo; WordPress no tiene una bandeja de consultas en este panel. Si no llega una consulta, revisá Spam y la configuración de correo del hosting. La confirmación de envío no garantiza recepción en bandeja de entrada.</p></details><details><summary>Demo y publicación final</summary><p>La demo de WordPress y la demo anterior de GitHub son sitios separados. Las ediciones se ven en WordPress. Esta versión permanece fuera de Google hasta pasar al dominio final; luego habrá que verificar correos, enlaces, sitemap e indexación.</p></details><a class="sg-link" href="<?php echo esc_url(
     admin_url("admin.php?page=sphere-contact-details"),
 ); ?>">Editar datos de contacto →</a></section>
<section id="sg-check" class="sg-section"><span class="sg-kicker">06 / REVISIÓN FINAL</span><h2>Un minuto antes de publicar</h2><ul class="sg-checklist"><li>El título y el texto están revisados.</li><li>La imagen destacada se ve nítida y tiene texto alternativo.</li><li>La categoría del proyecto y su orden son correctos.</li><li>Los enlaces, videos o recorridos abren bien.</li><li>La vista en teléfono y escritorio se lee sin cortes.</li><li>El contenido está publicado, no quedó solo como borrador.</li></ul><div class="sg-note"><strong>¿No ves el cambio?</strong> Confirmá que guardaste, abrí la web en otra pestaña y actualizá. Revisá que estés mirando WordPress y no el enlace de GitHub.</div></section>
 </main></div><footer class="sg-credit"><span>Hecho para Sphere Design.</span><a href="https://ideamos.com.ar/" target="_blank" rel="noopener">Diseño y desarrollo por <strong>Estudio Ideamos ↗</strong></a></footer>
</div>
<?php
}
