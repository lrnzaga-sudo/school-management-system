<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Subject</title>
    <style>
        body {
            margin: 0;
            padding: 2rem;
            color: #17212b;
            font: 16px/1.5 Arial, sans-serif;
            background: #f4f7f6;
        }

        main {
            max-width: 760px;
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
        }

        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            padding: 1.25rem;
            border: 1px solid #d4ddda;
            background: #fff;
        }

        label {
            display: grid;
            gap: .35rem;
            font-weight: 600;
        }

        input,
        textarea,
        button {
            font: inherit;
        }

        input,
        textarea {
            box-sizing: border-box;
            width: 100%;
            padding: .6rem .7rem;
            border: 1px solid #aab8b5;
            border-radius: 4px;
        }

        textarea {
            min-height: 6rem;
            resize: vertical;
        }

        .full-width,
        #message {
            grid-column: 1 / -1;
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

        #message {
            margin: 0;
            color: #9a3f32;
        }

        @media (max-width: 600px) {
            body {
                padding: 1rem;
            }

            header {
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
    <header>
        <h1>Add Subject</h1>
        <button type="button" onclick="window.location.href='/admin/subjects'">Back to Subjects</button>
    </header>

    <form id="createSubjectForm">
        <label>
            Subject code
            <input name="subject_code" type="text" maxlength="50" required>
        </label>
        <label>
            Subject name
            <input name="subject_name" type="text" maxlength="255" required>
        </label>
        <label class="full-width">
            Description
            <textarea name="description"></textarea>
        </label>
        <p id="message" role="alert" hidden></p>
        <button type="submit">Add Subject</button>
    </form>
</main>

<script>
    const token = localStorage.getItem('admin_token');
    const form = document.getElementById('createSubjectForm');
    const message = document.getElementById('message');

    if (!token) {
        window.location.href = '/admin/login';
    }

    form.addEventListener('submit', async event => {
        event.preventDefault();
        message.hidden = true;

        const data = Object.fromEntries(new FormData(form).entries());
        data.description = data.description || null;

        try {
            const response = await fetch('/api/admin/subjects', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    Authorization: 'Bearer ' + token
                },
                body: JSON.stringify(data)
            });

            const result = await response.json().catch(() => ({}));
            if (response.status === 401) {
                localStorage.removeItem('admin_token');
                window.location.href = '/admin/login';
                return;
            }

            if (!response.ok) {
                const validationMessages = result.errors
                    ? Object.values(result.errors).flat().join(' ')
                    : '';
                throw new Error(validationMessages || result.message || 'Unable to add subject.');
            }

            window.location.href = '/admin/subjects';
        } catch (error) {
            message.textContent = error.message;
            message.hidden = false;
        }
    });
</script>
</body>
</html>
