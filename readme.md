# Strategio SaaS
Tools for developing Webs & APIs by simple clicks.

Demo: https://saas.strategio.dev (u: admin@test.cz p: Test1234)

<img src="https://jzapletal.s3.eu-west-1.amazonaws.com/strategio-saas-edit-data.png" width="100%" alt="Strategio SaaS">

## Installation guide
1. Create project by `curl -sL bit.ly/3AnA49z | bash /dev/stdin create <project-folder>`
2. Move to your project folder & finish installation steps by [readme.md](https://github.com/strategio-digital/saas/blob/master/template/readme.md)

## Core features
- 🟢&nbsp; Website frontend builder (simple router & Latte templates)
- 🟢&nbsp; Vite assets builder for fast compiling scss, ts, vue, etc.
- 🟠&nbsp; Admin panel with visual Doctrine ORM entity builder & data editor.
- 🟢&nbsp; Fully configurable & extendable Vue 3 Admin panel.
- 🟢&nbsp; One click deployment with Dockerfile and easypanel.io.

## Backend features
- 🟢&nbsp; JWT Auth (user, admin, guest, +custom roles).
- 🟢&nbsp; Roles, resources and resource-guards in controllers.
- 🟢&nbsp; Schema validation for requests and controllers.
- 🟢&nbsp; Doctrine database entities with PHP attributes and migrations.
- 🟢&nbsp; File storage with AWS S3 adapter.
- 🟢&nbsp; Tracy/Debugger with AWS S3 logger adapter.
- 🟢&nbsp; Sending emails by custom SMTP servers.
- 🟢&nbsp; Symfony console commands.
- 🟢&nbsp; PHP-Stan static analysis on level 8.

## Working on
- User datagrid + CRUD with bulk inserts / updates
- Collection datagrid + CRUD
- Admin datagrid + CRUD
- App.php & Bootstrap.php refactoring

## Backlog
- **Collection Editor** (Doctrine entity builder, migrations, route generator, API permissions)
- Role access table (routes or resources)
- Request-validation by entity (by default)
- File uploader
- App settings
- Storage settings
- E-mail settings
- Enhanced API CRUD filters, joins, orders
- Application event hooks
- Doctrine SQL profiler: TracyBar, JsonResponse debugger (Queries count, SQL log, execution times)
- Extension (Simple cart + GoPay)
- Make some docs (inspire by [docusaurus.io](https://docusaurus.io/))
- Make intro videos (for developers & for administrators)
- Console cron jobs & progress dashboard
- Access log & error log dashboard with Tracy/BlueScreens

## Tutorials
**TODO Beginner:**

1. How to start new project and create first collections.
2. How to handle request and render collection data in Latte template.
3. How to handle API requests and send example e-mail.
4. How to make CRUD operations with Doctrine ORM.
5. How to upload files with S3 storage adapter.
6. How to deploy your application with easypanel.io.

### How to debug API with [Postman](https://documenter.getpostman.com/view/14885541/2s8YsqUZuv).

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
