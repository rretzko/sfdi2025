<?php

namespace App\Services;

use App\Models\Candidate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FindStudentOpenRehearsalsService
{
    private array $rehearsals = [];

    public function __construct(private int $studentId)
    {
        //get all open rehearsal version_ids
        $versionIds = $this->getOpenRehearsalVersionIds();

        //determine if $studentId is accepted into any ensemble in the open rehearsal version_ids
        $acceptedRehearsals = $this->getStudentAcceptanceVersionIds($versionIds);

        //determine if the version has a participation contract
        $hasParticipationContracts = $this->getVersionParticipationContract($acceptedRehearsals);

        //determine if the version has a participation fee
        $hasParticipationFees = $this->getVersionParticipationFees($acceptedRehearsals);

        $participationVersions = array_unique(array_merge($hasParticipationContracts, $hasParticipationFees));

        //return the filtered version_id
        $this->rehearsals = $participationVersions;
    }

    public function getRehearsals(): array
    {
        return $this->rehearsals;
    }

/** END OF PUBLIC FUNCTIONS **************************************************/

    private function getCandidateIds(): array
    {
        return Candidate::query()
            ->where('student_id', $this->studentId)
            ->pluck('id')
            ->toArray();
    }
    private function getOpenRehearsalVersionIds(): array
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');

        return DB::table('versions')
            ->join('version_config_dates AS open', 'open.version_id', '=', 'versions.id')
            ->join('version_config_dates AS close', 'close.version_id', '=', 'versions.id')
            ->where('open.date_type', 'rehearsal_open')
            ->where('open.version_date', '<=', $now)
            ->where('close.date_type', 'rehearsal_close')
            ->where('close.version_date', '>=', $now)
            ->pluck('versions.id')
            ->toArray();
    }

    private function getStudentAcceptanceVersionIds(array $versionIds): array
    {
        $candidateIds = $this->getCandidateIds();

        return DB::table('audition_results')
            ->whereIn('candidate_id', $candidateIds)
            ->whereIn('version_id', $versionIds)
            ->where('accepted', 1)
            ->pluck('version_id')
            ->toArray();
    }

    /**
     * @todo Rewrite this method after the addition of version configuration options:
     * @todo - participation contract
     * @todo - participation eSignature
     * @todo - participation ePayment
     * @param  array  $versionIds
     * @return array
     */
    private function getVersionParticipationContract(array $versionIds): array
    {
        //workaround
        $vars = [81,82,83];

        return count($versionIds)
            ? [81]
            : [];

//        return DB::table('versions')
//            ->whereIn('id', $versionIds)
//            ->where('fee_participation', "!=", 0)
//            ->pluck('id')
//            ->toArray();
    }

    private function getVersionParticipationFees(array $versionIds): array
    {
        return DB::table('versions')
            ->whereIn('id', $versionIds)
            ->where('fee_participation', "!=", 0)
            ->pluck('id')
            ->toArray();
    }

}
