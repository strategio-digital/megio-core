# Project name
Project backend build on top of SFramework.  

## Installation guide

1. Properly install [Docker](https://docs.docker.com/desktop/) (on Windows [Docker WSL2](https://docs.docker.com/desktop/windows/wsl/))
2. `cp .env.example .env`
3. `./project.sh serve`
4. `./project.sh app`
5. `composer i`
6. `bin/console migrate`
7. `bin/console install:permissions`
8. `bin/console user:create-admin <email> <password>`
9. Visit: [http://localhost:8090](http://localhost:8090)

## Tutorials

### If you want to use Postman to debug API
Add this script into `Postman -> Collection -> Tests` section and you will be able to use Tracy\Debuuger in Postman.

```JS
pm.test("set html", function() {
    var regex = /\"(.*)(\_tracy\_bar)/gm
    var protocol = pm.request.url.protocol
    var host = pm.request.url.host
    var port = pm.request.url.port
    var hostPort = port ? `${host}:${port}` : host

    var html = pm.response.text()
    var fixedHtml = html.replaceAll(regex, `${protocol}://${hostPort}$1$2`)

    pm.visualizer.set(fixedHtml)
});
```