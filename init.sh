#!/bin/bash

pushd $(dirname $0)

env=${1:-prod}

php app/console d:d:d --force -e=$env
php app/console d:d:c -e=$env
php app/console d:mi:m -n -e=$env
php app/console d:f:l -n -e=$env
php app/console ca:cl -n -e=$env

popd