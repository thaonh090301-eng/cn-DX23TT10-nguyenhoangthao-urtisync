<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index(): string
    {
        return $this->view('home/index', [
            'title' => \__('app.title'),
            'wellnessPosts' => $this->wellnessPosts(),
        ]);
    }

    private function wellnessPosts(): array
    {
        return [
            [
                'icon' => 'droplet',
                'tone' => 'water',
                'title' => \__('home.wellness.water.title'),
                'tag' => \__('home.wellness.water.tag'),
                'description' => \__('home.wellness.water.description'),
            ],
            [
                'icon' => 'dumbbell',
                'tone' => 'motion',
                'title' => \__('home.wellness.exercise.title'),
                'tag' => \__('home.wellness.exercise.tag'),
                'description' => \__('home.wellness.exercise.description'),
            ],
            [
                'icon' => 'leaf',
                'tone' => 'nutrition',
                'title' => \__('home.wellness.food.title'),
                'tag' => \__('home.wellness.food.tag'),
                'description' => \__('home.wellness.food.description'),
            ],
            [
                'icon' => 'book',
                'tone' => 'reading',
                'title' => \__('home.wellness.reading.title'),
                'tag' => \__('home.wellness.reading.tag'),
                'description' => \__('home.wellness.reading.description'),
            ],
            [
                'icon' => 'heart',
                'tone' => 'selfcare',
                'title' => \__('home.wellness.selfcare.title'),
                'tag' => \__('home.wellness.selfcare.tag'),
                'description' => \__('home.wellness.selfcare.description'),
            ],
        ];
    }
}
