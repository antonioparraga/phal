DIRFILE="dirfile.txt"
find . -type f > $DIRFILE
cat $DIRFILE
export PHP_COMMAND=/usr/bin/php
export PHING_HOME=${TRAVIS_BUILD_DIR}/build-libs/phing-2.4.9
export PHP_CLASSPATH=${PHP_CLASSPATH}:${PHING_HOME}/classes

${PHING_HOME}/bin/phing -logger phing.listener.DefaultLogger
