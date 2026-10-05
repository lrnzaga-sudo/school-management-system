<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Teacher</title>
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
        select,
        textarea,
        button {
            font: inherit;
        }

        input,
        select,
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
        <h1>Add Teacher</h1>
        <button type="button" onclick="window.location.href='/admin/teachers'">Back to Teachers</button>
    </header>

    <form id="createTeacherForm">
        <label>
            Existing user ID
            <input name="user_id" type="number" min="1" required>
        </label>
        <label>
            Employee number
            <input name="employee_number" type="text" maxlength="50" required>
        </label>
        <label>
            First name
            <input name="first_name" type="text" maxlength="255" required>
        </label>
        <label>
            Middle name
            <input name="middle_name" type="text" maxlength="255">
        </label>
        <label>
            Last name
            <input name="last_name" type="text" maxlength="255" required>
        </label>
        <label>
            Birth date
            <input name="birth_date" type="date">
        </label>
        <label>
            Gender
            <select name="gender" required>
                <option value="">Select gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </label>
        <label>
            Phone
            <input name="phone" type="tel" maxlength="20" required>
        </label>
        <label class="full-width">
            Address
            <textarea name="address" required></textarea>
        </label>
        <p id="message" role="alert" hidden></p>
        <button type="submit">Add Teacher</button>
    </form>
</main>

<script>
    const token = localStorage.getItem('admin_token');
    const form = document.getElementById('createTeacherForm');
    const message = document.getElementById('message');

    if (!token) {
        window.location.href = '/admin/login';
    }

    form.addEventListener('submit', async event => {
        event.preventDefault();
        message.hidden = true;

        const data = Object.fromEntries(new FormData(form).entries());
        data.user_id = Number(data.user_id);
        data.birth_date = data.birth_date || null;
        data.middle_name = data.middle_name || null;

        try {
            const response = await fetch('/api/admin/teachers', {
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
                throw new Error(validationMessages || result.message || 'Unable to add teacher.');
            }

            window.location.href = '/admin/teachers';
        } catch (error) {
            message.textContent = error.message;
            message.hidden = false;
        }
    });
</script>
</body>
</html>
