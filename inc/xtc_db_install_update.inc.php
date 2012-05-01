<?php
/* --------------------------------------------------------------
   $Id: start.php 10 2012-04-28 03:21:05Z deisold $

   Self-Commerce - Fresh up your eCommerce
   http://www.self-commerce.de
   Copyright (c) 2012 Self-Commerce
   --------------------------------------------------------------*/

function xtc_db_install_update($database, $sql_file) {
    global $db_error;

    $db_error = false;

    if (!@xtc_db_select_db($database)) { xtc_db_select_db($database); }
    else { echo $db_error; }

    if (!$db_error) {
        if (file_exists($sql_file)) {
            $fd = fopen($sql_file, 'rb');
            $restore_query = fread($fd, filesize($sql_file));
            fclose($fd);
        } else {
            $db_error = 'SQL file does not exist: ' . $sql_file;
            return false;
        }

        $sql_array = array();
        $sql_length = strlen($restore_query);
        $pos = strpos($restore_query, ';');
        for ($i=$pos; $i<$sql_length; $i++) {
            if ($restore_query[0] == '#') {
                $restore_query = ltrim(substr($restore_query, strpos($restore_query, "\n")));
                $sql_length = strlen($restore_query);
                $i = strpos($restore_query, ';')-1;
                continue;
            }
            if ($restore_query[($i+1)] == "\n") {
                for ($j=($i+2); $j<$sql_length; $j++) {
                    if (trim($restore_query[$j]) != '') {
                        $next = substr($restore_query, $j, 6);
                        if ($next[0] == '#') {
                            // find out where the break position is so we can remove this line (#comment line)
                            for ($k=$j; $k<$sql_length; $k++) {
                                if ($restore_query[$k] == "\n") { break; }
                            }
                            $query = substr($restore_query, 0, $i+1);
                            $restore_query = substr($restore_query, $k);
                            // join the query before the comment appeared, with the rest of the dump
                            $restore_query = $query . $restore_query;
                            $sql_length = strlen($restore_query);
                            $i = strpos($restore_query, ';')-1;
                            continue 2;
                        }
                        break;
                    }
                }
                // get the last insert query
                if ($next == '') { $next = 'insert'; }
                if ( (eregi('INSERT', $next)) || (eregi('ALTER', $next)) ) {
                    $next = '';
                    $sql_array[] = substr($restore_query, 0, $i);
                    $restore_query = ltrim(substr($restore_query, $i+1));
                    $sql_length = strlen($restore_query);
                    $i = strpos($restore_query, ';')-1;
                }
            }
        }
        foreach($sql_array AS $Sql) {
            xtc_db_query($Sql);
        }
    }
    else { return false; }
}
?>