<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Issue;
use App\Services\XmlExportService;
use Symfony\Component\HttpFoundation\Response;

class ExportController extends Controller
{
    private XmlExportService $xmlService;

    public function __construct(XmlExportService $xmlService)
    {
        $this->xmlService = $xmlService;
    }

    /**
     * Download XML artikel.
     */
    public function exportArticle(Article $article): Response
    {
        $xmlOutput = $this->xmlService->exportArticle($article);
        $fileName  = 'article-' . $article->id . '-' . $article->slug . '.xml';

        return response($xmlOutput, 200)
            ->header('Content-Type', 'text/xml')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    /**
     * Download XML Issue.
     */
    public function exportIssue(Issue $issue): Response
    {
        $xmlOutput = $this->xmlService->exportIssue($issue);
        $fileName  = 'issue-' . $issue->id . '-vol-' . $issue->volume . '-num-' . $issue->number . '.xml';

        return response($xmlOutput, 200)
            ->header('Content-Type', 'text/xml')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }
}
