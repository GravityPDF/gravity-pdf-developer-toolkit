#!/usr/bin/env bash

download() {
    if [ `which curl` ]; then
        curl -s "$1" > "$2";
    elif [ `which wget` ]; then
        wget -nv -O "$2" "$1"
    fi
}

if [ ! -f ./doctum.phar ]; then
    download https://doctum.long-term.support/releases/latest/doctum.phar ./doctum.phar
fi

php ./doctum.phar update .doctumconfig.php --force