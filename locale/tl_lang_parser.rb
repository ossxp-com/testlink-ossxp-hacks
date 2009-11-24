#!/usr/bin/ruby
=begin
 * TestLink Open Source Project - http:#testlink.sourceforge.net/
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
 *  Note: to have report about+arsing redirect script to file; for example
 * 		#tl_lang_parser.php strings.txt > report.txt
=end

#Set path to your en_GB english file 
file_eng = 'en_GB/strings.txt';

# Set true if you would like to have original file with 'bck' extension 
do_backup_file = FALSE;


# ---------------------------------------------------------------------------
if (ARGV.size < 1)
	puts "Usage: #tl_lang_parser.php <localization_file_to_be_updated>"
	exit
else
	file_lang_old = ARGV[0];
end
#$file_lang_old = '/home/havlatm/www/tl_head/locale/cs_CZ/strings.txt';
out=''; # data for output file
var_counter = 0;
var_counter_new = 0;
var_counter_untrans = 0;
var_counter_trans = 0;
puts "===== Start TestLink lang_parser =====";

# read english file
if File.exist?(file_eng) && File.readable?(file_eng)
	puts"English file #{file_eng} is readable OK."
	lines_eng = File.new(file_eng).readlines.size 
	lines_eng_content = File.new(file_eng).readlines 
else
	puts "English file #{file_eng} is readable - FAILED!. Exit."
end
# read language file
if (File.exist?(file_lang_old) && File.readable?(file_lang_old))
	puts "File #{file_lang_old} is readable OK."
	lines_lang_old = File.new(file_lang_old).readlines.size
	lines_lang_old_content = File.new(file_lang_old).readlines
else
	puts " #{file_lang_old} file is not readable - FAILED!. Exit."
end

puts "English file lines =#{lines_eng}"
puts "Old Language file lines =#{lines_lang_old}"

# find end of english header	:\s(\d+)\s
for i in 0..lines_eng
    if(/Revision:\s(\S+)\s/.match(lines_eng_content[i]))
    revision_comment=Regexp.last_match(1)
    puts "English file revision: #{revision_comment}.";
    end 

    if(/\*\//.match(lines_eng_content[i]))
        begin_line = i+1
        puts "Eng: End of header is line = #{i} " 
    end
end

# copy existing localization file header
for i in 1..lines_lang_old

    if(/\*\//.match(lines_lang_old_content[i]))
        puts "Old: End of header is line = #{i} "
        begin_line_old = i + 1
	    out+=" * Scripted update according en_GB string file (version: #{revision_comment})"
        
		out+=" * ---------------------------------------------------------------------------------- */"
=begin
    else
		if lines_lang_old_content[i].strip== "* Scripted update according en_GB string file (version: #{revision_comment})"
        next
		out+=lines_lang_old_content[i]
        end 
   end
=end
  end
end


# compile output array based on english file
for i in begin_line..lines_eng
	# copy comments 
    if /^\/\//.match(lines_eng_content[i])
        puts "(line #{i}) Copy comment";
        out+= lines_eng_content[i];

	# copy empty line
    elsif /^([\s\t]*)$/.match(lines_eng_content[i])
        puts "(line #{i}) Empty line"
        out+= "\n"

	# parse a line with variable definition
    elsif /^\$TLS_([\w]+)[\s]*=[\s]*(.*)$/.match(lines_eng_content[i]) 
        var_name = "TLS_#{Regexp.last_match(1)}"
        var_counter+=1
        bLocalized = FALSE
        localizedLine = ''
#        print_r(parsed_line)
        
        # get localized value if defined - parse old localized strings
		for k in begin_line_old..lines_lang_old
        	if /^\$#{var_name}[\s]*=.+$/.match(lines_lang_old_content[k])
		        puts "Found localized variable on (line #{k}) >>> #{lines_lang_old_content[k]}"
				bLocalized = TRUE
		        localizedLine = Regexp.last_match.to_s
				# check if localized value exceed to more lines - semicolon is not found
            	while (!(/;[\s]*/.match(lines_lang_old_content[k.to_i])||(/;[\s]*[\/]{2}/.match(lines_lang_old_content[k.to_i]))))
                    k+=1
			        puts "Multiline localized value (line #{k})"
				    localizedLine += lines_lang_old_content[k.to_i]
		        end
				k = file_lang_old; # exit more parsing old file
	        end	
       end
	    puts "Localization doesn't exists. Copy English.'";

        # Jiangxin: save english var and value pairs to orig_eng
        orig_eng = lines_eng[i];

        # check multiline value (check semicolon or semicolon with comment)
        while (!(/^(.*);[\s]*$/.match(lines_eng_content[i])||(/^(.*);[\s]*[\/]{2}/.match(lines_eng_content[i]))))
            puts "(line #{i}) English multiline value - copy the line >>#{lines_eng_content[i]}"
            break
        end
      #      orig_eng += lines_eng_content[i];
        
        puts "(line #{i}) Found variable '$#{var_name}'";
        if bLocalized
	        puts "Localization exists #{localizedLine}"
            puts
            if localizedLine == orig_eng
                var_counter_untrans+=1;
            else
                var_counter_trans+=1
            end
        	out+= localizedLine
		else 
	        puts "Localization doesn't exists. Copy English.";
            var_counter_untrans+=1
		    var_counter_new+=1
		    out+=orig_eng
        end
	# end of file    
    elsif /^\?\>/.match(lines_eng_content[i])
        out+= "?>";

	# skip unused multiline values (any text started by whitespace)
	# it could start with bracket, but there could be just any text that continues 
	# from previous without+rackets
    elsif /^\s+\S.*/.match(lines_eng_content[i])
    	puts "(line #{i}) Skipped line (expected unused multiline value)";

	# something wrong?
    else
    	puts "ERROR: please fix the unparsed line #{i}: #{lines_eng_content[i]};"
  end
# create backup if defined
    if (do_backup_file)
    	rename(file_lang_old, "#{file_lang_old}.bck");
    end
# save output
fp = open(file_lang_old, "w")
fp.puts out
fp.close

end
puts "Updated file: #{file_lang_old}";
puts "Completed! The script has parsed #{var_counter} strings and add #{var_counter_new} new variables.";
puts "Un-translate items: #{var_counter_untrans} , translated items: #{var_counter_trans}";
puts "===== Bye =====";
