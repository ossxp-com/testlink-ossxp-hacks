# Set change log file for your documents
CHANGELOG=../ossxp-testlink-doc.changelog

DOC_REV=$(shell head -1 $(CHANGELOG) | sed -e "s/^\S*\s\+(\(\S\+\)).*/\1/")
DOC_DATESTRING=$(shell grep "^ -- .* <.*>  .\+$$" $(CHANGELOG) | head -1 | sed -e "s/^.\+>  \(.\+\)$$/\1/g" )
DOC_DATE=$(shell date -d "$(DOC_DATESTRING)" +"%Y-%m-%d %H:%M:%S")

all: build

STAMP_FILE=doc-build-stamp

build: $(STAMP_FILE)
$(STAMP_FILE):
	(cd user_guide && DOC_REV="$(DOC_REV)" DOC_DATE="$(DOC_DATE)" RUBYOPT=rubygems rake html)
	(cd user_guide && DOC_REV="$(DOC_REV)" DOC_DATE="$(DOC_DATE)" RUBYOPT=rubygems rake pdf)
	(cd admin_guide && DOC_REV="$(DOC_REV)" DOC_DATE="$(DOC_DATE)" RUBYOPT=rubygems rake html)
	(cd admin_guide && DOC_REV="$(DOC_REV)" DOC_DATE="$(DOC_DATE)" RUBYOPT=rubygems rake pdf)
	touch $(STAMP_FILE)

clean:
	rm -f $(STAMP_FILE)

# vim: noet ts=4 sw=4
