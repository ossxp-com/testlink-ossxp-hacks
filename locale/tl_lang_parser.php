#!/usr/bin/php
<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 * 
 * @filesource $RCSfile: common.php,v $
 * @version $Revision: 1.138 $ $Author: franciscom $
 * @modified $Date: 2009/01/26 21:52:49 $
 * @author 	Martin Havlat
 *
 * SCOPE: command line script: update localization files according the current english
 * 		file - add a new variables.
 * 
 * Usage: 
 * 	1. correct the first line to point php binary
 *  2. Modify a path to master file (en_GB) - $file_eng
 *  3. Linux: Allow execute - #chmod u+x tl_lang_parser.php
 *  4. Run the file with to-be-updated file as argument
 * 		#tl_lang_parser.php /home/havlatm/www/tl_head/locale/cs_CZ/strings.txt
 * 
 *  Note: to have report about parsing redirect script to file; for example
 * 		#tl_lang_parser.php strings.txt > report.txt
 */

/** Set path to your en_GB english file */
$file_eng = 'en_GB/strings.txt';

/** Set true if you would like to have original file with 'bck' extension */
$do_backup_file = FALSE;



// ---------------------------------------------------------------------------
if ($argc < 1)
{
	echo 'Usage: #tl_lang_parser.php <localization_file_to_be_updated>';
	exit;
}
else
	$file_lang_old = $argv[1];


//$file_lang_old = '/home/havlatm/www/tl_head/locale/cs_CZ/strings.txt';
$out = ''; // data for output file
$var_counter = 0;
$var_counter_new = 0;




echo "\n===== Start TestLink lang_parser =====\n";

// read english file
if (file_exists($file_eng) && is_readable ($file_eng))
{ 
	echo "English file ($file_eng) is readable OK.\n";
	$lines_eng = file( $file_eng );
}
else
{
	echo "English file ($file_eng) is readable - FAILED!. Exit.\n";
	exit;
}
// read language file
if (file_exists($file_lang_old) && is_readable ($file_lang_old))
{ 
	echo "File ({$file_lang_old}) is readable OK.\n";
	$lines_lang_old = file( $file_lang_old );
}
else
{
	echo $lines_lang_old." file is not readable - FAILED!. Exit.\n";
	exit;
}

$lines_eng_count = sizeof($lines_eng);
$lines_old_count = sizeof($lines_lang_old);
echo "English file lines =".$lines_eng_count."\n";
echo "Old Language file lines =".$lines_old_count."\n";

// find end of english header	:\s(\d+)\s
for( $i = 0; $i < $lines_eng_count; $i++ )
{
    if (preg_match('/Revision:\s(\S+)\s/', $lines_eng[$i], $eng_revision) )
    {
        $revision_comment = $eng_revision[1];
        echo "English file revision: ".$revision_comment."\n";
    }
    if (preg_match("/\*\//", $lines_eng[$i]) )
    {
        echo "Eng: End of header is line = $i \n";
        $begin_line = $i + 1;
        $i = $lines_eng_count;
    }
}

// copy existing localization file header
for( $i = 0; $i < $lines_old_count; $i++ )
{
    if (preg_match("/\*\//", $lines_lang_old[$i]) )
    {
        echo "Old: End of header is line = $i \n";
        $begin_line_old = $i + 1;
        $i = $lines_old_count;
		$out .= " * Scripted update according en_GB string file (version: ".$revision_comment.") \n";
		$out .= " * --------------------------------------------------------------------" .
				"-------------- */\n";
    }
    else
		$out .= $lines_lang_old[$i];
}


// compile output array based on english file
for( $i = $begin_line; $i < $lines_eng_count; $i++ )
{
//    echo "$i >> {$lines_eng[$i]}\n";

	// copy comments 
    if (preg_match("/^\/\//", $lines_eng[$i]) )
    {
        echo "(line $i) Copy comment\n";
        $out .= $lines_eng[$i];
    }

	// copy empty line
    elseif (preg_match('/^([\s\t]*)$/', $lines_eng[$i]))
    {
        echo "(line $i) Empty line\n";
        $out .= "\n";
    }

	// parse a line with variable definition
    elseif (preg_match('/^\$TLS_([\w]+)[\s]*=[\s]*(.*)$/', $lines_eng[$i], $parsed_line))
    {
        $var_counter++;
        $var_name = '$TLS_'.$parsed_line[1];
        $bLocalized = FALSE;
        $localizedLine = '';
//        print_r($parsed_line);
        
        // get localized value if defined - parse old localized strings
		for( $k = $begin_line_old; $k < $lines_old_count; $k++ )
		{
			if (preg_match('/^\\'.$var_name.'[\s]*=[\s].+$/', $lines_lang_old[$k]))
			{
		        echo "\tFound localized variable on (line $k) >>> {$lines_lang_old[$k]}";
				$bLocalized = TRUE;
		        $localizedLine = $lines_lang_old[$k];
				
				// check if localized value exceed to more lines - semicolon is not found
				while (!(preg_match('/;[\s]*$/', $lines_lang_old[$k])
				|| preg_match('/;[\s]*[\/]{2}/', $lines_lang_old[$k])))
				{
			        $k++;
			        echo "\tMultiline localized value (line $k)\n";
				    $localizedLine .= $lines_lang_old[$k];
				}
				$k = $lines_old_count; // exit more parsing old file
			}
		}
		
        echo "(line $i) Found variable '$var_name'\n";
		if ($bLocalized)
		{
	        echo "\tLocalization exists '$localizedLine'\n";
        	$out .= $localizedLine;
		} 
		else 
		{
	        echo "\tLocalization doesn't exists. Copy English.'\n";
		    $out .= $lines_eng[$i];
		    $var_counter_new++;

        	// check multiline value (check semicolon or semicolon with comment)
			while (!(preg_match('/^(.*);[\s]*$/', $lines_eng[$i])
				|| preg_match('/^(.*);[\s]*[\/]{2}/', $lines_eng[$i])))
			{
				$i++;
				echo "(line $i) English multiline value - copy the line >>".$lines_eng[$i];
				$out .= $lines_eng[$i];
			}

		}
    }

	// end of file    
    elseif (preg_match('/^\?\>/', $lines_eng[$i]))
    {
        $out .= "?>";
    }

	// skip unused multiline values (any text started by whitespace)
	// it could start with bracket, but there could be just any text that continues 
	// from previous without brackets
    elseif (preg_match('/^\s+\S.*/', $lines_eng[$i]))
    	echo "(line $i) Skipped line (expected unused multiline value)\n";

	// something wrong?
    else
    {
    	echo "ERROR: please fix the unparsed line ($i): \n" . $lines_eng[$i];
    	exit;
    }
}


// create backup if defined
if ($do_backup_file)
	rename($file_lang_old, $file_lang_old.'.bck');

	
// save output
$fp = fopen($file_lang_old, "w");
fwrite($fp, $out);
fclose($fp);

echo "\n\nUpdated file: ".$file_lang_old;
echo "\nCompleted! The script has parsed $var_counter strings and add $var_counter_new new variables.\n";
echo "===== Bye =====\n";

?>
