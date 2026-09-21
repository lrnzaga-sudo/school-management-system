
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard</title>
</head>

<body>

    <h1>Admin Dashboard</h1>

    <div id="dashboard">
        Loading...
    </div>

    <button id="logoutButton">
        Logout
    </button>

    <script>

        const token = localStorage.getItem('admin_token');
        const dashboard = document.getElementById('dashboard');

        // Check if admin has a token
        if (!token) {

            window.location.href = '/admin/login';

        } else {

            loadDashboard();

        }


        async function loadDashboard() {

            try {

                const response = await fetch('/api/admin/dashboard', {

                    method: 'GET',

                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }

                });

                const result = await response.json();

                // Token is invalid or expired
                if (response.status === 401) {

                    localStorage.removeItem('admin_token');

                    window.location.href = '/admin/login';

                    return;
                }

                if (response.ok) {

                    dashboard.innerHTML = `
                        <h2>Welcome, ${result.admin.name}!</h2>

                        <a href="/admin/users">
                            Manage User Accounts
                        </a>
                        <p>
                            Email: ${result.admin.email}
                        </p>

                        <p>
                            You are logged in as an administrator.
                        </p>
                    `;

                } else {

                    dashboard.textContent = 'Unable to load dashboard.';

                }

            } catch (error) {

                console.error(error);

                dashboard.textContent =
                    'Something went wrong while loading the dashboard.';

            }

        }


        // Logout
        document.getElementById('logoutButton')
            .addEventListener('click', async function() {

                try {

                    await fetch('/api/admin/logout', {

                        method: 'POST',

                        headers: {
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${token}`
                        }

                    });

                } catch (error) {

                    console.error(error);

                }

                // Remove token from browser
                localStorage.removeItem('admin_token');

                // Return to login page
                window.location.href = '/admin/login';

            });

    </script>

</body>

</html>