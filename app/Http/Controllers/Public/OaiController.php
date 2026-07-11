<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ApiIntegration;
use App\Services\OaiPmhService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OaiController extends Controller
{
    /**
     * Endpoint OAI-PMH
     */
    public function __invoke(Request $request, OaiPmhService $oaiService): Response
    {
        // Cek apakah OAI-PMH diaktifkan di admin panel
        if (!ApiIntegration::isEnabled('oai_pmh') || ApiIntegration::getValue('oai_pmh', 'enabled') != '1') {
            return response('OAI-PMH service is disabled.', 503);
        }

        $params   = $request->all();
        $xmlOutput = $oaiService->handle($params);

        return response($xmlOutput, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
