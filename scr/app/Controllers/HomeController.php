<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index(): string
    {
        return $this->view('home/index', [
            'title' => 'Personal Time Optimizer',
        ]);
    }
}
