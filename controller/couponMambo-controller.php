<?php 
    require('../../../../wp-load.php');

    $couponMambo = woo_couponMambo();
    print_r(json_encode($couponMambo, true));
?>