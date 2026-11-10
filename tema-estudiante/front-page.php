<?php
/**
 * Portada de RUBOR.
 */
get_header();

$img       = get_template_directory_uri() . '/assets/img/';
$granada   = function_exists( 'wc_get_product_id_by_sku' ) ? wc_get_product_id_by_sku( 'RUB-LAB-01' ) : 0;
$shop_url  = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
$cat_count = function ( $slug ) {
	$t = get_term_by( 'slug', $slug, 'product_cat' );
	return $t ? (int) $t->count : 0;
};
?>
<main id="contenido">

  <!-- HERO -->
  <section class="hero" id="top">
    <div class="hero__copy">
      <h1 class="hero__title rise">
        <span class="line">Color que</span>
        <span class="line">se queda</span>
        <span class="line"><em>contigo.</em></span>
      </h1>
      <p class="hero__lede rise">Labiales de pigmento alto, bases que respiran y tonos pensados para la piel latina. Hechos en Bogotá, en lotes pequeños.</p>
      <div class="hero__actions rise">
        <a class="btn btn--dark" href="<?php echo esc_url( $shop_url ); ?>">Comprar la colección</a>
        <a class="link-arrow" href="#tonos">Probar los 8 tonos <span aria-hidden="true">→</span></a>
      </div>
    </div>

    <figure class="hero__media">
      <div class="hero__frame">
        <img src="<?php echo esc_url( $img ); ?>hero-labios.webp" srcset="<?php echo esc_url( $img ); ?>hero-labios-760.webp 760w, <?php echo esc_url( $img ); ?>hero-labios.webp 1400w" sizes="(max-width: 860px) calc(100vw - 32px), 46vw" alt="Retrato de una mujer de cabello cobrizo con labial rojo intenso" width="1400" height="2100" fetchpriority="high">
      </div>
      <figcaption class="hero__tag">
        <span class="hero__tag-swatch" style="--c:#8E1B2B"></span>
        <span>
          <strong>Labial Mate N.º 01</strong>
          Granada · $58.000
        </span>
        <?php if ( $granada ) : ?>
        <a href="<?php echo esc_url( '?add-to-cart=' . $granada ); ?>" class="hero__tag-add add_to_cart_button ajax_add_to_cart" data-product_id="<?php echo esc_attr( $granada ); ?>" data-quantity="1" rel="nofollow" aria-label="Añadir Labial Mate Granada a la bolsa">+</a>
        <?php endif; ?>
      </figcaption>
      <span class="hero__vertical" aria-hidden="true">Colección Otoño 2026 — Rubor</span>
    </figure>

    <ul class="hero__facts rise">
      <li><span>48 h</span>Envío a ciudades principales</li>
      <li><span>82%</span>Del catálogo es vegano</li>
      <li><span>30 días</span>Para cambios y devoluciones</li>
    </ul>
  </section>

  <!-- CATEGORÍAS -->
  <section class="cats" aria-labelledby="cats-title">
    <header class="section-head">
      <p class="eyebrow">Por dónde empezar</p>
      <h2 id="cats-title" class="section-title">Cinco cajones, <em>un tocador.</em></h2>
    </header>

    <div class="cats__grid">
      <?php
      $cats = array(
        array( 'rostro', 'a', 'Rostro', 'Bases, polvos y rubores', 'rostro-polvos.webp', 'Polvos sueltos y brochas sobre un tocador' ),
        array( 'ojos', 'b', 'Ojos', 'Sombras y máscaras', 'ojos-paleta.webp', 'Mano sosteniendo una paleta de sombras en tonos tierra' ),
        array( 'labios', 'c', 'Labios', 'Mates y bálsamos', 'labial.webp', 'Labial rosado destapado sobre fondo blanco' ),
        array( 'piel', 'd', 'Piel', 'Sérums y cremas', 'piel-guasha.webp', 'Gua sha de cuarzo rosa junto a un sérum' ),
        array( 'herramientas', 'e', 'Herramientas', 'Brochas y esponjas', 'brochas.webp', 'Brochas de maquillaje en dos vasos' ),
      );
      foreach ( $cats as $i => $c ) :
        $n = $cat_count( $c[0] );
        ?>
        <a class="cat cat--<?php echo esc_attr( $c[1] ); ?> reveal" href="<?php echo esc_url( rubor_term_link( $c[0] ) ); ?>">
          <img src="<?php echo esc_url( $img . $c[4] ); ?>" alt="<?php echo esc_attr( $c[5] ); ?>" loading="lazy">
          <span class="cat__num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
          <span class="cat__name"><?php echo esc_html( $c[2] ); ?></span>
          <span class="cat__meta"><?php echo esc_html( $c[3] . ( $n ? ' · ' . $n : '' ) ); ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- TONOS -->
  <section class="shades" id="tonos" aria-labelledby="shades-title">
    <div class="shades__inner">
      <header class="shades__head">
        <p class="eyebrow eyebrow--light">Estudio de color</p>
        <h2 id="shades-title" class="section-title section-title--light">Encuentra <em>tu tono</em> de labial.</h2>
        <p class="shades__intro">Elige el subtono de tu piel y prueba los ocho mates de la línea Permanencia. Cada uno dura ocho horas sin resecar.</p>
        <div class="undertone" role="radiogroup" aria-label="Subtono de piel">
          <button role="radio" aria-checked="true" data-undertone="todos">Todos</button>
          <button role="radio" aria-checked="false" data-undertone="calido">Cálido</button>
          <button role="radio" aria-checked="false" data-undertone="neutro">Neutro</button>
          <button role="radio" aria-checked="false" data-undertone="frio">Frío</button>
        </div>
      </header>

      <div class="shades__stage">
        <div class="shades__swatch" data-shade-swatch style="--shade:#8E1B2B">
          <svg class="shades__lips" viewBox="0 0 200 90" aria-hidden="true">
            <path d="M8 46c18-8 40-34 62-34 12 0 20 8 30 8s18-8 30-8c22 0 44 26 62 34-20 4-38 38-92 38S28 50 8 46Z"/>
            <path class="shades__lips-line" d="M8 46c30 6 52 10 92 10s62-4 92-10"/>
          </svg>
        </div>
        <div class="shades__info" aria-live="polite">
          <p class="shades__num" data-shade-num>N.º 01</p>
          <p class="shades__name" data-shade-name>Granada</p>
          <p class="shades__desc" data-shade-desc>Rojo de fondo azul. Hace ver los dientes más blancos y funciona de día o de noche.</p>
          <p class="shades__tone" data-shade-tone>Subtono frío</p>
          <div class="shades__buy">
            <span class="shades__price" data-shade-price>$58.000</span>
            <a href="<?php echo esc_url( $granada ? '?add-to-cart=' . $granada : $shop_url ); ?>" class="btn btn--light add_to_cart_button ajax_add_to_cart" data-shade-add data-product_id="<?php echo esc_attr( $granada ); ?>" data-quantity="1" rel="nofollow">Añadir a la bolsa</a>
          </div>
        </div>
      </div>

      <ul class="shades__list" data-shade-list aria-label="Tonos disponibles"></ul>
    </div>
  </section>

  <!-- TIENDA -->
  <section class="shop" id="tienda" aria-labelledby="shop-title">
    <header class="section-head section-head--split">
      <div>
        <p class="eyebrow">Los más pedidos</p>
        <h2 id="shop-title" class="section-title">No vuelven <em>al estante.</em></h2>
      </div>
      <div class="filters" role="tablist" aria-label="Filtrar productos">
        <button role="tab" aria-selected="true" data-filter="todos">Todos</button>
        <button role="tab" aria-selected="false" data-filter="labios">Labios</button>
        <button role="tab" aria-selected="false" data-filter="rostro">Rostro</button>
        <button role="tab" aria-selected="false" data-filter="ojos">Ojos</button>
        <button role="tab" aria-selected="false" data-filter="piel">Piel</button>
      </div>
    </header>

    <div class="products">
      <?php
      if ( function_exists( 'wc_get_products' ) ) {
        $featured = wc_get_products( array( 'featured' => true, 'limit' => 8, 'status' => 'publish', 'orderby' => 'date', 'order' => 'ASC' ) );
        foreach ( $featured as $product ) {
          rubor_product_card( $product );
        }
      }
      if ( empty( $featured ) ) :
        ?>
        <p class="shop__empty">Activa WooCommerce e importa el contenido desde <strong>Apariencia → RUBOR</strong> para ver el catálogo.</p>
      <?php endif; ?>
    </div>

    <p class="shop__more"><a class="link-arrow" href="<?php echo esc_url( $shop_url ); ?>">Ver toda la tienda <span aria-hidden="true">→</span></a></p>
  </section>

  <!-- RITUAL -->
  <section class="ritual" aria-labelledby="ritual-title">
    <div class="ritual__media">
      <img src="<?php echo esc_url( $img ); ?>ritual-aplicacion.webp" alt="Maquilladora aplicando labial rosado a una modelo" loading="lazy">
    </div>
    <div class="ritual__copy">
      <p class="eyebrow">El ritual de diez minutos</p>
      <h2 id="ritual-title" class="section-title">Tres pasos. <em>Ni uno más.</em></h2>
      <ol class="steps">
        <li class="step reveal">
          <span class="step__num">i.</span>
          <div>
            <h3>Preparar</h3>
            <p>Dos gotas de Sérum Luz sobre la piel húmeda. Espera un minuto: la base se asienta mejor sobre una piel hidratada.</p>
            <a href="<?php echo esc_url( rubor_term_link( 'piel' ) ); ?>">Ver cuidado de la piel</a>
          </div>
        </li>
        <li class="step reveal">
          <span class="step__num">ii.</span>
          <div>
            <h3>Unificar</h3>
            <p>Base Segunda Piel con los dedos, del centro hacia afuera. Sella solo la zona T con Polvo Seda.</p>
            <a href="<?php echo esc_url( rubor_term_link( 'rostro' ) ); ?>">Ver rostro</a>
          </div>
        </li>
        <li class="step reveal">
          <span class="step__num">iii.</span>
          <div>
            <h3>Dar color</h3>
            <p>Un mate en labios y el mismo tono, difuminado, en las mejillas. Un solo color hace todo el trabajo.</p>
            <a href="#tonos">Ver los 8 mates</a>
          </div>
        </li>
      </ol>
    </div>
  </section>

  <!-- PRINCIPIOS -->
  <section class="principles" aria-labelledby="principles-title">
    <h2 id="principles-title" class="visually-hidden">Nuestros principios</h2>
    <p class="principles__big reveal">Hacemos menos productos, <em>pero los hacemos bien</em>: fórmulas cortas, pigmento honesto y envases que vuelven a casa.</p>
    <dl class="principles__list">
      <div class="reveal"><dt>Libre de crueldad</dt><dd>Ningún producto ni ingrediente se prueba en animales, en ninguna etapa.</dd></div>
      <div class="reveal"><dt>Recargables</dt><dd>Labiales y polvos tienen repuesto. El estuche se queda; el plástico, no.</dd></div>
      <div class="reveal"><dt>Para tonos reales</dt><dd>42 tonos de base probados en pieles de todo el país antes de salir.</dd></div>
      <div class="reveal"><dt>Hecho aquí</dt><dd>Formulamos y envasamos en Bogotá con laboratorios certificados.</dd></div>
    </dl>
  </section>

  <!-- TESTIMONIO -->
  <section class="quote" aria-label="Lo que dicen nuestras clientas">
    <figure class="quote__inner">
      <img class="quote__img" src="<?php echo esc_url( $img ); ?>manos-crema.webp" alt="Manos abriendo un frasco de crema" loading="lazy">
      <blockquote>
        <p>“Llevaba años mezclando dos bases para dar con mi color. En Rubor me lo encontraron en la primera prueba, y la muestra llegó con el pedido.”</p>
      </blockquote>
      <figcaption>
        <strong>Valentina Restrepo</strong>
        Medellín · Base Segunda Piel, tono 24C
      </figcaption>
    </figure>
  </section>

  <!-- DIARIO: últimas entradas del blog -->
  <?php $posts_page = get_option( 'page_for_posts' ); ?>
  <section class="journal" id="diario" aria-labelledby="journal-title">
    <header class="section-head section-head--split">
      <div>
        <p class="eyebrow">Diario Rubor</p>
        <h2 id="journal-title" class="section-title">Notas desde <em>el tocador.</em></h2>
      </div>
      <a class="link-arrow" href="<?php echo esc_url( $posts_page ? get_permalink( $posts_page ) : home_url( '/' ) ); ?>">Ver todas las notas <span aria-hidden="true">→</span></a>
    </header>
    <div class="journal__grid">
      <?php
      $journal = new WP_Query( array( 'posts_per_page' => 3, 'ignore_sticky_posts' => true ) );
      while ( $journal->have_posts() ) :
        $journal->the_post();
        get_template_part( 'parts/post-card' );
      endwhile;
      wp_reset_postdata();
      ?>
    </div>
  </section>

  <!-- CLUB -->
  <section class="club" aria-labelledby="club-title">
    <div class="club__media">
      <img src="<?php echo esc_url( $img ); ?>terracota.webp" alt="Frascos de cosméticos ordenados sobre fondo terracota" loading="lazy">
    </div>
    <div class="club__copy">
      <p class="eyebrow">Club Rubor</p>
      <h2 id="club-title" class="section-title">15% en tu <em>primer pedido.</em></h2>
      <p>Una carta al mes con lanzamientos, guías de tono y preventas antes que nadie. Sin relleno.</p>
      <?php echo rubor_cf7( 'Club Rubor', 'club__form' ); // phpcs:ignore ?>
      <p class="club__note">Al suscribirte aceptas nuestra política de datos. Puedes salir cuando quieras.</p>
    </div>
  </section>

</main>
<?php
get_footer();
