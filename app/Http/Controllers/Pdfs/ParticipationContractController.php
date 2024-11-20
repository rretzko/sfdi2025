<?php

namespace App\Http\Controllers\Pdfs;

use App\Data\Pdfs\PdfApplicationDataFactory;
use App\Data\Pdfs\PdfParticipationContractDataFactory;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Version;
use App\Services\FindPdfPathService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ParticipationContractController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Version $version, Student $student)
    {
        $service = new FindPdfPathService;
        $path = $service->findParticipationContractPath($version, $student);

        $data = new PdfParticipationContractDataFactory($version, $student);
        $dto = $data->getDto();

        $viewPath = $this->getViewPath($path);
Log::info('*** controller viewPath: ' . $viewPath);
        $pdf = PDF::loadView($viewPath, compact('dto'));

        $studentName = $student->user->name;
        $prefix = str_replace(' ', '', $studentName);

        return $pdf->download($prefix . '_contract.pdf');
    }

    /**
     * @param  string  $path
     * @return string
     */
    private function getViewPath(string $path)
    {
        //production ex $path = "/var/task/resources/views/pdfs/participationContracts/events/18/pdf.blade.php"
        if(substr($path, 1, 3) === 'var'){
            $removeHead = str_replace('/var/task/resources/views/', '', $path);
        }

        //dev ex $path =  "C:\xampp\htdocs\staging\sfdi2025\resources\views\pdfs\participationContracts/events/19/pdf.blade.php"
        if(str_starts_with($path, "C")){
            $removeHead = str_replace("C:\\xampp\\htdocs\\staging\\sfdi2025\\resources\\views\\", '', $path);
        }

        //remove tail .blade.php
        return substr($removeHead, 0, -10);
    }
}
