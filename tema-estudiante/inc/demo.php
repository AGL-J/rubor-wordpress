<?php
/**
 * Rubor — panel del tema e importador de contenido de demostración.
 *
 * Apariencia → RUBOR: revisa los plugins requeridos y crea, con un clic,
 * productos de WooCommerce, entradas del Diario, páginas, formularios y menús.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function rubor_required_plugins() {
	return array(
		'woocommerce/woocommerce.php'                         => array( 'WooCommerce', 'Catálogo, bolsa de compras, pago y pedidos.' ),
		'contact-form-7/wp-contact-form-7.php'                => array( 'Contact Form 7', 'Formulario de contacto y del Club Rubor.' ),
		'flamingo/flamingo.php'                               => array( 'Flamingo', 'Guarda en el panel los mensajes de los formularios.' ),
		'wordpress-seo/wp-seo.php'                            => array( 'Yoast SEO', 'Títulos, descripciones y mapa del sitio para Google.' ),
		'creame-whatsapp-me/joinchat.php'                     => array( 'Joinchat', 'Botón flotante de WhatsApp para atención y asesoría de tono.' ),
		'cache-enabler/cache-enabler.php'                     => array( 'Cache Enabler', 'Caché de página: guarda cada página como HTML estático para cargar más rápido.' ),
		'webp-uploads/load.php'                               => array( 'Modern Image Formats', 'Convierte a WebP las imágenes que se suben a la Biblioteca de medios.' ),
		'query-monitor/query-monitor.php'                     => array( 'Query Monitor', 'Auditoría: muestra consultas a la base de datos y tiempos de carga.' ),
	);
}

function rubor_admin_menu() {
	add_theme_page( 'RUBOR', 'RUBOR', 'manage_options', 'rubor', 'rubor_admin_page' );
}
add_action( 'admin_menu', 'rubor_admin_menu' );

function rubor_admin_notice() {
	$missing = array_filter( array_keys( rubor_required_plugins() ), function ( $f ) { return ! is_plugin_active( $f ); } );
	if ( $missing && current_user_can( 'activate_plugins' ) ) {
		echo '<div class="notice notice-warning"><p><strong>RUBOR</strong> necesita algunos plugins. <a href="' . esc_url( admin_url( 'themes.php?page=rubor' ) ) . '">Ver cuáles</a>.</p></div>';
	}
}
add_action( 'admin_notices', 'rubor_admin_notice' );

function rubor_admin_page() {
	$log = null;
	if ( isset( $_POST['rubor_import'] ) && check_admin_referer( 'rubor_import' ) ) {
		$log = rubor_demo_import();
	}
	?>
	<div class="wrap">
		<h1>RUBOR · Casa de maquillaje</h1>
		<h2>Plugins requeridos</h2>
		<table class="widefat striped" style="max-width:820px">
			<thead><tr><th>Plugin</th><th>Para qué se usa</th><th>Estado</th></tr></thead>
			<tbody>
			<?php foreach ( rubor_required_plugins() as $file => $info ) : $on = is_plugin_active( $file ); ?>
				<tr>
					<td><strong><?php echo esc_html( $info[0] ); ?></strong></td>
					<td><?php echo esc_html( $info[1] ); ?></td>
					<td><?php echo $on ? '<span style="color:#1a7f37">● Activo</span>' : '<a href="' . esc_url( admin_url( 'plugin-install.php?s=' . rawurlencode( $info[0] ) . '&tab=search&type=term' ) ) . '">Instalar</a>'; ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

		<h2 style="margin-top:32px">Contenido de demostración</h2>
		<p>Crea productos con fotos, entradas del Diario, páginas, formularios, menús y la configuración de WooCommerce para Colombia. Se puede ejecutar varias veces: no duplica lo que ya existe.</p>
		<form method="post">
			<?php wp_nonce_field( 'rubor_import' ); ?>
			<p><button class="button button-primary button-hero" name="rubor_import" value="1">Importar contenido RUBOR</button></p>
		</form>
		<?php if ( $log ) : ?>
			<div class="notice notice-success inline"><p><strong>Listo.</strong></p><ul style="list-style:disc;padding-left:20px"><?php foreach ( $log as $line ) { echo '<li>' . esc_html( $line ) . '</li>'; } ?></ul></div>
		<?php endif; ?>
	</div>
	<?php
}

/* ---------- Utilidades ---------- */

/** Sube una imagen del tema a la Biblioteca de medios (una sola vez) y devuelve su ID. */
function rubor_demo_media( $file, $title ) {
	$found = get_posts( array( 'post_type' => 'attachment', 'meta_key' => '_rubor_src', 'meta_value' => $file, 'numberposts' => 1, 'fields' => 'ids' ) );
	if ( $found ) {
		return $found[0];
	}
	$path = get_template_directory() . '/assets/img/' . $file;
	if ( ! file_exists( $path ) ) {
		return 0;
	}
	$upload = wp_upload_bits( basename( $file ), null, file_get_contents( $path ) );
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$id = wp_insert_attachment( array(
		'post_title'     => $title,
		'post_mime_type' => wp_check_filetype( $upload['file'] )['type'],
		'post_status'    => 'inherit',
	), $upload['file'] );
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	update_post_meta( $id, '_wp_attachment_image_alt', $title );
	update_post_meta( $id, '_rubor_src', $file );
	return $id;
}

function rubor_demo_term( $name, $slug, $taxonomy, $description = '' ) {
	$term = get_term_by( 'slug', $slug, $taxonomy );
	if ( $term ) {
		return (int) $term->term_id;
	}
	$res = wp_insert_term( $name, $taxonomy, array( 'slug' => $slug, 'description' => $description ) );
	return is_wp_error( $res ) ? 0 : (int) $res['term_id'];
}

function rubor_demo_page( $title, $slug, $content = '' ) {
	$page = get_page_by_path( $slug );
	if ( $page ) {
		return $page->ID;
	}
	return wp_insert_post( array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_content' => $content,
	) );
}

/* ---------- Importador ---------- */

function rubor_demo_import() {
	$log = array();
	@set_time_limit( 300 );

	// Modern Image Formats: las imágenes que se suban se convierten a WebP.
	update_option( 'perflab_modern_image_format', 'webp' );

	if ( class_exists( 'WooCommerce' ) ) {
		$log = array_merge( $log, rubor_demo_woocommerce() );
	} else {
		$log[] = 'WooCommerce no está activo: se omitieron los productos.';
	}
	$log = array_merge( $log, rubor_demo_forms() );
	$log = array_merge( $log, rubor_demo_posts() );
	$log = array_merge( $log, rubor_demo_pages() );
	$log = array_merge( $log, rubor_demo_menus() );
	$log = array_merge( $log, rubor_demo_whatsapp() );
	$log = array_merge( $log, rubor_demo_roles() );

	update_option( 'permalink_structure', '/%postname%/' );
	flush_rewrite_rules();
	update_option( 'rubor_demo_done', time() );
	$log[] = 'Enlaces permanentes actualizados.';
	return $log;
}

function rubor_demo_woocommerce() {
	$log = array();

	// Tienda en Colombia: pesos sin decimales, IVA incluido.
	update_option( 'woocommerce_currency', 'COP' );
	update_option( 'woocommerce_price_num_decimals', 0 );
	update_option( 'woocommerce_price_thousand_sep', '.' );
	update_option( 'woocommerce_price_decimal_sep', ',' );
	update_option( 'woocommerce_currency_pos', 'left' );
	update_option( 'woocommerce_default_country', 'CO:DC' );
	update_option( 'woocommerce_store_address', 'Calle 79B #8-12' );
	update_option( 'woocommerce_store_city', 'Bogotá' );
	update_option( 'woocommerce_specific_allowed_countries', array( 'CO' ) );
	update_option( 'woocommerce_allowed_countries', 'specific' );
	update_option( 'woocommerce_ship_to_countries', 'specific' );
	update_option( 'woocommerce_specific_ship_to_countries', array( 'CO' ) );
	update_option( 'woocommerce_default_customer_address', 'base' );
	update_option( 'woocommerce_coming_soon', 'no' );
	update_option( 'woocommerce_task_list_hidden', 'yes' );
	update_option( 'woocommerce_enable_reviews', 'yes' );
	update_option( 'woocommerce_onboarding_profile', array( 'skipped' => true ) );
	update_option( 'woocommerce_cart_redirect_after_add', 'no' );
	update_option( 'woocommerce_enable_ajax_add_to_cart', 'yes' );

	// Métodos de pago de demostración (no mueven dinero real).
	update_option( 'woocommerce_cod_settings', array(
		'enabled'      => 'yes',
		'title'        => 'Pago contra entrega',
		'description'  => 'Pagas en efectivo o con Nequi cuando recibes tu pedido.',
		'instructions' => 'Ten el valor exacto o tu app de Nequi a la mano al recibir el pedido.',
	) );
	update_option( 'woocommerce_bacs_settings', array(
		'enabled'      => 'yes',
		'title'        => 'Transferencia bancaria',
		'description'  => 'Transfiere a nuestra cuenta y envíanos el comprobante. Despachamos al confirmar el pago.',
		'instructions' => 'Usa el número de pedido como referencia de la transferencia.',
	) );
	$log[] = 'WooCommerce: pesos colombianos, pago contra entrega y transferencia activados.';

	if ( class_exists( 'WC_Install' ) ) {
		WC_Install::create_pages();
	}

	// Direcciones en español: /tienda/, /producto/…, /categoria/…
	update_option( 'woocommerce_permalinks', array_merge( (array) get_option( 'woocommerce_permalinks', array() ), array(
		'product_base'  => 'producto',
		'category_base' => 'categoria',
		'tag_base'      => 'etiqueta-producto',
	) ) );
	$wc_pages = array(
		'shop'      => array( 'Tienda', 'tienda' ),
		'cart'      => array( 'Carrito', 'carrito' ),
		'checkout'  => array( 'Finalizar compra', 'finalizar-compra' ),
		'myaccount' => array( 'Mi cuenta', 'mi-cuenta' ),
	);
	foreach ( $wc_pages as $key => $page ) {
		$pid = wc_get_page_id( $key );
		if ( $pid > 0 ) {
			wp_update_post( array( 'ID' => $pid, 'post_title' => $page[0], 'post_name' => $page[1] ) );
		}
	}
	$terms_page = rubor_demo_page( 'Términos y condiciones', 'terminos', '<p>Este sitio es un proyecto académico. Los productos, precios y pedidos son de demostración y no generan cobros reales.</p>' );
	update_option( 'woocommerce_terms_page_id', $terms_page );

	// Envíos: tarifa fija y envío gratis desde $150.000.
	if ( class_exists( 'WC_Shipping_Zone' ) && ! get_option( 'rubor_zone_id' ) ) {
		$zone = new WC_Shipping_Zone();
		$zone->set_zone_name( 'Colombia' );
		$zone->add_location( 'CO', 'country' );
		$zone->save();
		$flat = $zone->add_shipping_method( 'flat_rate' );
		update_option( 'woocommerce_flat_rate_' . $flat . '_settings', array( 'title' => 'Envío estándar (2 a 4 días)', 'cost' => '12000', 'tax_status' => 'none' ) );
		$free = $zone->add_shipping_method( 'free_shipping' );
		update_option( 'woocommerce_free_shipping_' . $free . '_settings', array( 'title' => 'Envío gratis', 'requires' => 'min_amount', 'min_amount' => '150000', 'ignore_discounts' => 'no' ) );
		update_option( 'rubor_zone_id', $zone->get_id() );
		WC_Cache_Helper::get_transient_version( 'shipping', true );
		$log[] = 'Envíos: tarifa fija $12.000 y envío gratis desde $150.000.';
	}

	$cats = array(
		'rostro'       => array( 'Rostro', 'Bases, polvos y rubores.' ),
		'ojos'         => array( 'Ojos', 'Sombras, máscaras y delineadores.' ),
		'labios'       => array( 'Labios', 'Mates, bálsamos y gloss.' ),
		'piel'         => array( 'Piel', 'Sérums, cremas y rituales.' ),
		'herramientas' => array( 'Herramientas', 'Brochas y esponjas.' ),
	);
	$cat_ids = array();
	foreach ( $cats as $slug => $c ) {
		$cat_ids[ $slug ] = rubor_demo_term( $c[0], $slug, 'product_cat', $c[1] );
	}

	$products = array(
		array( 'sku' => 'RUB-LAB-01', 'name' => 'Labial Mate Granada', 'cat' => 'labios', 'price' => 58000, 'img' => 'labial.webp', 'gallery' => array( 'tonos/granada.webp' ), 'featured' => true, 'badge' => 'Más vendido', 'type' => 'Labial mate · 3,5 g', 'dots' => '#8E1B2B,#A55A45,#D9686A,#5B2033',
			'short' => 'Rojo de fondo azul con acabado mate aterciopelado. Dura ocho horas sin resecar.',
			'desc' => "El rojo de la casa. Granada tiene fondo azul, por eso hace ver los dientes más blancos y funciona en casi todos los tonos de piel.\n\nSu fórmula con manteca de karité y aceite de jojoba se desliza como un bálsamo y se asienta en un mate suave que no se cuartea. Un estuche recargable: cuando se acaba, compras solo el repuesto.\n\n<strong>Cómo usarlo:</strong> aplica desde el centro hacia las comisuras. Para un efecto difuminado, da toques con el dedo." ),
		array( 'sku' => 'RUB-OJO-01', 'name' => 'Máscara Noche Larga', 'cat' => 'ojos', 'price' => 64000, 'img' => 'mascara.webp', 'featured' => true, 'badge' => 'Nuevo', 'type' => 'Volumen y curva · 9 ml', 'dots' => '#1F1512,#4A2E24',
			'short' => 'Volumen y curva que duran todo el día, sin grumos.',
			'desc' => "Un cepillo de fibras cortas que separa pestaña por pestaña y una fórmula con cera de arroz que sostiene la curva hasta la noche.\n\nNo se corre con la humedad de Bogotá ni con el calor de la costa, y se retira con agua tibia." ),
		array( 'sku' => 'RUB-ROS-01', 'name' => 'Polvo Suelto Seda', 'cat' => 'rostro', 'price' => 72000, 'img' => 'polvo-brocha.webp', 'featured' => true, 'type' => 'Acabado difuminado · 12 g', 'dots' => '#EBD3C2,#D7B39A,#B88A6C',
			'short' => 'Sella la base sin marcar líneas ni dar aspecto de máscara.',
			'desc' => "Un polvo tan fino que desaparece sobre la piel. Controla el brillo de la zona T durante horas y deja un acabado de piel real, no empolvada.\n\nViene en tres tonos traslúcidos: claro, medio y profundo." ),
		array( 'sku' => 'RUB-ROS-02', 'name' => 'Base Segunda Piel', 'cat' => 'rostro', 'price' => 89000, 'img' => 'bases.webp', 'featured' => true, 'badge' => '42 tonos', 'type' => 'Cobertura media · 30 ml', 'dots' => '#F0D6C2,#D9AE8E,#B9805C,#7E4E33',
			'short' => 'Cobertura media que se construye, en 42 tonos probados en pieles colombianas.',
			'desc' => "Unifica sin tapar las pecas ni la textura natural. Su fórmula con niacinamida hidrata durante el día y no oxida.\n\n¿No sabes tu tono? Escríbenos por WhatsApp con una foto con luz natural y te asesoramos gratis." ),
		array( 'sku' => 'RUB-LAB-09', 'name' => 'Bálsamo Labial Miel', 'cat' => 'labios', 'price' => 36000, 'img' => 'balsamo.webp', 'featured' => true, 'type' => 'Reparador nocturno · 15 g', 'dots' => '#E9D5C0',
			'short' => 'Repara los labios mientras duermes. Con miel de abejas de Boyacá.',
			'desc' => "Una capa generosa antes de dormir y amaneces con labios suaves, listos para cualquier mate.\n\nMiel, manteca de cacao y vitamina E. Sin fragancias añadidas." ),
		array( 'sku' => 'RUB-PIE-01', 'name' => 'Sérum Luz', 'cat' => 'piel', 'price' => 95000, 'img' => 'serum.webp', 'featured' => true, 'type' => 'Vitamina C al 10% · 30 ml', 'dots' => '#E8B65A',
			'short' => 'Vitamina C estable que ilumina y prepara la piel para el maquillaje.',
			'desc' => "Dos gotas cada mañana sobre la piel húmeda. En cuatro semanas el tono se ve más parejo y luminoso.\n\nFrasco ámbar con gotero para proteger la vitamina C de la luz. Úsalo siempre con protector solar." ),
		array( 'sku' => 'RUB-OJO-02', 'name' => 'Paleta Tierra Fría', 'cat' => 'ojos', 'price' => 110000, 'img' => 'ojos-paleta.webp', 'featured' => true, 'badge' => 'Edición otoño', 'type' => '9 sombras · mate y satín', 'dots' => '#C9A38A,#9A6B55,#5E3A2C,#E3C8B6',
			'short' => 'Nueve sombras en tonos tierra para looks de día y de noche.',
			'desc' => "Seis mates y tres satinados que se difuminan solos. Pensada para ojos marrones y miel, pero funciona con cualquier color.\n\nIncluye una guía impresa con tres looks paso a paso." ),
		array( 'sku' => 'RUB-PIE-02', 'name' => 'Crema Velo', 'cat' => 'piel', 'price' => 68000, 'img' => 'manos-crema.webp', 'featured' => true, 'type' => 'Hidratante ligera · 50 ml', 'dots' => '#BFD9D6',
			'short' => 'Hidratante en gel-crema que se absorbe en segundos.',
			'desc' => "Ligera para climas cálidos y suficiente para climas fríos. Con ácido hialurónico y aloe vera.\n\nBase perfecta antes del maquillaje: no deja la piel grasosa." ),
		array( 'sku' => 'RUB-HER-01', 'name' => 'Set de Brochas Taller', 'cat' => 'herramientas', 'price' => 120000, 'img' => 'brochas.webp', 'type' => '7 brochas · fibra vegana', 'dots' => '#D8B9A6',
			'short' => 'Siete brochas de fibra sintética para rostro y ojos.',
			'desc' => "Las mismas brochas que usamos en la tienda. Fibras sintéticas suaves, mangos de madera certificada y un estuche de lino.\n\nIncluye: base, polvo, rubor, contorno, sombra, difuminado y labios." ),
	);

	foreach ( rubor_shade_list() as $s ) {
		if ( '01' === $s['num'] ) {
			continue;
		}
		$file       = 'tonos/' . sanitize_title( remove_accents( $s['name'] ) ) . '.webp';
		$products[] = array(
			'sku' => 'RUB-LAB-' . $s['num'], 'name' => 'Labial Mate ' . $s['name'], 'cat' => 'labios', 'price' => 58000, 'img' => $file, 'type' => 'Labial mate · 3,5 g · N.º ' . $s['num'], 'dots' => $s['hex'],
			'short' => $s['desc'],
			'desc'  => $s['desc'] . "\n\nParte de la línea Permanencia: ocho tonos mates que duran ocho horas sin resecar. Estuche recargable.",
		);
	}

	$created = 0;
	foreach ( $products as $p ) {
		$id      = wc_get_product_id_by_sku( $p['sku'] );
		$product = $id ? wc_get_product( $id ) : new WC_Product_Simple();
		$product->set_name( $p['name'] );
		$product->set_sku( $p['sku'] );
		$product->set_regular_price( (string) $p['price'] );
		$product->set_short_description( $p['short'] );
		$product->set_description( wpautop( $p['desc'] ) );
		$product->set_category_ids( array( $cat_ids[ $p['cat'] ] ) );
		$product->set_featured( ! empty( $p['featured'] ) );
		$product->set_manage_stock( true );
		$product->set_stock_quantity( 40 );
		$product->set_status( 'publish' );
		$product->set_image_id( rubor_demo_media( $p['img'], $p['name'] ) );
		if ( ! empty( $p['gallery'] ) ) {
			$product->set_gallery_image_ids( array_map( function ( $f ) use ( $p ) { return rubor_demo_media( $f, $p['name'] ); }, $p['gallery'] ) );
		}
		$product->update_meta_data( '_rubor_type', $p['type'] );
		$product->update_meta_data( '_rubor_dots', $p['dots'] );
		$product->update_meta_data( '_rubor_badge', isset( $p['badge'] ) ? $p['badge'] : '' );
		$is_new = ! $id;
		$id     = $product->save();
		if ( $is_new ) {
			$created++;
			rubor_demo_reviews( $id );
		}
	}
	$log[] = sprintf( 'Productos: %d en catálogo (%d nuevos), con fotos, precios, inventario y reseñas.', count( $products ), $created );
	return $log;
}

function rubor_demo_reviews( $product_id ) {
	$reviews = array(
		array( 'Valentina R.', 5, 'Me encantó, el color es idéntico al de la foto y dura todo el día.' ),
		array( 'Camila G.', 5, 'Llegó en dos días y con una muestra de regalo. Ya pedí el segundo.' ),
		array( 'Laura M.', 4, 'Muy buena calidad. Le pongo cuatro porque me hubiera gustado un tamaño más grande.' ),
	);
	foreach ( $reviews as $i => $r ) {
		$cid = wp_insert_comment( array(
			'comment_post_ID'      => $product_id,
			'comment_author'       => $r[0],
			'comment_author_email' => 'cliente' . $i . '@ejemplo.co',
			'comment_content'      => $r[2],
			'comment_type'         => 'review',
			'comment_approved'     => 1,
		) );
		add_comment_meta( $cid, 'rating', $r[1] );
		add_comment_meta( $cid, 'verified', 1 );
	}
	if ( class_exists( 'WC_Comments' ) ) {
		WC_Comments::clear_transients( $product_id );
	}
}

function rubor_demo_forms() {
	if ( ! class_exists( 'WPCF7_ContactForm' ) ) {
		return array( 'Contact Form 7 no está activo: se omitieron los formularios.' );
	}
	$forms = array(
		'Contacto' => '<p><label>Tu nombre<br>[text* nombre autocomplete:name]</label></p>
<p><label>Tu correo<br>[email* correo autocomplete:email]</label></p>
<p><label>Asunto<br>[select asunto "Asesoría de tono" "Estado de mi pedido" "Cambios y devoluciones" "Otro"]</label></p>
<p><label>Mensaje<br>[textarea* mensaje]</label></p>
<p>[acceptance datos] Acepto la política de tratamiento de datos. [/acceptance]</p>
<p>[submit "Enviar mensaje"]</p>',
		'Club Rubor' => '<label class="screen-reader-text" for="club-correo">Correo electrónico</label>[email* correo id:club-correo placeholder "tu@correo.com"][submit "Unirme"]',
	);
	$extra = array(
		'Contacto'   => array( 'subject' => '[asunto] — mensaje de [nombre]', 'body' => "De: [nombre] <[correo]>\nAsunto: [asunto]\n\n[mensaje]", 'reply' => 'Reply-To: [correo]', 'flamingo' => "flamingo_email: \"[correo]\"\nflamingo_name: \"[nombre]\"\nflamingo_subject: \"[asunto]\"" ),
		'Club Rubor' => array( 'subject' => 'Nueva suscripción al Club Rubor', 'body' => 'Correo: [correo]', 'reply' => 'Reply-To: [correo]', 'flamingo' => "flamingo_email: \"[correo]\"\nflamingo_subject: \"Suscripción al Club Rubor\"" ),
	);
	foreach ( $forms as $title => $body ) {
		$found = get_posts( array( 'post_type' => 'wpcf7_contact_form', 'title' => $title, 'numberposts' => 1 ) );
		$form  = $found ? WPCF7_ContactForm::get_instance( $found[0]->ID ) : WPCF7_ContactForm::get_template( array( 'title' => $title ) );
		$mail  = $form->prop( 'mail' );
		$mail['subject']            = $extra[ $title ]['subject'];
		$mail['body']               = $extra[ $title ]['body'];
		$mail['additional_headers'] = $extra[ $title ]['reply'];
		$form->set_properties( array(
			'form'                => $body,
			'mail'                => $mail,
			'additional_settings' => $extra[ $title ]['flamingo'],
			'messages'            => array_merge( $form->prop( 'messages' ), array(
				'mail_sent_ok' => 'Club Rubor' === $title ? 'Listo. Tu código CLUB15 va camino a tu correo.' : 'Gracias, te respondemos en menos de 24 horas.',
			) ),
		) );
		$form->save();
	}
	return array( 'Contact Form 7: formularios "Contacto" y "Club Rubor" creados (los mensajes se guardan en Flamingo).' );
}

function rubor_demo_posts() {
	$cats = array(
		'guias'      => rubor_demo_term( 'Guías', 'guias', 'category' ),
		'piel'       => rubor_demo_term( 'Piel', 'piel', 'category' ),
		'tendencias' => rubor_demo_term( 'Tendencias', 'tendencias', 'category' ),
		'labios'     => rubor_demo_term( 'Labios', 'labios', 'category' ),
	);

	$posts = array(
		array(
			'slug' => 'neceser-de-cinco-productos', 'cat' => 'guias', 'img' => 'flatlay-arena.webp', 'days' => 2,
			'title' => 'Cómo armar un neceser de cinco productos que sirva todo el año',
			'tags' => array( 'básicos', 'neceser' ),
			'kw' => 'neceser de maquillaje', 'meta' => 'Cinco productos de maquillaje que cubren el día, la oficina y la noche. Una guía práctica para gastar menos y usar todo lo que compras.',
			'body' => "<p>La mayoría de los cajones de maquillaje tienen veinte productos y se usan cuatro. Un buen neceser no se mide por cuántas cosas tiene, sino por cuántos looks puedes hacer con ellas.</p>
<h2>1. Una base que de verdad sea tu tono</h2>
<p>Es el producto donde más vale la pena invertir. Pruébala en la mandíbula, no en la mano, y mírala con luz natural. Si desaparece, es la tuya.</p>
<h2>2. Un polvo traslúcido</h2>
<p>Solo en la zona T. Sella la base y controla el brillo sin cubrir de más.</p>
<h2>3. Una máscara de pestañas</h2>
<p>Abre la mirada en diez segundos. Si solo tienes tiempo para un producto, que sea este.</p>
<h2>4. Un labial mate que también sirva de rubor</h2>
<p>Da dos toques en las mejillas y difumina con los dedos. Un solo color en labios y pómulos se ve pensado, no improvisado.</p>
<h2>5. Un bálsamo</h2>
<p>Para la noche, para los días de descanso y para preparar los labios antes del mate.</p>
<blockquote><p>Menos productos, mejor elegidos: así se gasta menos y se usa todo.</p></blockquote>",
		),
		array(
			'slug' => 'gua-sha-en-la-manana', 'cat' => 'piel', 'img' => 'piel-guasha.webp', 'days' => 6,
			'title' => 'Gua sha en la mañana: lo que sí hace y lo que no',
			'tags' => array( 'gua sha', 'rutina' ),
			'kw' => 'gua sha', 'meta' => 'Qué puede hacer el gua sha por tu piel, cómo usarlo en tres minutos y qué promesas conviene tomar con calma.',
			'body' => "<p>El gua sha es una piedra plana, normalmente de cuarzo o jade, que se desliza sobre la piel. Viene de la medicina tradicional china y se volvió popular en redes por sus resultados de \"antes y después\".</p>
<h2>Lo que sí hace</h2>
<ul><li>Ayuda a deshinchar el rostro por la mañana al mover el líquido acumulado.</li><li>Relaja la mandíbula y la frente, donde se acumula tensión.</li><li>Convierte la aplicación del sérum en un ritual que te obliga a ir despacio.</li></ul>
<h2>Lo que no hace</h2>
<p>No elimina arrugas de forma permanente ni reemplaza tratamientos. El efecto de deshinchado dura algunas horas.</p>
<h2>Cómo usarlo en tres minutos</h2>
<p>Aplica dos gotas de sérum o aceite para que la piedra se deslice. Haz movimientos hacia afuera y hacia arriba, de cinco a diez veces por zona, sin presionar fuerte. Lava la piedra con agua y jabón al terminar.</p>",
		),
		array(
			'slug' => 'el-regreso-del-nude-cafe', 'cat' => 'tendencias', 'img' => 'unas.webp', 'days' => 11,
			'title' => 'El regreso del nude café: tres maneras de llevarlo',
			'tags' => array( 'tendencias', 'nude' ),
			'kw' => 'labial nude café', 'meta' => 'El nude café volvió. Tres formas de llevarlo, de la oficina a la noche, según tu tono de piel.',
			'body' => "<p>El nude café fue el labial de los noventa y vuelve con más fuerza que nunca, pero esta vez pensado para pieles morenas y trigueñas, que durante años no encontraron un nude que no las hiciera ver apagadas.</p>
<h2>1. Mate y con delineador un tono más oscuro</h2>
<p>El look clásico. Delinea con un lápiz marrón y rellena con el labial. Difumina la línea con el dedo para que no se vea marcada.</p>
<h2>2. Con brillo en el centro</h2>
<p>Aplica el mate y da un toque de bálsamo solo en el centro del labio inferior. Se ve más jugoso y moderno.</p>
<h2>3. Monocromático</h2>
<p>El mismo tono en labios, mejillas y párpados. Es el look más rápido y uno de los más favorecedores.</p>
<p>Nuestro Nude Café, el N.º 08 de la línea Permanencia, se formuló específicamente para este propósito.</p>",
		),
		array(
			'slug' => 'como-encontrar-tu-subtono', 'cat' => 'guias', 'img' => 'rostro-polvos.webp', 'days' => 16,
			'title' => 'Cálido, frío o neutro: cómo saber el subtono de tu piel',
			'tags' => array( 'subtono', 'base' ),
			'kw' => 'subtono de piel', 'meta' => 'Tres pruebas sencillas para saber si tu subtono de piel es cálido, frío o neutro, y cómo usarlo para elegir base y labial.',
			'body' => "<p>El tono es qué tan clara u oscura es tu piel. El subtono es el color que hay debajo, y es lo que hace que una base se vea natural o grisácea. Hay tres: cálido, frío y neutro.</p>
<h2>Prueba de las venas</h2>
<p>Mira tu muñeca con luz natural. Si las venas se ven verdosas, tu subtono es cálido. Si se ven azules o moradas, es frío. Si no lo tienes claro, probablemente es neutro.</p>
<h2>Prueba del metal</h2>
<p>¿Te favorece más el dorado o el plateado? El dorado suele ir con subtonos cálidos y el plateado con fríos.</p>
<h2>Prueba del blanco</h2>
<p>Ponte una camiseta blanca pura y luego una color crema. Si el blanco te ilumina, eres fría; si te apaga y el crema te favorece, eres cálida.</p>
<p>En nuestro estudio de color puedes filtrar los labiales por subtono y ver al instante cuáles te favorecen.</p>",
		),
		array(
			'slug' => 'labial-que-dura-ocho-horas', 'cat' => 'labios', 'img' => 'ritual-aplicacion.webp', 'days' => 23,
			'title' => 'Cinco trucos para que el labial dure ocho horas',
			'tags' => array( 'labial', 'trucos' ),
			'kw' => 'labial de larga duración', 'meta' => 'Exfoliar, hidratar, delinear, sellar y retocar: cinco pasos de maquilladora para que tu labial dure todo el día.',
			'body' => "<p>Un buen labial mate puede durar ocho horas, pero la preparación cuenta tanto como la fórmula. Estos son los pasos que usan las maquilladoras profesionales.</p>
<ol><li><strong>Exfolia una vez por semana.</strong> Con un cepillo de dientes suave y un poco de bálsamo.</li><li><strong>Hidrata, pero espera.</strong> Aplica bálsamo y retira el exceso con un pañuelo antes del color.</li><li><strong>Delinea todo el labio.</strong> No solo el contorno: rellena con el lápiz para crear una base que agarre el color.</li><li><strong>Aplica en dos capas.</strong> Una capa, pañuelo encima, polvo traslúcido a través del pañuelo y segunda capa.</li><li><strong>Retoca solo el centro.</strong> Después de comer, el color se pierde en el centro. Retoca ahí y presiona los labios.</li></ol>",
		),
		array(
			'slug' => 'vitamina-c-en-la-rutina', 'cat' => 'piel', 'img' => 'serum.webp', 'days' => 30,
			'title' => 'Vitamina C en la rutina de la mañana: guía para empezar',
			'tags' => array( 'vitamina c', 'sérum' ),
			'kw' => 'sérum de vitamina C', 'meta' => 'Qué hace la vitamina C por la piel, en qué orden aplicarla y por qué siempre debe ir con protector solar.',
			'body' => "<p>La vitamina C es uno de los activos más estudiados en cosmética. Es antioxidante: ayuda a proteger la piel del daño ambiental y, con el uso constante, unifica el tono.</p>
<h2>En qué orden va</h2>
<p>Después de limpiar y antes de la crema hidratante. Sobre la piel limpia y ligeramente húmeda se absorbe mejor.</p>
<h2>Siempre con protector solar</h2>
<p>La vitamina C potencia la protección del bloqueador, pero no lo reemplaza. Úsala siempre en la mañana, con protector encima.</p>
<h2>Cómo saber si se oxidó</h2>
<p>Si el sérum pasó de transparente o amarillo claro a naranja oscuro, perdió eficacia. Guárdalo en un lugar fresco y lejos de la luz.</p>",
		),
	);

	$n = 0;
	foreach ( $posts as $p ) {
		if ( get_page_by_path( $p['slug'], OBJECT, 'post' ) ) {
			continue;
		}
		$id = wp_insert_post( array(
			'post_type'     => 'post',
			'post_status'   => 'publish',
			'post_title'    => $p['title'],
			'post_name'     => $p['slug'],
			'post_content'  => $p['body'],
			'post_excerpt'  => $p['meta'],
			'post_category' => array( $cats[ $p['cat'] ] ),
			'tags_input'    => $p['tags'],
			'post_date'     => gmdate( 'Y-m-d H:i:s', time() - $p['days'] * DAY_IN_SECONDS ),
		) );
		set_post_thumbnail( $id, rubor_demo_media( $p['img'], $p['title'] ) );
		update_post_meta( $id, '_yoast_wpseo_focuskw', $p['kw'] );
		update_post_meta( $id, '_yoast_wpseo_metadesc', $p['meta'] );
		$n++;
	}

	wp_update_term( (int) get_option( 'default_category' ), 'category', array( 'name' => 'General', 'slug' => 'general' ) );

	// La entrada de ejemplo que trae WordPress al instalarse.
	foreach ( array( 'hola-mundo', 'hello-world' ) as $slug ) {
		$hello = get_page_by_path( $slug, OBJECT, 'post' );
		if ( $hello ) {
			wp_delete_post( $hello->ID, true );
		}
	}
	$sample = get_page_by_path( 'sample-page' ) ?: get_page_by_path( 'pagina-ejemplo' );
	if ( $sample ) {
		wp_delete_post( $sample->ID, true );
	}
	return array( sprintf( 'Diario: %d entradas nuevas con imagen destacada, categorías, etiquetas y SEO de Yoast.', $n ) );
}

function rubor_demo_pages() {
	$home  = rubor_demo_page( 'Inicio', 'inicio' );
	$blog  = rubor_demo_page( 'Diario', 'diario' );
	rubor_demo_page( 'Nosotros', 'nosotros', '<p>RUBOR nació en 2019 en un pequeño taller de Bogotá con una idea simple: hacer maquillaje pensado para la piel latina, en lotes pequeños y con fórmulas cortas.</p>
<p>Hoy somos un equipo de nueve personas entre formuladoras, maquilladoras y asesoras. Probamos cada tono en pieles de todo el país antes de lanzarlo, y diseñamos nuestros estuches para que se puedan recargar.</p>
<h2>Lo que nos importa</h2>
<ul><li>Ningún producto ni ingrediente se prueba en animales.</li><li>Labiales y polvos con repuesto: el estuche se queda.</li><li>42 tonos de base, porque la piel colombiana no cabe en seis.</li><li>Formulamos y envasamos en Colombia con laboratorios certificados.</li></ul>' );
	rubor_demo_page( 'Contacto', 'contacto', '<p>Escríbenos para asesoría de tono, el estado de tu pedido o cualquier duda. Respondemos en menos de 24 horas, de lunes a sábado.</p>
<p><strong>Casa Rubor:</strong> Calle 79B #8-12, Bogotá · Lun a sáb, 10 a. m. – 7 p. m.</p>
[rubor_form titulo="Contacto"]' );
	rubor_demo_page( 'Envíos y devoluciones', 'envios', '<h2>Envíos</h2>
<p>Enviamos a toda Colombia. Bogotá, Medellín, Cali y Barranquilla: 1 a 2 días hábiles. Resto del país: 2 a 4 días hábiles.</p>
<p>El envío cuesta $12.000 y es gratis en pedidos desde $150.000.</p>
<h2>Cambios y devoluciones</h2>
<p>Tienes 30 días para cambiar un producto sin abrir. Si un tono de base no te quedó, te lo cambiamos aunque esté abierto, una vez por pedido.</p>' );
	rubor_demo_page( 'Preguntas frecuentes', 'preguntas-frecuentes', '<h3>¿Los productos son veganos?</h3><p>El 82% del catálogo es vegano. La ficha de cada producto lo indica.</p>
<h3>¿Cómo sé mi tono de base?</h3><p>Escríbenos con una foto con luz natural y te asesoramos gratis, o visita la tienda.</p>
<h3>¿Puedo pagar contra entrega?</h3><p>Sí, en las ciudades principales. También recibimos transferencia bancaria.</p>
<h3>¿Venden al por mayor?</h3><p>Sí, para maquilladoras y tiendas. Escríbenos desde la página de contacto.</p>' );

	// WordPress llena la primera barra lateral con widgets de ejemplo; se vacía para usar la del tema.
	$sidebars = get_option( 'sidebars_widgets', array() );
	$sidebars['sidebar-diario'] = array();
	update_option( 'sidebars_widgets', $sidebars );

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home );
	update_option( 'page_for_posts', $blog );
	return array( 'Páginas: Inicio, Diario, Nosotros, Contacto, Envíos y devoluciones, Preguntas frecuentes. Portada estática configurada.' );
}

/** [rubor_form titulo="Contacto"]: inserta un formulario de Contact Form 7 por su título. */
add_shortcode( 'rubor_form', function ( $atts ) {
	$atts = shortcode_atts( array( 'titulo' => 'Contacto' ), $atts );
	return rubor_cf7( $atts['titulo'], 'rubor-form' );
} );

function rubor_demo_menus() {
	$menus = array(
		'primary-menu'   => array( 'Menú principal', array( 'Rostro' => 'cat:rostro', 'Ojos' => 'cat:ojos', 'Labios' => 'cat:labios', 'Piel' => 'cat:piel' ) ),
		'secondary-menu' => array( 'Menú secundario', array( 'Tienda' => 'shop', 'Diario' => 'page:diario', 'Contacto' => 'page:contacto' ) ),
		'footer-menu'    => array( 'Menú del pie', array( 'Nosotros' => 'page:nosotros', 'Envíos y devoluciones' => 'page:envios', 'Preguntas frecuentes' => 'page:preguntas-frecuentes', 'Contacto' => 'page:contacto' ) ),
	);
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	foreach ( $menus as $location => $m ) {
		$menu = wp_get_nav_menu_object( $m[0] );
		if ( $menu ) {
			$locations[ $location ] = $menu->term_id;
			continue;
		}
		$menu_id = wp_create_nav_menu( $m[0] );
		foreach ( $m[1] as $label => $target ) {
			$item = array( 'menu-item-title' => $label, 'menu-item-status' => 'publish' );
			list( $kind, $slug ) = array_pad( explode( ':', $target ), 2, '' );
			if ( 'cat' === $kind && ( $term = get_term_by( 'slug', $slug, 'product_cat' ) ) ) {
				$item += array( 'menu-item-type' => 'taxonomy', 'menu-item-object' => 'product_cat', 'menu-item-object-id' => $term->term_id );
			} elseif ( 'page' === $kind && ( $page = get_page_by_path( $slug ) ) ) {
				$item += array( 'menu-item-type' => 'post_type', 'menu-item-object' => 'page', 'menu-item-object-id' => $page->ID );
			} elseif ( 'shop' === $kind && function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'shop' ) > 0 ) {
				$item += array( 'menu-item-type' => 'post_type', 'menu-item-object' => 'page', 'menu-item-object-id' => wc_get_page_id( 'shop' ) );
			} else {
				$item += array( 'menu-item-type' => 'custom', 'menu-item-url' => home_url( '/' ) );
			}
			wp_update_nav_menu_item( $menu_id, 0, $item );
		}
		$locations[ $location ] = $menu_id;
	}
	set_theme_mod( 'nav_menu_locations', $locations );
	return array( 'Menús: principal, secundario y pie de página asignados.' );
}

function rubor_demo_whatsapp() {
	if ( ! is_plugin_active( 'creame-whatsapp-me/joinchat.php' ) ) {
		return array( 'Joinchat no está activo: se omitió el botón de WhatsApp.' );
	}
	update_option( 'joinchat', array_merge( (array) get_option( 'joinchat', array() ), array(
		'telephone'     => '573001234567', // Número de ejemplo: cambiarlo en Joinchat → Ajustes.
		'button_tip'    => '¿Te ayudamos con tu tono?',
		'message_text'  => "Hola 👋
Soy Sara, asesora de RUBOR. ¿Buscas tu tono de base o de labial? Escríbeme y te ayudo.",
		'message_send'  => 'Hola RUBOR, quiero asesoría de tono. Estoy viendo: {TITLE}',
		'message_start' => 'Escribir por WhatsApp',
		'message_delay' => 0,
		'button_delay'  => 2,
		'position'      => 'right',
		'color'         => '#8E2B34',
	) ) );
	return array( 'Joinchat: botón de WhatsApp con mensaje de asesoría (número de ejemplo).' );
}

/** Usuaria de prueba con el rol Redactor Jr. y una entrada esperando aprobación. */
function rubor_demo_roles() {
	rubor_rol_redactor_jr();
	$user = get_user_by( 'login', 'redactora.jr' );
	if ( ! $user ) {
		$uid = wp_insert_user( array(
			'user_login'   => 'redactora.jr',
			'user_email'   => 'redactora@ejemplo.co',
			'user_pass'    => wp_generate_password( 20 ), // La demo no necesita entrar con ella; se cambia en Usuarios.
			'display_name' => 'Laura (Redactor Jr.)',
			'role'         => 'redactor_jr',
		) );
		if ( is_wp_error( $uid ) ) {
			return array( 'No se pudo crear la usuaria Redactor Jr.: ' . $uid->get_error_message() );
		}
		$user = get_user_by( 'id', $uid );
	}
	if ( ! get_page_by_path( 'rutina-de-noche-en-cinco-minutos', OBJECT, 'post' ) ) {
		$pid = wp_insert_post( array(
			'post_type'     => 'post',
			'post_status'   => 'pending',
			'post_author'   => $user->ID,
			'post_title'    => 'Rutina de noche en cinco minutos',
			'post_name'     => 'rutina-de-noche-en-cinco-minutos',
			'post_content'  => '<p>Desmaquillar, limpiar, hidratar: tres pasos que caben en cinco minutos y cambian cómo amanece la piel.</p><h2>1. Desmaquillar</h2><p>Un bálsamo limpiador disuelve la base y la máscara sin frotar.</p><h2>2. Limpiar</h2><p>Un gel suave con agua tibia retira lo que quedó.</p><h2>3. Hidratar</h2><p>Crema Velo en rostro y cuello, y Bálsamo Labial Miel en los labios.</p>',
			'post_excerpt'  => 'Tres pasos para cuidar la piel antes de dormir.',
			'post_category' => array( (int) get_option( 'default_category' ) ),
		) );
		set_post_thumbnail( $pid, rubor_demo_media( 'manos-crema.webp', 'Rutina de noche' ) );
	}
	return array( 'Rol "Redactor Jr." creado, con la usuaria redactora.jr y una entrada "Pendiente de revisión" para que un Editor la apruebe.' );
}
