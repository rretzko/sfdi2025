<div class="mt-4">

    <style>
        .pageBreak {
            page-break-after: always;
        }
    </style>

    {{-- HEADER --}}
    <header class="flex flex-row justify-between mx-2">
{{--        <div>--}}
{{--            @if($dto['logo'])--}}
{{--                --}}{{-- https://auditionsuite-production.s3.amazonaws.com/logos/testPublic.jpg --}}
{{--                <img src="{{ Storage::disk('s3')->url($dto['logo']) }}" alt="{{ $dto['organizationName'] }} logo"--}}
{{--                     height="60" width="60"/>--}}
{{--            @endif--}}
{{--        </div>--}}

        <div class="flex flex-col justify-center">
            <div style="text-align: center; font-size: 1.5rem; font-weight: bold;">
                Morris Area Honor Choir - Middle and High School
            </div>
            <div style="text-align: center; font-size: 1.5rem;">
                {{ $form->versionName }}
            </div>
            <div style="text-align: center; font-size: 0.66rem; margin-bottom: 0.25rem;">
                <p style="margin-bottom: 0.25rem">
                    eApplications are accepted through: <span
                        style="color: red;">{{ $form->closeApplicationDateFormatted }}</span><br/>
                    All audio files must be submitted to your sponsoring Directors.
                </p>
                <p class="">
                    PLEASE NOTE: Morris Area Choral Directors Associations reserved the right to require masks at any
                    time, based on current health guidelines and host school requirements.
                </p>
            </div>
        </div>

    </header>

    {{-- SUMMARY --}}
    <section id="studentProfile" class="pageBreak">

        {{-- NOTE: BUILT AS TABLE FOR EASIER TRANSLATION TO PDF --}}
        <style>
            table {
                margin-bottom: 1rem;
                width: 100%;
                text-align: left;
            }

            td, th {
                border: 0;
            }

            th.rowHeader {
                font-weight: bold;
                padding-left: 1rem;
                background-color: lightblue;
            }

            td.label {
                width: 8rem;
            }

            td.data {
                font-weight: bold;
            }
        </style>
        <table>
            <tr>
                <td class="label">Student Name</td>
                <td class="data">{{ $form->student->user->fullNameAlpha }}</td>
            </tr>
            <tr>
                <td class="label">Address</td>
                <td class="data">{{ $form->addressString }}</td>
            </tr>
            <tr>
                <td class="label">Height</td>
                <td class="data">{{ $form->footInch . ' (' . $form->height . '")' }}</td>
            </tr>
            <tr>
                <td colspan="2" style="height: 6px;" note="spacer row"></td>
            </tr>
            <tr>
                <td class="label">Home Phone</td>
                <td class="data">{{ $form->phoneHome }}</td>
            </tr>
            <tr>
                <td class="label">Cell Phone</td>
                <td class="data">{{ $form->phoneMobile }}</td>
            </tr>
            <tr>
                <td colspan="2" style="height: 6px;" note="spacer row"></td>
            </tr>
            <tr>
                <td class="label">Email</td>
                <td class="data">{{ $form->email }}</td>
            </tr>
        </table>

        {{-- EMERGENCY CONTACT INFORMATION --}}
        <table>
            <tr>
                <th colspan="2" class="rowHeader">Emergency Contact Information</th>
            </tr>
            <tr>
                <td class="label" style="vertical-align: top;">Parent Information</td>
                <td class="data">{!! $form->emergencyContactString !!}</td>
            </tr>
        </table>

        {{-- CHORAL DIRECTOR INFORMATION --}}
        <table>
            <tr>
                <th colspan="2" class="rowHeader">Choral Director Information</th>
            </tr>
            <tr>
                <td class="label">School</td>
                <td class="data">{{ $form->schoolName }}</td>
            </tr>
            <tr>
                <td class="label">Choral Director</td>
                <td class="data">{{ $form->teacherFullName }}</td>
            </tr>
            <tr>
                <td class="label">Email</td>
                <td class="data">{{ $form->teacherEmail }}</td>
            </tr>
            <tr>
                <td class="label" style="vertical-align: top;">Phones</td>
                <td class="data">{{ $form->teacherPhoneBlock }}</td>
            </tr>
        </table>

        {{-- AUDITION INFORMATION --}}
        <table>
            <tr>
                <th colspan="2" class="rowHeader">Audition Information</th>
            </tr>
            <tr>
                <td class="label">Grade</td>
                <td class="data">{{ $form->grade }}</td>
            </tr>
            <tr>
                <td class="label">Preferred Pronoun</td>
                <td class="data">{{ $form->pronounDescr }}</td>
            </tr>
            <tr>
                <td class="label">Voice Part</td>
                <td class="data">{{ $form->candidateVoicePartDescr }}</td>
            </tr>
        </table>

        {{-- PAYMENT RECORD --}}
        <table>
            <tr>
                <th colspan="2" class="rowHeader">Payment Record</th>
            </tr>
            <tr>
                <td colspan="2">
                    An audition Fee of ${{ $form->feeAudition }} per student will be charged. In addition, chorus
                    students
                    accepted will be assessed a participation fee of ${{ $form->feeParticipation }}. <br/>
{{--                    Payment Method: <b>none found</b>--}}
                </td>
            </tr>
        </table>
    </section>

    {{-- PAGE TWO --}}
    <section>
        {{-- HEADER --}}
        <header style="margin-bottom: 1rem;" class="flex flex-row justify-between mx-2">
{{--            <div>--}}
{{--                @if($dto['logo'])--}}
{{--                    --}}{{-- https://auditionsuite-production.s3.amazonaws.com/logos/testPublic.jpg --}}
{{--                    <img src="{{ Storage::disk('s3')->url($dto['logo']) }}" alt="{{ $dto['organizationName'] }} logo"--}}
{{--                         height="60" width="60"/>--}}
{{--                @endif--}}
{{--            </div>--}}

            <div class="flex flex-col justify-center">
                <div style="text-align: center; font-size: 1.5rem; font-weight: bold;">
                    Morris Area Honor Choir - Middle and High School
                </div>
                <div style="text-align: center; font-size: 1.5rem;">
                    {{ $form->versionName }}
                </div>
                <div style="text-align: center; font-size: 0.66rem; margin-bottom: 0.25rem;">
                    <p style="margin-bottom: 0.25rem">
                        eApplications are accepted through: <span
                            style="color: red;">{{ $form->closeApplicationDateFormatted }}</span><br/>
                        All audio files must be submitted to your sponsoring Directors.
                    </p>
                    <p class="">
                        PLEASE NOTE: Morris Area Choral Directors Associations reserved the right to require masks at
                        any
                        time, based on current health guidelines and host school requirements.
                    </p>
                </div>
                <div style="text-align: center; font-size: 1rem;">
                    Page 2/2
                </div>
            </div>

        </header>

        {{-- CHOIR SCHEDULE --}}
        <style>
            #choirSchedule {
                border-collapse: collapse;
                font-size: 0.8rem;
            }

            #choirSchedule td, th {
                border: 1px solid lightgray;
                padding: 0 0.25rem;
            }
        </style>
        <table id="choirSchedule">
            <tr>
                <th colspan="4" class="rowHeader">Morris Area Honor Choir Schedule</th>
            </tr>
            <tr>
                <td colspan="4" style="font-size: 0.6rem; font-weight: bold; text-align: center;">
                    ** All students will be given learning tracks and will be expected to learn their music before the
                    first rehearsal **
                </td>
            </tr>
            <tr>
                <th>Event</th>
                <th>Date</th>
                <th>Time</th>
                <th>Location</th>
            </tr>
            <tr>
                <td style="text-align: center;">
                    Rehearsal
                </td>
                <td style="text-align: center;">
                    Thursday, January 9th
                </td>
                <td style="text-align: center;">
                    4-8:00 pm (middle school)<br />
                    4-8:15 pm (high school)
                </td>
                <td style="text-align: center;">
                    Randolph High School
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">
                    Rehearsal
                </td>
                <td style="text-align: center;">
                    Monday, January 13th
                </td>
                <td style="text-align: center;">
                    4-8:00 pm (middle school)<br />
                    4-8:15 pm (high school)
                </td>
                <td style="text-align: center;">
                    Randolph High School
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">
                    SNOW DATE Rehearsal
                </td>
                <td style="text-align: center;">
                    Tuesday, January 14th
                </td>
                <td style="text-align: center;">
                    4-8:00 pm (middle school)<br />
                    4-8:15 pm (high school)
                </td>
                <td style="text-align: center;">
                    Randolph High School
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">
                    Rehearsal
                </td>
                <td style="text-align: center;">
                    Wednesday, January 15th
                </td>
                <td style="text-align: center;">
                    4-8:00 pm (middle school)<br />
                    4-8:15 pm (high school)
                </td>
                <td style="text-align: center;">
                    Randolph High School
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">
                    All-Day Rehearsal
                </td>
                <td style="text-align: center;">
                    Friday, January 17th
                </td>
                <td style="text-align: center;">
                    9:00 am - 3:15 pm
                </td>
                <td style="text-align: center;">
                    Mt. Olive Middle School
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">
                    Concert
                </td>
                <td style="text-align: center;">
                    Saturday, January 18th
                </td>
                <td style="text-align: center;">
                    Call: 1:00 pm<br />
                    Concert: 4:00 pm
                </td>
                <td style="text-align: center;">
                    Randolph High School
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">
                    SNOW DATE Concert
                </td>
                <td style="text-align: center;">
                    Sunday, January 19th
                </td>
                <td style="text-align: center;">
                    Call: 1:00 pm call<br />
                    Concert: 4:00 pm
                </td>
                <td style="text-align: center;">
                    Randolph High School
                </td>
            </tr>
            <tr>
                <td colspan="4" style="text-align: left; padding-left: 0.25rem;">
                    Randolph HS - 511 Millbrook Ave, Randolph, NJ 07869
                </td>
            </tr>
        </table>

        {{-- ENSEMBLE EXPECTATIONS AND POLICIES --}}
        <table>
            <tr>
                <th class="rowHeader">Ensemble Expectations and Policies</th>
            </tr>
            <tr>
                <td>
                    <ol style="margin-left: 2rem; list-style-type: decimal;">
                        <li>
                            Participants are required to attend all rehearsals and performances for their full duration.
                            Students who show up late or leave early will be counted as an absence for that rehearsal.
                        </li>
                        <li>
                            A single absence due to illness may be allowed, provided such absence is explained to the
                            satisfaction of the student's director, who in turn will notify the chorus manager as to the
                            nature of the absence. Absence for other extenuating circumstances of a serious nature,
                            beyond
                            the student's control, will be permitted provided the absence is approved by BOTH the
                            student's
                            school director and the chorus manager. In any event, only ONE evening absence for whatever
                            reason may be excused. (If either the director or manager finds the absence unexcused, the
                            student's membership will be terminated.)
                        </li>
                        <li>
                            Any student who misses more than one evening rehearsal for ANY reason, or who misses the
                            all-day
                            rehearsal before the concert or rehearsal the morning of the concert will not be allowed to
                            participate.
                        </li>
                        <li>
                            An audition fee of ${{ $form->feeAudition }} per student will be charged. In addition,
                            chorus students
                            accepted will be assessed a participation fee of ${{ $form->feeParticipation }}.
                        </li>
                    </ol>
                </td>
            </tr>
        </table>

        {{-- ENDORESEMENTS --}}
        <table>
            <tr>
                <th colspan="2" class="rowHeader">Endorsements</th>
            </tr>
            <tr>
                <td class="label" style="vertical-align: top;">
                    Student Certification
                </td>
                <td class="data">
                    I certify that I will accept the decisions of the judges and conductors as binding and if selected
                    will accept membership in this organization. I understand that membership in this organization
                    will be terminated if I fail to perform satisfactorily within my own school group or if I fail to
                    adhere to the rules set forth above.
                </td>
            </tr>
            <tr style="border-bottom: 1px solid black;">
                <td class="label" style="vertical-align: top;">
                    Signature
                </td>
                <td class="data flex flex-row items-center space-x-2">
                    <input
                        type="checkbox"
                        wire:model.live="form.signatureStudent"
                        @disabled($form->signatureStudent)
                    />
                    <label for="form.signatureStudent" class="@if($form->signatureStudent) italic @endif">
                        {{ $form->studentFullName }}
                        <span class="text-xs">{{ $form->signedAtStudent }}</span>
                    </label>
                </td>
            </tr>
            <tr>
                <td class="label" style="vertical-align: top;">
                    Parent or Guardian Certification
                </td>
                <td class="data">
                    As parent of legal guardian of <b>{{ $form->studentFullName }}</b>, I give my permission
                    for {{ $form->pronounPossessive }}
                    to be an applicant for this organization. I understand that neither {{ $form->schoolName }} nor
                    Morris Area
                    Choral Directors Association assumes responsibility for illness or accident. I further attest the
                    statement signed by {{ $form->studentFullName }} and will assist {{ $form->pronounPossessive }} in fulfilling the obligations
                    incurred.
                </td>
            </tr>
            <tr style="border-bottom: 1px solid black;">
                <td class="label" style="vertical-align: top;">
                    Signature
                </td>
                <td class="data flex flex-row items-center space-x-2">
                    <input
                        type="checkbox"
                        wire:model.live="form.signatureGuardian"
                        @disabled($form->signatureGuardian)
                    />
                    <label for="form.signatureGuardian" class="@if($form->signatureGuardian) italic @endif">
                        {{ $form->emergencyContactName }}
                        <span class="text-xs">{{ $form->signedAtGuardian }}</span>
                    </label>
                </td>
            </tr>
        </table>

        @if($form->signatureGuardian || $form->signatureStudent)
            <div class="text-red-600">
                Please see your teacher ({{ $form->teacherFullName }}) to remove either signature or to print a copy of this application.
            </div>
        @endif

    </section>

</div>

