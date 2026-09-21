<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login</title>
</head>

<body>

    <h1>Admin Login</h1>

    <form id="loginForm">

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

        <button type="submit">
            Login
        </button>

    </form>

    <p id="message"></p>

    <script>

        const form = document.getElementById('loginForm');
        const message = document.getElementById('message');

        form.addEventListener('submit', async function(event) {

            event.preventDefault();

            const data = {
                email: document.getElementById('email').value,
                password: document.getElementById('password').value
            };

            try {

                const response = await fetch('/api/admin/login', {
                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },

                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {

                    // Save the Sanctum token
                    localStorage.setItem('admin_token', result.token);

                    message.textContent = result.message;

                    form.reset();

                    // Go to dashboard
                    window.location.href = '/admin/dashboard';

                } else {

                    message.textContent = result.message;

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