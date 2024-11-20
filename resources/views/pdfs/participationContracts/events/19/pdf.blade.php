<div>
    <style>
        .pageBreak {
            page-break-after: always;
        }

        .bodyFontSize {
            font-size: 0.8rem;
        }
    </style>

    <header style="text-align: center; font-weight: bold; ">

        <div style="font-size: 2rem; margin-bottom: 0.5rem;">
            {{ $dto['versionName'] }}
        </div>

        <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">
            Student Contract
        </div>

        <div style="text-align: center; font-size: 1rem; font-weight: bold; margin-bottom: 0.5rem;">
            To be completed by STUDENT, PARENT/GUARDIAN, DIRECTOR, and PRINCIPAL.
        </div>

        <div style="text-align: center; font-size: 0.8rem; font-weight: bold; margin-bottom: 0.5rem;">
            Page 1/2
        </div>

    </header>

    {{-- Highlighted Instructions --}}
    <div style="background-color: yellow; padding: 0.25rem; border: 1px solid black; margin-bottom: 0.5rem;">
        This form must be completed, including all signatures. The student must turn it in at the first rehearsal
        along with the ${{ $dto['participationFee'] }} participation fee.
    </div>

    {{-- Student Bio --}}
    <div style="padding: 0.25rem; border: 1px solid black; margin-bottom: 1rem;">

        <div>

            <table style="border-collapse: collapse;">
                <tbody>
                <tr>
                    <td style="text-align: right;">
                        Student Name:
                    </td>
                    <td style="font-weight: bold; padding-left: 0.5rem;">
                        {{ $dto['studentName'] }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right;">
                        Voice Part:
                    </td>
                    <td style="font-weight: bold; padding-left: 0.5rem;">
                        {{ $dto['candidateVoicePartDescr'] }}
                    </td>
                </tr>
                </tbody>
            </table>

        </div>

    </div>

    {{-- GENERAL EXPECTATIONS AND STUDENT CODE OF CONDUCT --}}
    <div class="bodyFontSize" style="margin: 0 2%;">

        <div style="text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 0.5rem;">
            GENERAL EXPECTATIONS AND STUDENT CODE OF CONDUCT
        </div>

        <header>
            The following rules have been established for all participants in the {{ $dto['versionName'] }}:
        </header>

        <ol>
            <li>
                The student must bring their folder and a pencil to every rehearsal.
            </li>
            <li>
                All students must arrive on time and attend the dress rehearsal and concert in their entirety.
            </li>
            <li>
                Students are allotted 2 unexcused absences, and a third for a college audition.
                Proof of this audition must be provided prior to the absence.
                Two lates will be considered one absence.
                Leaving a rehearsal early will count as one late.
                If a student exceeds the allowed absences, they will be immediately dismissed.
            </li>
            <li>
                Any accidents, injuries, and/or illnesses must be reported to the staff on duty immediately.
            </li>
            <li>
                Students will cooperate fully with the rules and regulations of All Shore and the hosting school.
            </li>
            <li>
                The use of cell phones, tablets, laptops, and any other electronic device during rehearsals and
                performances is prohibited unless authorized by the manager or conductor.
            </li>
            <li>
                Pranks or vandalism of any kind will not be tolerated. Parents and/or the participant will assume full
                financial responsibility for damages caused by the participant.
            </li>
            <li>
                Use, possession, and/or acquisition of alcoholic beverages, tobacco products, or legal/illegal drugs
                is prohibited at any time during participation in the {{ $dto['versionName'] }}.
                Any violation of this rule will result in immediate dismissal.
            </li>
            <li>
                Students should notify All Shore immediately if it is necessary to withdraw from
                their {{ $dto['versionName'] }}.
            </li>
        </ol>

    </div>

    {{-- COVID UNDERSTANDING --}}
    <div class="bodyFontSize" style="margin: 0 2%; margin-bottom: 0.5rem;">

        <div style="text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 0.5rem;">
            COVID UNDERSTANDING
        </div>

        <div>
            There are risks of exposure to Covid-19, including traveling through high-risk areas and failures of
            others to follow proper protocols.
            All Shore has taken reasonable steps to address these risks based on NJ Department of Health and CDC
            guidelines.
            Agreement to this contract acknowledges these risks and releases All Shore, its staff, and its
            volunteers from any liability from exposure or harm that may occur while participating in All Shore
            events.
        </div>
    </div>

    {{-- STUDENT CODE OF CONDUCT --}}
    <div class="bodyFontSize" style="margin: 0 2%;">

        <div style="text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 0.5rem;">
            CODE OF CONDUCT
        </div>

        <ol>
            <li>
                Students shall not discriminate and shall be respectful of ethnic, national, religious, and cultural
                differences.
            </li>
            <li>
                Students shall respect the individual rights and safety of others.
                While participating in the All Shore Chorus, respectful discourse and appropriate behavior shall be
                required of students.
                Students shall avoid the use of profanity, exhibit good sportsmanship, and refrain from all gestures
                and comments that a reasonable person may judge as unwelcome.
            </li>
            <li>
                Students shall contribute to collegial, inclusive, professional, positive, and respectful environment
                for fellow participants, conductors, and staff, and shall model the best in participant behavior.
            </li>
            <li>
                Students shall communicate with others in a clear respectful manner and in appropriate settings.
            </li>
            <li>
                Inappropriate communications by email, online ensemble communication platforms, or in any public forum
                about All Shore Chorus, other student participants, conductors, staff, or stakeholders, is not
                acceptable.
                Issues regarding All Shore Chorus shall be taken up in private with the appropriate All Shore executive
                board member.
            </li>
            <li>
                Students shall treat all other participants, conductors, and staff fairly and not discriminate on the
                basis of religion, actual and/or perceived gender, gender expression, gender identity, civil status,
                family status, sexual orientation, age, medial condition or disability, race, ethnicity, socioeconomic
                status, or culture.
            </li>
        </ol>

    </div>

    {{-- PAGE BREAK --}}
    <div class="pageBreak"></div>

    {{-- PAGE 2 HEADER --}}
    <header style="text-align: center; font-weight: bold;  margin-bottom: 0.5rem;">

        <div style="font-size: 2rem; margin-bottom: 0.5rem;">
            {{ $dto['versionName'] }}
        </div>

        <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">
            Student Contract
        </div>

        <div style="text-align: center; font-size: 1rem; font-weight: bold; margin-bottom: 0.5rem;">
            To be completed by STUDENT, PARENT/GUARDIAN, DIRECTOR, and PRINCIPAL.
        </div>

        <div style="text-align: center; font-size: 0.8rem; font-weight: bold; margin-bottom: 0.5rem;">
            Page 2/2
        </div>

    </header>

    {{-- Student Bio --}}
    <div style="padding: 0.25rem; border: 1px solid black; margin-bottom: 1rem;">

        <div>

            <table style="border-collapse: collapse;">
                <tbody>
                <tr>
                    <td style="text-align: right;">
                        Student Name:
                    </td>
                    <td style="font-weight: bold; padding-left: 0.5rem;">
                        {{ $dto['studentName'] }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right;">
                        Voice Part:
                    </td>
                    <td style="font-weight: bold; padding-left: 0.5rem;">
                        {{ $dto['candidateVoicePartDescr'] }}
                    </td>
                </tr>
                </tbody>
            </table>

        </div>

    </div>

    {{-- GRANT OF PERMISSION TO USE LIKENESS --}}
    <div class="bodyFontSize" style="margin: 0 2%; margin-bottom: 0.5rem;">

        <div style="text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 0.5rem;">
            Grant of Permission to Use Likeness
        </div>

        <div>
            Permission is granted to All Shore Chorus Inc. and its agents to photograph, videotape, and/or record the
            student while participating in the All Shore program and to use, display, publish, reproduce, sell, edit,
            adapt, modify, perform, transmit, and otherwise use such photographs, videotapes, and/or recordings taken
            during All Shore's programs containing student likeness, including any derivative works created therefrom
            for any purpose associated with All Shore's mission to advance music education in the state without
            compensation.
            Students agree that photographs, videotapes, and/or recordings of students taken at All Shore Chorus
            programs will be the sole property of All Shore Chorus Inc.
        </div>
    </div>

    {{-- STUDENT SIGNATURE --}}
    <div style="margin-bottom: 1rem;">

        <header style="font-size: 0.9rem; font-weight: bold; ">
            FOR STUDENTS
        </header>

        <div style="margin-top: -0.5rem;">
            <p>
                I, the undersigned, have read this offical {{ $dto['versionName'] }} Student Contract, agree to its
                contents,
                and agree to support its enforcement.
                If a violation of the General Expectations and Student Code of Conduct occurs, I understand my Parent/
                Legal Guardian and director will be notified and that I may be dismissed from the program and removed
                from the final performance recording.
            </p>
            <p>
                I understand that All Shore Chorus has the sole discretion to make all decisions, including, but not
                limited
                to decisions regarding disciplinary matters and the final approval of students selected to perform.
            </p>
        </div>

        <table style="border-collapse: collapse; width: 100%; text-align: center; margin-top: 3rem;">
            <tr>
                <td>
                    <div style="border-bottom: 1px solid black; margin: 0 2rem;"></div>
                </td>
                <td>
                    <div style="border-bottom: 1px solid black; margin: 0 2rem;"></div>
                </td>
                <td>
                    <div style="border-bottom: 1px solid black; margin: 0 2rem;"></div>
                </td>
            </tr>
            <tr>
                <td>
                    {{ $dto['studentName']  }} (printed)
                </td>
                <td>
                    {{ $dto['studentName']  }} Signature
                </td>
                <td>
                    Date
                </td>
            </tr>
        </table>

    </div>

    {{-- PARENTS/LEGAL GUARDIANS, DIRECTOR, AND PRINCIPAL SIGNATURE --}}
    <div>

        <header style="font-size: 0.9rem; font-weight: bold; ">
            FOR PARENTS/LEGAL GUARDIANS, DIRECTOR and PRINCIPAL
        </header>

        <div style="margin-top: -0.5rem;">
            <p>
                We, the undersigned, have read this official {{ $dto['versionName'] }} Student Contract, agree to its
                contents and agree to support its enforcement.
                We permit the student to attend all rehearsals in their entirety during the event.
                If a violation of the General Expectations and Student Cod of Conduct occurs, we understand that the
                student, director, and parent will be notified, and that the student may be dismissed from the program
                and removed from the final performance recording.
            </p>
            <p>
                We understand that All Shore Chorus has sole discretion to make all decisions, including, but not
                limited
                to decisions regarding disciplinary matters and the final approval of students selected to perform.
            </p>
        </div>

        <table style="border-collapse: collapse; width: 100%; text-align: center; margin-top: 3rem;">
            {{-- printed row --}}
            <tr>
                <td>
                    <div style="border-bottom: 1px solid black; margin: 0 2rem;"></div>
                </td>
                <td>
                    <div style="border-bottom: 1px solid black; margin: 0 2rem;"></div>
                </td>
                <td>
                    <div style="border-bottom: 1px solid black; margin: 0 2rem;"></div>
                </td>
            </tr>
            <tr>
                <td>
                    {{ $dto['emergencyContactName']  }} (printed)
                </td>
                <td>
                    {{ $dto['teacherFullName']  }} (printed)
                </td>
                <td>
                    Principal's Name (printed)
                </td>
            </tr>

            {{-- signature row --}}
            <tr>
                <td>
                    <div style="border-bottom: 1px solid black; margin: 0 2rem;">
                        <br/>
                        <br/>
                    </div>
                </td>
                <td>
                    <div style="border-bottom: 1px solid black; margin: 0 2rem;">
                        <br/>
                        <br/>
                    </div>
                </td>
                <td>
                    <div style="border-bottom: 1px solid black; margin: 0 2rem;">
                        <br/>
                        <br/>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    {{ $dto['emergencyContactName']  }} Signature
                </td>
                <td>
                    {{ $dto['teacherFullName']  }} Signature
                </td>
                <td>
                    Principal's Name Signature
                </td>
            </tr>

            {{-- date row --}}
            {{-- signature row --}}
            <tr>
                <td>
                    <div style="border-bottom: 1px solid black; margin: 0 2rem;">
                        <br/>
                        <br/>
                    </div>
                </td>
                <td>
                    <div style="border-bottom: 1px solid black; margin: 0 2rem;">
                        <br/>
                        <br/>
                    </div>
                </td>
                <td>
                    <div style="border-bottom: 1px solid black; margin: 0 2rem;">
                        <br/>
                        <br/>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    Date
                </td>
                <td>
                    Date
                </td>
                <td>
                    Date
                </td>
            </tr>
        </table>

    </div>

</div>
