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

- also: make sure the stage URL has been registered against the google maps developer API key

### Stage release
```shell
git push stage master
cd /work/nswrail-stage
git checkout -- public_html/.htaccess public_html/php.ini   # revert
git pull
cp public_html/.htaccess-stage public_html/.htaccess
cp public_html/php.ini-stage public_html/php.ini
```

## Test release

### Test setup - first time only

TBD
- copy thirdparty packges down

### Test release
```shell
git push prod master
ssh nswrail.net
cd checkput/nswrail
git checkout -- public_html/.htaccess public_html/php.ini   # revert
git pull
cp public_html/.htaccess-test public_html/.htaccess
cp public_html/php.ini-test public_html/php.ini
rsync -av --delete phplib/ ~/phplib/
rsync -av --delete templates/ ~/templates/
rsync -av --delete --exclude=/.well-known/ public_html/ ~/public_html/test/
```


