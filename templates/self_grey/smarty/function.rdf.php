<?php
/**
 * Smarty Plugin: RDF
 *
 * The Smarty function "rdf" fetches the rdf content from somewhere.  You
 * must declare the following arguments:
 * - var        Where the result is stored. is an array on success or 0
 * - url        The URI of the RDF-Feed
 * You can declare the following arguments (defaults in brackets)
 * - expire     Secounds when the cache will be expire (86400)
 * - nocache    Do not cache anything when set to 1 (0), will delete
 *              previous stored data.
 *
 * A directory must be created $smarty->cache_dir.'rdf/' in order to work.
 *
 * @link http://tiro.sourceforge.net/
 * @author Hinrich Donner <tis@tiro.de>
 * @copyright 2001-2003 by Hinrich Donner
 * @version 2003-03-24
 * @since 2003-03-24
 */

/**
 * Include MagPieRSS
 *
 * @link http://sourceforge.net/projects/magpierss/
 */
define('LIBRARY_DIR', DIR_WS_CLASSES.'/magpierss');
require_once LIBRARY_DIR.'/rss_fetch.inc';

/**
 * rdf
 */
function smarty_function_rdf($params, &$smarty) {
    if (!array_key_exists('var', $params)) {
	    $smarty->trigger_error("rdf: 'var' argument is missing!");
    }

    if (!array_key_exists('url', $params)) {
	    $smarty->trigger_error("rdf: 'url' argument is missing!");
    }

    // When the content should be expired
    if (array_key_exists('expire', $params)) {
	    $expire = (int) $params['expire'];
    }
    if (empty($expire) || ($expire < 1)) {
	    $expire = 10800;
    }

    // Cache or not
    if (array_key_exists('nocache', $params)) {
	    $nocache = ($params['nocache'] == 1);
    }
    else {
	    $nocache = 0;
    }

    // Make my information file names. MD5 is not unique but close to.
    $datafile = $smarty->cache_dir.'rdf/'.md5($params['url']);

    // Get information on my cache if any
    if (file_exists($datafile)) {
	    $last_access = filemtime($datafile);
    }
    else {
	    $last_access = 0;
    }

    // Test if we should cache
    if (($nocache == 1) || !file_exists($datafile) || (($last_access + $expire) < time())) {
        // Fetch actual data
        // (Cache error level to hide MagpieRSS warnings)
        $er = error_reporting(E_ERROR);
        $rdf = fetch_rss($params['url']);
        $raw_rdf =& $rdf->items;

        // Update cache, if required
        if (($nocache == 0) && is_array($raw_rdf)) {
            // Update
            $file_handle = fopen($datafile, 'w');
            if (false === fputs($file_handle, serialize($raw_rdf))) {
	            $smarty->trigger_error("rdf: Can't write the file '{$datafile}'! Check permissions!");
            }
            fclose($file_handle);
        }
        elseif (($nocache == 1) && file_exists($datafile)) {
            // Remove old data file
            unlink($datafile);
        }
    }
    else {
        // Retrieve stored data
        if (false === ($file_handle = fopen($datafile, 'r'))) {
            $smarty->trigger_error("rdf: Can't read the file '{$datafile}'! Check permissions!");
        }
        else {
            $raw_rdf = unserialize(fread($file_handle, 65535));
            fclose($file_handle);
        }
    }

    if (!isset($raw_rdf) || !is_array($raw_rdf)) {
        // Set flag
        $raw_rdf = false;

        // In case of fatal errors we have to make sure to delete the file if exists
        if (file_exists($datafile)) {
	        unlink($datafile);
        }
    }

    $smarty->assign($params['var'], $raw_rdf);
}