SOURCEDIR=testlink-in-git

all:
	@echo "Nomal build procedures:"
	@echo "    make patch => make build"
	@echo "Tasks list:"
	@echo "    patch - export all t/* branch as debian patches."
	@echo "    clean - remove *.deb and other temporary files."
	@echo "    build - generate config files for current dists and run dpkg-buildpackage"
	@echo "    stub.build - build without update souce code."

$(SOURCEDIR):
	if [ ! -d $(SOURCEDIR) ]; then \
		 git clone git://bj.ossxp.com/git/ossxp/testlink-1.8.x.git $(SOURCEDIR) ; \
		 cd $(SOURCEDIR); \
		 tg remote --populate origin; \
		 git fetch; \
	fi

clean: $(SOURCEDIR) stub.clean
	-rm stub.*

stub.clean:
	#(cd $(SOURCEDIR); rm -f $$(dirname $$(pwd -P))/ossxp-testlink*changes; rm -f $$(dirname $$(pwd -P))/ossxp-testlink*deb;)
	-rm -f ossxp-testlink*changes ossxp-testlink*deb
	-rm -rf $(SOURCEDIR)/.pc
	(cd $(SOURCEDIR); git reset --hard; git checkout master; git clean -f -d; git pull)
	(cd $(SOURCEDIR); git checkout t/all; git clean -f -d; git fetch; tg summary; git checkout t/all; tg update; git status || true)
	(cd $(SOURCEDIR); git checkout master ; git clean -f -d;)
	(cd ..;  git clean -f -d; git status || true )
	touch stub.clean

dir: stub.dir
stub.dir: stub.clean
	(cd $(SOURCEDIR); [ -d debian ] || ln -s $$(dirname $(CURDIR))/debian debian; git status || true)
	sudo setfacl -Rb $(SOURCEDIR) $$(dirname $$(pwd -P))/debian
	touch stub.dir

patch: debian/patches
debian/patches: stub.dir
	(cd $(SOURCEDIR); make -f debian/rules debian/patches)
	-rm stub.*

build: stub.dir stub.build
stub.build:
	(cd $(SOURCEDIR); make -f debian/rules debian/control || true)
	(cd $(SOURCEDIR); dpkg-buildpackage -b -rfakeroot || true)
	(cd $(SOURCEDIR); if [ $$(dirname $$(pwd -P)) != $$(dirname $$(pwd)) ] ; then \
		mv $$(dirname $$(pwd -P))/ossxp-testlink*changes $(CURDIR); mv $$(dirname $$(pwd -P))/ossxp-testlink*deb $(CURDIR); \
		fi)
	-rm stub.clean

.PHONY: all clean build debian/patches
# vim: noet
