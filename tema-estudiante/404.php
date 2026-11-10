<?php
/**
 * Página no encontrada.
 */
get_header();
?>
<main id="contenido" class="page-shell">
  <header class="section-head">
    <p class="eyebrow">Error 404</p>
    <h1 class="section-title">Este tono <em>no existe.</em></h1>
    <p class="entry__lede">La página que buscas se movió o nunca estuvo aquí. Prueba desde la tienda o vuelve al inicio.</p>
    <p style="margin-top:32px;display:flex;gap:24px;align-items:center;flex-wrap:wrap">
      <a class="btn btn--dark" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>">Ir a la tienda</a>
      <a class="link-arrow" href="<?php echo esc_url( home_url( '/' ) ); ?>">Volver al inicio <span aria-hidden="true">→</span></a>
    </p>
  </header>
</main>
<?php
get_footer();
