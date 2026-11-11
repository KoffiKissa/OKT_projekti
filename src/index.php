<?php
$pageTitle = 'Sign in';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navigation.php';
?>
    <main class="mx-auto my-0 w-full max-w-[560px] rounded-lg bg-white p-8 shadow-sm sm:p-10">
        <h1 id="page-title" class="m-0 text-[30px] font-bold tracking-[-0.04em]">Welcome back</h1>
        <p id="page-intro" class="mb-7 mt-[9px] text-sm leading-[1.6] text-[#68756c]">Sign in to continue tracking your wellbeing and goals.</p>

        <div id="message" class="mb-[18px] rounded-[9px] border border-[#f0d2cf] bg-[#fff5f4] px-[14px] py-3 text-[13px] leading-[1.5] text-[#832e28]" role="alert" hidden></div>
        <div id="welcome" class="rounded-xl bg-[#f1faf3] p-[18px] leading-[1.6] text-[#245c36]" role="status" hidden>
            <strong>You're signed in.</strong>
            <p id="user-details" class="mb-0 mt-[5px] text-sm text-[#516c58]"></p>
            <button id="logout-button" class="mt-4 min-h-[38px] w-full cursor-pointer rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-2 text-[13px] font-bold text-[#26372d] hover:bg-[#f4f6f1] focus-visible:outline focus-visible:outline-[3px] focus-visible:outline-[#9fcba9] focus-visible:outline-offset-[3px] disabled:cursor-wait disabled:opacity-70" type="button">Log out</button>
        </div>

        <form id="login-form" class="w-full max-w-[520px]" action="api.php" method="post">
            <div class="mb-[18px]">
                <label class="mb-[7px] block text-[13px] font-semibold text-[#26372d]" for="username">Username</label>
                <input id="username" name="username" type="text" autocomplete="username"
                       maxlength="50" required class="min-h-[47px] w-full rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-[11px] text-sm text-[#17251e] outline-none focus:border-[#3a7752] focus:ring-[3px] focus:ring-[#3a77521c]">
            </div>
            <div class="mb-[18px]">
                <label class="mb-[7px] block text-[13px] font-semibold text-[#26372d]" for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password"
                       required class="min-h-[47px] w-full rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-[11px] text-sm text-[#17251e] outline-none focus:border-[#3a7752] focus:ring-[3px] focus:ring-[#3a77521c]">
            </div>
            <button id="submit-button" type="submit" class="mt-[5px] min-h-12 w-full cursor-pointer rounded-[9px] border-0 bg-[#28603e] text-sm font-bold text-white hover:bg-[#1d4d31] focus-visible:outline focus-visible:outline-[3px] focus-visible:outline-[#9fcba9] focus-visible:outline-offset-[3px] disabled:cursor-wait disabled:opacity-70">Sign in</button>
        </form>

        <form id="register-form" class="w-full max-w-[520px]" action="api.php" method="post" hidden>
            <div class="mb-[18px]">
                <label class="mb-[7px] block text-[13px] font-semibold text-[#26372d]" for="first-name">First name</label>
                <input id="first-name" name="name" type="text" autocomplete="given-name"
                       maxlength="50" required disabled class="min-h-[47px] w-full rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-[11px] text-sm text-[#17251e] outline-none focus:border-[#3a7752] focus:ring-[3px] focus:ring-[#3a77521c]">
            </div>
            <div class="mb-[18px]">
                <label class="mb-[7px] block text-[13px] font-semibold text-[#26372d]" for="last-name">Last name</label>
                <input id="last-name" name="last_name" type="text" autocomplete="family-name"
                       maxlength="50" required disabled class="min-h-[47px] w-full rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-[11px] text-sm text-[#17251e] outline-none focus:border-[#3a7752] focus:ring-[3px] focus:ring-[#3a77521c]">
            </div>
            <div class="mb-[18px]">
                <label class="mb-[7px] block text-[13px] font-semibold text-[#26372d]" for="new-username">Username</label>
                <input id="new-username" name="username" type="text" autocomplete="username"
                       minlength="3" maxlength="50" pattern="[A-Za-z0-9._-]+" required disabled class="min-h-[47px] w-full rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-[11px] text-sm text-[#17251e] outline-none focus:border-[#3a7752] focus:ring-[3px] focus:ring-[#3a77521c]">
            </div>
            <div class="mb-[18px]">
                <label class="mb-[7px] block text-[13px] font-semibold text-[#26372d]" for="new-password">Password</label>
                <input id="new-password" name="password" type="password" autocomplete="new-password"
                       minlength="8" maxlength="72" required disabled class="min-h-[47px] w-full rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-[11px] text-sm text-[#17251e] outline-none focus:border-[#3a7752] focus:ring-[3px] focus:ring-[#3a77521c]">
            </div>
            <div class="mb-[18px]">
                <label class="mb-[7px] block text-[13px] font-semibold text-[#26372d]" for="password-confirmation">Confirm password</label>
                <input id="password-confirmation" name="password_confirmation" type="password"
                       autocomplete="new-password" minlength="8" maxlength="72" required disabled class="min-h-[47px] w-full rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-[11px] text-sm text-[#17251e] outline-none focus:border-[#3a7752] focus:ring-[3px] focus:ring-[#3a77521c]">
            </div>
            <button id="register-button" type="submit" class="mt-[5px] min-h-12 w-full cursor-pointer rounded-[9px] border-0 bg-[#28603e] text-sm font-bold text-white hover:bg-[#1d4d31] focus-visible:outline focus-visible:outline-[3px] focus-visible:outline-[#9fcba9] focus-visible:outline-offset-[3px] disabled:cursor-wait disabled:opacity-70">Create account</button>
        </form>
        <p class="mb-0 mt-5 text-center text-[13px] text-[#738077]">
            <span id="switch-prompt">New here?</span>
            <button id="switch-button" class="ml-1 min-h-0 w-auto cursor-pointer border-0 bg-transparent p-[2px] text-[13px] text-[#28603e] hover:bg-transparent hover:text-[#1d4d31] hover:underline" type="button">Create an account</button>
        </p>
        <noscript>JavaScript must be enabled to sign in.</noscript>
    </main>

    <script>
        const form = document.getElementById('login-form');
        const message = document.getElementById('message');
        const welcome = document.getElementById('welcome');
        const userDetails = document.getElementById('user-details');
        const logoutButton = document.getElementById('logout-button');
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
            window.setSiteNavigationVisible(true);
        }

        function showError(text) {
            message.textContent = text;
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

        logoutButton.addEventListener('click', async () => {
            logoutButton.disabled = true;
            message.hidden = true;
            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'logout' })
                });
                await readResponse(response);
                welcome.hidden = true;
                userDetails.textContent = '';
                window.setSiteNavigationVisible(false);
                setMode('login');
            } catch (error) {
                showError(error.message || 'Unable to log out. Please try again.');
            } finally {
                logoutButton.disabled = false;
            }
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
<?php include __DIR__ . '/includes/footer.php'; ?>
