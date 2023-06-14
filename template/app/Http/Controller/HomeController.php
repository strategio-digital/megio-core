<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace App\Http\Controller;

use Saas\Helper\Path;
use Saas\Http\Controller\Controller;

class HomeController extends Controller
{
    public function index(): void
    {
        $this->getResponse()->render(Path::viewDir() . '/controller/home.latte', [
            'title' => 'Strategio SaaS',
            'description' => 'The Tool for developing webs & APIs by simple clicks.',
            'tech' => 'Doctrine ORM + Symfony Router + Latte + Vue + Vite'
        ]);
    }
}