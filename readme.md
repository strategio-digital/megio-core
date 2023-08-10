
# Strategio SaaS
Most powerful tool for creating webs, apps & APIs.

🟢 Demo: https://saas.strategio.dev (u: admin@test.cz p: Test1234)

🟡 Docs: https://docs.saas.strategio.dev (coming soon)

<img src="https://jzapletal.s3.eu-west-1.amazonaws.com/strategio-saas-edit-data.png" width="100%" alt="Strategio SaaS">

## Installation guide
1. Create project by `curl -sL bit.ly/3AnA49z | bash /dev/stdin create <project-folder>`
2. Move to your project folder & finish installation steps by [readme.md](https://github.com/strategio-digital/saas/blob/master/sandbox/readme.md)

## Core features
- [x] Web-ready dev-stack (simple router & Latte templates).
- [x] API-ready dev-stack (simple router & UI route permissions editor).
- [x] Fully configurable & extendable Vue 3 Admin panel.
- [ ] Admin panel with UI datagrid editor based on Doctrine entities.
- [x] Vite assets bundler for fast compiling scss, ts, vue, etc.
- [x] One click deployment with Dockerfile and easypanel.io.
- [x] Stateless and scalable architecture for PHP applications.
- [x] Optimized Docker image (Nginx & PHP-FPM) - about 20Mb costs.

## Backend features
- [x] JWT Auth with route resources protection.
- [x] Requests validation by Nette\Schema.
- [x] Symfony events & event subscribers for a lot of stuff.
- [x] Fully integrated Doctrine ORM.
- [x] Symfony Http\Kernel for handling requests.
- [x] File storage with AWS S3 adapter.
- [x] Tracy\Debugger with AWS S3 logger adapter.
- [x] Custom extensions with Nette\DI\Extensions.
- [x] Custom Symfony console commands.
- [x] PHPStan static analysis on level 8.

## Backlog
### Priority (ASAP, this or next month)
- [ ] Brainstorm entity mapping via orisai/object-mapper
- [ ] Collections edit/update view with custom vue-components
- [ ] Collections edit/update field types (inspired by Nova & Pocketbase)
- [ ] Enhanced CRUD with inner/outer joins 1:1, 1:N, M:N
- [ ] Admin datagrid + CRUD
- [ ] MultiFile uploader
- [ ] CRUD request-validation by entity props

### To release versions 1.0.0
- [ ] Custom page components (inspired by Strapi.io)
- [ ] Scheduled database backups to S3 & log dashboard
- [ ] AI text helper (Chat GPT + PHP Storm style)
- [ ] JSON translations (i18n, untranslated text finder, AI auto translate)
- [ ] App settings (edit envFile - dev only)
- [ ] Storage settings (edit envFile - dev only)
- [ ] E-mail settings (edit envFile - dev only)
- [ ] Console cron jobs & progress dashboard
- [ ] Access log & error log dashboard with Tracy/BlueScreens
- [ ] **Collection Editor** (Doctrine entity builder & safe migrations)
- [ ] Make useful docs from my notes & use [docusaurus.io](https://docusaurus.io/)

#### Create automatic tests
- [ ] Phpstan (GH Action)
- [ ] Vulnerability audits (GH Action)
- [ ] Doctrine schema-validation (GH Action)
- [ ] Nette tester / PHP Unit (GH Action)
- [ ] Sandbox project deploy (GH Action)
- [ ] Cors tests from another domain
- [ ] API endpoints tests


### Other ideas
- [ ] Add article extension (for testing puroposes)
- [ ] Add multi-tenant extensions (for invoice-gun app)
- [ ] Add cart extension (React with GoPay)
- [ ] Add localizations for collections (i18n)
- [ ] Button for fake-data bulk insert in collections
- [ ] Console redis jobs & progress dashboard
- [ ] Make intro videos (for devs & for admins)

## Tutorials

### 1. Video tutorials coming soon...

### 2. How to debug API with [Postman](https://documenter.getpostman.com/view/14885541/2s8YsqUZuv).

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

## Changelog

### 07/2023
- [x] App.php refactoring
- [x] Upgrade to symfony router 6.3
- [x] Add Symfony\Kernel and controller argument resolver with autowiring
- [x] Bootstrap.php refactoring (make it extendable in neon)
- [x] Add kernel events (CSP, CORS)
- [x] Split User entity into User & Admin entity
- [x] Admin & User login mechanism refactoring
- [x] Make User entity commutable and test it in sandbox project
- [x] Add Collection CRUD events & Application events
- [x] Add JWTAuth mechanism for Routes, Collections, CollectionNav
- [x] Split Login form into Admin & User form
- [x] Add Alert system and show alerts on response status 40X
- [x] Add Nginx request rate limiter & Symfony IP address proxy resolver
- [x] Add navbar resources, Vue composable and hide non-admin stuff
- [x] Resource loader for vue router & update button in admin panel
- [x] Role access table (Routes, Collections, CollectionsNav, Views)
- [x] Role add modal, role remove modal, cascade delete in SQLite / Postgres
- [x] Print composer.lock & yarn.lock version in admin panel
- [x] Doctrine SQL profiler: TracyBar, JsonResponse debugger (Queries count, SQL log, execution times)

### 06/2023
- [x] CRUD API trough Doctrine Entities on scalar types
- [x] Extendable collections with global configs
