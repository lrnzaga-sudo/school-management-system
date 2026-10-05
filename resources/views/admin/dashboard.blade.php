
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard</title>
    <style>
        :root {
            color-scheme: light;
            --text: #17212b;
            --muted: #56645f;
            --border: #d4ddda;
            --surface: #fff;
            --surface-muted: #eaf1ef;
            --accent: #27675b;
        }

        body {
            margin: 0;
            padding: 2rem;
            color: var(--text);
            font: 16px/1.5 Arial, sans-serif;
            background: #f4f7f6;
        }

        main {
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-header,
        .welcome-panel,
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .page-header {
            margin-bottom: 1.5rem;
        }

        h1,
        h2,
        p {
            margin-top: 0;
        }

        h1,
        h2,
        .welcome-panel p {
            margin-bottom: 0;
        }

        h1 {
            font-size: 1.75rem;
        }

        .welcome-panel {
            margin-bottom: 1.5rem;
            padding: 1.5rem;
            border: 1px solid var(--border);
            border-left: 4px solid var(--accent);
            background: var(--surface);
        }

        .welcome-panel h2 {
            margin-bottom: .35rem;
        }

        .welcome-panel p,
        .section-description {
            color: var(--muted);
        }

        .section-header {
            margin-bottom: 1rem;
        }

        .section-description {
            margin: .25rem 0 0;
        }

        .management-links {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }

        .management-card {
            display: block;
            min-height: 7rem;
            padding: 1.25rem;
            border: 1px solid var(--border);
            color: var(--text);
            background: var(--surface);
            text-decoration: none;
            transition: border-color .15s ease, background-color .15s ease, transform .15s ease;
        }

        .management-card:hover,
        .management-card:focus-visible {
            border-color: var(--accent);
            background: var(--surface-muted);
            transform: translateY(-2px);
        }

        .management-card strong {
            display: block;
            margin-bottom: .35rem;
            color: var(--accent);
            font-size: 1.1rem;
        }

        .management-card span {
            color: var(--muted);
        }

        button {
            padding: .55rem .85rem;
            border: 1px solid #aab8b5;
            border-radius: 4px;
            color: var(--text);
            background: var(--surface);
            font: inherit;
            cursor: pointer;
        }

        button:hover {
            background: var(--surface-muted);
        }

        #dashboardMessage {
            padding: 1rem;
            border: 1px solid var(--border);
            color: var(--muted);
            background: var(--surface);
        }

        @media (max-width: 700px) {
            body {
                padding: 1rem;
            }

            .management-links {
                grid-template-columns: 1fr;
            }

            .page-header,
            .welcome-panel,
            .section-header {
                align-items: stretch;
                flex-direction: column;
            }

            .welcome-panel {
                padding: 1.25rem;
            }
        }
    </style>
</head>

<body>
<main>
    <header class="page-header">
        <h1>Admin Dashboard</h1>
        <button id="logoutButton" type="button">Logout</button>
    </header>

    <div id="dashboard" aria-live="polite">
        <p id="dashboardMessage">Loading dashboard...</p>
    </div>
</main>

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
                    const welcome = document.createElement('section');
                    welcome.className = 'welcome-panel';

                    const welcomeText = document.createElement('div');
                    const welcomeHeading = document.createElement('h2');
                    welcomeHeading.textContent = `Welcome, ${result.admin.name}!`;
                    const email = document.createElement('p');
                    email.textContent = `Email: ${result.admin.email}`;
                    welcomeText.append(welcomeHeading, email);

                    const profileLink = document.createElement('a');
                    profileLink.className = 'management-card';
                    profileLink.href = '/profile';
                    profileLink.innerHTML = '<strong>My Profile</strong><span>View and update your profile</span>';
                    welcome.append(welcomeText, profileLink);

                    const managementSection = document.createElement('section');
                    const sectionHeader = document.createElement('div');
                    sectionHeader.className = 'section-header';
                    const sectionTitle = document.createElement('div');
                    const heading = document.createElement('h2');
                    heading.textContent = 'Management';
                    const description = document.createElement('p');
                    description.className = 'section-description';
                    description.textContent = 'Choose an area to manage.';
                    sectionTitle.append(heading, description);
                    sectionHeader.appendChild(sectionTitle);

                    const links = document.createElement('nav');
                    links.className = 'management-links';
                    links.setAttribute('aria-label', 'Management');
                    [
                        ['/admin/users', 'Manage User Accounts', 'Manage user access and account settings'],
                        ['/admin/students', 'Student Management', 'View and manage student records'],
                        ['/admin/teachers', 'Teacher Management', 'View and manage teacher records'],
                        ['/admin/subjects', 'Subject Management', 'View and manage subject records']
                    ].forEach(([href, title, detail]) => {
                        const link = document.createElement('a');
                        link.className = 'management-card';
                        link.href = href;
                        const linkTitle = document.createElement('strong');
                        linkTitle.textContent = title;
                        const linkDetail = document.createElement('span');
                        linkDetail.textContent = detail;
                        link.append(linkTitle, linkDetail);
                        links.appendChild(link);
                    });
                    managementSection.append(sectionHeader, links);

                    dashboard.replaceChildren(welcome, managementSection);

                } else {
                    dashboard.innerHTML = '<p id="dashboardMessage">Unable to load dashboard.</p>';

                }

            } catch (error) {

                console.error(error);

                dashboard.innerHTML =
                    '<p id="dashboardMessage">Something went wrong while loading the dashboard.</p>';

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