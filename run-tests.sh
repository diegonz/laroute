#!/usr/bin/env bash

# GET SCRIPT PATH
SOURCE="${BASH_SOURCE[0]}"
# Resolve $SOURCE until the file is no longer a symlink
while [[ -h "$SOURCE" ]]; do
  DIR="$( cd -P "$( dirname "$SOURCE" )" >/dev/null 2>&1 && pwd )"
  SOURCE="$(readlink "$SOURCE")"
  # if $SOURCE was a relative symlink, we need to resolve it relative
  # to the path where the symlink file was located
  [[ ${SOURCE} != /* ]] && SOURCE="$DIR/$SOURCE"
done
SCRIPT_PATH="$( cd -P "$( dirname "$SOURCE" )" >/dev/null 2>&1 && pwd )"
if [[ -z "$SCRIPT_PATH" ]] ; then
  exit 1  # fail
fi

# CD TO SCRIPT PATH
cd ${SCRIPT_PATH}

# CHECK TEST DEPENDENCIES
if ! hash grunt; then
    echo "You must have grunt-cli installed!"
    exit 1
fi
if [[ ! -f ./composer.phar ]] && ! hash composer; then
    echo "You must have composer installed or PHP and composer.phar!"
    exit 1
fi
if ! hash npm; then
    echo "You must have npm installed!"
    exit 1
fi

# GET TOOLS PATHS
if [[ -f ./composer.phar ]]; then
    if ! hash php; then
        echo "You must have PHP installed!"
        exit 1
    fi
    PHP_BIN=$(which php)
    COMPOSER_BIN="$PHP_BIN ./composer.phar"
else
    COMPOSER_BIN=$(which composer)
fi
GRUNT_BIN=$(which grunt)
NPM_BIN=$(which npm)

# RUN TESTS
# Uglify/minify JS template file
${GRUNT_BIN}
# Run PHP tests
${COMPOSER_BIN} test
# Run JS tests
${NPM_BIN} test
