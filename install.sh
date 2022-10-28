#!/bin/bash

VERSION='master'

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

help() {
  echo -e "${YELLOW}COMMANDS:"
  echo -e "${GREEN}./install.sh create"
  echo -e "./project.sh update"
  echo -e "${NC}"
}

if test "$1" = "create"; then
  echo -e "${GREEN}Starting installation process${NC}"

  curl -L0 https://github.com/strategio-digital/framework/archive/refs/heads/${VERSION}.zip --output ./project.zip
  unzip -q project.zip -d ./
  cp -a ./framework-${VERSION}/template/* ./
  rm -rf framework-${VERSION} project.zip
  cp ./.env.example ./.env

  {
    docker ps -q
  } || {
    echo -e "${RED}ERROR: Docker is not running. Please start docker on your computer."
    echo -e "When docker has finished starting up press [ENTER] to continue.${NC}"
    read
  }

  echo -e "${GREEN}Starting Docker containers${NC}"
  docker stop $(docker ps -a -q)
  docker-compose up --build -d
  docker-compose exec app composer i
  docker-compose exec app bin/console migrate
  docker-compose exec app bin/console install:permissions
  docker-compose exec app bin/console user:create-admin test@test.cz Test1234

  echo -e "${GREEN}Installation finished${NC}"

elif test "$1" = "update"; then
  echo -e "${GREEN}Starting update proces${NC}"
  echo -e "${YELLOW}TODO:....${NC}"
else
  help
fi
