<?php 
/* ----------------------------------------------------------------------------------------- 
   $Id: outputfilter.note.php 779 2005-02-19 17:19:28Z novalis $ 

   XT-Commerce - community made shopping 
   http://www.xt-commerce.com 

   Copyright (c) 2003 XT-Commerce 
   ----------------------------------------------------------------------------------------- 
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/ 



function smarty_outputfilter_note($tpl_output, &$smarty) { 
    /* 
    The following copyright announcement is in compliance 
    to section 2c of the GNU General Public License, and 
    thus can not be removed, or can only be modified 
    appropriately.*/ 
$cop=' 
    <div id="copyright"> 
      Copyright  &copy; 2007 <a href="http://www.self-commerce.de">Self-Commerce</a> based on <a href="http://www.xtcommerce.de" target="_blank">XT:Commerce</a><br />
    </div>
    <div id="gnu_copy">
      Self-Commerce provides no warranty and is redistributable under the <a href="http://www.fsf.org/licenses/gpl.txt" target="_blank">GNU General Public License</a>
    </div> 
';
 function NoEntities($Input) {
    $TransTable1 = get_html_translation_table (HTML_ENTITIES);
    foreach($TransTable1 as $ASCII => $Entity) {
        $TransTable2[$ASCII] = '&#'.ord($ASCII).';';
    }
    $TransTable1 = array_flip ($TransTable1);
    $TransTable2 = array_flip ($TransTable2);
    return strtr (strtr ($Input, $TransTable1), $TransTable2);
}
function AmpReplace($Treffer) {
    return $Treffer[1].htmlentities(NoEntities($Treffer[2])).$Treffer[3];
}
$tpl_output = preg_replace_callback("/(<[^>]*['\"])(http[s]?\:\/\/[^'\"]*)(['\"][^<]*>)/Usi","AmpReplace",$tpl_output);
 
return $tpl_output.$cop; 
} 

?>
