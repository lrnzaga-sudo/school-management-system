<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Registration</title>
    <style>
        body {
            display: grid;
            min-height: 100vh;
            box-sizing: border-box;
            place-items: center;
            margin: 0;
            padding: 2rem;
            color: #17212b;
            font: 16px/1.5 Arial, sans-serif;
            background: #f4f7f6;
        }

        main {
            box-sizing: border-box;
            width: min(100%, 480px);
            padding: 2rem;
            border: 1px solid #d4ddda;
            background: #fff;
        }

        h1 {
            margin: 0 0 .5rem;
            font-size: 1.75rem;
        }

        .intro {
            margin: 0 0 1.5rem;
            color: #56645f;
        }

        form {
            display: grid;
            gap: 1rem;
        }

        label {
            display: grid;
            gap: .35rem;
            font-weight: 600;
        }

        input,
        button {
            font: inherit;
        }

        input {
            box-sizing: border-box;
            width: 100%;
            padding: .65rem .75rem;
            border: 1px solid #aab8b5;
            border-radius: 4px;
        }

        input:focus {
            border-color: #27675b;
            outline: 2px solid rgb(39 103 91 / 20%);
        }

        button {
            width: 100%;
            padding: .65rem .85rem;
            border: 1px solid #27675b;
            border-radius: 4px;
            color: #fff;
            background: #27675b;
            font: inherit;
            font-weight: 600;
            cursor: pointer;
        }

        button:hover {
            background: #20564c;
        }

        #message {
            margin: 1rem 0 0;
            color: #9a3f32;
        }

        @media (max-width: 500px) {
            body {
                padding: 1rem;
            }

            main {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
<main>
    <h1>Admin Registration</h1>
    <p class="intro">Create an administrator account for the school management system.</p>

    <form id="registerForm">

        <label for="name">Name
            <input
                type="text"
                id="name"
                name="name"
                autocomplete="name"
                required
            >
        </label>

        <label for="email">Email
            <input
                type="email"
                id="email"
                name="email"
                autocomplete="email"
                required
            >
        </label>

        <label for="password">Password
            <input
                type="password"
                id="password"
                name="password"
                autocomplete="new-password"
                required
            >
        </label>

        <label for="password_confirmation">Confirm Password
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                autocomplete="new-password"
                required
            >
        </label>
        <button type="submit">Register</button>
    </form>
    <p id="message" role="status"></p>
</main>

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