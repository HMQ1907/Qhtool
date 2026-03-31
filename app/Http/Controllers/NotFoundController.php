<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class NotFoundController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('NotFound');
    }
}
