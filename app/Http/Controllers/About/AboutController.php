<?php

namespace App\Http\Controllers\About;

use App\Http\Controllers\Controller;
use App\Services\Content\PageContentService;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function index(PageContentService $pages): View
    {
        $page = $pages->forSlug('about');

        return view('site.about.about', [
            'pageTitle' => $page['title'],
            'content'   => $page['content'],
        ]);
    }
}
