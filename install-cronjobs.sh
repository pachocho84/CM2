#!/bin/bash

dir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# create useful files and directories
if [ ! -d "$dir/src/CM/CMBundle/Resources/public/temp" ]; then
   mkdir $dir/src/CM/CMBundle/Resources/public/temp
fi
sudo touch /etc/cron.hourly/cm2

sudo sh -c "echo \"find src/CM/CMBundle/Resources/public/temp -mindepth 1 -mmin +120 -exec rm -fr '{}' \;\">/etc/cron.hourly/cm2"