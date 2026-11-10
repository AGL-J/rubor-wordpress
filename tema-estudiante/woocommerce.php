<?php
/**
 * Contenedor para las páginas de WooCommerce: tienda, categorías y ficha de producto.
 */
get_header();
?>
<main id="contenido" class="page-shell wc-shell">
  <?php if ( is_shop() || is_product_taxonomy() ) : ?>
    <header class="section-head">
      <p class="eyebrow"><?php echo is_shop() ? 'Tienda' : 'Categoría'; ?></p>
      <h1 class="section-title"><?php woocommerce_page_title(); ?></h1>
      <?php if ( is_product_taxonomy() && term_description() ) : ?>
        <div class="wc-shell__intro"><?php echo wp_kses_post( term_description() ); ?></div>
      <?php endif; ?>
      <nav class="filters filters--links" aria-label="Categorías">
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"<?php echo is_shop() ? ' aria-current="page"' : ''; ?>>Todo</a>
        <?php foreach ( get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'exclude' => get_option( 'default_product_cat' ) ) ) as $term ) : ?>
          <a href="<?php echo esc_url( get_term_link( $term ) ); ?>"<?php echo is_product_category( $term->slug ) ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $term->name ); ?></a>
        <?php endforeach; ?>
      </nav>
    </header>
  <?php endif; ?>

  <div class="wc-shell__body">
    <?php woocommerce_content(); ?>
  </div>
</main>
<?php
get_footer();
