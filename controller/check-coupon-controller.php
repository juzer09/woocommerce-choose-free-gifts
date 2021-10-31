<?php 
    require('../../../../wp-load.php');

    function checkCoupon($data) {    
        global $woocommerce;
        $coupon_id = $data['coupon'];
        $response = false;
    
        if(in_array(strtolower($coupon_id), $woocommerce->cart->get_applied_coupons())){
            $response = true;
        }

        return $response;
    }

    $response = array();
    $response['response'] = checkCoupon($_POST);
    print_r(json_encode($response, true));
?>