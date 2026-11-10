<?php
/**
 * Barra lateral del Diario. Se edita en Apariencia → Widgets → "Barra lateral del Diario".
 */
?>
<aside class="sidebar" aria-label="Más del Diario">
  <?php if ( is_active_sidebar( 'sidebar-diario' ) ) : ?>
    <?php dynamic_sidebar( 'sidebar-diario' ); ?>
  <?php else : ?>
    <section class="widget">
      <h2 class="widget-title">Categorías</h2>
      <ul><?php wp_list_categories( array( 'title_li' => '', 'hide_empty' => true ) ); ?></ul>
    </section>
    <section class="widget">
      <h2 class="widget-title">Entradas recientes</h2>
      <ul>
        <?php foreach ( wp_get_recent_posts( array( 'numberposts' => 4, 'post_status' => 'publish' ) ) as $p ) : ?>
          <li><a href="<?php echo esc_url( get_permalink( $p['ID'] ) ); ?>"><?php echo esc_html( $p['post_title'] ); ?></a></li>
        <?php endforeach; ?>
      </ul>
    </section>
  <?php endif; ?>
</aside>
