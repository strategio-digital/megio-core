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
  echo -e "./install.sh update"
  echo -e "${NC}"
}

if test "$1" = "create"; then
  echo -e ""
  echo -e "${GREEN}Project creation started...${NC}"

  mkdir $2
  curl -L0 https://github.com/strategio-digital/saas/archive/refs/heads/${VERSION}.zip --output "./$2/project.zip"
  unzip -q "./$2/project.zip" -d "./$2/"
  cp -r "./$2/saas-${VERSION}/template/" "./$2/"
  rm -rf "./$2/saas-${VERSION}" "./$2/project.zip"

  echo -e ""
  echo -e "${GREEN}Project successfully created!"
  echo -e "Now, you can follow installation guide in ${YELLOW}readme.md${NC}"

elif test "$1" = "update"; then
  echo -e ""
  echo -e "${GREEN}Project update started...${NC}"

  curl -L0 https://github.com/strategio-digital/saas/archive/refs/heads/${VERSION}.zip --output "./project.zip"
  unzip -q "./project.zip" -d "./.strategio-saas/"

  cp -r "./.strategio-saas/saas-${VERSION}/template/bin/console" "./bin/console"
  cp -r "./.strategio-saas/saas-${VERSION}/template/docker/" "./docker/"
  cp -r "./.strategio-saas/saas-${VERSION}/template/www/index.php" "./www/index.php"
  cp -r "./.strategio-saas/saas-${VERSION}/template/.env.example" "./.env.example"
  cp -r "./.strategio-saas/saas-${VERSION}/template/.gitignore" "./.gitignore"
  cp -r "./.strategio-saas/saas-${VERSION}/template/docker-compose.yml" "./docker-compose.yml"
  cp -r "./.strategio-saas/saas-${VERSION}/template/docker-entrypoint.sh" "./docker-entrypoint.sh"
  cp -r "./.strategio-saas/saas-${VERSION}/template/Dockerfile" "./Dockerfile"
  cp -r "./.strategio-saas/saas-${VERSION}/template/project.sh" "./project.sh"

  rm -rf "./project.zip" "./.strategio-saas" "./temp/*"
  echo -e ""
  echo -e "${GREEN}Project successfully updated!"
  echo -e "Now, you can run command ${YELLOW}'docker-compose up --build -d'${NC}."
else
  help
fi
