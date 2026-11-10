<?php
/**
 * Entrada individual del Diario.
 */
get_header();
?>
<main id="contenido" class="page-shell">
  <?php while ( have_posts() ) : the_post(); $rubor_cat = get_the_category(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
      <header class="entry__head">
        <p class="eyebrow"><?php echo esc_html( ( $rubor_cat ? $rubor_cat[0]->name . ' · ' : '' ) . get_the_date() . ' · ' . get_the_author() ); ?></p>
        <h1 class="section-title"><?php the_title(); ?></h1>
        <?php if ( has_excerpt() ) : ?><p class="entry__lede"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
      </header>
      <?php if ( has_post_thumbnail() ) : ?><figure class="entry__img"><?php the_post_thumbnail( 'full' ); ?></figure><?php endif; ?>
      <div class="entry__content"><?php the_content(); ?></div>
      <footer class="entry__foot">
        <?php the_tags( '<p class="entry__tags">', '', '</p>' ); ?>
        <nav class="entry__nav">
          <?php previous_post_link( '<span>%link</span>', '← %title' ); ?>
          <?php next_post_link( '<span>%link</span>', '%title →' ); ?>
        </nav>
      </footer>
    </article>
  <?php endwhile; ?>
  <?php get_sidebar(); ?>
</main>
<?php
get_footer();
