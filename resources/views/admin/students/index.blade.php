<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
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
        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
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

        #studentSearch {
            width: min(100%, 22rem);
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
            display: flex;
            flex-wrap: wrap;
            gap: .4rem;
            min-width: 280px;
        }

        @media (max-width: 600px) {
            body {
                padding: 1rem;
            }

            .page-header,
            .toolbar {
                align-items: stretch;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
<main>
    <header class="page-header">
        <h1>Manage Students</h1>
        <div class="actions">
            <button type="button" onclick="window.location.href='/admin/students/create'">Add Student</button>
            <button type="button" onclick="window.location.href='/admin/dashboard'">Back to Dashboard</button>
        </div>
    </header>

    <section aria-label="Student list">
        <div class="toolbar">
            <h2>Students</h2>
            <input id="studentSearch" type="search" placeholder="Search students..." aria-label="Search students">
        </div>

        <p id="tableMessage" role="status">Loading students...</p>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Student #</th>
                        <th scope="col">Name</th>
                        <th scope="col">Birth date</th>
                        <th scope="col">Gender</th>
                        <th scope="col">Phone</th>
                        <th scope="col">Address</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody id="studentsTable"></tbody>
            </table>
        </div>
    </section>
</main>

<script>
    const token = localStorage.getItem('admin_token');
    const tableBody = document.getElementById('studentsTable');
    const searchInput = document.getElementById('studentSearch');
    const tableMessage = document.getElementById('tableMessage');
    let students = [];

    if (!token) {
        window.location.href = '/admin/login';
    }

    function studentName(student) {
        return [student.first_name, student.middle_name, student.last_name]
            .filter(Boolean)
            .join(' ');
    }

    function addCell(row, value) {
        const cell = document.createElement('td');
        cell.textContent = value || '—';
        row.appendChild(cell);
    }

    function addActionButton(container, label, onClick = null) {
        const button = document.createElement('button');
        button.type = 'button';
        button.textContent = label;
        if (onClick) {
            button.addEventListener('click', onClick);
        }
        container.appendChild(button);
    }

    function renderStudents() {
        const query = searchInput.value.trim().toLowerCase();
        const matchingStudents = students.filter(student => {
            const searchableText = [
                student.student_number,
                studentName(student),
                student.birth_date,
                student.gender,
                student.phone,
                student.address
            ].filter(Boolean).join(' ').toLowerCase();

            return searchableText.includes(query);
        });

        tableBody.replaceChildren();
        tableMessage.hidden = matchingStudents.length > 0;
        tableMessage.textContent = students.length === 0
            ? 'No students available.'
            : 'No students match your search.';

        matchingStudents.forEach(student => {
            const row = document.createElement('tr');
            addCell(row, student.student_number);
            addCell(row, studentName(student));
            addCell(row, student.birth_date);
            addCell(row, student.gender);
            addCell(row, student.phone);
            addCell(row, student.address);

            const actions = document.createElement('td');
            actions.className = 'actions';
            addActionButton(actions, 'View', () => {
                window.location.href = `/admin/students/${encodeURIComponent(student.id)}`;
            });
            addActionButton(actions, 'Edit', () => {
                window.location.href = `/admin/students/${encodeURIComponent(student.id)}/edit`;
            });
            addActionButton(actions, 'Delete', () => deleteStudent(student));
            row.appendChild(actions);
            tableBody.appendChild(row);
        });
    }

    async function loadStudents() {
        try {
            const response = await fetch('/api/admin/students', {
                headers: {
                    Accept: 'application/json',
                    Authorization: `Bearer ${token}`
                }
            });

            if (response.status === 401) {
                localStorage.removeItem('admin_token');
                window.location.href = '/admin/login';
                return;
            }

            if (!response.ok) {
                throw new Error('Unable to load students.');
            }

            const result = await response.json();
            students = result.students || [];
            renderStudents();
        } catch (error) {
            tableMessage.hidden = false;
            tableMessage.textContent = error.message;
        }
    }

    async function deleteStudent(student) {
        const fullName = studentName(student);
        if (!window.confirm(`Delete ${fullName}? This action cannot be undone.`)) {
            return;
        }

        try {
            const response = await fetch(`/api/admin/students/${encodeURIComponent(student.id)}`, {
                method: 'DELETE',
                headers: {
                    Accept: 'application/json',
                    Authorization: `Bearer ${token}`
                }
            });

            if (response.status === 401) {
                localStorage.removeItem('admin_token');
                window.location.href = '/admin/login';
                return;
            }

            const result = await response.json().catch(() => ({}));
            if (!response.ok) {
                throw new Error(result.message || 'Unable to delete student.');
            }

            students = students.filter(item => item.id !== student.id);
            renderStudents();
            tableMessage.textContent = result.message || 'Student deleted successfully.';
            tableMessage.hidden = false;
        } catch (error) {
            window.alert(error.message);
        }
    }

    searchInput.addEventListener('input', renderStudents);
    loadStudents();
</script>
</body>
</html>