<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Profile</title>
    <style>
        body {
            margin: 0;
            padding: 2rem;
            color: #17212b;
            font: 16px/1.5 Arial, sans-serif;
            background: #f4f7f6;
        }

        main {
            max-width: 900px;
            margin: 0 auto;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        h1,
        h2 {
            margin: 0;
        }

        h1 {
            font-size: 1.75rem;
        }

        .profile-section {
            margin-bottom: 1.5rem;
            padding: 1.25rem;
            border: 1px solid #d4ddda;
            background: #fff;
        }

        .profile-section h2 {
            margin-bottom: 1rem;
        }

        form {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        form div {
            display: grid;
            gap: .35rem;
        }

        form br {
            display: none;
        }

        label {
            font-weight: 600;
        }

        input,
        button {
            font: inherit;
        }

        input {
            box-sizing: border-box;
            width: 100%;
            padding: .6rem .7rem;
            border: 1px solid #aab8b5;
            border-radius: 4px;
        }

        input[readonly] {
            color: #56645f;
            background: #f4f7f6;
        }

        button {
            width: fit-content;
            padding: .55rem .85rem;
            border: 1px solid #aab8b5;
            border-radius: 4px;
            color: #17212b;
            background: #fff;
            cursor: pointer;
        }

        button:hover {
            background: #eaf1ef;
        }

        form button {
            grid-column: 1 / -1;
        }

        .message {
            margin: 1rem 0 0;
            color: #56645f;
        }

        @media (max-width: 600px) {
            body {
                padding: 1rem;
            }

            .page-header {
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
    <header class="page-header">
        <h1>My Profile</h1>
        <button type="button" onclick="window.location.href='/admin/dashboard'">
            Back to Dashboard
        </button>
    </header>

    <section class="profile-section" aria-labelledby="profileInformationHeading">
        <h2 id="profileInformationHeading">Profile Information</h2>

        <form id="profileForm">

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

            <input
                type="text"
                id="role"
                readonly
            >

        </div>


        <br>


        <button type="submit">
            Save Changes
        </button>

        </form>
        <p id="profileMessage" class="message" role="status"></p>
    </section>

    <section class="profile-section" aria-labelledby="changePasswordHeading">
        <h2 id="changePasswordHeading">Change Password</h2>

        <form id="passwordForm">

        <div>

            <label>
                Current Password
            </label>

            <input
                type="password"
                id="current_password"
                required
            >

        </div>


        <br>


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
                Confirm New Password
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
        <p id="passwordMessage" class="message" role="status"></p>
    </section>
</main>


<script>

const token = localStorage.getItem('admin_token');


// Check login

if (!token) {

    window.location.href = '/admin/login';

}


// ========================================
// LOAD PROFILE
// GET /api/profile
// ========================================

async function loadProfile() {

    const response = await fetch('/api/profile', {

        method: 'GET',

        headers: {

            'Accept': 'application/json',

            'Authorization': `Bearer ${token}`

        }

    });


    // Token expired / invalid

    if (response.status === 401) {

        localStorage.removeItem('admin_token');

        window.location.href = '/admin/login';

        return;

    }


    const result = await response.json();


    if (!response.ok) {

        alert(result.message);

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


// ========================================
// UPDATE PROFILE
// PUT /api/profile
// ========================================

document
    .getElementById('profileForm')
    .addEventListener('submit', async function(event) {

        event.preventDefault();


        const data = {

            name:
                document.getElementById('name').value,

            email:
                document.getElementById('email').value

        };


        const response = await fetch('/api/profile', {

            method: 'PUT',

            headers: {

                'Content-Type':
                    'application/json',

                'Accept':
                    'application/json',

                'Authorization':
                    `Bearer ${token}`

            },

            body: JSON.stringify(data)

        });


        const result = await response.json();


        document.getElementById(
            'profileMessage'
        ).textContent = result.message;


        if (response.ok) {

            loadProfile();

        }

    });


// ========================================
// CHANGE PASSWORD
// PATCH /api/profile/password
// ========================================

document
    .getElementById('passwordForm')
    .addEventListener('submit', async function(event) {

        event.preventDefault();


        const currentPassword =
            document.getElementById(
                'current_password'
            ).value;


        const password =
            document.getElementById(
                'password'
            ).value;


        const passwordConfirmation =
            document.getElementById(
                'password_confirmation'
            ).value;


        // Check passwords

        if (password !== passwordConfirmation) {

            document.getElementById(
                'passwordMessage'
            ).textContent =
                'New passwords do not match.';

            return;

        }


        const data = {

            current_password:
                currentPassword,

            password:
                password,

            password_confirmation:
                passwordConfirmation

        };


        const response = await fetch(
            '/api/profile/password',
            {

                method: 'PATCH',

                headers: {

                    'Content-Type':
                        'application/json',

                    'Accept':
                        'application/json',

                    'Authorization':
                        `Bearer ${token}`

                },

                body: JSON.stringify(data)

            }
        );


        const result = await response.json();


        document.getElementById(
            'passwordMessage'
        ).textContent =
            result.message;


        if (response.ok) {

            document
                .getElementById('passwordForm')
                .reset();

        }

    });


// Load profile when page opens

loadProfile();

</script>

</body>

</html>