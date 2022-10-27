#!/bin/bash

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

help() {
  echo -e "${YELLOW}COMMANDS:"
  echo -e "${GREEN}./install.sh install"
  echo -e "./project.sh update"
  echo -e "${NC}"
}

if test "$1" = "install"
then
  echo -e "${GREEN}Installation started${NC}"
elif test "$1" = "update"
then
  echo -e "${GREEN}Updating started${NC}"
else
  help
fi