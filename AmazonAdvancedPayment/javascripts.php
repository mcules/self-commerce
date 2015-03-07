<?php
/* --------------------------------------------------------------
   Amazon Advanced Payment APIs Modul  V2.00
   javascripts.php 2014-06-03

   alkim media
   http://www.alkim.de

   patworx multimedia GmbH
   http://www.patworx.de/

   Released under the GNU General Public License
   --------------------------------------------------------------
*/
if($_SESSION["amz_access_token_set_time"] < time()-3000){
    unset($_SESSION["amz_access_token"]);
}

$scope = 'profile postal_code payments:widget payments:shipping_address';

if (MODULE_PAYMENT_AM_APA_LPA_MODE == 'pay')
	$buttonType = 'PwA';
elseif (MODULE_PAYMENT_AM_APA_LPA_MODE == 'login')
	$buttonType = 'Login';
elseif (MODULE_PAYMENT_AM_APA_LPA_MODE == 'login_pay')
	$buttonType = 'PwA';
include_once('AmazonAdvancedPayment/AlkimAmazonClasses/AlkimAmazonHandler.class.php');
echo '<link rel="stylesheet" href="templates/'.CURRENT_TEMPLATE.'/amazon-checkout.css" type="text/css" />';

// load unallowed modules into array
$unallowed_modules = explode(',', $_SESSION['customers_status']['customers_status_payment_unallowed'].','.$order->customer['payment_unallowed']);

if (
        MODULE_PAYMENT_AM_APA_STATUS == 'True'
            &&
        !in_array('am_apa', $unallowed_modules)
            &&
        !(AMZ_DEBUG_MODE=='True' && $_SESSION["customers_status"]["customers_status_id"] != 0)
            &&
        !AlkimAmazonHandler::hasCartExcludedProducts()
        ) {
?>
<script type="text/javascript" src="AmazonAdvancedPayment/js/static.js"></script>
<script type="text/javascript">
<?php
if (MODULE_PAYMENT_AM_APA_LPA_MODE != 'pay'){
?>
window.onAmazonLoginReady = function() {
	amazon.Login.setClientId('<?php echo MODULE_PAYMENT_AM_APA_CLIENTID; ?>');
};
<?php
}
?>
$(document).ready(function(){
    $('#logoff_link, a[href*="logoff.php"]').click(function() {
		amazonLogout();
	});
    <?php
    if(strpos($_SERVER['PHP_SELF'],'logoff.php')!==false || strpos($PHP_SELF,'logoff.php')!==false){
    ?>
        amazonLogout();
    <?php } ?>
});

<?php
$sellerId = MODULE_PAYMENT_AM_APA_MERCHANTID;


	$sLeft = $xtPrice->currencies[$xtPrice->actualCurr]['symbol_left'];
	$sRight = $xtPrice->currencies[$xtPrice->actualCurr]['symbol_right'];

		?>

function initAmazon(){
    var amazonOrderReferenceId;

<?php if (MODULE_PAYMENT_AM_APA_LPA_MODE == 'pay') { ?>

            //PAY ONLY
 new OffAmazonPayments.Widgets.Button ({
   sellerId: '<?php echo $sellerId; ?>',
   buttonSettings: {color:'<?php echo AMZ_BUTTON_COLOR; ?>', size:'<?php echo AMZ_BUTTON_SIZE; ?>'},
   <?php
if((AMZ_DOWNLOAD_ONLY == 'True' || $_SESSION['cart']->get_content_type() == 'virtual') && (AMZ_AUTHORIZATION_MODE == 'fast_auth' || $_SESSION['customer_id'])){
    echo 'useAmazonAddressBook: false,';
}
?>
   onSignIn: function(orderReference) {
     amazonOrderReferenceId = orderReference.getAmazonOrderReferenceId();

    $('#payWithAmazonDiv').html('<img src="<?php echo AMZ_WAITING_IMG; ?>" /> <?php echo AMZ_WAITING; ?>');
    $.ajax({
            type: 'GET',
            url: '<?php echo xtc_href_link('checkout_amazon_handler.php', '', $request_type); ?>',
            data: 'handleraction=setsession&amazon_id=' + amazonOrderReferenceId,
            success: function(htmlcontent){
               window.location = '<?php echo xtc_href_link('checkout_amazon.php','','SSL'); ?>';
            }
    });
   },
   onError: function(error) {
    // your error handling code
   }
 }).bind("payWithAmazonDiv");

  if($('#payWithAmazonDiv_Top').length > 0){

  new OffAmazonPayments.Widgets.Button ({
   sellerId: '<?php echo $sellerId; ?>',
   buttonSettings: {color:'<?php echo AMZ_BUTTON_COLOR; ?>', size:'<?php echo AMZ_BUTTON_SIZE; ?>'},
   <?php
if((AMZ_DOWNLOAD_ONLY == 'True' || $_SESSION['cart']->get_content_type() == 'virtual') && (AMZ_AUTHORIZATION_MODE == 'fast_auth' || $_SESSION['customer_id'])){
    echo 'useAmazonAddressBook: false,';
}
?>
   onSignIn: function(orderReference) {
     amazonOrderReferenceId = orderReference.getAmazonOrderReferenceId();

    $('#payWithAmazonDiv_Top').html('<img src="<?php echo AMZ_WAITING_IMG; ?>" /> <?php echo AMZ_WAITING; ?>');
    $.ajax({
            type: 'GET',
            url: '<?php echo xtc_href_link('checkout_amazon_handler.php', '', $request_type); ?>',
            data: 'handleraction=setsession&amazon_id=' + amazonOrderReferenceId,
            success: function(htmlcontent){
               window.location = '<?php echo xtc_href_link('checkout_amazon.php','','SSL'); ?>';
            }
    });
   },
   onError: function(error) {
    // your error handling code
   }
 }).bind("payWithAmazonDiv_Top");


 }


 if($('#amazon_checkout_button_cart').length > 0){

  new OffAmazonPayments.Widgets.Button ({
   sellerId: '<?php echo $sellerId; ?>',
   buttonSettings: {color:'<?php echo AMZ_BUTTON_COLOR; ?>', size:'<?php echo AMZ_BUTTON_SIZE; ?>'},
   <?php
if((AMZ_DOWNLOAD_ONLY == 'True' || $_SESSION['cart']->get_content_type() == 'virtual') && (AMZ_AUTHORIZATION_MODE == 'fast_auth' || $_SESSION['customer_id'])){
    echo 'useAmazonAddressBook: false,';
}
?>
   onSignIn: function(orderReference) {
     amazonOrderReferenceId = orderReference.getAmazonOrderReferenceId();

    $('#amazon_checkout_button_cart').html('<img src="<?php echo AMZ_WAITING_IMG; ?>" /> <?php echo AMZ_WAITING; ?>');
    $.ajax({
            type: 'GET',
            url: '<?php echo xtc_href_link('checkout_amazon_handler.php', '', $request_type); ?>',
            data: 'handleraction=setsession&amazon_id=' + amazonOrderReferenceId,
            success: function(htmlcontent){
               window.location = '<?php echo xtc_href_link('checkout_amazon.php','','SSL'); ?>';
            }
    });
   },
   onError: function(error) {
    // your error handling code
   }
 }).bind("amazon_checkout_button_cart");


 }
<?php } else { 
            //WITH LOGIN FUNCTIONALITY 
            
            $isSsl = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443);
            $forceRedirect = (MODULE_PAYMENT_AM_APA_POPUP=='False');
            $useRedirect = ($forceRedirect || !$isSsl);
            

?>
                
	                        var authRequest;
	                        if($('.amazonLoginWr').length > 0){
	                            
	                           $('.amazonLoginWr').each(function(){
		                            OffAmazonPayments.Button($(this).attr('id'), "<?php echo $sellerId; ?>", {
				                            type: "<?php echo AMZ_BUTTON_TYPE_LOGIN; ?>", 
				                            size:'<?php echo AMZ_BUTTON_SIZE_LPA; ?>',
				                            color:'<?php echo AMZ_BUTTON_COLOR_LPA; ?>',
				                            authorization: function() {
				                            loginOptions =  {scope: "<?php echo $scope; ?>", popup: <?php echo ($useRedirect?'false':'true')?>};
				                            authRequest = amazon.Login.authorize (loginOptions<?php echo ($useRedirect?", '".xtc_href_link('checkout_amazon_login_processing.php', '', 'SSL', false)."'":''); ?>);
			                            },    
			                            onSignIn: function(orderReference) {
			                                var amazonOrderReferenceId = orderReference.getAmazonOrderReferenceId();
				                            $.ajax({
					                            type: 'GET',
					                            url: '<?php echo xtc_href_link('checkout_amazon_handler.php', '', $request_type); ?>',
					                            data: 'handleraction=setusertoshop&access_token=' + authRequest.access_token + '&amazon_id=' + amazonOrderReferenceId,
					                            success: function(htmlcontent){
						                            if (htmlcontent == 'error') {
							                            alert('An error occured - please try again or contact our support');
						                            } else {
							                            window.location = htmlcontent;
						                            }					   
					                            }
				                            });				
			                            },
			                            onError: function(error) {
				                            console.log(error); 
			                            }
		                            });
		                            
		                            
		                      });
	                        }
	                        <?php if (MODULE_PAYMENT_AM_APA_LPA_MODE == 'login_pay') { ?>
	                        if($('#payWithAmazonDiv').length > 0){
		                        OffAmazonPayments.Button("payWithAmazonDiv", "<?php echo $sellerId; ?>", {
				                        type: "<?php echo AMZ_BUTTON_TYPE_PAY; ?>", 
				                        size:'<?php echo AMZ_BUTTON_SIZE_LPA; ?>',
				                        color:'<?php echo AMZ_BUTTON_COLOR_LPA; ?>',
				                        authorization: function() {
				                        loginOptions =  {scope: "<?php echo $scope; ?>", popup: <?php echo ($useRedirect?'false':'true')?>};
				                        authRequest = amazon.Login.authorize (loginOptions<?php echo ($useRedirect?", '".xtc_href_link('checkout_amazon_login_processing.php', 'action=checkout', 'SSL', false)."'":''); ?>);
			                        },
			                        onSignIn: function(orderReference) {
				                        amazonOrderReferenceId = orderReference.getAmazonOrderReferenceId();
				
				                        $('#payWithAmazonDiv').html('<img src="<?php echo AMZ_WAITING_IMG; ?>" /> <?php echo AMZ_WAITING; ?>');
				                        $.ajax({
						                        type: 'GET',
						                        url: '<?php echo xtc_href_link('checkout_amazon_handler.php', '', $request_type); ?>',
						                        data: 'handleraction=setusertoshop&access_token=' + authRequest.access_token + '&amazon_target=checkout&amazon_id=' + amazonOrderReferenceId,
						                        success: function(htmlcontent){
						                                    if (htmlcontent == 'error') {
							                                    alert('An error occured - please try again or contact our support');
						                                    } else {
							                                   window.location = htmlcontent;
						                                    }			
						                        }
				                        });
			                        },
			                        onError: function(error) {
				                        console.log(error); 
			                        }
		                        });
		                        <?php
		                            if(isset($_SESSION["amz_access_token"]) && $_SESSION["amz_access_token"] != ''){
		                            ?>
		                            var buttonImg = $('#payWithAmazonDiv').find('img').attr('src');
		                            var newButton = $('<img src="'+buttonImg+'" style="cursor:pointer;" />');
		                            $('#payWithAmazonDiv').html(newButton);
		                            newButton.click(function(){
		                                window.location.href = "<?php echo xtc_href_link('checkout_amazon.php','fromRedirect=1','SSL');?>";
		                            });
		                            <?php
		                            }
		                        ?>
	                        }
	                        <?php } ?>
<?php } ?>
}
<?php
	if (strpos($_SERVER['PHP_SELF'],'checkout_amazon.php')!==false || strpos($PHP_SELF,'checkout_amazon.php')!==false) {
?>
			var setGV = 0;
			var runningUpdate;
			function update_shipping(amazon_id, address_changed) {
	      		var select_id = $("input[name=cba_select_shipping]:checked").attr('id');
	      		$.ajax({
					type: "GET",
				   	url: '<?php echo xtc_href_link('checkout_amazon_handler.php', '', $request_type); ?>',
				   	data: 'shippingAddressChanged='+(address_changed?1:0)+'&amazon_id=' + amazon_id+'&cba_select_shipping=' + $("input[name=cba_select_shipping]:checked").val(),
				   	success: function(data){
						inputJson(data);
						$('#' + select_id).attr('checked',true);
						if(address_changed){
						    updateAmzBoxes();
						}
					}
				});
			}

			function submitFunctionGV() {
            	$.ajax({
			   		type: "GET",
			   		url: '<?php echo xtc_href_link('checkout_amazon_handler.php', '', $request_type); ?>',
			   		data: 'handleraction=cot_gv_setter&cot_gv=' + ($("input[name=cot_gv]").attr("checked") ? '1' : '0'),
			   		success: function(htmlcontent){
				    	setGV = htmlcontent;
						updateAmzBoxes()
			   		}
				});
			}



			$(document).ready(function() {
        		//updateAmzBoxes();
			});

            function updateAmzBoxes(){
                var select_id = $("input[name=cba_select_shipping]:checked").attr('id');
                if(typeof(runningUpdate)!='undefined'){
                    runningUpdate.abort();
                }
                runningUpdate = $.ajax({
					type: "GET",
				   	url: '<?php echo xtc_href_link('checkout_amazon_handler.php', '', $request_type); ?>',
				   	data: 'cba_select_shipping=' + $("input[name=cba_select_shipping]:checked").val(),
				   	success: function(data){
						inputJson(data);
						$('#' + select_id).attr('checked',true);
					}
				});
            }


            function inputJson(data){
                data = $.parseJSON(data);
                $('#AmazonProducts').html(data.products);
                $('#AmazonOrderTotals').html(data.total);
                $('#AmazonShipping').html(data.shipping);
                lastButtonResponse = data.showButton;
                if(data.showButton && isPaymentAvailable){
                    $('#amazon_button').show();
                }else{
                    $('#amazon_button').hide();
                }
            }

<?php
if(!$_SESSION["amazon_id"] && $_SESSION["amz_access_token"]){
?>
        var accessToken = '<?php echo $_SESSION["amz_access_token"]; ?>';
        if (typeof accessToken === 'string' && accessToken.match(/^Atza/)) {
            document.cookie = "amazon_Login_accessToken=" + accessToken +";secure";
        }


        window.onAmazonLoginReady = function() {
            amazon.Login.setClientId('<?php echo MODULE_PAYMENT_AM_APA_CLIENTID; ?>');
            amazon.Login.setUseCookie(true); 
        };
<?php        
}
echo "	
var isPaymentAvailable = false;
var lastButtonResponse = false;
var amzAddressSelectCounter = 0;
$(document).ready(function() {

var amazonOrderReferenceId = '".$_SESSION['amazon_id']."';
if($('#addressBookWidgetDiv').length > 0){
new OffAmazonPayments.Widgets.AddressBook({
  sellerId: '". $sellerId ."',";

if(!$_SESSION["amazon_id"]){
?>
  onOrderReferenceCreate: function(orderReference) {
                        $.ajax({
                                    type: 'GET',
                                    url: '<?php echo xtc_href_link('checkout_amazon_handler.php', '', $request_type); ?>',
                                    data: 'handleraction=setsession&amazon_id=' + orderReference.getAmazonOrderReferenceId(),
                                    success: function(htmlcontent){
                                        if(htmlcontent.indexOf('{') != -1){
                                            inputJson(htmlcontent);
                                        }
                                        //isPaymentAvailable = false;
                                        $('#walletWidgetDiv').show();
                                        updateAmzBoxes();
                                        
                                    }
                            });
  },
  <?php 
  }else{
  ?>
  amazonOrderReferenceId: amazonOrderReferenceId,
  <?php
  }
  
echo "
  // amazonOrderReferenceId obtained from Button widget
  onAddressSelect: function(orderReference) {
    amzAddressSelectCounter++;
    if(orderReference.getContractId() != null || amzAddressSelectCounter > 1){
    //isPaymentAvailable = false;
    $('#walletWidgetDiv').show();
   // $('#amazon_button').hide();
    update_shipping(amazonOrderReferenceId, true);
    }
  },
  design: {
     designMode: 'responsive'
  },
  onError: function(error) {
   // your error handling code
   console.log(error.getErrorMessage());
  }
}).bind(\"addressBookWidgetDiv\");
}else{
    $('#walletWidgetDiv').show();
}
new OffAmazonPayments.Widgets.Wallet({
  sellerId: '". $sellerId ."',
  amazonOrderReferenceId: amazonOrderReferenceId,
  // amazonOrderReferenceId obtained from Button widget
  design: {
    designMode: 'responsive'
  },
  onPaymentSelect: function(orderReference) {
     isPaymentAvailable = true;
    if(lastButtonResponse){
        $('#amazon_button').show();
    }
  },
  onError: function(error) {
    // your error handling code
    console.log(error.getErrorMessage());
  }
}).bind(\"walletWidgetDiv\");

});
";
?>

		
			$(document).ready(function () {
				$(".rm_slide_header").click(function () {
					var elm = $(this);
			        $(this).next('.rm_slide_content').slideToggle('medium', function() {
						elm.find('.open').toggle(); elm.find('.close').toggle();
					});
				});
		    	$(".cba_confirm_boxes").click(function() {
					if ($(this).attr('checked') != true) {
		            } else {
		            	if ($(this).attr("name") == 'conditions') {
		                	$('body').find(".rm_slide_content:eq(1)").hide('medium');
							$('body').find(".close:eq(1)").hide(); $('body').find(".open:eq(1)").show();
		                } else {
		                    $('body').find(".rm_slide_content:eq(0)").hide('medium');
		                    $('body').find(".close:eq(0)").hide(); $('body').find(".open:eq(0)").show();
		                }
					}
				});
			});

			function cba_submit_order() {
		    	var error = 0;
		        $(".cba_confirm_boxes").each(
		        	function(intIndex, elem) {
		            	if ($(this)[0].checked != true) {
		                	error = 1;
		          		}
		        	}
				);
		    	if ($("#cba_allow_shipping").attr('value') == '0') {
		    		error = 2;
				}
				if (error == 1) {
					alert('<?php echo ACCEPT; ?>');
				} else if (error == 2) {
					alert('<?php echo NO_SHIPPING; ?>');
				} else {
					disableAmzWidget($('#addressBookWidgetDiv'));
					disableAmzWidget($('#walletWidgetDiv'));
					$('#amazon_button').hide();
					$('#loading_image').show();
					$('#checkout_amazon').submit();

				}
		    }


<?php
	}elseif(strpos($_SERVER['PHP_SELF'],'checkout_amazon_wallet.php')!==false || strpos($PHP_SELF,'checkout_amazon_wallet.php')!==false){
		echo "

                $(document).ready(function() {

                var amazonOrderReferenceId = '".$_SESSION['amazon_id']."';



                new OffAmazonPayments.Widgets.Wallet({
                  sellerId: '". $sellerId ."',
                  amazonOrderReferenceId: amazonOrderReferenceId,

                  design: {
    designMode: 'responsive'
  },
                  onPaymentSelect: function(orderReference) {
                    $('#amazon_button').show();
                  },
                  onError: function(error) {
                    console.log(error.getErrorMessage());
                    console.log(error);
                    $('#amazon_button').hide();
                  }
                }).bind(\"walletWidgetDiv\");

                });";
	}elseif(strpos($_SERVER['PHP_SELF'],'checkout_amazon_set_address.php')!==false || strpos($PHP_SELF,'checkout_amazon_set_address.php')!==false){
?>	
var amzAddressSelectCounter = 0;

<?php

if(!$_SESSION["amazon_id"] && $_SESSION["amz_access_token"]){
?>
        var accessToken = '<?php echo $_SESSION["amz_access_token"]; ?>';
        if (typeof accessToken === 'string' && accessToken.match(/^Atza/)) {
            document.cookie = "amazon_Login_accessToken=" + accessToken +";secure";
	}
        window.onAmazonLoginReady = function() {
            amazon.Login.setClientId('<?php echo MODULE_PAYMENT_AM_APA_CLIENTID; ?>');
            amazon.Login.setUseCookie(true); 
        };
<?php        
}
?>
$(document).ready(function() {

var amazonOrderReferenceId = '<?php echo $_SESSION['amazon_id']; ?>';
if($('#addressBookWidgetDiv').length > 0){

new OffAmazonPayments.Widgets.AddressBook({
  sellerId: '<?php echo $sellerId; ?>',
  
  <?php
  // amazonOrderReferenceId is not set in redirect mode
  if($_SESSION["amazon_id"]){
  ?>
  amazonOrderReferenceId: amazonOrderReferenceId,
  <?php 
  }
  ?>
  // amazonOrderReferenceId obtained from Button widget
  onAddressSelect: function(orderReference) {
    amzAddressSelectCounter++;
    if(orderReference.getContractId() != null || amzAddressSelectCounter > 1){
    $.ajax({
					type: "GET",
				   	url: '<?php echo xtc_href_link('checkout_amazon_handler.php', '', $request_type); ?>',
				   	data: 'handleraction=setAddress',
				   	success: function(data){
						$('#setAddressResponse').html(data);
					}
				});
	}			
  },
  <?php
  if(!$_SESSION["amazon_id"]){
  ?>
  onOrderReferenceCreate: function(orderReference) {
                        $.ajax({
                                    type: 'GET',
                                    url: '<?php echo xtc_href_link('checkout_amazon_handler.php', '', $request_type); ?>',
                                    data: 'handleraction=setAddress&amazon_id=' + orderReference.getAmazonOrderReferenceId(),
                                    success: function(htmlcontent){}
                            });
  },
  <?php 
  }
  ?>
  design: {
    designMode: 'responsive'
  },
  onError: function(error) {
   // your error handling code
   console.log(error.getErrorMessage());
  }
}).bind("addressBookWidgetDiv");
}
});
<?php

	
	}elseif(strpos($_SERVER['PHP_SELF'],'checkout_amazon_login_processing.php')!==false || strpos($PHP_SELF,'checkout_amazon_login_processing.php')!==false){
        ?>
         
        var accessToken = getURLParameter("access_token", location.hash);
        
        
        $(document).ready(function() {	
	        $.ajax({
					                                    type: 'GET',
					                                    url: '<?php echo xtc_href_link('checkout_amazon_handler.php', '', $request_type); ?>',
					                                    data: 'handleraction=setusertoshop&access_token=' + accessToken<?php if(isset($_GET["action"])){echo ' + "&action='.$_GET["action"].'"';} ?>,
					                                    success: function(htmlcontent){
						                                    if (htmlcontent == 'error') {
							                                    alert('An error occured - please try again or contact our support');
						                                    } else {
							                                   window.location = htmlcontent;
						                                    }					   
					                                    }
				                                    });	
        });
        
        <?php
     }
?>
</script>
<?php     
	if (MODULE_PAYMENT_AM_APA_MODE == 'live') {
		if (MODULE_PAYMENT_AM_APA_LPA_MODE == 'pay')
			echo "<script type='text/javascript' src='https://static-eu.payments-amazon.com/OffAmazonPayments/de/js/Widgets.js?sellerId=".$sellerId."'></script>";
		else
			echo "<script type='text/javascript' src='https://static-eu.payments-amazon.com/OffAmazonPayments/de/lpa/js/Widgets.js'></script>";
	} else {
		if (MODULE_PAYMENT_AM_APA_LPA_MODE == 'pay')
			echo "<script type='text/javascript' src='https://static-eu.payments-amazon.com/OffAmazonPayments/de/sandbox/js/Widgets.js?sellerId=".$sellerId."'></script>";
		else
			echo "<script type='text/javascript' src='https://static-eu.payments-amazon.com/OffAmazonPayments/de/sandbox/lpa/js/Widgets.js'></script>";
	}
}
?>
