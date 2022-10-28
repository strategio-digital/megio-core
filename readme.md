# SFramework
Framework for building APIs in PHP 8.1

## Installation guide 
1. Create project by `curl -sL bit.ly/3W8gS95 | bash /dev/stdin create <project-folder>`
2. Finish installation by steps in your project-folder [readme.md](https://github.com/strategio-digital/framework/blob/master/template/readme.md)

## Features
- 🟢&nbsp; JWT Auth (user, admin, guest, +custom roles)
- 🟢&nbsp; Roles, resources and resource-guards in controllers
- 🟢&nbsp; Schema validation for requests and controllers
- 🟢&nbsp; Doctrine database entities with PHP attributes and migrations
- 🟢&nbsp; File storage with AWS S3 adapter
- 🟢&nbsp; Tracy/Debugger with AWS S3 logger adapter
- 🟢&nbsp; Sending emails by custom SMTP servers
- 🟢&nbsp; Symfony console commands
- 🟢&nbsp; PHP-Stan static analysis on level 8
- 🟢&nbsp; One click deployment with Dockerfile and [easypanel.io](https://easypanel.io/)

## Backlog
- App.php refactoring
- Doctrine SQL profiler: TracyBar, JsonResponse debugger (Queries count, SQL log)
- Vue 3 frontend (admin-login, user datagrid + revoke, admin datagrid, role access table, edit-profile + file uploader)
- Visitor_id for each visitor
- Make some docs on [docusaurus.io](https://docusaurus.io/)
- Create admin dashboard like [pocketbase.io](https://pocketbase.io/) in Vue 3 (Doctrine entity builder, migrations, CRUD, API)

## Tutorials

### 1. How to setup new project
Todo video..

### 2.  How to handle requests
Todo video...

### 3.  How to use Doctrine ORM
Todo video...

### 4.  How to upload files with S3 storage adapter
Todo video...

### 5.  How to send e-mails & use Symfony commands
Todo video...

### 6.  How to deploy with easypanel.io
Todo video...

### 7. How to debug API with [Postman](https://documenter.getpostman.com/view/14885541/2s8YKCGNpF)
If you want to use Postman to debug API, just add this script into `Postman -> Collection -> Tests` section and you will be able to use Tracy\Debuuger in Postman.

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