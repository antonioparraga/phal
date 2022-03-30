export PHING_HOME=./build-libs/phing-2.4.9
export PHP_CLASSPATH=${PHP_CLASSPATH}:${PHING_HOME}/classes
export VERSION=${RELEASE_VERSION}
export PHP_VERSION=${PHP_VERSION}
export CHANGELIST=${RELEASE_VERSION}
export BRANCH=${RELEASE_VERSION}

${PHING_HOME}/bin/phing -logger phing.listener.DefaultLogger
