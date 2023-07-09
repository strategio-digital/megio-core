
# Strategio SaaS
Most powerful tool for creating webs, apps & APIs.

Demo: https://saas.strategio.dev (u: admin@test.cz p: Test1234)

<img src="https://jzapletal.s3.eu-west-1.amazonaws.com/strategio-saas-edit-data.png" width="100%" alt="Strategio SaaS">

## Installation guide
1. Create project by `curl -sL bit.ly/3AnA49z | bash /dev/stdin create <project-folder>`
2. Move to your project folder & finish installation steps by [readme.md](https://github.com/strategio-digital/saas/blob/master/template/readme.md)

## Core features
- 🟢&nbsp; Web-ready dev-stack (simple router & Latte templates)
- 🟢&nbsp; API-ready dev-stack (simple router & UI route permissions editor)
- 🟢&nbsp; Fully configurable & extendable Vue 3 Admin panel.
- 🟠&nbsp; Admin panel with UI Doctrine ORM entity builder.
- 🟠&nbsp; Admin panel with UI datagrid editor based on Doctrine entities.
- 🟢&nbsp; Vite assets bundler for fast compiling scss, ts, vue, etc.
- 🟢&nbsp; One click deployment with Dockerfile and easypanel.io.
- 🟢&nbsp; Stateless and scalable architecture for PHP applications.
- 🟢&nbsp; Optimized image for maximum memory savings (about 20Mb costs)

## Backend features
- 🟢&nbsp; JWT Auth with route resources protection.
- 🟢&nbsp; Requests validation by Nette\Schema.
- 🟢&nbsp; Symfony events & event subscribers for a lot of stuff.
- 🟢&nbsp; Fully integrated Doctrine ORM.
- 🟢&nbsp; Symfony Http\Kernel for handling requests.
- 🟢&nbsp; File storage with AWS S3 adapter.
- 🟢&nbsp; Tracy\Debugger with AWS S3 logger adapter.
- 🟢&nbsp; Custom extensions with Nette\DI\Extensions.
- 🟢&nbsp; Custom Symfony console commands.
- 🟢&nbsp; PHPStan static analysis on level 8.

## Working on (07/2023)
- 🟢 App.php refactoring
- 🟢 Upgrade to symfony router 6.3
- 🟢 Add Symfony\Kernel and controller argument resolver with autowiring
- 🟢 Bootstrap.php refactoring (make it extendable in neon)
- 🟢 Add kernel events (CSP, CORS)
- 🟡 Split User entity into User & Admin entity
- 🟡 Admin & User login mechanism refactoring
- 🟡 Add new Admin & User permissions auth mechanism (JWT Kernel event)
- 🟡 Make User entity commutable and test it in sandbox project

## Done (06/2023)
- 🟢 API end-point for CRUD actions trough Doctrine Entities
- 🟢 Extendable (collections) datagrid with global configs

## Priority
- 🟡 Collections edit/update form
- 🟡 CRUD request-validation by entity props
- 🟠 Make some docs (inspire by [docusaurus.io](https://docusaurus.io/))
- 🟠 Admin datagrid + CRUD
- 🟠 Role access table (Routes & Collections CRUD)
- 🟠 **Collection Editor** (Doctrine entity builder, migrations, API permissions)
- 🟠 Enhanced CRUD with inner/outer joins 1:1, 1:N, M:N
- 🟠 MultiFile uploader
- 🟠 Collection CRUD form with most useful field types (inspired by Nova & Pocketbase)

## Backlog
- 🟠 App settings (edit envFile - dev only)
- 🟠 Storage settings (edit envFile - dev only)
- 🟠 E-mail settings (edit envFile - dev only)
- 🟠 Collection CRUD & Application events
- 🟠 Button for fake-data bulk insert into collection.
- 🟠 AI Text helper (Chat GPT + PHP Storm style)
- 🟠 Navbar resources access (vue composable)
- 🟠 Scheduled database backups to S3 & log dashboard
- 🟠 Doctrine SQL profiler: TracyBar, JsonResponse debugger (Queries count, SQL log, execution times)
- 🟠 Console cron jobs & progress dashboard
- 🟠 Console redis jobs & progress dashboard
- 🟠 Access log & error log dashboard with Tracy/BlueScreens
- 🟠 Print composer.json & package.json version in admin panel
- 🟠 JSON translations (i18n, untranslated text finder, AI auto translate)
- 🟠 GitHub actions (phpstan, tester, vulnerability audits, cmd tests, API tests)
- 🟠 Extension (Simple cart + GoPay)
- 🟠 Make intro videos (for developers & for administrators)

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
