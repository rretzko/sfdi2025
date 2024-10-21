<div class="mt-4">

    <style>
        .sectionHeader {
            background-color: lightblue;
            text-transform: uppercase;
            padding: 0 0.25rem;
            font-size: 1.0rem;
            font-weight: bold;
            margin-bottom: 0.25rem;
        }

        .conditions {
            font-size: 0.9rem;
        }

        .pageBreak {
            page-break-after: always;
        }
    </style>

    {{-- HEADER --}}
    <table style="border-collapse: collapse; width: 99%; margin: auto; margin-bottom: 0.5rem;">
        <tbody>
        <tr>
            <th style="text-align: center; font-size: 1.5rem; font-weight: bold;">
                All Shore Chorus Audition Contract
            </th>
        </tr>
        </tbody>
    </table>

    {{-- SUMMARY --}}
    <style>
        #summaryTbl table {
            width: 99%;
            margin: auto;
        }

        #summaryTbl td {
            border: 1px solid black;
            text-align: center;
        }
    </style>
    <table id="summaryTbl" style="border-collapse: collapse; width: 99%; margin: auto; margin-bottom: 0.5rem;">
        <tbody>
        <tr>
            <td>{{ $dto['fullNameAlpha'] }}</td>
            <td style="color: red;">{{ $dto['candidateVoicePartDescr'] }}</td>
            <td>Grade: {{ $dto['grade'] }}</td>
            <td>{{ $dto['schoolShortName'] }}</td>
        </tr>
        </tbody>
    </table>

    {{-- AUDITION FEE --}}
    <section id="auditionFee" style="text-align: right; font-weight: bold; margin-bottom: 0.5rem;">
        <div class="mr-2">THE AUDITION FEE IS: ${{ $dto['auditionFee'] }}</div>
    </section>

    {{-- STUDENT ENDORSEMENT --}}
    <section id="studentEndorsement" class="pageBreak">

        <header class="sectionHeader">
            Student Endorsement - Signatures Required
        </header>

        <div class="conditions">
            <div class="flex flex-col italic justify-self-stretch mx-4 mb-4">
                <b class="mb-2">In return for the privilege of participating in the {{ $dto['versionShortName'] }}, I
                    agree to
                    the following:</b>
                <ul class="ml-8 list-disc">
                    <style>li {
                            margin-bottom: .5rem;
                        }</style>
                    <li>
                        I, <b>{{ $dto['fullName'] }}</b>, agree to accept the decision of the
                        judges as binding. If selected, I will accept membership in the {{ $dto['versionShortName'] }}
                        for which I have auditioned. I also agree to pay the ${{ $dto['participationFee'] }} (subject to
                        change) participation fee.
                        I understand that membership in this organization may be terminated
                        by the endorsers of my application if I fail to comply with the rules set forth or if
                        I fail to learn my music.
                    </li>
                    <li>
                        @if(count($dto['ensembleNames']))
                            I understand that {{ $dto['ensembleNames'][0] }} members are expected to attend rehearsals
                            in January.
                        @else
                            <span class="text-red-600">[No ensembles found]</span>
                        @endif
                        Two absences will be tolerated. An absence is defined as missing any scheduled rehearsal.
                        Seniors auditioning for college may have a third absence that must be pre-approved before that
                        absence with documentation to the executive board.
                        Two lates will be considered one absence.
                        A late is defined as arriving 10 or more minutes late to rehearsal, as well as leaving more
                        than 10 minutes early from rehearsal.
                        I understand that it is not possible for me to be a member of the {{ $dto['versionShortName'] }}
                        if I
                        can not attend the full Dress Rehearsal and Concert.
                        Failure to fulfill my {{ $dto['versionShortName'] }} obligations will
                        result in disqualification from the ensemble.
                        I understand that the president, with the approval of the NJ All Shore Chorus Executive
                        Committee,
                        will resolve all serious conflicts and/or questionable circumstances
                        not specifically covered by the above.
                    </li>
                    <li>
                        I will respect the property of others, will act professionally, and will treat all members of
                        the
                        ensemble with respect.
                    </li>
                    <li>
                        I will learn all the music to the best of my ability.
                    </li>
                    <li>
                        I will cooperate fully with staff, conductor, accompanist, and school personnel of the NJ All
                        Shore
                        Chorus and the hosting school's rules and regulations..
                    </li>
                    <li>
                        I will assume all responsibility for my music, folder, performance apparel, luggage and other
                        belongings at the sites of all rehearsals and concerts.
                    </li>
                    <li>
                        I will neither use nor have in my possession, at any time, alcoholic beverages, illegal drugs or
                        weapons of any kind. Vaping will not be tolerated, and will result in immediate expulsion from
                        the NJ All Shore Chorus.
                    </li>
                    <li>
                        I will adhere to all dates concerning fees/forms or any other deadlines requested for my
                        participation.
                    </li>
                    <li>
                        I understand that NJ All Shore Chorus members are required to comply with all obligations set
                        forth
                        above. Non-compliance with any provision contained herein shall constitute a breach of this
                        Agreement and shall serve as the basis of the participant's immediate termination and exclusion
                        from all performances.
                    </li>
                    <li>
                        I further understand that as a {{ $dto['versionShortName'] }} member, I must remain an active
                        member in
                        good standing with the school ensemble that corresponds to my NJ All Shore ensemble throughout
                        my entire NJ All Shore experience.
                    </li>

                </ul>

            </div>{{-- end of class=conditions --}}

            {{-- SIGNATURES --}}
            <table style="width: 100%; font-weight: bold;">
                <tr>
                    <td style="text-align: left; width: 50%;">
                        ____________________________________________<br/>
                        {{ strtoupper($dto['fullName']) }} SIGNATURE
                    </td>
                    <td style="text-align: right; width: 50%;">
                        DATE: _________
                    </td>
                </tr>
            </table>

        </div>
    </section>
    {{-- END OF STUDENT ENDORSEMENT --}}

    {{-- PAGE BREAK HERE --}}

    {{-- START PAGE 2 --}}
    {{-- HEADER --}}
    <table style="border-collapse: collapse; width: 99%; margin: auto; margin-bottom: 1rem;">
        <tbody>
        <tr>
            <td style="">
                <div style="text-align: center;">
                    <div style="font-weight: bold; mb-2">
                        {{ $dto['schoolShortName'] }}
                    </div>
                    <div class="text-center mb-2">
                        {{ $dto['versionShortName'] }}
                    </div>
                    <div class="text-center mb-2">
                        Student Application - Page 2/2
                    </div>
                </div>
            </td>
        </tr>
        </tbody>
    </table>

    {{-- SUMMARY --}}
    <style>
        #summaryTbl table {
            width: 99%;
            margin: auto;
        }

        #summaryTbl td {
            border: 1px solid black;
            text-align: center;
        }
    </style>
    <table id="summaryTbl" style="border-collapse: collapse; width: 99%; margin: auto; margin-bottom: 0.5rem;">
        <tbody>
        <tr>
            <td>{{ $dto['fullNameAlpha'] }}</td>
            <td style="color: red;">{{ $dto['candidateVoicePartDescr'] }}</td>
            <td>Grade: {{ $dto['grade'] }}</td>
            <td>{{ $dto['schoolShortName'] }}</td>
        </tr>
        </tbody>
    </table>

    {{-- AUDITION FEE --}}
    <section id="auditionFee" style="text-align: right; font-weight: bold; margin-bottom: 0.5rem;">
        <div class="mr-2">THE AUDITION FEE IS: ${{ $dto['auditionFee'] }}</div>
    </section>

    {{-- GUARDIAN ENDORSEMENT --}}
    <section id="guardianEndorsement" style="margin-bottom: 1rem;">

        <header class="sectionHeader ">
            Parent/Legal Guardian Endorsement - Signatures Required
        </header>

        <div class="conditions" style="margin-bottom: 0.5rem;">
            <div class="italic justify-self-stretch mx-4 mb-4">
                As the parent or legal guardian of <b>{{ $dto['fullName'] }}</b>, I declare that I have
                read the endorsement, which {{ $dto['first'] }} has signed, and I give permission
                for {{ $dto['pronounObject'] }} to audition to become a member of the
                {{ $dto['versionShortName'] }}. I promise to assist {{ $dto['first'] }} in
                fulfilling {{ $dto['versionShortName'] }} obligations and in meeting any expenses necessary for
                rehearsals and concerts. I
                understand the attendance requirements, and all rules and regulations listed in this contract.
                I will accept the decision of the judges on my child's admission into the ensemble based on their
                audition scoring.
            </div>
        </div>{{-- end of class=conditions --}}

        <div class="signatures">

            <table style="width: 100%; font-weight: bold;">
                <tr>
                    <td style="text-align: left; width: 75%;">
                        ________________________________________________<br/>
                        SIGNATURE OF {{ strtoupper($dto['emergencyContactName']) }}<br/>
                        <span
                            style="font-size: 0.8rem; @if(strstr($dto['emergencyContactMobile'], 'found')) color: red; @endif">{{ strtoupper($dto['emergencyContactName']) }} CELL PHONE: {!! $dto['emergencyContactMobile'] !!}</span>

                    </td>
                    <td style="text-align: right; width: 25%;">
                        DATE: _________
                    </td>
                </tr>
            </table>

        </div>

    </section>
    {{-- END OF GUARDIAN ENDORSEMENT --}}

    {{-- TEACHER ENDORSEMENT --}}
    <section id="teacherEndorsement" class="mb-2">

        <header class="sectionHeader">
            Principal/Teacher Endorsement - Signatures Required
        </header>

        <div class="conditions" style="margin-bottom: 0.5rem;">

            <div class="italic justify-self-stretch mx-4 mb-4">
                <p>We recommend <b>{{ $dto['fullName'] }}</b> for participation in the {{ $dto['versionShortName'] }}.
                    <b>{{ $dto['first'] }}</b> is a qualified candidate in good
                    standing, is a current member of chorus at the student's home school, and is presently enrolled in
                    grade {{ $dto['grade'] }} at {{ $dto['schoolName'] }}.
                    We understand that <b>{{ $dto['teacherFullName'] }}</b>, who is sponsoring
                    <b>{{ $dto['fullName'] }}</b>,
                    is required to
                    participate as a JUDGE AT LIVE AUDITIONS on Tuesday, November 19th, 2024 at Wall Township High School.
                    If the director can not attend, they must provide an adequate substitute to fulfill the commitment.
                    The director also understands that if they have students accepted to
                    the {{ $dto['versionShortName'] }},
                    they must fulfill on rehearsal duty and their assigned duty at the concert.
                </p>
                <p>
                    We will review this application to ensure that all parts are complete and accurate. This application
                    will be presented to the Registration Manager at the time of entrance to the audition.
                    FAILURE TO PRODUCE THIS SIGNED CONTRACT AT AUDITIONS WILL RESULT IN THE STUDENT NOT BEING ELIGIBLE
                    TO AUDITION.
                    If <b>{{ $dto['fullName'] }}</b> is accepted,
                    we will ensure that <b>{{ $dto['pronounPersonal'] }}</b> is prepared and adheres to
                    the rules and regulations set forth by the {{ $dto['organizationName'] }}.
                </p>

            </div>

        </div>{{-- end of class=conditions --}}

        {{-- SIGNATURES --}}
        <div class="signatures">

            <table style="width: 100%; font-weight: bold;">
                <tr>
                    <td style="text-align: left; width: 75%; height: 6rem;">
                        PRINCIPAL SIGNATURE: ________________________
                    </td>
                    <td style="text-align: right; width: 25%; height: 6rem;">
                        DATE: _________
                    </td>
                </tr>
                <tr>
                    <td style="text-align: left; width: 75%;">
                        ____________________________________________<br/>
                        {{ strtoupper($dto['teacherFullName']) }} SIGNATURE
                    </td>
                    <td style="text-align: right; width: 25%;">
                        DATE: _________
                    </td>
                </tr>

            </table>

        </div>

    </section>
    {{-- END OF TEACHER ENDORSEMENT --}}

</div>
