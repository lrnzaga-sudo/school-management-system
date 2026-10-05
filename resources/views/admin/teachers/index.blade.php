<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers</title>
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

        #teacherSearch {
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
        <h1>Manage Teachers</h1>
        <div class="header-actions">
            <button type="button" onclick="window.location.href='/admin/teachers/create'">Add Teacher</button>
            <button type="button" onclick="window.location.href='/admin/dashboard'">Back to Dashboard</button>
        </div>
    </header>

    <section aria-label="Teacher list">
        <div class="toolbar">
            <h2>Teachers</h2>
            <form id="teacherSearchForm" class="search-form" role="search">
                <input id="teacherSearch" type="search" placeholder="Search teachers..." aria-label="Search teachers">
                <button type="submit">Search</button>
            </form>
        </div>

        <p id="tableMessage" role="status">Loading teachers...</p>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Employee #</th>
                        <th scope="col">Name</th>
                        <th scope="col">Gender</th>
                        <th scope="col">Phone</th>
                        <th scope="col">Address</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody id="teachersTable"></tbody>
            </table>
        </div>
    </section>
</main>

<script>
    const token = localStorage.getItem('admin_token');
    const tableBody = document.getElementById('teachersTable');
    const searchInput = document.getElementById('teacherSearch');
    const tableMessage = document.getElementById('tableMessage');
    let teachers = [];

    if (!token) {
        window.location.href = '/admin/login';
    }

    function teacherName(teacher) {
        return [teacher.first_name, teacher.middle_name, teacher.last_name]
            .filter(Boolean)
            .join(' ');
    }

    function addCell(row, value) {
        const cell = document.createElement('td');
        cell.textContent = value || '—';
        row.appendChild(cell);
    }

    function renderTeachers() {
        const query = searchInput.value.trim().toLowerCase();
        const matchingTeachers = teachers.filter(teacher => [
            teacher.employee_number,
            teacherName(teacher),
            teacher.gender,
            teacher.phone,
            teacher.address
        ].filter(Boolean).join(' ').toLowerCase().includes(query));

        tableBody.replaceChildren();
        tableMessage.hidden = matchingTeachers.length > 0;
        tableMessage.textContent = teachers.length === 0
            ? 'No teachers available.'
            : 'No teachers match your search.';

        matchingTeachers.forEach(teacher => {
            const row = document.createElement('tr');
            addCell(row, teacher.employee_number);
            addCell(row, teacherName(teacher));
            addCell(row, teacher.gender);
            addCell(row, teacher.phone);
            addCell(row, teacher.address);

            const actions = document.createElement('td');
            actions.className = 'actions';
            ['View', 'Edit', 'Delete'].forEach(label => {
                if (label === 'View' || label === 'Edit') {
                    const link = document.createElement('a');
                    const teacherId = encodeURIComponent(teacher.id);
                    link.href = label === 'View'
                        ? `/admin/teachers/${teacherId}`
                        : `/admin/teachers/${teacherId}/edit`;
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
                    button.addEventListener('click', () => deleteTeacher(teacher));
                }
                actions.appendChild(button);
            });
            row.appendChild(actions);
            tableBody.appendChild(row);
        });
    }

    async function loadTeachers() {
        try {
            const response = await fetch('/api/admin/teachers', {
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
                throw new Error('Unable to load teachers.');
            }

            const result = await response.json();
            teachers = result.teachers;
            renderTeachers();
        } catch (error) {
            tableMessage.hidden = false;
            tableMessage.textContent = error.message;
        }
    }

    async function deleteTeacher(teacher) {
        const name = teacherName(teacher);
        if (!window.confirm(`Delete ${name}? This action cannot be undone.`)) {
            return;
        }

        try {
            const response = await fetch(`/api/admin/teachers/${encodeURIComponent(teacher.id)}`, {
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
                throw new Error(result.message || 'Unable to delete teacher.');
            }

            teachers = teachers.filter(item => item.id !== teacher.id);
            renderTeachers();
            tableMessage.textContent = result.message || 'Teacher deleted successfully.';
            tableMessage.hidden = false;
        } catch (error) {
            window.alert(error.message);
        }
    }

    document.getElementById('teacherSearchForm')
        .addEventListener('submit', event => {
            event.preventDefault();
            renderTeachers();
        });
    loadTeachers();
</script>
</body>
</html>
