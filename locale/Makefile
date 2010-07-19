help:
	@echo "make all - format all languages according to en_GB (slow)"
	@echo "make zh  - format zh_CN according to en_GB"

all:
	ls -d *|grep -v "tl_lang"| grep -v "en_GB" | xargs -i{} ./tl_lang_parser.rb -o {}/strings.txt -u {}/strings_untrans.txt

zh: zh_CN

zh_CN:
	ruby tl_lang_parser.rb -o $@/strings.txt -u $@/strings_untrans.txt

.PHONY: zh zh_CN all

# vim: noet ts=4 sw=4
