<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\EksporService;

class EksporController extends Controller
{
    protected $eksporService;

    public function __construct(EksporService $eksporService)
    {
        $this->eksporService = $eksporService;
    }

    public function rekapExcel()
    {
        return $this->eksporService->eksporRekapExcel();
    }

    public function rekapPdf()
    {
        return $this->eksporService->eksporRekapPdf();
    }
}
