<?php
// Fragmento agregado a wp-config.php (antes de "That's all, stop editing!").

/* ---------- Bastionado (hardening) — Proyecto RUBOR ---------- */
// Deshabilita el editor de archivos de temas y plugins en el panel (Apariencia/Plugins → Editor).
define( 'DISALLOW_FILE_EDIT', true );
// Limita las revisiones guardadas por entrada para no inflar la base de datos.
define( 'WP_POST_REVISIONS', 5 );
// FORCE_SSL_ADMIN se activa en producción (con HTTPS). En local no hay certificado, por eso queda comentado.
// define( 'FORCE_SSL_ADMIN', true );

