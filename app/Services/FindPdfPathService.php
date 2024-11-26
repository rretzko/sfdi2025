<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Student;
use App\Models\Version;
use App\Models\School;
use Illuminate\Support\Facades\Log;

class FindPdfPathService
{
    public function findApplicationPath(Candidate $candidate): string
    {
        $versionId = $candidate->version_id;
        $header = "../resources/views/";
        $path = "pdfs/applications/versions/{$versionId}/pdf.blade.php";
        $file = $header.$path;
        $view = "pdfs.applications.versions.{$versionId}.pdf";

        if (file_exists($file)) {
            return $view;
        }

        $version = Version::find($versionId);
        if ($version) {
            $eventId = $version->event_id;
            return "pdfs.applications.events.{$eventId}.pdf";
        }

        // Optionally handle the case where the version is not found
        throw new \Exception("Version with ID {$versionId} not found.");
    }

    public function findCandidateScorePath(Candidate $candidate): string
    {
        $versionId = $candidate->version_id;
        $header = "../resources/views/";
        $path = "pdfs/candidateScores/versions/$versionId}/pdf.blade.php";
        $file = $header.$path;
        $view = "pdfs.candidateScores.versions.$versionId.pdf";

        if (file_exists($file)) {
            return $view;
        }

        $version = Version::find($versionId);
        if ($version) {
            $eventId = $version->event_id;
            return "pdfs.candidateScores.events.{$eventId}.pdf";
        }

        // Optionally handle the case where the version is not found
        throw new \Exception("Version with ID {$versionId} not found.");
    }

    public function findCandidateScoresSchoolPath(Version $version): string
    {
        $versionId = $version->id;
        $header = "../resources/views/";
        $path = "pdfs/candidateScoresSchool/versions/$versionId}/pdf.blade.php";
        $file = $header.$path;
        $view = "pdfs.candidateScoresSchool.versions.$versionId.pdf";

        //if a versions/{$versionId}/pdf.blade.php file is found use that
        if (file_exists($file)) {
            return $view;
        }

        //otherwise, look for the file in the events directory
        $eventId = $version->event_id;
        $path = "pdfs/candidateScoresSchool/events/$eventId}/pdf.blade.php";
        $file = $header.$path;
        $view = "pdfs.candidateScoresSchool.events.$eventId.pdf";

        if (file_exists($file)) {
            return $view;
        }

        //lastly use the default pdf.blade.php file
        return "pdfs.candidateScoresSchool.pdf";
    }

    public function findContractPath(Candidate $candidate): string
    {
        $versionId = $candidate->version_id;
        $header = "../resources/views/";
        $path = "pdfs/contracts/versions/{$versionId}/pdf.blade.php";
        $file = $header.$path;
        $view = "pdfs.contacts.versions.{$versionId}.pdf";

        if (file_exists($file)) {
            return $view;
        }

        $version = Version::find($versionId);
        if ($version) {
            $eventId = $version->event_id;
            return "pdfs.contracts.events.{$eventId}.pdf";
        }

        // Optionally handle the case where the version is not found
        throw new \Exception("Version with ID {$versionId} not found.");
    }

    public function findEstimatePath(Version $version): string
    {
        $versionId = $version->id;
        $header = "../resources/views/";
        $path = "pdfs/estimates/versions/{$versionId}/pdf.blade.php";
        $file = $header.$path;
        $view = "pdfs.estimates.versions.{$versionId}.pdf";

        if (file_exists($file)) {
            return $view;
        }

        $version = Version::find($versionId);
        if ($version) {
            $eventId = $version->event_id;
            return "pdfs.estimates.events.{$eventId}.pdf";
        }

        // Optionally handle the case where the version is not found
        throw new \Exception("Version with ID {$versionId} not found.");
    }

    public function findParticipationContractPath(Version $version, Student $student): string
    {
        $root = resource_path(DIRECTORY_SEPARATOR
            . 'views'
            . DIRECTORY_SEPARATOR
            . 'pdfs'
            . DIRECTORY_SEPARATOR
            . 'participationContracts');

        $fileName = 'pdf.blade.php';

        $default = $root . DIRECTORY_SEPARATOR . $fileName;
        $versionId = $version->id;

        $file = $root
            . DIRECTORY_SEPARATOR
            . 'versions'
            . DIRECTORY_SEPARATOR
            . $versionId
            . DIRECTORY_SEPARATOR
            . $fileName;

        if(! file_exists($file)){
            $eventId = $version->event_id;
            $file = "$root/events/$eventId/$fileName";
        }

        if(! file_exists($file)){

            $file = $default;
        }
//Log::info('*** final file: ' . $file . ' ***');
        return $file;
    }
}
