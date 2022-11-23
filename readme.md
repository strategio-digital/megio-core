# Strategio SaaS
The Tool for developing Webs & APIs by simple clicks.

## Installation guide
1. Create project by `curl -sL bit.ly/3AnA49z | bash /dev/stdin create <project-folder>`
2. Finish installation by steps in your project-folder [readme.md](https://github.com/strategio-digital/saas/blob/master/template/readme.md)

## Features
- 🟠&nbsp; Vue 3 administration (collections, users, admins, app settings)
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
- User datagrid + CRUD with bulk inserts / deletes / updates /
- Admin datagrid + CRUD
- Latte template rendering + Nginx Routing \/_\/\* \/api\/*, /\*
- Role access table (routes or resources)
- App settings
- File uploader
- Storage settings
- E-mail settings
- Collection Editor (Doctrine entity builder, migrations, route generator, API permissions)
- Request-validation by entity (by default)
- Enhanced API CRUD filters, joins, orders
- Admin navigation configurator
- App.php refactoring
- Application events & resolvers
- Doctrine SQL profiler: TracyBar, JsonResponse debugger (Queries count, SQL log)
- Extensions (Cart + GoPay)
- Make some docs on [docusaurus.io](https://docusaurus.io/)

## Tutorials

### 1. How to setup new project
Todo video..

### 2.  How to handle requests
Todo video...

### 3.  How to use Doctrine ORM
Todo video...

### 4.  How to upload files with S3 storage adapter
Todo video...

### 5.  How to send e-mails & use console commands
Todo video...

### 6.  How to protect data by resource-guards or custom guards
Todo video...

### 7.  How to deploy application with easypanel.io
Todo video...

### 8. How to debug API with [Postman](https://documenter.getpostman.com/view/14885541/2s8YKCGNpF)
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