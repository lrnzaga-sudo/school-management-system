<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>View User</title>
</head>

<body>

    <h1>User Details</h1>

    <button onclick="window.location.href='/admin/users'">
        Back to Users
    </button>

    <hr>


    <p>
        <strong>ID:</strong>

        <span id="userId"></span>
    </p>


    <p>
        <strong>Name:</strong>

        <span id="userName"></span>
    </p>


    <p>
        <strong>Email:</strong>

        <span id="userEmail"></span>
    </p>


    <p>
        <strong>Role:</strong>

        <span id="userRole"></span>
    </p>


    <a href="/admin/users/{{ $id }}/edit">

        <button>
            Edit User
        </button>

    </a>


<script>

const token = localStorage.getItem('admin_token');

const userId = "{{ $id }}";


if (!token) {

    window.location.href = '/admin/login';

}


// Get user

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


    document.getElementById('userId').textContent =
        user.id;

    document.getElementById('userName').textContent =
        user.name;

    document.getElementById('userEmail').textContent =
        user.email;

    document.getElementById('userRole').textContent =
        user.role;

}


loadUser();

</script>

</body>

</html>