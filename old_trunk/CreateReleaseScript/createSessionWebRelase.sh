hash svn 2>/dev/null || { echo >&2 "I require svn but it's not installed.  Aborting."; exit 1; }
hash zip 2>/dev/null || { echo >&2 "I require zip but it's not installed.  Aborting."; exit 1; }

if [[ "$#" < "1" || "$#" > "1" ]]; then
 echo "Usage: $0 <versioname>"
 exit 1
fi

VERSION=$1
mkdir /tmp/sessionweb/
mkdir /tmp/sessionweb/${VERSION}/

echo "Create SVN TAG $VERSION"
sleep 2
svn --username mcyrrer@gmail.com  copy https://sessionweb.googlecode.com/svn/trunk/Sessionweb-Web/ https://sessionweb.googlecode.com/svn/tags/sessionweb-${VERSION} -m "Create tag for release $VERSION"
echo "SVN tag https://sessionweb.googlecode.com/svn/tags/sessionweb-${VERSION} created"

echo "Checkout of new Tag"
sleep 2
svn co --username mcyrrer@gmail.com  https://sessionweb.googlecode.com/svn/tags/sessionweb-${VERSION} /tmp/sessionweb/${VERSION}/sessionweb_co  
sed -i s/"loglevel = array(\"TIMER\",\"SQL\", \"DEBUG\", \"INFO\", \"WARN\", \"ERROR\", \"FATAL\");"/"loglevel = array(\"INFO\", \"WARN\", \"ERROR\", \"FATAL\");"/g /tmp/sessionweb/${VERSION}/sessionweb_co/classes/logging.php

echo "Commit of logging changes"
sleep 2
svn commit --username mcyrrer@gmail.com  -m "Changed logging level to release" /tmp/sessionweb/${VERSION}/sessionweb_co

echo "Export files to zip"
sleep 2
svn export http://sessionweb.googlecode.com/svn/tags/sessionweb-${VERSION} /tmp/sessionweb/${VERSION}/sessionweb


pushd /tmp/sessionweb/${VERSION}/
zip -r sessionweb_${VERSION}.zip sessionweb/*
popd
mv /tmp/sessionweb/${VERSION}/sessionweb_${VERSION}.zip .

rm -fR /tmp/sessionweb

