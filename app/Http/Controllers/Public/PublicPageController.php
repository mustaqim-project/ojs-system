<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SitePage;
use Illuminate\Http\Request;

class PublicPageController extends Controller
{
    /**
     * Map route names → slug yang digunakan di SitePage
     */
    private const SLUG_MAP = [
        'public.about'           => 'about',
        'public.editorial-team'  => 'editorial-team',
        'public.reviewer-board'  => 'reviewer-board',
        'public.author-guidelines' => 'author-guidelines',
        'public.ethics'          => 'ethics',
        'public.peer-review'     => 'peer-review',
        'public.focus-and-scope' => 'focus-and-scope',
        'public.journal-policies'=> 'journal-policies',
        'public.indexing'        => 'indexing',
        'public.contact'         => 'contact',
        'public.privacy-policy'  => 'privacy-policy',
        'public.terms-conditions'=> 'terms-conditions',
        'public.announcements'   => 'announcements',
        'public.call-for-papers' => 'call-for-papers',
        'public.current-issue'   => 'current-issue',
        'public.archive'         => 'archive',
    ];

    /**
     * Map slug → view template
     */
    private const VIEW_MAP = [
        'about'            => 'public.about',
        'editorial-team'   => 'public.editorial-team',
        'reviewer-board'   => 'public.reviewer-board',
        'author-guidelines'=> 'public.author-guidelines',
        'ethics'           => 'public.ethics',
        'peer-review'      => 'public.peer-review',
        'focus-and-scope'  => 'public.focus-and-scope',
        'journal-policies' => 'public.journal-policies',
        'indexing'         => 'public.indexing',
        'contact'          => 'public.contact',
        'privacy-policy'   => 'public.privacy-policy',
        'terms-conditions' => 'public.terms-conditions',
        'announcements'    => 'public.announcements',
        'call-for-papers'  => 'public.call-for-papers',
        'current-issue'    => 'public.current-issue',
        'archive'          => 'public.archive',
    ];

    public function show(Request $request)
    {
        $routeName = $request->route()->getName();
        $slug      = self::SLUG_MAP[$routeName] ?? ltrim($request->path(), '/');
        $viewName  = self::VIEW_MAP[$slug] ?? 'public.about';

        $page = SitePage::getPage($slug);

        return view($viewName, compact('page'));
    }
}
