<?php
/**
 * Plantilla principal (respaldo de la jerarquía): Diario, categorías, etiquetas y búsqueda con The Loop.
 */
get_header();
?>
<main id="contenido" class="page-shell">
    <header class="section-head">
      <p class="eyebrow">Diario Rubor</p>
      <h1 class="section-title">
        <?php
        if ( is_category() || is_tag() ) {
          echo esc_html( single_term_title( '', false ) );
        } elseif ( is_search() ) {
          echo esc_html( 'Resultados para "' . get_search_query() . '"' );
        } else {
          echo 'Notas desde <em>el tocador.</em>';
        }
        ?>
      </h1>
      <?php
      $rubor_cats = get_categories( array( 'hide_empty' => true ) );
      if ( $rubor_cats ) :
        $rubor_blog = get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/' );
        ?>
        <nav class="filters filters--links" aria-label="Categorías del Diario">
          <a href="<?php echo esc_url( $rubor_blog ); ?>"<?php echo is_home() ? ' aria-current="page"' : ''; ?>>Todas</a>
          <?php foreach ( $rubor_cats as $c ) : ?>
            <a href="<?php echo esc_url( get_category_link( $c ) ); ?>"<?php echo is_category( $c->term_id ) ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $c->name ); ?></a>
          <?php endforeach; ?>
        </nav>
      <?php endif; ?>
    </header>

    <?php if ( have_posts() ) : ?>
      <div class="journal__grid">
        <?php while ( have_posts() ) : the_post(); get_template_part( 'parts/post-card' ); endwhile; ?>
      </div>
      <div class="page-pager"><?php the_posts_pagination( array( 'prev_text' => '←', 'next_text' => '→' ) ); ?></div>
    <?php else : ?>
      <p class="section-head">Aquí no hay nada todavía.</p>
    <?php endif; ?>
</main>
<?php
get_footer();
