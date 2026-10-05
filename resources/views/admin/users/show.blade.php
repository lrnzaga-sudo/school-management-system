<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User</title>
    <style>
        body {
            margin: 0;
            padding: 2rem;
            color: #17212b;
            font: 16px/1.5 Arial, sans-serif;
            background: #f4f7f6;
        }

        main {
            max-width: 800px;
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

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .button {
            display: inline-block;
            padding: .5rem .75rem;
            border: 1px solid #aab8b5;
            border-radius: 4px;
            color: #17212b;
            background: #fff;
            text-decoration: none;
        }

        .button:hover {
            background: #eaf1ef;
        }

        #userDetails {
            padding: 1.25rem;
            border: 1px solid #d4ddda;
            background: #fff;
        }

        dl {
            display: grid;
            grid-template-columns: minmax(9rem, .4fr) 1fr;
            gap: .75rem 1rem;
            margin: 0;
        }

        dt {
            font-weight: 700;
        }

        dd {
            margin: 0;
            overflow-wrap: anywhere;
        }

        #message {
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

            dl {
                grid-template-columns: 1fr;
                gap: .25rem;
            }

            dd {
                margin-bottom: .75rem;
            }
        }
    </style>
</head>

<body>
<main>
    <header>
        <h1>User Details</h1>
        <nav class="actions" aria-label="User actions">
            <a class="button" href="/admin/users">Back to Users</a>
            <a class="button" href="/admin/users/{{ $id }}/edit">Edit User</a>
        </nav>
    </header>

    <p id="message" role="status">Loading user information...</p>
    <section id="userDetails" aria-label="User information" hidden>
        <dl>
            <dt>ID</dt>
            <dd id="userId"></dd>
            <dt>Name</dt>
            <dd id="userName"></dd>
            <dt>Email</dt>
            <dd id="userEmail"></dd>
            <dt>Role</dt>
            <dd id="userRole"></dd>
        </dl>
    </section>


<script>

const token = localStorage.getItem('admin_token');

const userId = "{{ $id }}";
const message = document.getElementById('message');
const userDetails = document.getElementById('userDetails');


if (!token) {

    window.location.href = '/admin/login';

}


// Get user

async function loadUser() {

    try {
        const response = await fetch(`/api/admin/users/${encodeURIComponent(userId)}`, {

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


    if (!response.ok) {

            throw new Error(result.message || 'Unable to load user information.');

    }


        const user = result.user;


        document.getElementById('userId').textContent = user.id;

        document.getElementById('userName').textContent = user.name;

        document.getElementById('userEmail').textContent = user.email;

        document.getElementById('userRole').textContent = user.role;
        userDetails.hidden = false;
        message.hidden = true;
    } catch (error) {
        message.textContent = error.message;
    }

}


loadUser();

</script>

</body>

</html>