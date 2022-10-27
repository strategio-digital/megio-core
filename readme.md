# SFramework
Framework for building REST APIs written in PHP 8.1.

## Installation guide

1. `cp .env.example .env`
2. `./project.sh serve`
3. `./project.sh app`
4. `composer i`
5. `bin/console migrate`
6. `bin/console install:permissions`
7. `bin/console user:create-admin <email> <password>`
8. Visit: [http://localhost:8090](http://localhost:8090)

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