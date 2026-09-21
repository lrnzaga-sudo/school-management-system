<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add User</title>
</head>

<body>

    <h1>Add User</h1>

    <button onclick="window.location.href='/admin/users'">
        Back to Users
    </button>

    <hr>


    <form id="createUserForm">

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

            <label>Password</label>

            <input
                type="password"
                id="password"
                required
            >

        </div>


        <br>


        <div>

            <label>Role</label>

            <select id="role" required>

                <option value="">
                    Select Role
                </option>

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
            Create User
        </button>

    </form>


    <p id="message"></p>


<script>

const token = localStorage.getItem('admin_token');


if (!token) {

    window.location.href = '/admin/login';

}


// Create user

document
    .getElementById('createUserForm')
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

            document.getElementById('createUserForm').reset();

        }

    });

</script>

</body>

</html>