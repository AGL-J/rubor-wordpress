<?php
/**
 * Rubor — funciones del tema.
 *
 * Plugins que usa el tema:
 * - WooCommerce: catálogo, bolsa, pago y pedidos.
 * - Contact Form 7 + Flamingo: formularios de contacto y del Club, con registro de mensajes.
 * - Yoast SEO: títulos y descripciones para buscadores.
 * - Joinchat: botón flotante de WhatsApp.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RUBOR_VERSION', '3.0.0' );

require get_template_directory() . '/inc/demo.php';

/* ---------- Configuración ---------- */

function rubor_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array(
		'height'      => 100,
		'width'       => 400,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'woocommerce', array(
		'thumbnail_image_width' => 600,
		'single_image_width'    => 900,
	) );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );

	register_nav_menus( array(
		'primary-menu'   => __( 'Menú Principal', 'tema-estudiante' ),
		'secondary-menu' => __( 'Menú Secundario', 'tema-estudiante' ),
		'footer-menu'    => __( 'Menú del Pie de Página', 'tema-estudiante' ),
	) );
}
add_action( 'after_setup_theme', 'rubor_setup' );

function rubor_assets() {
	$uri = get_template_directory_uri();

	wp_enqueue_style( 'rubor-fuentes', 'https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..700;1,6..96,400..700&family=Instrument+Sans:wght@400;500;600&display=swap', array(), null );
	wp_enqueue_style( 'tema-estudiante-style', get_stylesheet_uri(), array(), RUBOR_VERSION );
	wp_enqueue_style( 'rubor', $uri . '/assets/css/rubor.css', array( 'rubor-fuentes', 'tema-estudiante-style' ), RUBOR_VERSION );
	wp_enqueue_style( 'rubor-wp', $uri . '/assets/css/rubor-wp.css', array( 'rubor' ), RUBOR_VERSION );

	$deps = array( 'jquery' );
	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_script( 'wc-add-to-cart' );
		wp_enqueue_script( 'wc-cart-fragments' );
		$deps[] = 'wc-add-to-cart';
	}
	wp_enqueue_script( 'rubor', $uri . '/assets/js/rubor.js', $deps, RUBOR_VERSION, true );
	wp_add_inline_script( 'rubor', 'window.RUBOR = ' . wp_json_encode( array(
		'shades'  => rubor_shades(),
		'cartUrl' => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '',
	) ) . ';', 'before' );
}
add_action( 'wp_enqueue_scripts', 'rubor_assets' );

function rubor_preconnect( $urls, $relation ) {
	if ( 'preconnect' === $relation ) {
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'rubor_preconnect', 10, 2 );

/* ---------- Rendimiento: nada bloquea el primer pintado ---------- */

// Los scripts que WordPress y los plugins ponen en <head> (jQuery incluido) se imprimen al final del <body>.
function rubor_scripts_al_pie() {
	if ( ! is_admin() ) {
		remove_action( 'wp_head', 'wp_print_head_scripts', 9 );
		add_action( 'wp_footer', 'wp_print_head_scripts', 5 );
	}
}
add_action( 'wp_enqueue_scripts', 'rubor_scripts_al_pie' );

// Las fuentes de Google cargan sin bloquear; mientras llegan se ve la fuente del sistema (display=swap).
function rubor_fuentes_sin_bloqueo( $html, $handle, $href ) {
	if ( 'rubor-fuentes' !== $handle ) {
		return $html;
	}
	return '<link rel="preload" as="style" href="' . esc_url( $href ) . '" onload="this.onload=null;this.rel=\'stylesheet\'">'
		. '<noscript><link rel="stylesheet" href="' . esc_url( $href ) . '"></noscript>' . "\n";
}
add_filter( 'style_loader_tag', 'rubor_fuentes_sin_bloqueo', 10, 3 );

/* ---------- Tonos de labial (estudio de color) ---------- */

function rubor_shade_list() {
	return array(
		array( 'num' => '01', 'name' => 'Granada',   'hex' => '#8E1B2B', 'tone' => 'frio',   'desc' => 'Rojo de fondo azul. Hace ver los dientes más blancos y funciona de día o de noche.' ),
		array( 'num' => '02', 'name' => 'Arcilla',   'hex' => '#A55A45', 'tone' => 'calido', 'desc' => 'Terracota suave con fondo naranja. El tono de todos los días para pieles doradas.' ),
		array( 'num' => '03', 'name' => 'Guayaba',   'hex' => '#D9686A', 'tone' => 'calido', 'desc' => 'Coral rosado, luminoso y fresco. Pide poco: una capa y listo.' ),
		array( 'num' => '04', 'name' => 'Malva',     'hex' => '#9C6474', 'tone' => 'frio',   'desc' => 'Rosa empolvado con un toque gris. Elegante sin parecer serio.' ),
		array( 'num' => '05', 'name' => 'Canela',    'hex' => '#8A4B36', 'tone' => 'calido', 'desc' => 'Marrón cálido, el clásico de los noventa con acabado aterciopelado.' ),
		array( 'num' => '06', 'name' => 'Ciruela',   'hex' => '#5B2033', 'tone' => 'frio',   'desc' => 'Vino profundo. Para la noche, o para quien no le teme al color.' ),
		array( 'num' => '07', 'name' => 'Pétalo',    'hex' => '#C98B8E', 'tone' => 'neutro', 'desc' => 'El color de tus labios, pero mejor. Va con cualquier subtono.' ),
		array( 'num' => '08', 'name' => 'Nude Café', 'hex' => '#A9776A', 'tone' => 'neutro', 'desc' => 'Nude con cuerpo, pensado para pieles morenas y trigueñas.' ),
	);
}

/** Los tonos con el ID y precio del producto de WooCommerce que les corresponde (SKU RUB-LAB-01…08). */
function rubor_shades() {
	$shades = rubor_shade_list();
	foreach ( $shades as &$s ) {
		$s['id']    = 0;
		$s['price'] = '';
		if ( function_exists( 'wc_get_product_id_by_sku' ) ) {
			$id = wc_get_product_id_by_sku( 'RUB-LAB-' . $s['num'] );
			if ( $id && ( $p = wc_get_product( $id ) ) ) {
				$s['id']    = $id;
				$s['price'] = html_entity_decode( wp_strip_all_tags( wc_price( $p->get_price() ) ), ENT_QUOTES, 'UTF-8' );
			}
		}
	}
	return $shades;
}

/* ---------- WooCommerce ---------- */

/** Contador y mini bolsa se actualizan solos al añadir productos (fragmentos AJAX de WooCommerce). */
function rubor_cart_fragments( $fragments ) {
	$fragments['span.bag-btn__count']    = '<span class="bag-btn__count">' . WC()->cart->get_cart_contents_count() . '</span>';
	$fragments['span.bag-mobile__count'] = '<span class="bag-mobile__count">' . WC()->cart->get_cart_contents_count() . '</span>';
	$fragments['div.bag__ship']         = rubor_shipping_bar();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'rubor_cart_fragments' );

/** Barra de "te faltan $X para el envío gratis". El mínimo sale de la zona de envío gratis de WooCommerce. */
function rubor_free_shipping_min() {
	$min = 150000;
	if ( class_exists( 'WC_Shipping_Zones' ) ) {
		foreach ( WC_Shipping_Zones::get_zones() as $zone ) {
			foreach ( $zone['shipping_methods'] as $method ) {
				if ( 'free_shipping' === $method->id && 'yes' === $method->enabled && $method->min_amount ) {
					return (float) $method->min_amount;
				}
			}
		}
	}
	return $min;
}

function rubor_shipping_bar() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return '<div class="bag__ship"></div>';
	}
	$min   = rubor_free_shipping_min();
	$total = (float) WC()->cart->get_displayed_subtotal();
	$left  = $min - $total;
	$pct   = $min > 0 ? min( 100, $total / $min * 100 ) : 100;
	$text  = $left > 0
		? sprintf( 'Te faltan %s para el envío gratis.', wc_price( $left ) )
		: 'Tu envío es gratis.';
	return '<div class="bag__ship"><p>' . wp_kses_post( $text ) . '</p><div class="bag__bar"><span style="--p:' . esc_attr( round( $pct ) / 100 ) . '"></span></div></div>';
}

/** Tarjeta de producto con el diseño de RUBOR (portada y tienda). */
function rubor_product_card( $product ) {
	$id     = $product->get_id();
	$terms  = get_the_terms( $id, 'product_cat' );
	$cat    = $terms && ! is_wp_error( $terms ) ? $terms[0]->slug : '';
	$badge  = $product->get_meta( '_rubor_badge' );
	$type   = $product->get_meta( '_rubor_type' );
	$dots   = array_filter( explode( ',', (string) $product->get_meta( '_rubor_dots' ) ) );
	$rating = $product->get_average_rating();
	$count  = $product->get_review_count();
	?>
	<article class="product" data-cat="<?php echo esc_attr( $cat ); ?>">
		<div class="product__media">
			<a href="<?php echo esc_url( get_permalink( $id ) ); ?>" tabindex="-1" aria-hidden="true">
				<?php echo $product->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) ); // phpcs:ignore ?>
			</a>
			<?php if ( $badge ) : ?><span class="product__badge"><?php echo esc_html( $badge ); ?></span><?php endif; ?>
			<?php if ( $product->is_purchasable() && $product->is_in_stock() ) : ?>
			<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $id ); ?>" data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>" class="product__add add_to_cart_button ajax_add_to_cart" rel="nofollow" aria-label="<?php echo esc_attr( 'Añadir ' . $product->get_name() . ' a la bolsa' ); ?>">Añadir<span class="product__add-price"> · <?php echo wp_strip_all_tags( wc_price( $product->get_price() ) ); // phpcs:ignore ?></span></a>
			<?php endif; ?>
		</div>
		<div class="product__row">
			<h3 class="product__name"><a href="<?php echo esc_url( get_permalink( $id ) ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>
			<span class="product__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
		</div>
		<?php if ( $type ) : ?><p class="product__type"><?php echo esc_html( $type ); ?></p><?php endif; ?>
		<div class="product__foot">
			<span class="product__dots"><?php foreach ( $dots as $c ) : ?><i style="--c:<?php echo esc_attr( trim( $c ) ); ?>"></i><?php endforeach; ?></span>
			<?php if ( $count ) : ?><span>★ <?php echo esc_html( number_format_i18n( $rating, 1 ) . ' · ' . $count ); ?></span><?php endif; ?>
		</div>
	</article>
	<?php
}

/* El tema pone su propio contenedor alrededor de las páginas de WooCommerce. */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

add_filter( 'woocommerce_show_page_title', '__return_false' );
add_filter( 'loop_shop_columns', function () { return 4; } );
add_filter( 'loop_shop_per_page', function () { return 16; } );
add_filter( 'woocommerce_output_related_products_args', function ( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
} );

/* La tienda usa la tarjeta propia del tema en lugar de la de WooCommerce. */
function rubor_loop_card() {
	global $product;
	rubor_product_card( $product );
}
function rubor_swap_loop_card() {
	remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
	remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	add_action( 'woocommerce_before_shop_loop_item', 'rubor_loop_card', 10 );
}
add_action( 'init', 'rubor_swap_loop_card' );

/* ---------- Contact Form 7 ---------- */

/*
 * El sitio de demostración corre en local, sin servidor de correo.
 * Los mensajes no se envían por email: Flamingo los guarda en el panel (Flamingo → Mensajes entrantes).
 * Para producción, borrar esta línea y configurar el correo del hosting.
 */
add_filter( 'wpcf7_skip_mail', '__return_true' );

/** Muestra un formulario de Contact Form 7 buscándolo por su título. */
function rubor_cf7( $title, $class = '' ) {
	if ( ! function_exists( 'wpcf7_contact_form' ) ) {
		return '';
	}
	$forms = get_posts( array( 'post_type' => 'wpcf7_contact_form', 'title' => $title, 'numberposts' => 1 ) );
	if ( ! $forms ) {
		return '';
	}
	return do_shortcode( sprintf( '[contact-form-7 id="%d" html_class="%s"]', $forms[0]->ID, esc_attr( $class ) ) );
}

/* ---------- Menús ---------- */

/** Pinta un menú de WordPress; si aún no existe, usa enlaces por defecto. */
function rubor_menu( $location, $fallback ) {
	if ( has_nav_menu( $location ) ) {
		wp_nav_menu( array(
			'theme_location' => $location,
			'container'      => false,
			'items_wrap'     => '%3$s',
			'depth'          => 1,
			'walker'         => new Rubor_Link_Walker(),
		) );
		return;
	}
	foreach ( $fallback as $label => $url ) {
		printf( '<a href="%s">%s</a>', esc_url( $url ), esc_html( $label ) );
	}
}

/** Menú plano: solo enlaces <a>, sin listas, para que encaje en el diseño. */
class Rubor_Link_Walker extends Walker_Nav_Menu {
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$current = in_array( 'current-menu-item', (array) $item->classes, true ) ? ' aria-current="page"' : '';
		$output .= sprintf( '<a href="%s"%s>%s</a>', esc_url( $item->url ), $current, esc_html( $item->title ) );
	}
	public function end_el( &$output, $item, $depth = 0, $args = null ) {}
}

function rubor_term_link( $slug ) {
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	return $term ? get_term_link( $term ) : home_url( '/#tienda' );
}

function rubor_page_link( $path ) {
	$page = get_page_by_path( $path );
	return $page ? get_permalink( $page ) : home_url( '/' );
}

/* ---------- Barra lateral (sidebar.php) ---------- */

function rubor_widgets() {
	register_sidebar( array(
		'name'          => __( 'Barra lateral del Diario', 'tema-estudiante' ),
		'id'            => 'sidebar-diario',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'rubor_widgets' );

/* ---------- Rol personalizado: Redactor Jr. ---------- */

/*
 * Escribe borradores y sube imágenes, pero NO publica: sus entradas quedan "Pendiente de revisión"
 * hasta que un Editor (o Administrador) las aprueba.
 */
function rubor_rol_redactor_jr() {
	if ( get_role( 'redactor_jr' ) ) {
		return;
	}
	add_role( 'redactor_jr', __( 'Redactor Jr.', 'tema-estudiante' ), array(
		'read'           => true,  // Entrar al panel
		'edit_posts'     => true,  // Crear y editar sus propias entradas (borradores)
		'upload_files'   => true,  // Subir imágenes a la Biblioteca de medios
		'delete_posts'   => true,  // Borrar sus propios borradores
		'publish_posts'  => false, // No publica: requiere aprobación de un Editor
		'edit_published_posts' => false,
		'edit_others_posts'    => false,
		'edit_pages'     => false,
	) );
}
add_action( 'init', 'rubor_rol_redactor_jr' );

/* ---------- Bastionado (hardening) desde el tema ---------- */

remove_action( 'wp_head', 'wp_generator' );   // No mostrar la versión de WordPress en el HTML
add_filter( 'the_generator', '__return_empty_string' ); // …ni en los feeds RSS
add_filter( 'xmlrpc_enabled', '__return_false' );        // Cierra XML-RPC, objetivo común de fuerza bruta
add_action( 'init', function () {
	remove_action( 'wp_head', 'webp_uploads_render_generator' ); // Tampoco la versión del plugin de imágenes
} );
