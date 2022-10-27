# Project name
Project backend build on top of SFramework.  

## Installation guide

1. `cp .env.example .env`
2. `./project.sh serve`
3. `./project.sh app`
4. `composer i`
5. `bin/console migrate`
6. `bin/console install:permissions`
7. `bin/console user:create-admin <email> <password>`
8. Visit: [http://localhost:8090](http://localhost:8090)

## Tutorials

### If you want to use Postman
Add this script into `Postman -> Collection -> Tests` section. 

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