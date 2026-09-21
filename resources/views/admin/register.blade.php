<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Registration</title>
</head>

<body>

    <h1>Admin Registration</h1>

    <form id="registerForm">

        <div>
            <label for="name">Name</label>

            <input
                type="text"
                id="name"
                name="name"
                required
            >
        </div>

        <br>

        <div>
            <label for="email">Email</label>

            <input
                type="email"
                id="email"
                name="email"
                required
            >
        </div>

        <br>

        <div>
            <label for="password">Password</label>

            <input
                type="password"
                id="password"
                name="password"
                required
            >
        </div>

        <br>

        <div>
            <label for="password_confirmation">
                Confirm Password
            </label>

            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                required
            >
        </div>

        <br>

        <button type="submit">
            Register
        </button>

    </form>

    <p id="message"></p>

    <script>

        const form = document.getElementById('registerForm');
        const message = document.getElementById('message');

        form.addEventListener('submit', async function(event) {

            event.preventDefault();

            const data = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                password_confirmation: document.getElementById('password_confirmation').value
            };

            try {

                const response = await fetch('/api/admin/register', {
                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },

                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {

                    message.textContent = result.message;

                    form.reset();

                    console.log(result.admin);

                } else {

                    message.textContent = 'Registration failed.';

                    console.log(result);

                }

            } catch (error) {

                message.textContent = 'Something went wrong.';

                console.error(error);
            }

        });

    </script>

</body>
</html>