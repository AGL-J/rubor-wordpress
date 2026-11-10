<footer class="site-footer">
  <div class="site-footer__cols">
    <div>
      <h3>Tienda</h3>
      <ul>
        <li><a href="<?php echo esc_url( rubor_term_link( 'rostro' ) ); ?>">Rostro</a></li>
        <li><a href="<?php echo esc_url( rubor_term_link( 'ojos' ) ); ?>">Ojos</a></li>
        <li><a href="<?php echo esc_url( rubor_term_link( 'labios' ) ); ?>">Labios</a></li>
        <li><a href="<?php echo esc_url( rubor_term_link( 'piel' ) ); ?>">Piel</a></li>
        <li><a href="<?php echo esc_url( rubor_term_link( 'herramientas' ) ); ?>">Herramientas</a></li>
      </ul>
    </div>
    <div>
      <h3>Ayuda</h3>
      <ul class="site-footer__menu">
        <?php if ( has_nav_menu( 'footer-menu' ) ) : ?>
          <?php wp_nav_menu( array( 'theme_location' => 'footer-menu', 'container' => false, 'items_wrap' => '%3$s', 'depth' => 1 ) ); ?>
        <?php else : ?>
          <li><a href="<?php echo esc_url( rubor_page_link( 'envios' ) ); ?>">Envíos y devoluciones</a></li>
          <li><a href="<?php echo esc_url( rubor_page_link( 'contacto' ) ); ?>">Contacto</a></li>
        <?php endif; ?>
      </ul>
    </div>
    <div>
      <h3>Casa Rubor</h3>
      <address>
        Calle 79B #8-12, Bogotá<br>
        Lun a sáb · 10 a. m. – 7 p. m.<br>
        hola@rubor.co
      </address>
    </div>
    <div>
      <h3>Pagos</h3>
      <p>Contraentrega, Nequi y transferencia bancaria. Envíos a toda Colombia.</p>
    </div>
  </div>
  <p class="site-footer__word" aria-hidden="true"><?php bloginfo( 'name' ); ?></p>
  <div class="site-footer__legal">
    <p>© <?php echo esc_html( gmdate( 'Y' ) ); ?> Rubor Casa de Maquillaje · Proyecto académico de Sofía</p>
    <p><a href="<?php echo esc_url( get_privacy_policy_url() ? get_privacy_policy_url() : rubor_page_link( 'contacto' ) ); ?>">Privacidad</a> · <a href="<?php echo esc_url( rubor_page_link( 'envios' ) ); ?>">Envíos y devoluciones</a></p>
  </div>
</footer>

<?php if ( function_exists( 'woocommerce_mini_cart' ) ) : ?>
<div class="bag-scrim" data-cart-scrim hidden></div>
<aside class="bag" data-cart aria-label="Bolsa de compras" aria-hidden="true">
  <div class="bag__head">
    <h2>Tu bolsa</h2>
    <button class="icon-btn" data-cart-close aria-label="Cerrar bolsa">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
    </button>
  </div>
  <?php echo rubor_shipping_bar(); // phpcs:ignore ?>
  <div class="bag__body">
    <div class="widget_shopping_cart_content"><?php woocommerce_mini_cart(); ?></div>
  </div>
</aside>
<?php endif; ?>

<div class="toast" data-toast role="status" aria-live="polite"></div>

<?php wp_footer(); ?>
</body>
</html>
