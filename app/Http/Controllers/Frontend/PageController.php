<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $page = Page::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with(['translations'])
            ->firstOrFail();

        return view('frontend.pages.show', [
            'page' => $page,
            'translation' => $page->translation(),
        ]);
    }
}
