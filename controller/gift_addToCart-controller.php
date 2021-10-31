<?php 
    require('../../../../wp-load.php');

    $couponMambo = gift_addToCart($_POST['data']);
    print_r(json_encode($couponMambo, true));
?>