<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Change Password</title>
</head>

<body>

    <h1>Change User Password</h1>

    <button onclick="window.location.href='/admin/users'">
        Back to Users
    </button>

    <hr>


    <form id="passwordForm">

        <div>

            <label>
                New Password
            </label>

            <input
                type="password"
                id="password"
                required
            >

        </div>


        <br>


        <div>

            <label>
                Confirm Password
            </label>

            <input
                type="password"
                id="password_confirmation"
                required
            >

        </div>


        <br>


        <button type="submit">
            Change Password
        </button>

    </form>


    <p id="message"></p>


<script>

const token = localStorage.getItem('admin_token');

const userId = "{{ $id }}";


if (!token) {

    window.location.href = '/admin/login';

}


document
    .getElementById('passwordForm')
    .addEventListener('submit', async function(event) {

        event.preventDefault();


        const password =
            document.getElementById('password').value;

        const passwordConfirmation =
            document.getElementById(
                'password_confirmation'
            ).value;


        if (password !== passwordConfirmation) {

            document.getElementById('message').textContent =
                'Passwords do not match.';

            return;

        }


        const response = await fetch(
            `/api/admin/users/${userId}/password`,
            {

                method: 'PATCH',

                headers: {

                    'Content-Type': 'application/json',

                    'Accept': 'application/json',

                    'Authorization': `Bearer ${token}`

                },

                body: JSON.stringify({

                    password: password,

                    password_confirmation:
                        passwordConfirmation

                })

            }
        );


        const result = await response.json();


        document.getElementById('message').textContent =
            result.message;


        if (response.ok) {

            document
                .getElementById('passwordForm')
                .reset();

        }

    });

</script>

</body>

</html>