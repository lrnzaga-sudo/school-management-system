<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subjects</title>
    <style>
        body {
            margin: 0;
            padding: 2rem;
            color: #17212b;
            font: 16px/1.5 Arial, sans-serif;
            background: #f4f7f6;
        }

        main {
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-header,
        .toolbar,
        .header-actions,
        .search-form,
        .actions {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .page-header,
        .toolbar {
            justify-content: space-between;
        }

        .page-header {
            margin-bottom: 1.5rem;
        }

        h1,
        h2 {
            margin: 0;
        }

        button,
        input {
            font: inherit;
        }

        button {
            padding: .5rem .75rem;
            border: 1px solid #aab8b5;
            border-radius: 4px;
            color: #17212b;
            background: #fff;
            cursor: pointer;
        }

        button:hover {
            background: #eaf1ef;
        }

        .search-form {
            width: min(100%, 28rem);
        }

        #subjectSearch {
            flex: 1;
            min-width: 0;
            padding: .6rem .75rem;
            border: 1px solid #aab8b5;
            border-radius: 4px;
        }

        #tableMessage {
            padding: 1rem;
            color: #56645f;
        }

        .table-wrap {
            overflow-x: auto;
            border: 1px solid #d4ddda;
            background: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th,
        td {
            padding: .75rem;
            border-bottom: 1px solid #e1e7e5;
            vertical-align: top;
        }

        th {
            background: #eaf1ef;
            font-size: .875rem;
        }

        .actions {
            flex-wrap: wrap;
            min-width: 220px;
        }

        @media (max-width: 600px) {
            body {
                padding: 1rem;
            }

            .page-header,
            .toolbar,
            .header-actions {
                align-items: stretch;
                flex-direction: column;
            }

            .search-form {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<main>
    <header class="page-header">
        <h1>Manage Subjects</h1>
        <div class="header-actions">
            <button type="button" onclick="window.location.href='/admin/subjects/create'">Add Subject</button>
            <button type="button" onclick="window.location.href='/admin/dashboard'">Back to Dashboard</button>
        </div>
    </header>

    <section aria-label="Subject list">
        <div class="toolbar">
            <h2>Subjects</h2>
            <form id="subjectSearchForm" class="search-form" role="search">
                <input id="subjectSearch" type="search" placeholder="Search subjects..." aria-label="Search subjects">
                <button type="submit">Search</button>
            </form>
        </div>

        <p id="tableMessage" role="status">Loading subjects...</p>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Subject Code</th>
                        <th scope="col">Subject Name</th>
                        <th scope="col">Description</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody id="subjectsTable"></tbody>
            </table>
        </div>
    </section>
</main>

<script>
    const token = localStorage.getItem('admin_token');
    const tableBody = document.getElementById('subjectsTable');
    const searchInput = document.getElementById('subjectSearch');
    const tableMessage = document.getElementById('tableMessage');
    let subjects = [];

    if (!token) {
        window.location.href = '/admin/login';
    }

    function addCell(row, value) {
        const cell = document.createElement('td');
        cell.textContent = value || '—';
        row.appendChild(cell);
    }

    function renderSubjects() {
        const query = searchInput.value.trim().toLowerCase();
        const matchingSubjects = subjects.filter(subject => [
            subject.subject_code,
            subject.subject_name,
            subject.description
        ].filter(Boolean).join(' ').toLowerCase().includes(query));

        tableBody.replaceChildren();
        tableMessage.hidden = matchingSubjects.length > 0;
        tableMessage.textContent = subjects.length === 0
            ? 'No subjects available.'
            : 'No subjects match your search.';

        matchingSubjects.forEach(subject => {
            const row = document.createElement('tr');
            addCell(row, subject.subject_code);
            addCell(row, subject.subject_name);
            addCell(row, subject.description);

            const actions = document.createElement('td');
            actions.className = 'actions';

            ['View', 'Edit', 'Delete'].forEach(label => {
                if (label === 'View') {
                    const link = document.createElement('a');
                    link.href = `/admin/subjects/${encodeURIComponent(subject.id)}`;
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.textContent = label;
                    link.appendChild(button);
                    actions.appendChild(link);
                    return;
                }

                if (label === 'Edit') {
                    const link = document.createElement('a');
                    link.href = `/admin/subjects/${encodeURIComponent(subject.id)}/edit`;
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.textContent = label;
                    link.appendChild(button);
                    actions.appendChild(link);
                    return;
                }

                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = label;
                if (label === 'Delete') {
                    button.addEventListener('click', () => deleteSubject(subject));
                }
                actions.appendChild(button);
            });

            row.appendChild(actions);
            tableBody.appendChild(row);
        });
    }

    async function loadSubjects() {
        try {
            const response = await fetch('/api/admin/subjects', {
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

            if (!response.ok) {
                throw new Error('Unable to load subjects.');
            }

            const result = await response.json();
            subjects = Array.isArray(result.subjects) ? result.subjects : [];
            renderSubjects();
        } catch (error) {
            console.error(error);
            tableMessage.textContent = error.message || 'Unable to load subjects.';
            tableMessage.hidden = false;
        }
    }

    async function deleteSubject(subject) {
        const name = subject.subject_name || subject.subject_code || 'this subject';
        if (!window.confirm(`Delete ${name}? This action cannot be undone.`)) {
            return;
        }

        try {
            const response = await fetch(`/api/admin/subjects/${encodeURIComponent(subject.id)}`, {
                method: 'DELETE',
                headers: {
                    Accept: 'application/json',
                    Authorization: 'Bearer ' + token
                }
            });

            const result = await response.json().catch(() => ({}));
            if (response.status === 401) {
                localStorage.removeItem('admin_token');
                window.location.href = '/admin/login';
                return;
            }

            if (!response.ok) {
                throw new Error(result.message || 'Unable to delete subject.');
            }

            subjects = subjects.filter(item => item.id !== subject.id);
            renderSubjects();
            tableMessage.textContent = result.message || 'Subject deleted successfully.';
            tableMessage.hidden = false;
        } catch (error) {
            window.alert(error.message || 'Unable to delete subject.');
        }
    }

    document.getElementById('subjectSearchForm').addEventListener('submit', function (event) {
        event.preventDefault();
        renderSubjects();
    });

    searchInput.addEventListener('input', renderSubjects);

    loadSubjects();
</script>
</body>
</html>
