#!/bin/bash

# create useful files and directories
if [ ! -d "$(dirname $0)/src/CM/CMBundle/Resources/public/temp" ]; then
   mkdir $(dirname $0)/src/CM/CMBundle/Resources/public/temp
fi
sudo touch /etc/cron.hourly/cm2

sudo sh -c "echo \"find src/CM/CMBundle/Resources/public/temp -mindepth 1 -mmin +120 -exec rm -fr '{}' \;\">/etc/cron.hourly/cm2"