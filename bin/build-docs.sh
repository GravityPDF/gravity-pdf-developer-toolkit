#!/usr/bin/env bash

download() {
    if [ `which curl` ]; then
        curl -s "$1" > "$2";
    elif [ `which wget` ]; then
        wget -nv -O "$2" "$1"
    fi
}

if [ ! -f ./sami.phar ]; then
    download http://get.sensiolabs.org/sami.phar ./sami.phar
fi

php ./sami.phar update .samiconfig.php --force