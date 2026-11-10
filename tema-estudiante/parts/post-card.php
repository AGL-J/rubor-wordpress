<?php
/**
 * Tarjeta de entrada del Diario.
 */
$rubor_cat = get_the_category();
?>
<article <?php post_class( 'post reveal' ); ?>>
  <a href="<?php the_permalink(); ?>">
    <div class="post__img">
      <?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'large', array( 'loading' => 'lazy' ) ); } ?>
    </div>
    <p class="post__meta"><?php echo esc_html( ( $rubor_cat ? $rubor_cat[0]->name : 'Diario' ) . ' · ' . get_the_date() ); ?></p>
    <h3><?php the_title(); ?></h3>
  </a>
</article>
