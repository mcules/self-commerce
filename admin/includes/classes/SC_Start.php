<?php
/* --------------------------------------------------------------
   $Id$

   Self-Commerce - Fresh up your eCommerce
   http://www.self-commerce.de
   Copyright (c) 2012 Self-Commerce
   --------------------------------------------------------------
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------*/
	require_once(DIR_FS_CATALOG . 'inc/sc_xtc_href_link.inc.php');
	
	class SC_Start {

		var $host			= 'http://news.self-commerce.de';
		var $path			= '/Shop_Admin.php';

		/**
		 *	get the Self-Commerce News
		 */
		function getNews() {
			/* check if curl exists, else use fsockopen or iframe */
			if (function_exists('curl_init')) { $data = $this->use_curl(); }
			elseif(function_exists('fsockopen')) { $data = $this->use_fsockopen(); }
			else { $data =  $this->use_iframe(); }
			
			return $data;		
		}

		/**
		 *	curl
		 */
		function use_curl() {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->host . $this->path);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$data = curl_exec($ch);
			curl_close($ch);
			return $data;
		}

		/**
		 * fsockopen
		 */
		function use_fsockopen() {
			$sock = fsockopen(str_replace('http://', '', $this->host), 80, $errno, $errstr, 5);
			fputs($sock, "GET " . $this->path . " HTTP/1.1\r\n");
			fputs($sock, "Host: " . str_replace('http://', '', $this->host) . "\r\n");
			fputs($sock, "Connection: close\r\n\r\n");

			while(!feof($sock)) { $data .= fgets($sock, 4096); }
			fclose($sock);
			return substr($data, strpos($data, '<'));
		}
		
		/**
		 * using iframe
		 */
		function use_iframe() {
			return '<iframe src="' . $this->host . $this->path . '" width="100%" scrolling="yes" height="400" frameborder="0"></iframe>';
		}
	}
?>