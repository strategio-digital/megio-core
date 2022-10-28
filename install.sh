#!/bin/bash

VERSION='master'

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

help() {
  echo -e "${YELLOW}COMMANDS:"
  echo -e "${GREEN}./install.sh create <project-folder>"
  echo -e "./project.sh update"
  echo -e "${NC}"
}

if test "$1" = "create"; then
  echo -e "${GREEN}Creating...${NC}"
  mkdir $2
  curl -L0 https://github.com/strategio-digital/framework/archive/refs/heads/${VERSION}.zip --output "./$2/project.zip"
  unzip -q "./$2/project.zip" -d "./$2/"
  cp -r "./$2/framework-${VERSION}/template/" "./$2/"
  rm -rf "./$2/framework-${VERSION}" "./$2/project.zip"

elif test "$1" = "update"; then
  echo -e "${GREEN}Updatig...${NC}"
  echo -e "${YELLOW}TODO:....${NC}"
else
  help
fi
