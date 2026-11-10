<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip" href="#contenido">Saltar al contenido</a>

<?php
$rubor_count = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
$rubor_home  = home_url( '/' );
?>

<div class="announce">
  <p><span>Envío gratis desde $150.000</span><span class="announce__dot" aria-hidden="true"></span><span>Muestra de regalo en cada pedido</span><span class="announce__dot" aria-hidden="true"></span><span>Devoluciones hasta 30 días</span></p>
</div>

<header class="site-header" data-header>
  <div class="site-header__inner">
    <button class="icon-btn menu-toggle" data-menu-open aria-label="Abrir menú">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7h18M3 12h18M3 17h12"/></svg>
    </button>

    <nav class="nav nav--left" aria-label="Principal">
      <?php rubor_menu( 'primary-menu', array( 'Rostro' => rubor_term_link( 'rostro' ), 'Ojos' => rubor_term_link( 'ojos' ), 'Labios' => rubor_term_link( 'labios' ), 'Piel' => rubor_term_link( 'piel' ) ) ); ?>
    </nav>

    <?php if ( has_custom_logo() ) : ?>
      <div class="logo logo--img"><?php the_custom_logo(); ?></div>
    <?php else : ?>
      <a class="logo" href="<?php echo esc_url( $rubor_home ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>, inicio"><?php bloginfo( 'name' ); ?></a>
    <?php endif; ?>

    <nav class="nav nav--right" aria-label="Secundaria">
      <a href="<?php echo esc_url( $rubor_home ); ?>#tonos">Encuentra tu tono</a>
      <?php rubor_menu( 'secondary-menu', array( 'Diario' => rubor_page_link( 'diario' ) ) ); ?>
      <button class="bag-btn" data-cart-open aria-label="Abrir bolsa">
        Bolsa <span class="bag-btn__count"><?php echo esc_html( $rubor_count ); ?></span>
      </button>
    </nav>

    <button class="icon-btn bag-mobile" data-cart-open aria-label="Abrir bolsa">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 8h14l-1.2 12H6.2L5 8Z"/><path d="M9 8V6.5a3 3 0 0 1 6 0V8"/></svg>
      <span class="bag-mobile__count"><?php echo esc_html( $rubor_count ); ?></span>
    </button>
  </div>
</header>

<div class="mobile-menu" data-menu hidden>
  <div class="mobile-menu__top">
    <span class="logo"><?php bloginfo( 'name' ); ?></span>
    <button class="icon-btn" data-menu-close aria-label="Cerrar menú">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
    </button>
  </div>
  <nav class="mobile-menu__nav" aria-label="Menú móvil">
    <?php
    rubor_menu( 'primary-menu', array() );
    rubor_menu( 'secondary-menu', array() );
    ?>
    <a href="<?php echo esc_url( $rubor_home ); ?>#tonos">Encuentra tu tono</a>
  </nav>
  <p class="mobile-menu__foot">Atención por WhatsApp · Lun a sáb, 9 a. m. – 7 p. m.</p>
</div>
