<?php
$pageTitle = 'Your health goals';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navigation.php';
?>
    <main id="goals-page" class="container mx-auto my-0 w-full max-w-6xl" hidden>
        <div class="mb-7">
            <h1 class="m-0 text-[30px] font-bold tracking-[-0.04em]">Health goals</h1>
            <p class="mb-0 mt-[9px] text-sm leading-[1.6] text-[#68756c]">Choose a health parameter and set a goal to work toward.</p>
        </div>

        <div id="goal-message" class="mb-[18px] rounded-[9px] border border-[#f0d2cf] bg-[#fff5f4] px-[14px] py-3 text-[13px] leading-[1.5] text-[#832e28]" role="alert" hidden>
            <button id="cancel-saved-goal" class="ml-3 cursor-pointer rounded-[7px] border border-[#dce2da] bg-white px-3 py-1 font-bold text-[#26372d] hover:bg-[#f4f6f1] focus-visible:outline focus-visible:outline-[3px] focus-visible:outline-[#9fcba9] disabled:cursor-wait disabled:opacity-70" type="button" hidden>Cancel</button>
        </div>

        <form id="goal-form" class="mb-10 grid grid-cols-2 gap-x-5 rounded-lg bg-white p-6 shadow-sm sm:p-9 max-[700px]:grid-cols-1">
            <div class="mb-[18px]">
                <label class="mb-[7px] block text-[13px] font-semibold text-[#26372d]" for="goal-name">Goal name</label>
                <input id="goal-name" name="goal_name" type="text" maxlength="100" required placeholder="e.g. Run regularly"
                       class="min-h-[47px] w-full rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-[11px] text-sm text-[#17251e] outline-none focus:border-[#3a7752] focus:ring-[3px] focus:ring-[#3a77521c]">
            </div>
            <div class="mb-[18px]">
                <label class="mb-[7px] block text-[13px] font-semibold text-[#26372d]" for="category">Health parameter</label>
                <select id="category" name="category" required class="min-h-[47px] w-full rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-[11px] text-sm text-[#17251e] outline-none focus:border-[#3a7752] focus:ring-[3px] focus:ring-[#3a77521c]">
                    <option value="">Choose a parameter</option>
                    <option value="weight">Weight (kg)</option>
                    <option value="sleep">Average daily sleep</option>
                    <option value="steps">Average daily steps</option>
                    <option value="blood_pressure">Blood pressure (mmHg)</option>
                </select>
            </div>
            <div class="mb-[18px]">
                <label class="mb-[7px] block text-[13px] font-semibold text-[#26372d]" for="goal">Target value <span id="goal-unit"></span></label>
                <input id="goal" name="goal" type="number" min="0.01" max="99999999.99"
                       step="0.01" inputmode="decimal" placeholder="Enter a number" required class="min-h-[47px] w-full rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-[11px] text-sm text-[#17251e] outline-none focus:border-[#3a7752] focus:ring-[3px] focus:ring-[#3a77521c]">
                <span id="goal-hint" class="mt-[6px] block text-[11px] leading-[1.4] text-[#7a857d]">Choose a health parameter to see its unit.</span>
            </div>
            <div class="mb-[18px]">
                <label class="mb-[7px] block text-[13px] font-semibold text-[#26372d]" for="start-date">Start date</label>
                <input id="start-date" name="start_date" type="date" required class="min-h-[47px] w-full rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-[11px] text-sm text-[#17251e] outline-none focus:border-[#3a7752] focus:ring-[3px] focus:ring-[#3a77521c]">
            </div>
            <div class="mb-[18px]">
                <label class="mb-[7px] block text-[13px] font-semibold text-[#26372d]" for="done-by-date">End date</label>
                <input id="done-by-date" name="done_by_date" type="date" required class="min-h-[47px] w-full rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-[11px] text-sm text-[#17251e] outline-none focus:border-[#3a7752] focus:ring-[3px] focus:ring-[#3a77521c]">
            </div>
            <div class="col-span-2 mt-3 flex gap-[9px] max-[700px]:col-span-1 max-[480px]:flex-col">
                <button id="save-goal-button" type="submit" class="mt-0 min-h-[38px] w-auto flex-1 cursor-pointer rounded-[9px] border-0 bg-[#28603e] px-[13px] py-2 text-[13px] font-bold text-white hover:bg-[#1d4d31] focus-visible:outline focus-visible:outline-[3px] focus-visible:outline-[#9fcba9] focus-visible:outline-offset-[3px] disabled:cursor-wait disabled:opacity-70 max-[480px]:w-full">Save goal</button>
                <button id="cancel-edit" type="button" class="mt-0 min-h-[38px] w-auto cursor-pointer rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-2 text-[13px] font-bold text-[#26372d] hover:bg-[#f4f6f1] focus-visible:outline focus-visible:outline-[3px] focus-visible:outline-[#9fcba9] focus-visible:outline-offset-[3px] disabled:cursor-wait disabled:opacity-70 max-[480px]:w-full" hidden>Cancel changes</button>
            </div>
        </form>

        <section aria-labelledby="saved-goals-heading">
            <h2 id="saved-goals-heading" class="mb-[14px] mt-[26px] text-lg font-bold tracking-[-0.02em]">Your saved goals</h2>
            <p id="goal-empty" class="rounded-[10px] bg-[#f4f6f1] p-[14px] text-sm leading-[1.5] text-[#68756c]" hidden>You haven't added any goals yet.</p>
            <ul id="goal-list" class="m-0 grid list-none gap-6 p-0 md:grid-cols-2 xl:grid-cols-3"></ul>
        </section>
    </main>
    <p id="loading-message" class="mb-7 mt-[9px] text-sm leading-[1.6] text-[#68756c]">Checking your sign-in...</p>

<?php include __DIR__ . '/includes/footer.php'; ?>
