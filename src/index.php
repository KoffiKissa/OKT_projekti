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
                <label class="mb-[7px] block text-[13px] font-semibold text-[#26372d]" for="birthday">Birthday</label>
                <input id="birthday" name="birthday" type="date" autocomplete="bday"
                       required disabled class="min-h-[47px] w-full rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-[11px] text-sm text-[#17251e] outline-none focus:border-[#3a7752] focus:ring-[3px] focus:ring-[#3a77521c]">
            </div>
            <div class="mb-[18px]">
                <label class="mb-[7px] block text-[13px] font-semibold text-[#26372d]" for="length">Length (cm)</label>
                <input id="length" name="length" type="number" min="1" step="1" inputmode="numeric"
                       required disabled class="min-h-[47px] w-full rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-[11px] text-sm text-[#17251e] outline-none focus:border-[#3a7752] focus:ring-[3px] focus:ring-[#3a77521c]">
            </div>
            <div class="mb-[18px]">
                <label class="mb-[7px] block text-[13px] font-semibold text-[#26372d]" for="sex">Sex</label>
                <select id="sex" name="sex" required disabled class="min-h-[47px] w-full rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-[11px] text-sm text-[#17251e] outline-none focus:border-[#3a7752] focus:ring-[3px] focus:ring-[#3a77521c]">
                    <option value="" selected disabled>Choose an option</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
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

<?php include __DIR__ . '/includes/footer.php'; ?>
