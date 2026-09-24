<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Profile</title>

</head>

<body>

    <h1>My Profile</h1>

    <button onclick="window.location.href='/admin/dashboard'">
        Back to Dashboard
    </button>

    <hr>


    <!-- PROFILE INFORMATION -->

    <h2>Profile Information</h2>


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


    <p id="profileMessage"></p>


    <hr>


    <!-- CHANGE PASSWORD -->

    <h2>Change Password</h2>


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


    <p id="passwordMessage"></p>


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