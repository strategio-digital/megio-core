
# Strategio SaaS
Most powerful tool for creating webs, apps & APIs.

- [x] Demo: https://saas.strategio.dev (u: admin@test.cz p: Test1234)
- [ ] Docs: https://docs.saas.strategio.dev (coming soon)
- [x] Backlog: https://docs.saas.strategio.dev/backlog
- [x] Changelog: https://docs.saas.strategio.dev/changelog

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
