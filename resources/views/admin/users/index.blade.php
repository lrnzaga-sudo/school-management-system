<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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

        #userSearch {
            width: min(100%, 22rem);
            padding: .6rem .75rem;
            border: 1px solid #aab8b5;
            border-radius: 4px;
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
        <h1>Manage User Accounts</h1>
        <div class="actions">
            <button type="button" onclick="window.location.href='/admin/users/create'">Add User</button>
            <button type="button" onclick="window.location.href='/admin/dashboard'">Back to Dashboard</button>
        </div>
    </header>

    <section aria-label="User list">
        <div class="toolbar">
            <h2>Users</h2>
            <input
                type="search"
                id="userSearch"
                placeholder="Search by ID, name, email, or role"
                aria-label="Search users by ID, name, email, or role"
                autocomplete="off"
            >
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Role</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTable"></tbody>
            </table>
        </div>
    </section>
</main>

<script>

const token = localStorage.getItem('admin_token');
const searchInput = document.getElementById('userSearch');
let searchTimeout;
let latestUsersRequest = 0;


// Check if admin is logged in

if (!token) {

    window.location.href = '/admin/login';

}


// Load users

async function loadUsers() {

    const requestId = ++latestUsersRequest;
    const query = searchInput.value.trim();
    const url = new URL('/api/admin/users', window.location.origin);

    if (query) {
        url.searchParams.set('q', query);
    }

    const response = await fetch(url, {
        method: 'GET',

        headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
        }

    });


    if (response.status === 401) {

        localStorage.removeItem('admin_token');

        window.location.href = '/admin/login';

        return;

    }


    const result = await response.json();

    if (requestId !== latestUsersRequest) {
        return;
    }

    const table = document.getElementById('usersTable');

    table.innerHTML = '';

    if (!result.users.length) {
        const row = document.createElement('tr');
        row.innerHTML = '<td colspan="6">No users found.</td>';
        table.appendChild(row);
        return;
    }

    result.users.forEach(user => {

        const row = document.createElement('tr');


        row.innerHTML = `

            <td>${user.id}</td>

            <td>${user.name}</td>

            <td>${user.email}</td>

            <td>${user.role}</td>

            <td>
                ${user.is_active ? 'Active' : 'Inactive'}
            </td>

            <td class="actions">

                <a href="/admin/users/${user.id}">
                    <button>View</button>
                </a>

                <a href="/admin/users/${user.id}/edit">
                    <button>Edit</button>
                </a>

                <a href="/admin/users/${user.id}/password">
                    <button>
                        Change Password
                    </button>
                </a>

                <button onclick="toggleStatus(${user.id})">
                    ${user.is_active ? 'Deactivate' : 'Activate'}
                </button>
                

                <button onclick="deleteUser(${user.id})">
                    Delete
                </button>

            </td>

        `;


        table.appendChild(row);

    });

}

searchInput.addEventListener('input', () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(loadUsers, 250);
});


// Delete user

async function deleteUser(id) {

    const confirmDelete = confirm(
        'Are you sure you want to delete this user?'
    );


    if (!confirmDelete) {

        return;

    }


    const response = await fetch(
        `/api/admin/users/${id}`,
        {

            method: 'DELETE',

            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            }

        }
    );


    const result = await response.json();


    alert(result.message);


    if (response.ok) {

        loadUsers();

    }

}

async function toggleStatus(id) {

    const response = await fetch(
        `/api/admin/users/${id}/status`,
        {
            method: 'PATCH',

            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        }
    );

    const result = await response.json();

    alert(result.message);

    if (response.ok) {
        loadUsers();
    }
}


loadUsers();

</script>

</body>

</html>