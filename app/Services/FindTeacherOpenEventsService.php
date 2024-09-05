<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Version;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class FindTeacherOpenEventsService
{
    public function getTeacherEvents(int $teacherId): array //[version_id, version name]
    {
        //get all open versions
        $openVersions = $this->getOpenVersions();

        //filter versions for which $teacherId is in some participant status (i.e. not just invited and not prohibited)
        $filteredTeacherVersions = $this->filterVersionsByParticipantStatus($openVersions, $teacherId);

        //filter versions for student grade eligibility with the auth()->user();
        //return [id, version_name] for those versions
        return $this->filterVersionsByGradeEligibility($filteredTeacherVersions);
    }

    private function filterVersionsByGradeEligibility(array $filteredTeacherVersions): array
    {
        if(empty($filteredTeacherVersions)){
            return $filteredTeacherVersions;
        }

        $a = [];
        $student = Student::where('user_id', auth()->id())->first();
        $classOf = $student->class_of;
        $service = new CalcGradeFromClassOfService();
        $grade = $service->getGrade($classOf);

        foreach($filteredTeacherVersions AS $key => $versionArray){

            $version = Version::find($versionArray->id);
            $event = $version->event;
            $grades = explode(',', $event->grades);

            if( in_array($grade, $grades)){

                $a[] = $versionArray;
            }
        }

        return $a;
    }

    private function filterVersionsByParticipantStatus(array $openVersions, int $teacherId): array
    {
        $filtered = [];
        $userId = Teacher::find($teacherId)->user_id;
        $participatingStatuses = ['obligated','participating'];
        $versionIds = array_column($openVersions, 'id');

        return DB::table('version_participants')
            ->join('versions', 'versions.id', '=', 'version_participants.version_id')
            ->whereIn('version_participants.version_id', $versionIds)
            ->where('version_participants.user_id', $userId)
            ->whereIn('version_participants.status', $participatingStatuses)
            ->select('versions.id', 'versions.name')
            ->get()
            ->toArray();
    }

    private function getOpenVersions()
    {
        return Version::query()
            ->join('version_config_dates AS studentOpen', 'studentOpen.version_id', '=', 'versions.id')
            ->join('version_config_dates AS studentClose', 'studentClose.version_id', '=', 'versions.id')
            ->where('versions.status', 'active')
            ->where('studentOpen.date_type', 'student_open')
            ->where('studentOpen.version_date', '<=', Carbon::now())
            ->where('studentClose.date_type', 'student_close')
            ->where('studentClose.version_date', '>=', Carbon::now())
            ->select('versions.id', 'versions.name')
            ->get()
            ->toArray();
    }

    /**
     * Iterate through $a to remove any empty arrays
     * @param  array  $a
     * @return array
     */
    private function removeEmptyBranches(array $a): array
    {
        $clean = [];

        foreach($a AS $branch){

            if(! empty($branch)){

                $clean[] = $branch;
            }
        }

        return $clean;
    }
}
