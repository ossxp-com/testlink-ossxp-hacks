#!/usr/bin/ruby

# lang_parser.rb : rewrite from testlink tl_lang_parser.php

require 'getoptlong'

DEFAULT_REFERENCE="en_GB/strings.txt"
$opt_verbose = 0

def verbose(msg, level=1)
    $stderr.puts msg if level <= $opt_verbose
end

def translate(ref, input, out=$stdout, untrans=$stderr)
  if out == input and not out.is_a?(IO)
    do_backup_file = TRUE
  else
    do_backup_file = FALSE
  end

  if not File.readable?(ref)
    $stderr.puts "Reference file #{ref} not found or not readable."
    exit 1
  elsif not File.readable? input
    $stderr.puts "Translate file #{input} not found or not readable."
    exit 1
  elsif out and not out.is_a?(IO) and File.exists? out and not File.writable? out
    $stderr.puts "Output file #{out} not writable."
    exit 1
  elsif untrans and not untrans.is_a?(IO) and File.exists? untrans and not File.writable? untrans
    $stderr.puts "Untranslate file #{untrans} not writable."
    exit 1
  end

  # open untrans file
  if !untrans
    fp_untrans = $stderr
  elsif untrans.is_a? IO
    fp_untrans = untrans
  else
    fp_untrans = open(untrans, "w")
  end
 
  buffers=[] # data for output file
  counter_total = 0
  counter_new = 0
  counter_untrans = 0
  counter_trans = 0

  verbose "===== Start lang_parser ====="

  # read ref file
  ref_lines = File.new(ref).readlines

  # read language file
  input_lines = File.new(input).readlines

  verbose "Reference file lines =#{ref_lines.size}", 2
  verbose "Translate file lines =#{input_lines.size}", 2

  # find end of reference file header  :\s(\d+)\s
  ref_header_size = 0
  ref_revision = ""
  for line in ref_lines
    break if not ( line =~ /<\?php/ or line =~ /^\/\*/ or line =~ /^ \*/ )
    ref_header_size += 1
    ref_revision = $1 if line =~ /\$Revision:\s*(\S+)\s*\$/
    break if line =~ /\*\//
  end

  input_header_size = 0
  # copy existing localization file header
  for line in input_lines
    break if not ( line =~ /<\?php/ or line =~ /^\/\*/ or line =~ /^ \*/ )
    input_header_size += 1
		if  line =~ /\* Scripted update according en_GB string file/
      buffers << " * Scripted update according en_GB string file (version: #{ref_revision})"
      next
    end
    buffers << line.rstrip
    break if line =~ /\*\//
  end

  # compile output array based on reference file
  i = ref_header_size-1
  while true
    i += 1
    break if i >= ref_lines.size

    # copy empty line
    if ref_lines[i] =~ /^\s*$/
      verbose "(line #{i}) Empty line", 3
      buffers << ""

    # parse a line with variable definition
    elsif ref_lines[i] =~ /^\$TLS_([\w]+)\s*=\s*(.*)/
      key = $1
      value = ($2 or "")
      counter_total+=1
      bLocalized = FALSE
      localizedLine = ''
      ref_origin = ref_lines[i].rstrip

      verbose "(line #{i}) Found variable '$TLS_#{key}'", 3

      # check multiline value (check semicolon or semicolon with comment)
      while not ref_lines[i] =~ /^(.*);\s*$/ and not ref_lines[i] =~ /^(.*);[\s]*[\/]{2}/
        i += 1
        value += "\n" + ref_lines[i].strip
        ref_origin += "\n" + ref_lines[i].rstrip
        verbose "(line #{i}) reference file key ($TLS_#{key}) with multiline value.", 3
      end

      # get localized value if defined - parse trans localized strings
      for k in input_header_size...input_lines.size
        if input_lines[k] =~ /^\$TLS_#{key}\s*=\s*(.*)$/
          trans_value = ($1 or "")
          localizedLine = $&.rstrip
          verbose "Found localized variable on (line #{k}) >>> #{input_lines[k]}", 3
          # check multiline value (check semicolon or semicolon with comment)
          while not input_lines[k] =~ /^(.*);\s*$/ and not input_lines[k] =~ /^(.*);[\s]*[\/]{2}/
            k += 1
            trans_value += "\n" + input_lines[k].strip
            localizedLine += "\n" + input_lines[k].rstrip
            verbose "(line #{k}) translate file key ($TLS_#{key}) with multiline value.", 3
          end
          bLocalized = TRUE
          break
        end
      end

      if bLocalized
        verbose "Localization exists #{localizedLine}", 3
        if value.strip == trans_value.strip and not value.strip.none?
            counter_untrans += 1
            fp_untrans.puts ref_origin
            verbose "Not translate: #{ref_origin}", 3
        elsif value.strip.none?
            counter_trans += 1
            fp_untrans.puts ref_origin
            verbose "Blank translate: #{ref_origin}/#{localizedLine} !!!", 3
        else
            counter_trans += 1
        end
        buffers << localizedLine
      else
        verbose "Localization doesn't exists. Copy from reference."
        counter_untrans +=1
        counter_new += 1
        buffers << ref_origin
      end

    # Otherwize, copy to output
    else
      buffers << ref_lines[i].rstrip
    end
  end

  # create backup if defined
  if do_backup_file
    File.rename input, "#{input}.bak"
  end

  # save output
  if out.is_a? IO
    out.puts buffers.join("\n")
  elsif not out
    $stdout.puts buffers.join("\n")
  else
    verbose "Updated file: #{out}"
    fp = open(out, "w")
    fp.puts buffers.join("\n")
    fp.close
  end

  if fp_untrans and fp_untrans != untrans
    fp_untrans.close
  end

  verbose "Completed! The script has parsed #{counter_total} strings and add #{counter_new} new variables.", 0
  verbose "Un-translate items: #{counter_untrans} , translated items: #{counter_trans}", 0
  verbose "===== Bye ====="
end

def usage(msg="")
  puts <<END
Command:
  #{$0} [options...] <input_file>
Usage:
  --reference, -r  <reference_file>
      Default is en_GB/string.txt

  --output, -o <output_file>
      Default is stdout

  --untrans, -u <untrans_output_file>
      Default is stderr

  --verbose
      Show verbose message on stderr

  --help
      This screen

  <input_file>
      Target l10n file.
END
  if not msg.none?
    puts
    puts msg
  end
end

def main
  ref = DEFAULT_REFERENCE
  input = nil
  out = $stdout
  untrans = $stderr
  opts = GetoptLong.new(
    [ "--reference","--source","-s","-r", GetoptLong::REQUIRED_ARGUMENT ],
    [ "--output",   "-o",                 GetoptLong::REQUIRED_ARGUMENT ],
    [ "--untrans",  "-u",                 GetoptLong::REQUIRED_ARGUMENT ],
    [ "--verbose",  "-v",                 GetoptLong::NO_ARGUMENT ],
    [ "--help",     "-h",                 GetoptLong::NO_ARGUMENT ]
  )
  # process the parsed options
  opts.each do |opt, arg|
    case opt
    when "--reference"
      ref = arg
    when "--output"
      out = arg
    when "--untrans"
      untrans = arg
    when "--verbose"
      $opt_verbose += 1
    when "--help"
      usage
      exit 0
    end
  end

  if ARGV.size != 1
    if out and not out.is_a? IO
      input = out
    else
      usage "No --out option provided, and needs one argument, but you provide #{ARGV.size}."
      exit 1
    end
  else
    input = ARGV[0]
  end
  translate ref, input, out, untrans
end

main

