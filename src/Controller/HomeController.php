<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Framework\Controller;

use Framework\Storage\Storage;

class HomeController extends Controller
{
    public function index(Storage $storage): void
    {
        $dt = new \DateTime();
        
        $this->getResponse()->send([
            'name' => $_ENV['APP_NAME'],
            'mode' => $_ENV['APP_ENV_MODE'],
            'storage_adapter' => $storage->getAdapterName(),
            'current_dt' => [
                'date_time' => $dt->format('Y.m.d H:i:s:u'),
                'time_zone' => $dt->getTimezone()->getName()
            ]
        ]);
    }
}