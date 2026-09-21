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

    <hr>

    <!-- ADD USER -->
    <h2>Add User</h2>

    <form id="addUserForm">

        <input
            type="text"
            id="name"
            placeholder="Name"
            required
        >

        <input
            type="email"
            id="email"
            placeholder="Email"
            required
        >

        <input
            type="password"
            id="password"
            placeholder="Password"
            required
        >

        <select id="role" required>
            <option value="">Select Role</option>
            <option value="admin">Admin</option>
            <option value="teacher">Teacher</option>
            <option value="student">Student</option>
        </select>

        <button type="submit">
            Add User
        </button>

    </form>

    <p id="message"></p>

    <hr>

    <!-- USERS TABLE -->
    <h2>Users</h2>

    <table border="1">

        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody id="usersTable">
        </tbody>

    </table>


    <!-- EDIT USER -->
    <div id="editSection" style="display: none;">

        <hr>

        <h2>Edit User</h2>

        <form id="editUserForm">

            <input type="hidden" id="editId">

            <input
                type="text"
                id="editName"
                placeholder="Name"
                required
            >

            <input
                type="email"
                id="editEmail"
                placeholder="Email"
                required
            >

            <select id="editRole" required>
                <option value="admin">Admin</option>
                <option value="teacher">Teacher</option>
                <option value="student">Student</option>
            </select>

            <button type="submit">
                Update User
            </button>

            <button type="button" onclick="cancelEdit()">
                Cancel
            </button>

        </form>

    </div>


<script>

const token = localStorage.getItem('admin_token');


// ========================================
// CHECK LOGIN
// ========================================

if (!token) {

    window.location.href = '/admin/login';

}


// ========================================
// LOAD USERS
// index()
// ========================================

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

                <button onclick="viewUser(${user.id})">
                    View
                </button>

                <button onclick="editUser(${user.id})">
                    Edit
                </button>

                <button onclick="deleteUser(${user.id})">
                    Delete
                </button>

            </td>

        `;

        table.appendChild(row);

    });

}


// ========================================
// ADD USER
// store()
// ========================================

document
    .getElementById('addUserForm')
    .addEventListener('submit', async function(event) {

        event.preventDefault();


        const data = {

            name: document.getElementById('name').value,

            email: document.getElementById('email').value,

            password: document.getElementById('password').value,

            role: document.getElementById('role').value

        };


        const response = await fetch('/api/admin/users', {

            method: 'POST',

            headers: {

                'Content-Type': 'application/json',

                'Accept': 'application/json',

                'Authorization': `Bearer ${token}`

            },

            body: JSON.stringify(data)

        });


        const result = await response.json();


        document.getElementById('message').textContent =
            result.message;


        if (response.ok) {

            document.getElementById('addUserForm').reset();

            loadUsers();

        }

    });


// ========================================
// VIEW USER
// show()
// ========================================

async function viewUser(id) {

    const response = await fetch(
        `/api/admin/users/${id}`,
        {

            method: 'GET',

            headers: {

                'Accept': 'application/json',

                'Authorization': `Bearer ${token}`

            }

        }
    );


    const result = await response.json();


    if (!response.ok) {

        alert(result.message);

        return;

    }


    const user = result.user;


    alert(
        `ID: ${user.id}\n` +
        `Name: ${user.name}\n` +
        `Email: ${user.email}\n` +
        `Role: ${user.role}`
    );

}


// ========================================
// EDIT USER
// show() + update()
// ========================================

async function editUser(id) {

    const response = await fetch(
        `/api/admin/users/${id}`,
        {

            method: 'GET',

            headers: {

                'Accept': 'application/json',

                'Authorization': `Bearer ${token}`

            }

        }
    );


    const result = await response.json();


    if (!response.ok) {

        alert(result.message);

        return;

    }


    const user = result.user;


    document.getElementById('editId').value = user.id;

    document.getElementById('editName').value = user.name;

    document.getElementById('editEmail').value = user.email;

    document.getElementById('editRole').value = user.role;


    document.getElementById('editSection').style.display = 'block';

}


// ========================================
// UPDATE USER
// update()
// ========================================

document
    .getElementById('editUserForm')
    .addEventListener('submit', async function(event) {

        event.preventDefault();


        const id = document.getElementById('editId').value;


        const data = {

            name: document.getElementById('editName').value,

            email: document.getElementById('editEmail').value,

            role: document.getElementById('editRole').value

        };


        const response = await fetch(
            `/api/admin/users/${id}`,
            {

                method: 'PUT',

                headers: {

                    'Content-Type': 'application/json',

                    'Accept': 'application/json',

                    'Authorization': `Bearer ${token}`

                },

                body: JSON.stringify(data)

            }
        );


        const result = await response.json();


        alert(result.message);


        if (response.ok) {

            cancelEdit();

            loadUsers();

        }

    });


// ========================================
// CANCEL EDIT
// ========================================

function cancelEdit() {

    document.getElementById('editSection').style.display = 'none';

    document.getElementById('editUserForm').reset();

}


// ========================================
// DELETE USER
// destroy()
// ========================================

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


// ========================================
// INITIAL LOAD
// ========================================

loadUsers();

</script>

</body>
</html>