<?php
/* -----------------------------------------------------------------------------------------
   $Id: general.js.php 1262 2005-09-30 10:00:32Z mz $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


   // this javascriptfile get includes at the BOTTOM of every template page in shop
   // you can add your template specific js scripts here
?>
<script src="<?php echo 'templates/'.CURRENT_TEMPLATE; ?>/javascript/jquery.js" type="text/javascript"></script>
<script src="<?php echo 'templates/'.CURRENT_TEMPLATE; ?>/javascript/bootstrap.min.js" type="text/javascript"></script>
<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
  <script src="<?php echo 'templates/'.CURRENT_TEMPLATE; ?>/javascript/html5shiv.js"></script>
  <script src="<?php echo 'templates/'.CURRENT_TEMPLATE; ?>/javascript/css3-mediaqueries.js"></script>
<![endif]-->
<script src="<?php echo 'templates/'.CURRENT_TEMPLATE; ?>/javascript/css_button.js" type="text/javascript"></script>
<script type="text/javascript">
$( document ).ready(function() {
// Servicebox
$('#ServiceTabs a').click(function (e) {e.preventDefault();var url = $(this).attr("data-url");var href = this.hash;var pane = $(this);$(href).load(url,function(result){pane.tab('show');});});
// load first tab content
var url1 = $('#ServiceTabs li.active a').attr("data-url");$('#ServiceModal').on('shown', function () {$('.tab-pane.first').load(url1,function(result){$('.active a').tab('show');});})
// Bilder in Modalbox
$('.thickbox').click(function(e){e.preventDefault();var imgPath = $(this).attr("href");$('#modal #modalLabel').html(this.title);$('#modal .modal-body').html('<img src="' +imgPath+ '" alt="" class="center-block" />');$("#modal").modal('show');$('#modal').on('hidden', function() {$(this).removeData('modal');});});
// Content in Modalbox
$('.contentbox').click(function(ev){ev.preventDefault();var target = $(this).attr("href");$('#modal #modalLabel').html(this.title);$('#modal').modal({remote: target});$("#modal").modal('show');$('#modal').on('hidden', function() {$(this).removeData('modal');});});
// collapse boxes in and out
var c = document.cookie;
$('.collapsebox').each(function () {if (this.id) { var pos = c.indexOf(this.id + "_collapse_in="); if (pos > -1) { c.substr(pos).split('=')[1].indexOf('false') ? $(this).addClass('in') : $(this).removeClass('in');}}}).on('hidden shown', function () {if (this.id) { document.cookie = this.id + "_collapse_in=" + $(this).hasClass('in');}});
$('.collapsebox').each(function () {if($(this).hasClass("in")){$(this).parent('div').find('span.caret').addClass('caret-reversed');}});
$('.btn-default.btn-mini').click(function () {if ($(this).children().hasClass('caret-reversed')){$(this).children().removeClass('caret-reversed');}else{$(this).children().addClass('caret-reversed');}});
//superfishmenu
$('ul.dropdown-menu [data-toggle=dropdown]').on('click', function(event){event.preventDefault();event.stopPropagation();$(this).parent().siblings().removeClass('open');$(this).parent().toggleClass('open');});
//noch superfish genaue Bildschirmbreite
function getWindowWidth(){var div, body, W = window.browserScrollbarWidth;if (W === undefined){body = document.body, div = document.createElement('div');div.innerHTML = '<div style="width: 50px; height: 50px; position: absolute; left: -100px; top: -100px; overflow: auto;"><div style="width: 1px; height: 100px;"></div></div>';div = div.firstChild;body.appendChild(div);W = window.browserScrollbarWidth = div.offsetWidth - div.clientWidth;body.removeChild(div);}return($( document ).width() +W);};
var $WindowWidth = getWindowWidth();
if ($WindowWidth < 979){$('.dropdown.men').find('.dropdown-submenu').removeClass('dropdown-submenu').addClass('dropdown');}
// Carousel
$('#myCarousel').carousel({interval: 5000})
// accordion
$('div.accordion-body').on('shown', function () { $(this).parent("div").find(".icon-chevron-down").removeClass("icon-chevron-down").addClass("icon-chevron-up");});
$('div.accordion-body').on('hidden', function () { $(this).parent("div").find(".icon-chevron-up").removeClass("icon-chevron-up").addClass("icon-chevron-down");});
// GotoTop
$(window).scroll(function() {if ($(this).scrollTop()) {$('#GotoTop').fadeIn();} else {$('#GotoTop').fadeOut();}});$("#GotoTop").click(function() {$("html, body").animate({scrollTop: 0}, 1000);});
});
</script>
<?php
if (strstr($PHP_SELF, FILENAME_PRODUCT_INFO )) {
?>
<script src="<?php echo 'templates/'.CURRENT_TEMPLATE; ?>/javascript/cloud-zoom.1.0.3.min.js" type="text/javascript"></script>
<?php } ?>