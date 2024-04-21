#!/usr/bin/env bash

bold=$(tput bold)
normal=$(tput sgr0)
green=$(tput setaf 2)
red=$(tput setaf 1)


PACKAGE_VERSION=$(cat composer.json \
  | grep version \
  | head -1 \
  | awk -F: '{ print $2 }' \
  | sed 's/[",]//g' \
  | tr -d '[[:space:]]')

echo "${bold}Package version: ${green}${PACKAGE_VERSION}${normal}"

echo "Creating package..."
echo "Version: ${bold}$PACKAGE_VERSION ${normal}"

rm -rf bm-bluepayment-*.zip

zip -r "volt-payment-$PACKAGE_VERSION.zip" ./ \
  -x *.idea* \
  -x *.git* \
  -x *.DS_Store* \
  -x *create_package.sh* \
  -x *.doc*

echo "======================================================================================================"
echo "${green}Package ${bold}volt-payment-$PACKAGE_VERSION.zip${normal}${green} created"
