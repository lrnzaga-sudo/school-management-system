<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Users</title>
</head>

<body>

    <h1>Manage User Accounts</h1>

    <button onclick="window.location.href='/admin/dashboard'">
        Back to Dashboard
    </button>

    <a href="/admin/users/create">
        <button>
            Add User
        </button>
    </a>

    <hr>

    <h2>Users</h2>

    <table border="1">

        <thead>

            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
                <th>Status</th>
            </tr>

        </thead>

        <tbody id="usersTable">
        </tbody>

    </table>


<script>

const token = localStorage.getItem('admin_token');


// Check if admin is logged in

if (!token) {

    window.location.href = '/admin/login';

}


// Load users

async function loadUsers() {

    const response = await fetch('/api/admin/users', {

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

    const table = document.getElementById('usersTable');

    table.innerHTML = '';


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

            <td>

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