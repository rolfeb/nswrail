## Stage release

### Stage setup - first time only
```shell
mkdir /work/git
(cd /work/git && git init --bare nswrail.git)
git remote add stage ssh://moonbase/work/git/nswrail.git
git push stage master
mkdir /work/nswrail-stage
(cd /work/nswrail-stage && git clone ssh://moonbase/work/git/nswrail.git .)
(cd /work/nswrail-stage && mkdir log)
```

### Stage release
```shell
git push stage master
cd /work/nswrail-stage
git pull
cp .htaccess-stage .htaccess
cp php.ini-stage php.ini
```

