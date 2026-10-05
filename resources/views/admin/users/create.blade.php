<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <style>
        body {
            margin: 0;
            padding: 2rem;
            color: #17212b;
            font: 16px/1.5 Arial, sans-serif;
            background: #f4f7f6;
        }

        main {
            max-width: 760px;
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

        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            padding: 1.25rem;
            border: 1px solid #d4ddda;
            background: #fff;
        }

        label {
            display: grid;
            gap: .35rem;
            font-weight: 600;
        }

        input,
        select,
        button {
            font: inherit;
        }

        input,
        select {
            box-sizing: border-box;
            width: 100%;
            padding: .6rem .7rem;
            border: 1px solid #aab8b5;
            border-radius: 4px;
        }

        button,
        .button {
            width: fit-content;
            padding: .55rem .85rem;
            border: 1px solid #aab8b5;
            border-radius: 4px;
            color: #17212b;
            background: #fff;
            text-decoration: none;
            cursor: pointer;
        }

        button:hover,
        .button:hover {
            background: #eaf1ef;
        }

        .full-width,
        #message {
            grid-column: 1 / -1;
        }

        #message {
            margin: 0;
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

            form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
<main>
    <header>
        <h1>Add User</h1>
        <a class="button" href="/admin/users">Back to Users</a>
    </header>

    <form id="createUserForm">
        <label>
            Name
            <input type="text" id="name" required>
        </label>
        <label>
            Email
            <input type="email" id="email" required>
        </label>
        <label>
            Password
            <input type="password" id="password" required>
        </label>
        <label>
            Role
            <select id="role" required>
                <option value="">Select Role</option>
                <option value="admin">Admin</option>
                <option value="teacher">Teacher</option>
                <option value="student">Student</option>
            </select>
        </label>
        <p id="message" role="status"></p>
        <button type="submit">Create User</button>
    </form>
</main>


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