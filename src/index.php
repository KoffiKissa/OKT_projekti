<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f4f6f1">
    <title>Sign in</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="card">
        <div class="brand"><span class="brand-mark" aria-hidden="true">+</span> WELLBEING</div>
        <h1 id="page-title">Welcome back</h1>
        <p id="page-intro" class="intro">Sign in to continue tracking your wellbeing and goals.</p>

        <div id="message" class="message" role="alert" hidden></div>
        <div id="welcome" class="welcome" role="status" hidden>
            <strong>You're signed in.</strong>
            <p id="user-details"></p>
            <a class="goal-link" href="goals.php">View and add health goals</a>
            <a class="goal-link" href="entries.php">Add a new health entry</a>
        </div>

        <form id="login-form" action="api.php" method="post">
            <div class="field">
                <label for="username">Username</label>
                <input id="username" name="username" type="text" autocomplete="username"
                       maxlength="50" required>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password"
                       required>
            </div>
            <button id="submit-button" type="submit">Sign in</button>
        </form>

        <form id="register-form" action="api.php" method="post" hidden>
            <div class="field">
                <label for="first-name">First name</label>
                <input id="first-name" name="name" type="text" autocomplete="given-name"
                       maxlength="50" required disabled>
            </div>
            <div class="field">
                <label for="last-name">Last name</label>
                <input id="last-name" name="last_name" type="text" autocomplete="family-name"
                       maxlength="50" required disabled>
            </div>
            <div class="field">
                <label for="new-username">Username</label>
                <input id="new-username" name="username" type="text" autocomplete="username"
                       minlength="3" maxlength="50" pattern="[A-Za-z0-9._-]+" required disabled>
            </div>
            <div class="field">
                <label for="new-password">Password</label>
                <input id="new-password" name="password" type="password" autocomplete="new-password"
                       minlength="8" maxlength="72" required disabled>
            </div>
            <div class="field">
                <label for="password-confirmation">Confirm password</label>
                <input id="password-confirmation" name="password_confirmation" type="password"
                       autocomplete="new-password" minlength="8" maxlength="72" required disabled>
            </div>
            <button id="register-button" type="submit">Create account</button>
        </form>
        <p class="account-switch">
            <span id="switch-prompt">New here?</span>
            <button id="switch-button" class="text-button" type="button">Create an account</button>
        </p>
        <noscript>JavaScript must be enabled to sign in.</noscript>
    </main>

    <script>
        const form = document.getElementById('login-form');
        const message = document.getElementById('message');
        const welcome = document.getElementById('welcome');
        const userDetails = document.getElementById('user-details');
        const submitButton = document.getElementById('submit-button');
        const registerForm = document.getElementById('register-form');
        const registerButton = document.getElementById('register-button');
        const pageTitle = document.getElementById('page-title');
        const pageIntro = document.getElementById('page-intro');
        const switchPrompt = document.getElementById('switch-prompt');
        const switchButton = document.getElementById('switch-button');
        let currentMode = 'login';

        function showUser(user) {
            userDetails.textContent = `${user.name} ${user.last_name} (@${user.username})`;
            welcome.hidden = false;
            form.hidden = true;
            message.hidden = true;
        }

        function showError(text) {
            message.textContent = text;
            message.classList.remove('success');
            message.hidden = false;
        }

        async function readResponse(response) {
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.error || 'Unable to sign in.');
            }
            return data;
        }

        function setMode(mode) {
            currentMode = mode;
            const isLogin = mode === 'login';
            form.hidden = !isLogin;
            registerForm.hidden = isLogin;
            form.querySelectorAll('input').forEach((input) => {
                input.disabled = !isLogin;
            });
            registerForm.querySelectorAll('input').forEach((input) => {
                input.disabled = isLogin;
            });
            pageTitle.textContent = isLogin ? 'Welcome back' : 'Create your account';
            pageIntro.textContent = isLogin
                ? 'Sign in to continue tracking your wellbeing and goals.'
                : 'Create an account to start tracking your wellbeing.';
            switchPrompt.textContent = isLogin ? 'New here?' : 'Already have an account?';
            switchButton.textContent = isLogin ? 'Create an account' : 'Sign in';
            message.hidden = true;
        }

        switchButton.addEventListener('click', () => {
            setMode(currentMode === 'login' ? 'register' : 'login');
        });

        async function submitAccountForm(accountForm, action, button) {
            message.hidden = true;
            button.disabled = true;

            const payload = Object.fromEntries(new FormData(accountForm).entries());
            payload.action = action;

            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await readResponse(response);
                accountForm.querySelectorAll('input[type="password"]').forEach((input) => {
                    input.value = '';
                });
                showUser(data.user);
            } catch (error) {
                showError(error.message || 'Unable to continue. Please try again.');
            } finally {
                button.disabled = false;
            }
        }

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            await submitAccountForm(form, 'login', submitButton);
        });

        registerForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            await submitAccountForm(registerForm, 'register', registerButton);
        });

        fetch('api.php')
            .then(async (response) => {
                if (response.status === 401) {
                    return;
                }
                const data = await readResponse(response);
                showUser(data.user);
            })
            .catch(() => showError('Unable to check your sign-in status. Please try again.'));
    </script>
</body>
</html>
