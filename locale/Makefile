help:
	echo "make all - format according to en_GB"

all:
	ls -d *|grep -v "tl_lang"| grep -v "en_GB" | xargs -i{} ./tl_lang_parser.rb -o {}/strings.txt
