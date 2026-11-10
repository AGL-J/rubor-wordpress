<?php
/**
 * Página estática (Nosotros, Contacto, Envíos, Carrito, Finalizar compra…).
 */
get_header();
?>
<main id="contenido" class="page-shell">
  <?php while ( have_posts() ) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--page' ); ?>>
      <header class="entry__head">
        <h1 class="section-title"><?php the_title(); ?></h1>
      </header>
      <div class="entry__content"><?php the_content(); ?></div>
    </article>
  <?php endwhile; ?>
</main>
<?php
get_footer();
