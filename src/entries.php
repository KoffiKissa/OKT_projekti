<?php
$pageTitle = 'New health entry';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navigation.php';
?>
    <main id="entries-page" class="container mx-auto my-0 w-full max-w-6xl" hidden>
        <div class="mb-7">
            <h1 id="entry-form-heading" class="m-0 text-[30px] font-bold tracking-[-0.04em]">New entry</h1>
            <p id="entry-form-intro" class="mb-0 mt-[9px] text-sm leading-[1.6] text-[#68756c]">Enter a health measurement and save it.</p>
        </div>

        <div id="entry-message" class="mb-[18px] rounded-[9px] border border-[#f0d2cf] bg-[#fff5f4] px-[14px] py-3 text-[13px] leading-[1.5] text-[#832e28]" role="status" hidden>
            <button id="undo-entry-button" class="ml-3 cursor-pointer rounded-[7px] border border-[#dce2da] bg-white px-3 py-1 font-bold text-[#26372d] hover:bg-[#f4f6f1] focus-visible:outline focus-visible:outline-[3px] focus-visible:outline-[#9fcba9] disabled:cursor-wait disabled:opacity-70" type="button" hidden>Undo changes</button>
        </div>

        <form id="entry-form" class="mb-10 grid grid-cols-2 gap-x-5 rounded-lg bg-white p-6 shadow-sm sm:p-9 max-[700px]:grid-cols-1">
            <div class="mb-[18px]">
                <label class="mb-[7px] block text-[13px] font-semibold text-[#26372d]" for="category">Health parameter</label>
                <select id="category" name="category" required class="min-h-[47px] w-full rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-[11px] text-sm text-[#17251e] outline-none focus:border-[#3a7752] focus:ring-[3px] focus:ring-[#3a77521c]">
                    <option value="">Choose a parameter</option>
                    <option value="weight">Weight (kg)</option>
                    <option value="sleep">Sleep (h)</option>
                    <option value="steps">Daily steps (steps/day)</option>
                    <option value="blood_pressure">Blood pressure (mmHg)</option>
                </select>
            </div>

            <div id="single-measurement-field" class="mb-[18px]">
                <label id="measurement-label" class="mb-[7px] block text-[13px] font-semibold text-[#26372d]" for="measurement">Measurement</label>
                <input id="measurement" name="measurement" type="number" min="0" max="500"
                       step="0.01" inputmode="decimal" placeholder="Choose a parameter first" disabled required class="min-h-[47px] w-full rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-[11px] text-sm text-[#17251e] outline-none focus:border-[#3a7752] focus:ring-[3px] focus:ring-[#3a77521c]">
                <span id="measurement-hint" class="mt-[6px] block text-[11px] leading-[1.4] text-[#7a857d]">Choose a parameter to see the expected unit and range.</span>
            </div>

            <div id="blood-pressure-fields" class="col-span-2 grid-cols-2 gap-[14px] max-[700px]:col-span-1 max-[480px]:grid-cols-1 max-[480px]:gap-0" hidden>
                <div class="mb-2">
                    <label class="mb-[7px] block text-[13px] font-semibold text-[#26372d]" for="systolic">Systolic (mmHg)</label>
                    <input id="systolic" name="systolic" type="number" min="50" max="300"
                           step="1" inputmode="numeric" placeholder="e.g. 120" disabled required class="min-h-[47px] w-full rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-[11px] text-sm text-[#17251e] outline-none focus:border-[#3a7752] focus:ring-[3px] focus:ring-[#3a77521c]">
                </div>
                <div class="mb-2">
                    <label class="mb-[7px] block text-[13px] font-semibold text-[#26372d]" for="diastolic">Diastolic (mmHg)</label>
                    <input id="diastolic" name="diastolic" type="number" min="30" max="200"
                           step="1" inputmode="numeric" placeholder="e.g. 80" disabled required class="min-h-[47px] w-full rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-[11px] text-sm text-[#17251e] outline-none focus:border-[#3a7752] focus:ring-[3px] focus:ring-[#3a77521c]">
                </div>
                <span class="col-span-full mb-[18px] text-[11px] leading-[1.4] text-[#7a857d] max-[480px]:col-auto">Enter whole numbers; systolic must be higher than diastolic.</span>
            </div>

            <div class="col-span-2 mb-[18px] max-[700px]:col-span-1">
                <label class="mb-[7px] block text-[13px] font-semibold text-[#26372d]" for="date">Measurement date</label>
                <input id="date" name="date" type="date" required class="min-h-[47px] w-full rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-[11px] text-sm text-[#17251e] outline-none focus:border-[#3a7752] focus:ring-[3px] focus:ring-[#3a77521c]">
            </div>
            <button id="entry-submit-button" type="submit" class="col-span-2 mt-[5px] min-h-12 w-full cursor-pointer rounded-[9px] border-0 bg-[#28603e] text-sm font-bold text-white hover:bg-[#1d4d31] focus-visible:outline focus-visible:outline-[3px] focus-visible:outline-[#9fcba9] focus-visible:outline-offset-[3px] disabled:cursor-wait disabled:opacity-70 max-[700px]:col-span-1">Save entry</button>
            <button id="cancel-edit-button" type="button" class="col-span-2 min-h-12 w-full cursor-pointer rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-2 text-sm font-bold text-[#26372d] hover:bg-[#f4f6f1] focus-visible:outline focus-visible:outline-[3px] focus-visible:outline-[#9fcba9] focus-visible:outline-offset-[3px] max-[700px]:col-span-1" hidden>Cancel editing</button>
        </form>

        <section aria-labelledby="entry-history-heading">
            <h2 id="entry-history-heading" class="mb-[14px] mt-[26px] text-lg font-bold tracking-[-0.02em]">Your recent entries</h2>
            <p id="entry-empty" class="rounded-[10px] bg-[#f4f6f1] p-[14px] text-sm leading-[1.5] text-[#68756c]" hidden>You haven't recorded any measurements yet.</p>
            <ul id="entry-list" class="m-0 grid list-none gap-6 p-0 md:grid-cols-2 xl:grid-cols-3"></ul>
        </section>
    </main>
    <p id="loading-message" class="mb-7 mt-[9px] text-sm leading-[1.6] text-[#68756c]">Checking your sign-in...</p>

<?php include __DIR__ . '/includes/footer.php'; ?>
