# App name
Application built on [Strategio SaaS](https://github.com/strategio-digital/saas).  

## Installation guide

1. Properly install [Docker](https://docs.docker.com/desktop/) (on Windows [Docker WSL2](https://docs.docker.com/desktop/windows/wsl/))
2. `cp .env.example .env`
3. `./project.sh serve`
4. `./project.sh app`
5. `composer i`
6. `bin/console diff`
7. `bin/console migrate`
8. `bin/console permissions:update`
9. `bin/console user:create-admin <email> <password>`
10. Exit container by `exit` and then run `yarn && yarn dev`
11. Visit: [http://localhost:8090](http://localhost:8090)

## Video tutorials
Learn Strategio SaaS by video tutorials on [this page](https://github.com/strategio-digital/saas#tutorials).