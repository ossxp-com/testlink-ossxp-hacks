SOURCEDIR=testlink-in-git

all:
	@echo "clean - remove *.deb and other temporary files."
	@echo "dir   - extract source package."
	@echo "build - generate config files for current dists and run dpkg-buildpackage"

clean:
	#(cd $(SOURCEDIR); rm -f $$(dirname $$(pwd -P))/ossxp-testlink*changes; rm -f $$(dirname $$(pwd -P))/ossxp-testlink*deb;)
	-rm -f ossxp-testlink*changes ossxp-testlink*deb
	-rm -rf $(SOURCEDIR)/.pc
	(cd $(SOURCEDIR); git reset --hard; git checkout master; git clean -f -d;)

dir: clean
	(cd $(SOURCEDIR); [ -d debian ] || ln -s $$(dirname $(CURDIR))/debian debian; git status || true)
	sudo setfacl -Rb $(SOURCEDIR) $$(dirname $$(pwd -P))/debian

build:
	(cd $(SOURCEDIR); make -f debian/rules debian/control || true)
	(cd $(SOURCEDIR); dpkg-buildpackage -b -rfakeroot || true)
	(cd $(SOURCEDIR); mv $$(dirname $$(pwd -P))/ossxp-testlink*changes $(CURDIR); mv $$(dirname $$(pwd -P))/ossxp-testlink*deb $(CURDIR);)

.PHONY: all clean dir build
# vim: noet
