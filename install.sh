#!/bin/bash

# Repository info
VERSION='master' #can be '0.0.1', 'master', 'develop', 'whatever'
TAGS_OR_HEADS='heads' # 'heads' for branches, 'tags' for tag-releases
REPOSITORY_OWNER='strategio-digital'
REPOSITORY_NAME='megio-starter'

# Github
GITHUB_FOLDER_NAME="${REPOSITORY_NAME}-${VERSION}"
GITHUB_ZIP_URL="https://github.com/${REPOSITORY_OWNER}/${REPOSITORY_NAME}/archive/refs/${TAGS_OR_HEADS}/${VERSION}.zip"

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
    curl -L0 ${GITHUB_ZIP_URL} --output "./$2/project.zip"
    unzip -q "./$2/project.zip" -d "./$2/"
    cp -r "./$2/${GITHUB_FOLDER_NAME}/" "./$2/"
    rm -rf "./$2/${GITHUB_FOLDER_NAME}" "./$2/project.zip"

    echo -e ""
    echo -e "${GREEN}Project successfully created!"
    echo -e "Now, you can follow installation guide in ${YELLOW}readme.md${NC}"

elif test "$1" = "update"; then
  echo -e ""
  echo -e "${GREEN}Project update started...${NC}"

  curl -L0 ${GITHUB_ZIP_URL} --output "./project.zip"
  unzip -q "./project.zip" -d "./.temp-folder/"

  cp -r "./.temp-folder/${GITHUB_FOLDER_NAME}/bin/console" "./bin/console"
  cp -r "./.temp-folder/${GITHUB_FOLDER_NAME}/docker/" "./docker/"
  cp -r "./.temp-folder/${GITHUB_FOLDER_NAME}/www/index.php" "./www/index.php"
  cp -r "./.temp-folder/${GITHUB_FOLDER_NAME}/.env.example" "./.env.example"
  cp -r "./.temp-folder/${GITHUB_FOLDER_NAME}/.gitignore" "./.gitignore"
  cp -r "./.temp-folder/${GITHUB_FOLDER_NAME}/docker-compose.yml" "./docker-compose.yml"
  cp -r "./.temp-folder/${GITHUB_FOLDER_NAME}/docker-entrypoint.sh" "./docker-entrypoint.sh"
  cp -r "./.temp-folder/${GITHUB_FOLDER_NAME}/Dockerfile" "./Dockerfile"
  cp -r "./.temp-folder/${GITHUB_FOLDER_NAME}/project.sh" "./project.sh"
  cp -r "./.temp-folder/${GITHUB_FOLDER_NAME}/vite.config.ts" "./vite.config.ts"

  rm -rf "./project.zip" "./.temp-folder" "./temp/*", "./www/temp"
  echo -e ""
  echo -e "${GREEN}Project successfully updated!"
  echo -e "Now, you can run command ${YELLOW}'docker-compose up --build -d'${NC}."
else
  help
fi
