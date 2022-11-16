<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';

$startedAt = microtime(true);
$container = (new \Saas\Bootstrap())
    ->projectRootPath(__DIR__ . '/../')
    ->configure([\Saas\Helper\Path::configDir() . '/app.neon'], $startedAt);

/** @var \Saas\App $app */
$app = $container->getByType(\Saas\App::class);
$app->run($container);