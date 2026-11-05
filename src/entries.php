<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f4f6f1">
    <title>New health entry</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="grid min-h-screen min-w-[320px] place-items-center bg-gradient-to-br from-[#e1eee2] via-[#f4f6f1] to-[#e8eee1] p-6 font-['Segoe_UI',Arial,sans-serif] text-[#1b2b21] [font-synthesis:none] [text-rendering:optimizeLegibility] max-[480px]:p-[14px]">
    <main id="entries-page" class="w-full max-w-[560px] rounded-[22px] border border-[#e6e9e1] bg-white p-11 shadow-[0_24px_70px_#253c2c14] max-[480px]:rounded-[17px] max-[480px]:px-[26px] max-[480px]:py-8" hidden>
        <div class="mb-[26px] inline-flex items-center gap-[10px] text-sm font-bold tracking-[0.06em] text-[#193d2d]"><span class="grid h-[34px] w-[34px] place-items-center rounded-[11px] bg-[#c9e6c6] text-[21px] text-[#193d2d]" aria-hidden="true">+</span> WELLBEING</div>
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="m-0 text-[30px] font-bold tracking-[-0.04em]">New entry</h1>
                <p class="mb-7 mt-[9px] text-sm leading-[1.6] text-[#68756c]">Record a health measurement and check it before saving.</p>
            </div>
            <a class="mt-2 whitespace-nowrap text-[13px] font-bold text-[#28603e] no-underline hover:underline" href="index.php">Back to account</a>
        </div>

        <div id="entry-message" class="mb-[18px] rounded-[9px] border border-[#f0d2cf] bg-[#fff5f4] px-[14px] py-3 text-[13px] leading-[1.5] text-[#832e28]" role="status" hidden></div>

        <form id="entry-form">
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

            <div id="blood-pressure-fields" class="grid grid-cols-2 gap-[14px] max-[480px]:grid-cols-1 max-[480px]:gap-0" hidden>
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

            <div class="mb-[18px]">
                <label class="mb-[7px] block text-[13px] font-semibold text-[#26372d]" for="date">Measurement date</label>
                <input id="date" name="date" type="date" required class="min-h-[47px] w-full rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-[11px] text-sm text-[#17251e] outline-none focus:border-[#3a7752] focus:ring-[3px] focus:ring-[#3a77521c]">
            </div>
            <button type="submit" class="mt-[5px] min-h-12 w-full cursor-pointer rounded-[9px] border-0 bg-[#28603e] text-sm font-bold text-white hover:bg-[#1d4d31] focus-visible:outline focus-visible:outline-[3px] focus-visible:outline-[#9fcba9] focus-visible:outline-offset-[3px] disabled:cursor-wait disabled:opacity-70">Review entry</button>
        </form>

        <section id="entry-review" class="mb-[18px] rounded-[10px] border border-[#cce5d1] bg-[#f1faf3] p-4" aria-labelledby="review-heading" hidden>
            <h2 id="review-heading" class="mb-3 mt-0 text-[17px] font-bold text-[#245c36]">Review your entry</h2>
            <p class="mt-[7px] text-sm leading-[1.5] text-[#516c58]"><strong>Health parameter:</strong> <span id="review-category"></span></p>
            <p class="mt-[7px] text-sm leading-[1.5] text-[#516c58]"><strong>Measurement:</strong> <span id="review-measurement"></span></p>
            <p class="mt-[7px] text-sm leading-[1.5] text-[#516c58]"><strong>Date:</strong> <span id="review-date"></span></p>
            <div class="mt-4 grid grid-cols-2 gap-[10px] max-[480px]:grid-cols-1">
                <button id="edit-entry" class="mt-0 min-h-12 w-auto cursor-pointer rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-2 text-sm font-bold text-[#26372d] hover:bg-[#f4f6f1] focus-visible:outline focus-visible:outline-[3px] focus-visible:outline-[#9fcba9] focus-visible:outline-offset-[3px] disabled:cursor-wait disabled:opacity-70" type="button">Make changes</button>
                <button id="confirm-entry" type="button" class="mt-0 min-h-12 w-full cursor-pointer rounded-[9px] border-0 bg-[#28603e] text-sm font-bold text-white hover:bg-[#1d4d31] focus-visible:outline focus-visible:outline-[3px] focus-visible:outline-[#9fcba9] focus-visible:outline-offset-[3px] disabled:cursor-wait disabled:opacity-70">Confirm and save</button>
            </div>
        </section>

        <section aria-labelledby="entry-history-heading">
            <h2 id="entry-history-heading" class="mb-[14px] mt-[26px] text-lg font-bold tracking-[-0.02em]">Your recent entries</h2>
            <p id="entry-empty" class="rounded-[10px] bg-[#f4f6f1] p-[14px] text-sm leading-[1.5] text-[#68756c]" hidden>You haven't recorded any measurements yet.</p>
            <ul id="entry-list" class="m-0 grid list-none gap-3 p-0"></ul>
        </section>
    </main>
    <p id="loading-message" class="mb-7 mt-[9px] text-sm leading-[1.6] text-[#68756c]">Checking your sign-in...</p>

    <script>
        const page = document.getElementById('entries-page');
        const loadingMessage = document.getElementById('loading-message');
        const message = document.getElementById('entry-message');
        const form = document.getElementById('entry-form');
        const review = document.getElementById('entry-review');
        const categoryInput = document.getElementById('category');
        const measurementInput = document.getElementById('measurement');
        const measurementLabel = document.getElementById('measurement-label');
        const measurementHint = document.getElementById('measurement-hint');
        const singleField = document.getElementById('single-measurement-field');
        const bloodPressureFields = document.getElementById('blood-pressure-fields');
        const systolicInput = document.getElementById('systolic');
        const diastolicInput = document.getElementById('diastolic');
        const entryList = document.getElementById('entry-list');
        const emptyMessage = document.getElementById('entry-empty');
        const confirmButton = document.getElementById('confirm-entry');
        const categoryLabels = {
            weight: 'Weight (kg)',
            sleep: 'Sleep (h)',
            steps: 'Daily steps (steps/day)',
            blood_pressure: 'Blood pressure (mmHg)'
        };
        const measurementSettings = {
            weight: {
                label: 'Weight (kg)',
                min: '0.1',
                max: '500',
                step: '0.01',
                hint: 'Enter a value between 0.1 and 500 kg.'
            },
            sleep: {
                label: 'Sleep (h)',
                min: '0',
                max: '24',
                step: '0.01',
                hint: 'Enter a value between 0 and 24 hours.'
            },
            steps: {
                label: 'Daily steps (steps/day)',
                min: '0',
                max: '200000',
                step: '1',
                hint: 'Enter a whole number between 0 and 200,000 steps.'
            }
        };
        let reviewedEntry = null;

        async function readResponse(response) {
            const data = await response.json();
            if (!response.ok) {
                if (response.status === 401) {
                    window.location.href = 'index.php';
                }
                throw new Error(data.error || 'Unable to load your health entries.');
            }
            return data;
        }

        function showMessage(text, success = false) {
            message.textContent = text;
            message.classList.toggle('border-[#cce5d1]', success);
            message.classList.toggle('bg-[#f1faf3]', success);
            message.classList.toggle('text-[#245c36]', success);
            message.classList.toggle('border-[#f0d2cf]', !success);
            message.classList.toggle('bg-[#fff5f4]', !success);
            message.classList.toggle('text-[#832e28]', !success);
            message.hidden = false;
        }

        function formatMeasurement(entry) {
            if (entry.category === 'blood_pressure') {
                return `${entry.measurement} mmHg`;
            }
            const units = { weight: 'kg', sleep: 'h', steps: 'steps/day' };
            return `${entry.measurement} ${units[entry.category] || ''}`.trim();
        }

        function addEntryToList(entry) {
            const item = document.createElement('li');
            item.className = 'rounded-[10px] border border-[#e6e9e1] bg-[#fbfcfa] p-[14px]';
            const title = document.createElement('strong');
            title.className = 'mb-[5px] block text-sm font-bold text-[#26372d]';
            title.textContent = categoryLabels[entry.category] || entry.category;
            const value = document.createElement('p');
            value.className = 'm-0 text-[13px] leading-[1.5] text-[#68756c]';
            value.textContent = formatMeasurement(entry);
            const date = document.createElement('time');
            date.className = 'mt-[5px] block text-[13px] leading-[1.5] text-[#68756c]';
            date.dateTime = entry.date;
            date.textContent = `Measured: ${entry.date}`;
            item.append(title, value, date);
            entryList.prepend(item);
            emptyMessage.hidden = true;
        }

        function renderEntries(entries) {
            entryList.replaceChildren();
            entries.forEach(addEntryToList);
            emptyMessage.hidden = entries.length !== 0;
        }

        categoryInput.addEventListener('change', () => {
            const category = categoryInput.value;
            const isBloodPressure = category === 'blood_pressure';
            const settings = measurementSettings[category];

            singleField.hidden = isBloodPressure;
            bloodPressureFields.hidden = !isBloodPressure;
            measurementInput.disabled = !settings;
            measurementInput.required = !isBloodPressure && Boolean(settings);
            systolicInput.disabled = !isBloodPressure;
            diastolicInput.disabled = !isBloodPressure;

            if (settings) {
                measurementLabel.textContent = settings.label;
                measurementInput.min = settings.min;
                measurementInput.max = settings.max;
                measurementInput.step = settings.step;
                measurementHint.textContent = settings.hint;
            } else {
                measurementLabel.textContent = 'Measurement';
                measurementHint.textContent = 'Choose a parameter to see the expected unit and range.';
            }

            measurementInput.value = '';
            systolicInput.value = '';
            diastolicInput.value = '';
        });

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            if (!form.reportValidity()) {
                return;
            }

            const category = categoryInput.value;
            const values = new FormData(form);
            reviewedEntry = {
                category,
                date: values.get('date')
            };

            if (category === 'blood_pressure') {
                reviewedEntry.systolic = Number(values.get('systolic'));
                reviewedEntry.diastolic = Number(values.get('diastolic'));
                if (
                    reviewedEntry.systolic <= reviewedEntry.diastolic
                    || reviewedEntry.systolic < 50
                    || reviewedEntry.systolic > 300
                    || reviewedEntry.diastolic < 30
                    || reviewedEntry.diastolic > 200
                ) {
                    showMessage('Check the values: systolic must be higher than diastolic (50–300 / 30–200 mmHg).');
                    return;
                }
            } else {
                reviewedEntry.measurement = Number(values.get('measurement'));
                const settings = measurementSettings[category];
                if (
                    !Number.isFinite(reviewedEntry.measurement)
                    || reviewedEntry.measurement < Number(settings.min)
                    || reviewedEntry.measurement > Number(settings.max)
                    || (category === 'steps' && !Number.isInteger(reviewedEntry.measurement))
                ) {
                    showMessage('The measurement is outside the valid range for this parameter.');
                    return;
                }
            }

            document.getElementById('review-category').textContent = categoryLabels[category];
            document.getElementById('review-measurement').textContent = category === 'blood_pressure'
                ? `${reviewedEntry.systolic}/${reviewedEntry.diastolic} mmHg`
                : formatMeasurement({ category, measurement: reviewedEntry.measurement });
            document.getElementById('review-date').textContent = reviewedEntry.date;
            form.hidden = true;
            review.hidden = false;
            message.hidden = true;
        });

        document.getElementById('edit-entry').addEventListener('click', () => {
            review.hidden = true;
            form.hidden = false;
        });

        confirmButton.addEventListener('click', async () => {
            if (!reviewedEntry) {
                showMessage('Review the measurement before saving it.');
                return;
            }

            confirmButton.disabled = true;
            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'create_entry', ...reviewedEntry })
                });
                const data = await readResponse(response);
                addEntryToList(data.entry);
                form.reset();
                categoryInput.dispatchEvent(new Event('change'));
                reviewedEntry = null;
                review.hidden = true;
                form.hidden = false;
                showMessage('Your measurement has been verified and saved.', true);
            } catch (error) {
                showMessage(error.message || 'Unable to save the entry. Please try again.');
            } finally {
                confirmButton.disabled = false;
            }
        });

        const today = new Date();
        const localToday = new Date(today.getTime() - today.getTimezoneOffset() * 60000)
            .toISOString()
            .slice(0, 10);
        document.getElementById('date').max = localToday;
        document.getElementById('date').value = localToday;

        fetch('api.php?resource=health_markers')
            .then(readResponse)
            .then((data) => {
                renderEntries(data.entries);
                loadingMessage.hidden = true;
                page.hidden = false;
            })
            .catch((error) => {
                loadingMessage.textContent = error.message || 'Unable to load your health entries.';
            });
    </script>
</body>
</html>
