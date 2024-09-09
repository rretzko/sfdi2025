<div>
    <div>
        *** NEW STUDENT NOTIFICATION ***
    </div>

    <div>
        Hi, {{ $teacherFirstName }} -
        A new student, {{ auth()->user()->name }}, has just added {{ auth()->user()->pronoun->intensive }}
        to your Students roster.
    </div>

    <div>
        Thank you for using TheDirectorsRoom.com!
    </div>
</div>
