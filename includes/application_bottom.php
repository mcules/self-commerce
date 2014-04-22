<?php
/* -----------------------------------------------------------------------------------------
   $Id: application_bottom.php 17 2012-06-04 20:33:29Z deisold $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(application_bottom.php,v 1.14 2003/02/10); www.oscommerce.com
   (c) 2003	 nextcommerce (application_bottom.php,v 1.6 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

if (STORE_PAGE_PARSE_TIME == 'true') {
	$time_start = explode(' ', PAGE_PARSE_START_TIME);
	$time_end = explode(' ', microtime());
	$parse_time = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);
	error_log(strftime(STORE_PARSE_DATE_TIME_FORMAT) . ' - ' . getenv('REQUEST_URI') . ' (' . $parse_time . 's)' . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
}

if (DISPLAY_PAGE_PARSE_TIME != 'true') {
	echo '</div>';
}

if (DISPLAY_PAGE_PARSE_TIME == 'true') {
	$time_start = explode(' ', PAGE_PARSE_START_TIME);
	$time_end = explode(' ', microtime());
	$parse_time = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);
	echo '<div id="parsetime">Parse Time: ' . $parse_time . 's</div></div>';
}

if ( (GZIP_COMPRESSION == 'true') && ($ext_zlib_loaded == true) && ($ini_zlib_output_compression < 1) ) {
	if ( (PHP_VERSION < '4.0.4') && (PHP_VERSION >= '4') ) {
		xtc_gzip_output(GZIP_LEVEL);
	}
}

if (GOOGLE_ANAL_ON == 'true' && GOOGLE_ANAL_CODE != '') {
	echo '<script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script><script type="text/javascript">_uacct = "'.GOOGLE_ANAL_CODE.'";urchinTracker();</script>';
}
if (PIWIK_ANAL_ON == 'true' && PIWIK_ANAL_SITE_ID != '' && PIWIK_ANAL_URL) {
    ?>
    <!-- Piwik -->
    <script type="text/javascript">
        var _paq = _paq || [];
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        (function() {
            var u=(("https:" == document.location.protocol) ? "https" : "http") + "://<?php echo PIWIK_ANAL_URL; ?>/";
            _paq.push(['setTrackerUrl', u+'piwik.php']);
            _paq.push(['setSiteId', <?php echo PIWIK_ANAL_SITE_ID; ?>]);
            var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0]; g.type='text/javascript';
            g.defer=true; g.async=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
        })();
    </script>
    <noscript><p><img src="http://<?php echo PIWIK_ANAL_URL; ?>/piwik.php?idsite=<?php echo PIWIK_ANAL_SITE_ID; ?>" style="border:0;" alt="" /></p></noscript>
    <!-- End Piwik Code -->
    <?php
}
echo '</body></html>';