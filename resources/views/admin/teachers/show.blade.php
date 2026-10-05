<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Details</title>
    <style>
        body {
            margin: 0;
            padding: 2rem;
            color: #17212b;
            font: 16px/1.5 Arial, sans-serif;
            background: #f4f7f6;
        }

        main {
            max-width: 800px;
            margin: 0 auto;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
        }

        h1 {
            margin: 0;
            font-size: 1.75rem;
        }

        .button {
            display: inline-block;
            padding: .5rem .75rem;
            border: 1px solid #aab8b5;
            border-radius: 4px;
            color: #17212b;
            background: #fff;
            text-decoration: none;
        }

        .button:hover {
            background: #eaf1ef;
        }

        #teacherDetails {
            padding: 1.25rem;
            border: 1px solid #d4ddda;
            background: #fff;
        }

        dl {
            display: grid;
            grid-template-columns: minmax(9rem, .4fr) 1fr;
            gap: .75rem 1rem;
            margin: 0;
        }

        dt {
            font-weight: 700;
        }

        dd {
            margin: 0;
            overflow-wrap: anywhere;
        }

        #message {
            color: #56645f;
        }

        @media (max-width: 600px) {
            body {
                padding: 1rem;
            }

            header {
                align-items: stretch;
                flex-direction: column;
            }

            dl {
                grid-template-columns: 1fr;
                gap: .25rem;
            }

            dd {
                margin-bottom: .75rem;
            }
        }
    </style>
</head>
<body>
<main id="teacherPage" data-teacher-id="{{ $id }}">
    <header>
        <h1>Teacher Details</h1>
        <nav class="actions" aria-label="Teacher actions">
            <a class="button" href="/admin/teachers">Back to Teachers</a>
            <a class="button" href="/admin/teachers/{{ $id }}/edit">Edit Teacher</a>
        </nav>
    </header>

    <p id="message" role="status">Loading teacher information...</p>
    <section id="teacherDetails" aria-label="Teacher information" hidden></section>
</main>

<script>
    const token = localStorage.getItem('admin_token');
    const teacherId = document.getElementById('teacherPage').dataset.teacherId;
    const message = document.getElementById('message');
    const details = document.getElementById('teacherDetails');

    if (!token) {
        window.location.href = '/admin/login';
    } else {
        loadTeacher();
    }

    async function loadTeacher() {
        try {
            const response = await fetch(`/api/admin/teachers/${encodeURIComponent(teacherId)}`, {
                headers: {
                    Accept: 'application/json',
                    Authorization: 'Bearer ' + token
                }
            });

            if (response.status === 401) {
                localStorage.removeItem('admin_token');
                window.location.href = '/admin/login';
                return;
            }

            const result = await response.json();
            if (!response.ok) {
                throw new Error(result.message || 'Unable to load teacher information.');
            }

            const teacher = result.teacher;
            const fields = [
                ['Employee number', teacher.employee_number],
                ['First name', teacher.first_name],
                ['Middle name', teacher.middle_name],
                ['Last name', teacher.last_name],
                ['Birth date', teacher.birth_date],
                ['Gender', teacher.gender],
                ['Phone', teacher.phone],
                ['Address', teacher.address]
            ];

            const list = document.createElement('dl');
            fields.forEach(([label, value]) => {
                const term = document.createElement('dt');
                const description = document.createElement('dd');
                term.textContent = label;
                description.textContent = value || '—';
                list.append(term, description);
            });

            details.replaceChildren(list);
            details.hidden = false;
            message.hidden = true;
        } catch (error) {
            message.textContent = error.message;
        }
    }
</script>
</body>
</html>
