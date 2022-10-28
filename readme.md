# SFramework
Framework for building APIs in PHP 8.1

## Installation guide 
1. Create project by `curl -sL bit.ly/3W8gS95 | bash /dev/stdin create <project-folder>`
2. Finish installation by steps in your project-folder [readme.md](https://github.com/strategio-digital/framework/blob/master/template/readme.md)

## Features
- 🟢&nbsp; JWT Auth (user, admin, guest, +custom roles)
- 🟢&nbsp; Roles, resources and resource-guards on controller layer
- 🟢&nbsp; Controller actions & requests schema validations
- 🟢&nbsp; Doctrine database entities with PHP attributes and migrations
- 🟢&nbsp; File storage with AWS S3 adapter
- 🟢&nbsp; Tracy/Debugger with AWS S3 logger adapter
- 🟢&nbsp; Sending emails by custom SMTP servers
- 🟢&nbsp; Symfony console commands
- 🟢&nbsp; PHP-Stan static analysis on level 8
- 🟢&nbsp; One click deployment with Dockerfile and easypanel.io

### Feature backlog
- App.php refactoring
- Doctrine SQL profiler: TracyBar, JsonResponse debugger (Queries count, SQL log)
- Vue 3 frontend (admin-login, user datagrid + revoke, admin datagrid, role access table, edit-profile + file uploader)
- Visitor_id for each visitor
- Make some docs on docusaurus.io
- Create admin dashboard like pocketbase.io in Vue 3 (Doctrine entity builder, migrations, CRUD, API)

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