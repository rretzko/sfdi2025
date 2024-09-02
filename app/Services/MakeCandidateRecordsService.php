<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Candidate;
use App\Models\Version;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\UserConfig;
use Illuminate\Support\Facades\Log;

/**
 * Scan database to ensure that all students under auth()->id()
 * have a candidate record for $this->versionId
 */
class MakeCandidateRecordsService
{
    public function __construct(
        private readonly array $coTeachers,
        private readonly int $schoolId,
        private readonly int $studentId,
        private readonly int $teacherId,
        private readonly int $versionId,
    )
    {
        $this->init();
    }

    private function init(): void
    {
        $versionIdLength = strlen((string)$this->versionId);

        if ($this->schoolId) {

            foreach ($this->missingStudentIds() as $studentId) {

                $student = Student::find($studentId);

                $id = $this->makeUniqueCandidateId();

                $candidate = Candidate::create(
                    [
                        'id' => $id,
                        'ref' => substr($id, 0, $versionIdLength).'-'.substr($id, $versionIdLength),
                        'student_id' => $studentId,
                        'version_id' => $this->versionId,
                        'school_id' => $this->schoolId,
                        'teacher_id' => $this->teacherId,
                        'voice_part_id' => $student->voice_part_id,
                        'status' => 'eligible',
                        'program_name' => $student->user->name,
                    ]
                );
            }
        }
    }

    /**
     * Modified version of TheDirectorsRoom.com original to maintain the structure
     * while repointing the inputs
     * and modifying the purpose from identifying ALL missing student ids to isolating
     * that search to the current $user
     * @return array
     */
    private function missingStudentIds(): array
    {
        //array of student ids
        $studentIds = [$this->studentId];

        // Get all candidate student ids for the given version
        $candidateStudentIds = Candidate::query()
            ->whereIn('teacher_id', $this->coTeachers)
            ->where('version_id', $this->versionId)
            ->pluck('student_id')
            ->toArray();

        //reduce the $studentIds to only those missing from $candidateStudentIds
        //i.e. return the one studentId if it is not found in $canddidateStudentIds
        return array_filter($studentIds, function ($studentId) use ($candidateStudentIds) {
            return !in_array($studentId, $candidateStudentIds);
        });
    }

    private function getEligibleStudentIds(): array
    {
        //only update active or sandbox versions
        if (in_array($this->version->status, ['active', 'sandbox'])) {

            $event = Event::find($this->version->event_id);

            $classOfs = ConvertGradesToClassOfsArray::convertGrades(explode(',', $event->grades));

            return Student::query()
                ->join('student_teacher', 'student_teacher.student_id', '=', 'students.id')
                ->whereIn('student_teacher.teacher_id', $this->coTeachers)
                ->whereIn('class_of', $classOfs)
                ->pluck('students.id')
                ->toArray();

        } else {

            return [];
        }
    }

    private function makeUniqueCandidateId(): int
    {
        $attempts = 0;
        $maxAttempts = 300;
        $testId = null;

        do {

            $testId = $this->versionId.random_int(1000, 9999);
            $attempts++;

        } while (Candidate::find($testId) && ($attempts < $maxAttempts));

        if ($attempts >= $maxAttempts) {
            throw new \Exception(('Unable to generate a unique candidate ID after '
                .$maxAttempts.'attempts.'));
        }

        return $testId;
    }


}
