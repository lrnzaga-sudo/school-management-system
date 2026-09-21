<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit User</title>
</head>

<body>

    <h1>Edit User</h1>

    <button onclick="window.location.href='/admin/users'">
        Back to Users
    </button>

    <hr>


    <form id="editUserForm">

        <div>

            <label>Name</label>

            <input
                type="text"
                id="name"
                required
            >

        </div>


        <br>


        <div>

            <label>Email</label>

            <input
                type="email"
                id="email"
                required
            >

        </div>


        <br>


        <div>

            <label>Role</label>

            <select id="role" required>

                <option value="admin">
                    Admin
                </option>

                <option value="teacher">
                    Teacher
                </option>

                <option value="student">
                    Student
                </option>

            </select>

        </div>


        <br>


        <button type="submit">
            Save Changes
        </button>

    </form>


    <p id="message"></p>


<script>

const token = localStorage.getItem('admin_token');

const userId = "{{ $id }}";


if (!token) {

    window.location.href = '/admin/login';

}


// Load existing user information

async function loadUser() {

    const response = await fetch(
        `/api/admin/users/${userId}`,
        {

            method: 'GET',

            headers: {

                'Accept': 'application/json',

                'Authorization': `Bearer ${token}`

            }

        }
    );


    if (response.status === 401) {

        localStorage.removeItem('admin_token');

        window.location.href = '/admin/login';

        return;

    }


    const result = await response.json();


    if (!response.ok) {

        alert(result.message);

        window.location.href = '/admin/users';

        return;

    }


    const user = result.user;


    document.getElementById('name').value =
        user.name;

    document.getElementById('email').value =
        user.email;

    document.getElementById('role').value =
        user.role;

}


// Update user

document
    .getElementById('editUserForm')
    .addEventListener('submit', async function(event) {

        event.preventDefault();


        const data = {

            name: document.getElementById('name').value,

            email: document.getElementById('email').value,

            role: document.getElementById('role').value

        };


        const response = await fetch(
            `/api/admin/users/${userId}`,
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


        document.getElementById('message').textContent =
            result.message;


        if (response.ok) {

            setTimeout(function() {

                window.location.href =
                    `/admin/users/${userId}`;

            }, 1000);

        }

    });


loadUser();

</script>

</body>

</html>