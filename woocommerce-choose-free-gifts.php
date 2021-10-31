<?php

/**
* Plugin Name: WooCommerce Choose Free Gifts
* Plugin URI: https://www.yourwebsiteurl.com/
* Description: This is a plugin for WooCommerce that allows you to choose multiple free gifts for your customers based on your cart Value.
* Version: 1.0
* Author: QSD IT Solutions
* Author URI: https://quickshiftdigital.com/
**/

define('coupon_name', 'royal600');

function wpdocs_theme_name_scripts() {
    wp_enqueue_style( 'main-css', home_url()  . '/wp-content/plugins/woocommerce-choose-free-gifts/assets/css/main.css');
    wp_enqueue_script( 'main', home_url()  . '/wp-content/plugins/woocommerce-choose-free-gifts/assets/js/main.js', array(), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'wpdocs_theme_name_scripts' );


add_filter('woocommerce_coupon_get_discount_amount', 'woocommerce_coupon_get_discount_amount', 10, 5 );
function woocommerce_coupon_get_discount_amount( $discount, $discounting_amount, $cart_item, $single, $coupon ) {
    if ($coupon->type == 'percent_product' || $coupon->type == 'percent') {
        global $woocommerce;
        $cart_total = 0;

        foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ) {

         $variable_product1= new WC_Product_Variation( $cart_item["variation_id"] );

         $cart_total += $variable_product1 ->regular_price * $cart_item['quantity'];
        } 
        $discount = round( ( $cart_total / 100 ) * $coupon->amount, $woocommerce->cart->dp );
        return $discount;
    }
    return $discount;
}


add_action( 'woocommerce_before_cart', 'bbloomer_find_product_in_cart_alt' );
add_action( 'woocommerce_before_cart', 'woo_adjust_cart_qty' );
add_action( 'woocommerce_before_cart', 'woo_couponMambo' );
    
function bbloomer_find_product_in_cart_alt() {

    $product_ids = array(2256, 2255);
    $in_cart = false;
    
    foreach ($product_ids as $product_id) {
        global $woocommerce;

        if ( ! isset( $product_id ) ) {
            return false;
        }
        
        foreach ( $woocommerce->cart->get_cart() as $cart_item ) {
            if ( $cart_item['variation_id'] === $product_id ) {
                if($product_id == 2256) {
                    if (!woo_in_cart(2451)) {
                        WC()->cart->add_to_cart( 2451 );     
                    }
                }
                else if($product_id == 2255) {
                    if (!woo_in_cart(2456)) {
                        WC()->cart->add_to_cart( 2456 );                
                    }
                }
            }
        }
    }
  
}

function woo_adjust_cart_qty() {
    $product_ids = array(2256, 2255);
    $can_ids = array(2451, 2456);
    $in_cart = false;
    $x = false;
    
    foreach ($product_ids as $product_id) {
        global $woocommerce;

        if ( ! isset( $product_id ) ) {
            return false;
        }
        
        foreach ( $woocommerce->cart->get_cart() as $cart_item ) {
            // print_r($cart_item);
            if ( $cart_item['variation_id'] === $product_id ) {
                if($cart_item['quantity'] > 0) {
                    $qty = $cart_item['quantity'];
                    foreach ( $woocommerce->cart->get_cart() as $cart_it ) {
                        if(($cart_it['product_id'] === $can_ids[0] && $cart_item['variation_id'] == 2256) || $cart_it['product_id'] === $can_ids[1] && $cart_item['variation_id'] == 2255) {
                            $key = $cart_it['key'];                     
                            if(isset($key)) {
                                $woocommerce->cart->set_quantity($key, $qty);
                            }
                        }                        
                    }
                }
                $x = true;
            }
        }
    }
    
    for ($x=0; $x < count($can_ids); $x++) { 
        $y = false;
        if(woo_in_cart($can_ids[$x])) {
            foreach ( $woocommerce->cart->get_cart() as $cart_it ) {
                if($cart_it['variation_id'] == $product_ids[$x]) {
                    $y = true;
                }                      
            }

            if(!$y) {
                foreach ( $woocommerce->cart->get_cart() as $cart_it ) {
                    if($cart_it['product_id'] === $can_ids[$x]) {
                        $key = $cart_it['key'];                    
                        if(isset($key)) {
                            $woocommerce->cart->remove_cart_item($key);
                        }
                    }                        
                }
            }            
        }
    }
    

}

function woo_in_cart($product_id) {
    global $woocommerce;
    $response = false;

    foreach ( $woocommerce->cart->get_cart() as $cart_item ) {
        if ( $cart_item['product_id'] === $product_id ) {
            $response = true;
        }
    }

    return $response;
}

function variation_in_cart($variation_id) {
    global $woocommerce;
    $response = false;

    foreach ( $woocommerce->cart->get_cart() as $cart_item ) {
        if ( $cart_item['variation'] === $variation_id ) {
            $response = true;
        }
    }

    return $response;
}

function get_cart_quantity($product_id, $data="") {
    $response = 0;
    global $woocommerce;

    if(woo_in_cart($product_id)) {
        if(isset($data) && $data['is_variation']) {
            foreach ( $woocommerce->cart->get_cart() as $cart_item ) {
                if ( $cart_item['variation_id'] === $data['id'] ) {
                    $product = $cart_item;
                }
            }
        }
        else {
            foreach ( $woocommerce->cart->get_cart() as $cart_item ) {
                if ( $cart_item['product_id'] === $product_id ) {
                    $product = $cart_item;
                }
            }
        }

        if(isset($product)) {
            $response = $product['quantity'];
        }
    }

    return $response;
}


function manageCartTotals($woocommerce) {
    $cart_total = $woocommerce->cart->get_cart_contents_total();
    $product_ids = array(2451, 2456, 1685);

    foreach ($product_ids as $product) {
        foreach ( $woocommerce->cart->get_cart() as $cart_it ) {
            if($cart_it['product_id'] === $product) {
                $product_total = $cart_it['line_total'];
                $cart_total = $cart_total - $product_total;
            }                        
        }
    }

    return $cart_total;

}

function get_free_productsList() {
    $free_product_id = array(
        array(
            'is_variation'  => true,
            'id'            => 2009,
            'value'         => 400,
            'parent_id'     => 1748
        ),
        array(
            'is_variation'  => true,
            'id'            => 2008,
            'value'         => 800,
            'parent_id'     => 1748
        ),
        array(
            'is_variation'  => true,
            'id'            => 1740,
            'value'         => 1200,
            'parent_id'     => 1739
        ),
        array(
            'is_variation'  => true,
            'id'            => 1731,
            'value'         => 1200,
            'parent_id'     => 1730
        ),
        array(
            'is_variation'  => true,
            'id'            => 1689,
            'value'         => 1600,
            'parent_id'     => 1688
        ),
    );

    return $free_product_id;
}

// add_action('woocommerce_applied_coupon', 'apply_product_on_coupon');
function woo_couponMambo() {
    global $woocommerce;
    $coupon_id = coupon_name;
    $free_product_id = get_free_productsList();
    $cart_total = manageCartTotals($woocommerce);

    if(in_array(strtolower($coupon_id), $woocommerce->cart->get_applied_coupons())){
        for ($x=0; $x < count($free_product_id); $x++) { 
            if($cart_total > $free_product_id[$x]['value']) {
                $free_product_id[$x]['maximum'] = floor($cart_total / $free_product_id[$x]['value']);
                $free_product_id[$x]['product'] = get_the_title($free_product_id[$x]['id']);
                $free_product_id[$x]['qty_in_cart'] = get_cart_quantity($free_product_id[$x]['parent_id'], $free_product_id[$x]);
            }
            else {
                $free_product_id[$x] = '';
            }
        }
        
        return $free_product_id;
    }
}

add_action('woocommerce_before_cart', 'removeDiscountedPrice');
add_action( 'woocommerce_cart_calculate_fees', 'woo_discount_price' );

function woo_discount_price($discount_price) { 
    global $woocommerce; //Set the price for user role.
    session_start();

    if(isset($_SESSION['total'])) {
        $discount_price = $_SESSION['total'];
        $woocommerce->cart->add_fee( 'Discounted Price', -$discount_price, true, 'standard' ); 
    }

} 

function gift_addToCart($data) {
    global $woocommerce;
    $total = 0;

    for ($x=0; $x < count($data); $x++) {
        if($data[$x]['is_variation']) {
            if(variation_in_cart($data[$x]['id'])) {
                $qty = get_cart_quantity($data[$x]['parent_id'], $data[$x]);
                if($qty < $data[$x]['qty']) {
                    foreach ( $woocommerce->cart->get_cart() as $cart_it ) {
                        if($cart_it['variation_id'] == $data[$x]['id']) {
                            $key = $cart_it['key'];                     
                            if(isset($key)) {
                                $woocommerce->cart->set_quantity($key, $data[$x]['qty']);
                            }
                        }                        
                    }
                }
            }
            else {
                WC()->cart->add_to_cart($data[$x]['id'], $data[$x]['qty']);
            }
        }
        
        $price = get_post_meta($data[$x]['id'], '_price', true);
        $price = $price * $data[$x]['qty'];
        $total = $total + $price;
        
        session_start();
        $_SESSION['total'] = $total;

        $response['total'] = $_SESSION['total'];
    }
    
    return $response['response'] = true;
}


function removeDiscountedPrice() {
    global $woocommerce;
    $coupon_id = coupon_name;
    $free_products = get_free_productsList();
    $set = false;

    foreach ($free_products as $product) {
        if(woo_in_cart($product['parent_id']) && in_array(strtolower($coupon_id), $woocommerce->cart->get_applied_coupons())) {
            $set = true;
        }
    }
    
    if(!$set) {
        session_start();
        unset($_SESSION['total']);
    }
}

add_action( 'woocommerce_after_cart_table',  'addChooseBox'); 
function addChooseBox() {
    ?>
    <?php global $woocommerce; if(in_array(strtolower(coupon_name), $woocommerce->cart->get_applied_coupons())): $cart_total = manageCartTotals($woocommerce); $products = get_free_productsList(); ?>
		<div class="free_gifts">
			<h3>Choose Free Gifts with Coupon Royal600</h3>
			<table class="shop_table">
				<thead>
					<tr>
						<th>
							Products
						</th>
						<th>
							Totals
						</th>
					</tr>
				</thead>
				<tbody>
					<?php if(isset($products)): ?>
						<?php foreach ($products as $product ) : ?>
							<tr>
								<td><?php echo get_the_title($product['id']); ?></td>
								<td>
									<input class="fg_item" data-id="<?php echo $product['id']; ?>" data-number="" value="<?php echo get_cart_quantity($product['parent_id'], $product); ?>" data-variation="<?php echo $product['is_variation']; ?>" data-parent="<?php echo $product['parent_id']; ?>" data-value="<?php echo $product['value']; ?>" type="number" min="0" step="1" max="<?php echo floor($cart_total / $product['value']); ?>"><span>Max <?php echo floor($cart_total / $product['value']); ?></span>
								</td>
							</tr>'
						<?php endforeach; ?>
					<?php endif; ?>
					<tr>
						<td colspan="2" class="actions text-right">					
							<button class="button gift_atc" disabled value="Update basket">Add to Cart</button>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	<?php endif;
}