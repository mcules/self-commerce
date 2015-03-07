function disableAmzWidget(wrObj){
        if(wrObj.length > 0){
            var width = wrObj.width();
            var height = wrObj.height();
            var offset = wrObj.offset();
            var blocker = $('<div style="width:'+width+'px; height:'+height+'px; position:absolute; top:'+offset.top+'px;  left:'+offset.left+'px; background:#fff; opacity: 0.5; z-index:1000;">&nbsp;</div>');
            $('body').append(blocker);
        }
}

function amzPopupWindow(url) {
  window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=400,height=600,screenX=150,screenY=150,top=150,left=150')
}

function getURLParameter(name, source) {
            return decodeURIComponent((new RegExp('[?|&|#]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(source)||[,""])[1].replace(/\+/g,'%20'))||null; 
         }

function amazonLogout(){
    amazon.Login.logout();
	document.cookie = "amazon_Login_accessToken=; expires=Thu, 01 Jan 1970 00:00:00 GMT";
}
$(document).ready(function(){
        initAmazon();
        if($('#amazon_checkout_button_placehholder').length>0){
                        
                        $('#amazon_checkout_button_placehholder').height($('#payWithAmazonDiv').height());
                        var os = $('#amazon_checkout_button_placehholder').offset();
                        var os2 = $('#payWithAmazonDiv').offset();
                        $('#payWithAmazonDiv').css({position:'relative', top:(os.top-os2.top)+'px'});
                }
});
