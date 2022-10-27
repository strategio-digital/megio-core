# SFramework
Framework for building REST APIs written in PHP 8.1.

## Installation guide
1. Install [Docker](https://docs.docker.com/desktop/) (on Windows use [Docker WSL2](https://docs.docker.com/desktop/windows/wsl/) ) 
2. Run `curl -s bit.ly/3W8gS95 | bash /dev/stdin install`

## Features
- 🟢&nbsp; JWT Auth (user, admin, guest, +custom roles)
- 🟢&nbsp; Roles, resources and resource-guards on controller's layer
- 🟢&nbsp; Controllers & Requests with schema validation
- 🟢&nbsp; Doctrine database entities with PHP attributes and migrations
- 🟢&nbsp; File storage with AWS S3 adapter
- 🟢&nbsp; Tracy/Debugger with AWS S3 logger adapter
- 🟢&nbsp; Sending emails via custom SMTP servers
- 🟢&nbsp; Symfony console commands
- 🟢&nbsp; Static analysis with PHP-Stan level 8
- 🟢&nbsp; One click deploy with Dockerfile and easypanel.io
- 🟢&nbsp; And more powerful utilities for speed app-development

### Feature backlog
- App.php refactoring
- Doctrine SQL profiler: TracyBar, JsonResponse debugger (Queries count, SQL log)
- Vue 3 frontend (admin-login, user datagrid + revoke, admin datagrid, role access table, edit-profile + file uploader)
- Visitor_id for each visitor
- Make some docs on docusaurus.io

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