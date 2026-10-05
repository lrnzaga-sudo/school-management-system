<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subject Details</title>
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

        #subjectDetails {
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
<main id="subjectPage" data-subject-id="{{ $id }}">
    <header>
        <h1>Subject Details</h1>
        <nav aria-label="Subject actions">
            <a class="button" href="/admin/subjects">Back to Subjects</a>
            <a class="button" href="/admin/subjects/{{ $id }}/edit">Edit Subject</a>
        </nav>
    </header>

    <p id="message" role="status">Loading subject information...</p>
    <section id="subjectDetails" aria-label="Subject information" hidden></section>
</main>

<script>
    const token = localStorage.getItem('admin_token');
    const subjectId = document.getElementById('subjectPage').dataset.subjectId;
    const message = document.getElementById('message');
    const details = document.getElementById('subjectDetails');

    if (!token) {
        window.location.href = '/admin/login';
    } else {
        loadSubject();
    }

    async function loadSubject() {
        try {
            const response = await fetch(`/api/admin/subjects/${encodeURIComponent(subjectId)}`, {
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
                throw new Error(result.message || 'Unable to load subject information.');
            }

            const subject = result.subject;
            const fields = [
                ['Subject code', subject.subject_code],
                ['Subject name', subject.subject_name],
                ['Description', subject.description]
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
            message.textContent = error.message || 'Unable to load subject information.';
        }
    }
</script>
</body>
</html>
