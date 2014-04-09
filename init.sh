#!/bin/bash

pushd $(dirname $0)

env=${1:-prod}

php app/console d:d:d -n --force -e=$env
php app/console d:d:c -n -e=$env
php app/console d:mi:m -n -e=$env
php app/console d:f:l -n -e=$env
php app/console ca:cl -n -e=$env
php app/console ca:cl -n -e=$env
php app/console assets:i -n --symlink -e=$env
php app/console asseti:d -n -e=$env

popd