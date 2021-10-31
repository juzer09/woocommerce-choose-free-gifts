const page_url = pageUrl(window.location.href);
const coupon_name = 'royal600';

function pageUrl(page_url) {
    if (page_url.includes('localhost')) {
        ajax_url = 'http://localhost/preeti/';
    }
    else {
        ajax_url = 'https://preetiproducts.com/';
    }

    return ajax_url;
}

function couponMambo() {
    jQuery(document).on('click', '.coupon_btn', function() {
        setTimeout(() => {
            fetchgifts();
        }, 4500);
    });
}

function checkCouponAdded() {
        jQuery.ajax({
            type: "post",
            data: {
                'coupon': coupon_name
            },
            url: page_url + "wp-content/plugins/woocommerce-choose-free-gifts/controller/check-coupon-controller.php",
            success: function (response_form_action) {
                data = jQuery.parseJSON(response_form_action);
                if(data.response) {
                    fetchgifts();
                }
            }
        });

}

function fetchgifts() {
    if(jQuery('.coupon-' + coupon_name + ' td').attr('data-title').includes('coupon_name')) {
        jQuery.ajax({
            type: "post",
            url: page_url + "wp-content/plugins/woocommerce-choose-free-gifts/controller/couponMambo-controller.php",
            success: function (response_form_action) {
                response_form_action = jQuery.parseJSON(response_form_action);
                addOptions(response_form_action);
            }
        });
    }
}

function addOptions(data) {    
    if(data) {
        const giftOptions = data;

        // addCouponOption(data);
        adjustFreeQuantityWork('Master');
        jQuery('.free_gifts').slideDown();
    }

    function addCouponOption(data) {
        data.forEach(element => {
            some = true;
            jQuery('.fg_item').each(function(){
                if(Number(jQuery(this).attr('data-id')) == Number(element.id)) {
                    some = false;
                }
            });

            if(some) {
                jQuery('.free_gifts table tbody').prepend('<tr><td>' + element.product + '</td><td><input class="fg_item" data-id="' + element.id + '" data-number="" value="' + element.qty_in_cart + '" data-variation="' + element.is_variation + '" data-parent="' + element.parent_id + '" data-value="' + element.value + '" type="number" min="0" step="1" max="' + element.maximum + '"><span>Max ' + element.maximum + '</span></td></tr>');
            }
        });
    }
}

function adjustFreeQuantity() {
    maxFree = 1600;
    jQuery(document).on('change', '.fg_item', function() {
        adjustFreeQuantityWork('Switch');
    });
}

function adjustFreeQuantityWork(state) {
    jQuery('.fg_item').each(function() {
        total = 0;
        jQuery('.fg_item').each(function() {
            mince = Number(jQuery(this).attr('data-value')) * Number(jQuery(this).val());
            total = Number(total) + Number(mince);
        });

        if(Number(total) > maxFree) {
            change_back = Number(jQuery(this).val() - Number(1));
            if(Number(change_back) < 0) {
                change_back = 0;
            }
            jQuery(this).val(change_back).attr('value', change_back);            
        }
        else {
            balance = Number(maxFree) - Number(total);
            jQuery('.fg_item').each(function() {
                mince = Number(balance) / Number(jQuery(this).attr('data-value'));
                jQuery(this).next('span').html('Max ' + Math.floor(mince));
            });
        }
    });

    if(state == 'Switch') {
        jQuery('.gift_atc').removeAttr('disabled');
    }
    else if(state == 'Master') {
        gift_addToCartWork();
    }
}

function gift_addToCart() {
    jQuery(document).on('click', '.gift_atc', function(a_obj) {
        a_obj.preventDefault();
        gift_addToCartWork();
    });
}

function gift_addToCartWork() {
    baseArr = [];
    jQuery('.fg_item').each(function() {
        if(Number(jQuery(this).val()) > Number(0)) {
            smallArr = {};
            smallArr['id'] = jQuery(this).attr('data-id');
            smallArr['qty'] = jQuery(this).val();
            smallArr['is_variation'] = jQuery(this).attr('data-variation');
            smallArr['parent_id'] = jQuery(this).attr('data-parent');
            
            baseArr.push(smallArr);
        }
    });
    
    jQuery.ajax({
        type: "post",
        data: {
            'data': baseArr
        },
        url: page_url + "wp-content/plugins/woocommerce-choose-free-gifts/controller/gift_addToCart-controller.php",
        success: function (response_form_action) {
            if(response_form_action) {
                jQuery('.update_cart_btn').removeAttr('disabled');
                jQuery('.update_cart_btn').click();
            }
        }
    });
}

function hideFreeGifts() {
    jQuery('.free_gifts').addClass('hidden');
}

function checkUpdateCart() {
    jQuery(document).on('click', '.update_cart_btn', function() {
        setTimeout(() => {
            adjustFreeQuantityWork();
        }, 4000);
    });
}


function cookie(item, requirement, value = '') {
    if (requirement == "Get") {
        var item = getCookie(item);
        return item;
    }
    else if (requirement == "Set") {
        setCookie(item, value, time() + (10 * 365 * 24 * 60 * 60));
        cookie(item, "Get");
    }
    else if (requirement == "Remove") {
        Cookies.remove(item);
    }
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function time() {
    var d = new Date();
    d.toLocaleString('en-US', { timeZone: 'Europe/London' })
    var n = d.getTime();

    return n;
}

jQuery(document).ready(function() {
    hideFreeGifts();
    couponMambo();
    checkCouponAdded();
    adjustFreeQuantity();
    gift_addToCart();
    checkUpdateCart();
});