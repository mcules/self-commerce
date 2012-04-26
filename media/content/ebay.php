<?php
########################################## 
# php eBay List 1.2.1
# Grab and Display your eBay auctions on your web site
# --------------------------------------------------                  
# Written by: Chrispian H. Burks
# email: chris@aeoninteractive.com                                       
# website: http://www.aeoninteractive.net                                  
# Please submit bugs to chris@aeoninteractive.com
# Special Touch by: Joseph Smith at joe@smittys.pointclark.net
#
# Thanks to all the users who sent in feedback and to everyone at
# sitepoint.com for all their help and ideas. 
# --------------------------------------------------                  
# This software is linkware! Please leave the link back 
# to Aeon Interactive. Feel free to modify this script. 
# If you do, send it to me and I'll include any cool 
# mods in the next release.
# --------------------------------------------------                  
# Changes:
#   1.2.1
#     - Added Option to open links in a new window
#
#   1.2
#     - Added Sort by option (Joseph Smith)
#     - Added Display Ended Auctions On/Off option (Joseph Smith)
#     - Added Time Zone Offset (Joseph Smith)
#
#   1.1 
#     - Complete Rewrite of the code. Now lean and fast (still needs caching for large lists).
#     - Changed to use serach by user rather than about user page
#       
#   1.0 
#     - Added Limit Option
#     - Added Thumbnails On/Off Option
#
# To Do:
#   - Add caching to speed up large lists
# --------------------------------------------------                  
# Install: Define your variables. Upload. Done.
#
########################################## 
#
#
# Modifications for German language,
# .de ebay servers and some layout changes
# by polkhigh33, highscore.dad@gmx.de
#
#
###########################################


$ebayid = EBAY_NAME;
$thumbs = SHOW_THUMP;
$newwindow = NEW_WIN;
$table_border_color = TABLE_BORDER;
$table_header_color = TABLE_BORDER_ROW;
$table_row1_color = TABLE_ALT_COLOR_1;
$table_row2_color = TABLE_ALT_COLOR_2;
$timezone = TIME_ZONE;
$since = SHOW_SINCE;
$sort = SORT_BY;
$limit = LIMIT_TO;


//-----------------------------------------------------------------
// Nothing Below Here needs editing.

// Start our border table
echo "	
	<TABLE border=\"0\" cellPadding=\"0\" cellSpacing=\"0\" width=\"100%\">
	<TR><TD bgcolor=\"$table_border_color\"><TABLE border=\"0\" cellPadding=\"2\" cellSpacing=\"1\" width=\"100%\">
	<TR>
	<TD bgcolor=\"$table_header_color\"><font size=\"2\"><strong>Artikel</strong></font></TD>
	
	<TD width=\"100%\" bgcolor=\"$table_header_color\"><font size=\"2\"><strong>Beschreibung</strong></font></TD>
	<TD bgcolor=\"$table_header_color\"><font size=\"2\"><strong>Dauer</strong></font></TD>
	<TD bgcolor=\"$table_header_color\"><font size=\"2\"><strong>Preis</strong></font></TD>
	<TD bgcolor=\"$table_header_color\"><font size=\"2\"><strong>Endet</strong></font></TD>
	
	</TR>	
";


// Build the ebay url	
$URL = "http://cgi6.ebay.at/ws/eBayISAPI.dll?MfcISAPICommand=ViewListedItems&userid=$ebayid&since=$since&sort=$sort&rows=0"; 

// Where to Start grabbing and where to End grabbing
$Start = "Zum Sortieren klicken Sie auf die Spaltenüberschriften";
$GrabEnd = "";

// Open the file
$file = fopen("$URL", "r");

// Read the file

if (!function_exists('file_get_contents')) {
     $r = fread($file, 80000);
} 
else {
    $r = file_get_contents($URL);  
}



// Grab just the contents we want
$stuff = eregi("$Start(.*)$GrabEnd", $r, $content);

// Get rid of some rubbish we don't need.
// And set things up to be split into lines and items.

$content[1] = ereg_replace("</a><table border=\"1\" cellpadding=\"3\".*</td><br /></tr>","",$content[1]);
$content[1] = ereg_replace("<tr bgcolor=\"#CCCCFF\">.*Höchstbietender\/Status","",$content[1]);
$content[1] = ereg_replace("<tr bgcolor=\"#CCCCFF\">.*Höchstbietender\/Status","",$content[1]);
$content[1] = str_replace("</table>", "", $content[1]);
$content[1] = str_replace("</th>", "", $content[1]);
$content[1] = str_replace("\r\n", "", $content[1]);
$content[1] = str_replace("\n", "", $content[1]);
$content[1] = str_replace("\r", "", $content[1]);
$content[1] = str_replace("<td align=\"center\">", "", $content[1]);
$content[1] = str_replace("<td>", "", $content[1]);
$content[1] = str_replace("<td align=\"right\">", "", $content[1]);
$content[1] = str_replace("<font color=\"\">", "", $content[1]);
$content[1] = str_replace("</font>", "", $content[1]);
$content[1] = str_replace("<tr bgcolor=\"#ffffff\">", "", $content[1]);
$content[1] = str_replace("<tr bgcolor=\"#efefef\">", "", $content[1]);
$content[1] = str_replace("<tr bgcolor=\"#efefef\">", "", $content[1]);
$content[1] = str_replace("</td>", "[ITEMS]", $content[1]);
$content[1] = str_replace("</tr>", "[LINES]\n", $content[1]);

/* freed: 02.09.2004 */
$content[1] = str_replace("<td color=\"\">", "", $content[1]);

// Line used during debug
// echo "<hr>$content[1]<br /><br /> <hr>";


// Close the file
fclose($file);

$stuff = $content[1];

// Build our first array for EOF
$items = explode("[LINES]",$stuff);

// Loop through our lines

$count = "0";

foreach ($items as $listing) {
	// Break apart each line into individual items

	list($Item,$Start,$End,$Price,$Title,$HighBidder ) = explode("[ITEMS]",$listing);

	//Use a countdown to get Time Left
	//We first need to break apart End and convert the months to numbers
	$seperate = split('[. :]', $End);

//	$seperate[0] = str_replace("Jan", "1", $seperate[0]);
//	$seperate[0] = str_replace("Feb", "2", $seperate[0]);
//  $seperate[0] = str_replace("Mar", "3", $seperate[0]);
//	$seperate[0] = str_replace("Apr", "4", $seperate[0]);
//	$seperate[0] = str_replace("May", "5", $seperate[0]);
//  $seperate[0] = str_replace("Jun", "6", $seperate[0]);
//  $seperate[0] = str_replace("Jul", "7", $seperate[0]);
//	$seperate[0] = str_replace("Aug", "8", $seperate[0]);
//	$seperate[0] = str_replace("Sep", "9", $seperate[0]);
//	$seperate[0] = str_replace("Oct", "10", $seperate[0]);
//	$seperate[0] = str_replace("Nov", "11", $seperate[0]);
//	$seperate[0] = str_replace("Dec", "12", $seperate[0]);
		
    	$day = $seperate[0];
    	$month = $seperate[1];
    	$year = $seperate[2];
    	$hour = $seperate[3]+$timezone; 
    	$minute = $seperate[4];
	$second = $seperate[5];

	// mktime is the marked time, and time() is the current time.
  // php5 ready 
	$target = mktime((int)$hour,(int)$minute,(int)$second,(int)$month,(int)$day,(int)$year);
	$diff = $target - time(); 

	$days = ($diff - ($diff % 86400)) / 86400; 
	$diff = $diff - ($days * 86400); 
	$hours = ($diff - ($diff % 3600)) / 3600; 
	$diff = $diff - ($hours * 3600); 
	$minutes = ($diff - ($diff % 60)) / 60; 
	$diff = $diff - ($minutes * 60); 
	$seconds = ($diff - ($diff % 1)) / 1; 

	// next we put it into a presentable format
	$Time_Left =  $days . "Tage" . " " . $hours . "Std" . " " . $minutes . "Min";
	
	// and last we want to print auction ended when the auction has ended
	if ($seconds < 0) {
		$TimeLeft = "Auktion beendet";
		}
	else {
		$TimeLeft = $Time_Left;
		}

		// Make sure we have content to print out and print it
		if ($Start && $End && $Title && ($count < $limit)) {

			$count++;
	
			$colour = ( $colour == "$table_row1_color" ) ? "$table_row2_color" : "$table_row1_color"; 

					$line = "<TR><TD bgcolor=\"$colour\" align=\"center\">$Item</TD><TD bgcolor=\"$colour\">$Start</TD><TD bgcolor=\"$colour\">$End</TD><TD bgcolor=\"$colour\"><font color=\"#FF0000\">$TimeLeft</font></TD><TD bgcolor=\"$colour\">$Price</TD><TD bgcolor=\"$colour\">$Title</TD><TD bgcolor=\"$colour\">$HighBidder</TD></TR>\n";
                    preg_match('/item=([^"&]*)/', $line, $match); 
					$itemnum=$match[1]; 

                  if ($newwindow == 1) {
				  $tnURL = "<a target=\"_blank\" href=\"http://cgi.ebay.de/ws/eBayISAPI.dll?ViewItem&item=$itemnum\"><img src=\"http://thumbs.ebay.com/pict/$itemnum.jpg\" border=\"0\"></a>";
                  $Item = str_replace("a href=", "a target=\"_blank\" href=", $Item);
				  $HighBidder = str_replace("a href=", "a target=\"_blank\" href=", $HighBidder);
				  }
				  
				  else {
				  $tnURL = "<a href=\"http://cgi.ebay.at/ws/eBayISAPI.dll?ViewItem&item=$itemnum\"><img src=\"http://thumbs.ebay.com/pict/$itemnum.jpg\" border=\"0\"></a>";
				  }
					
					         if ($newwindow == 1) {
				  $tnURL1 = "<a target=\"_blank\" href=\"http://cgi.ebay.de/ws/eBayISAPI.dll?ViewItem&item=$itemnum\">$Title</a>";
                  $Item = str_replace("a href=", "a target=\"_blank\" href=", $Item);
				  $HighBidder = str_replace("a href=", "a target=\"_blank\" href=", $HighBidder);
				  }
				  
				  else {
				  $tnURL1 = "<a href=\"http://cgi.ebay.at/ws/eBayISAPI.dll?ViewItem&item=$itemnum\">$Title</a>";
				  }
					
				// If Thumbnails are enabled show them
				 if ($thumbs == 1) {
	                echo "
	                <TR><TD bgcolor=\"#ffffff\" align=\"center\">$tnURL<br /><font size=\"1\">$Item</font></TD><TD bgcolor=\"$colour\"><font size=\"2\"><strong>$tnURL1</strong></font></TD><TD bgcolor=\"$colour\"><font color=\"#FF0000\" size=\"2\">$TimeLeft</font></TD><TD bgcolor=\"$colour\"><font size=\"2\">$Price</font></TD><TD bgcolor=\"$colour\"><font size=\"1\">$End</font></TD></TR>\n";
				 }
	
				// Otherwise just show the Bid Now link
				 else {
	                echo "<TR><TD bgcolor=\"#ffffff\" align=\"center\"><font size=\"1\">$Item</font></TD><TD bgcolor=\"$colour\"><font size=\"2\"><strong>$tnURL1</strong></font></TD><TD bgcolor=\"$colour\"><font color=\"#FF0000\" size=\"2\">$TimeLeft</font></TD><TD bgcolor=\"$colour\"><font size=\"2\">$Price</font></TD><TD bgcolor=\"$colour\"><font size=\"1\">$End</font></TD></TR>\n";
				 }


	
		}    
	
	}

// Wrap up the border table
echo "</TABLE></td></tr> </table>";

?>
