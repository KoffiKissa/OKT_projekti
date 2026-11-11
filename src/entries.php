<?php
$pageTitle = 'New health entry';
include __DIR__ . '/includes/header.html';
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

    <script>
        const page = document.getElementById('entries-page');
        const loadingMessage = document.getElementById('loading-message');
        const message = document.getElementById('entry-message');
        const undoButton = document.getElementById('undo-entry-button');
        const form = document.getElementById('entry-form');
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
        const submitButton = document.getElementById('entry-submit-button');
        const cancelEditButton = document.getElementById('cancel-edit-button');
        const formHeading = document.getElementById('entry-form-heading');
        const formIntro = document.getElementById('entry-form-intro');
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
        let entryToSave = null;
        let editingEntryId = null;
        let undoEntry = null;
        let entries = [];

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

        function showMessage(text, success = false, showUndo = false) {
            message.textContent = text;
            undoButton.hidden = !showUndo;
            if (showUndo) {
                message.append(undoButton);
            }
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
            item.className = Number(entry.id) === editingEntryId
                ? 'flex items-start justify-between gap-3 rounded-lg border border-[#3a7752] bg-white p-6 shadow-sm ring-2 ring-[#9fcba9]'
                : 'flex items-start justify-between gap-3 rounded-lg border border-[#E5E7EB] bg-white p-6 shadow-sm transition hover:shadow-md';
            const details = document.createElement('div');
            details.className = 'min-w-0';
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
            const editButton = document.createElement('button');
            editButton.className = 'min-h-[38px] shrink-0 cursor-pointer rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-2 text-[13px] font-bold text-[#26372d] hover:bg-[#f4f6f1] focus-visible:outline focus-visible:outline-[3px] focus-visible:outline-[#9fcba9] focus-visible:outline-offset-[3px]';
            editButton.type = 'button';
            editButton.textContent = 'Edit entry';
            editButton.addEventListener('click', () => startEditingEntry(entry));
            details.append(title, value, date);
            item.append(details, editButton);
            return item;
        }

        function renderEntries(entries) {
            entryList.replaceChildren();
            [...entries]
                .sort((left, right) => right.date.localeCompare(left.date) || Number(right.id) - Number(left.id))
                .forEach((entry) => entryList.append(addEntryToList(entry)));
            emptyMessage.hidden = entries.length !== 0;
        }

        function resetEntryForm(preserveUndo = false) {
            const wasEditing = editingEntryId !== null;
            form.reset();
            categoryInput.dispatchEvent(new Event('change'));
            document.getElementById('date').value = localToday;
            editingEntryId = null;
            entryToSave = null;
            formHeading.textContent = 'New entry';
            formIntro.textContent = 'Enter a health measurement and save it.';
            submitButton.textContent = 'Save entry';
            cancelEditButton.hidden = true;
            form.hidden = false;
            if (!preserveUndo) {
                undoEntry = null;
                undoButton.hidden = true;
            }
            if (wasEditing) {
                renderEntries(entries);
            }
        }

        function startEditingEntry(entry) {
            editingEntryId = Number(entry.id);
            entryToSave = null;
            undoEntry = null;
            undoButton.hidden = true;
            renderEntries(entries);
            categoryInput.value = entry.category;
            categoryInput.dispatchEvent(new Event('change'));
            if (entry.category === 'blood_pressure') {
                const [systolic, diastolic] = entry.measurement.split('/');
                systolicInput.value = systolic;
                diastolicInput.value = diastolic;
            } else {
                measurementInput.value = entry.measurement;
            }
            document.getElementById('date').value = entry.date;
            formHeading.textContent = 'Edit entry';
            formIntro.textContent = 'Correct the measurement or date, then save your changes.';
            submitButton.textContent = 'Confirm changes';
            cancelEditButton.hidden = false;
            form.hidden = false;
            message.hidden = true;
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        async function saveEntry(isEditing) {
            const entryId = editingEntryId;
            const previousEntry = isEditing
                ? entries.find((entry) => Number(entry.id) === entryId)
                : null;
            if (isEditing && !previousEntry) {
                showMessage('The entry could not be found. Please reload your entries and try again.');
                return;
            }

            submitButton.disabled = true;
            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: isEditing ? 'update_entry' : 'create_entry',
                        ...(isEditing ? { id: entryId } : {}),
                        ...entryToSave
                    })
                });
                const data = await readResponse(response);
                if (isEditing) {
                    entries = entries.map((entry) => Number(entry.id) === entryId ? data.entry : entry);
                    undoEntry = { type: 'restore', entry: { ...previousEntry } };
                } else {
                    entries.push(data.entry);
                    undoEntry = { type: 'delete', entry: data.entry };
                }
                resetEntryForm(true);
                renderEntries(entries);
                undoButton.textContent = isEditing ? 'Undo changes' : 'Undo entry';
                showMessage(
                    isEditing ? 'Your changes have been saved.' : 'Your entry has been saved.',
                    true,
                    true
                );
            } catch (error) {
                showMessage(error.message || 'Unable to save the entry. Please try again.');
            } finally {
                submitButton.disabled = false;
            }
        }

        categoryInput.addEventListener('change', () => {
            const category = categoryInput.value;
            const isBloodPressure = category === 'blood_pressure';
            const settings = measurementSettings[category];

            singleField.hidden = isBloodPressure;
            bloodPressureFields.classList.toggle('grid', isBloodPressure);
            bloodPressureFields.hidden = !isBloodPressure;
            measurementInput.disabled = !settings;
            measurementInput.required = !isBloodPressure && Boolean(settings);
            systolicInput.disabled = !isBloodPressure;
            diastolicInput.disabled = !isBloodPressure;

            if (settings) {
                measurementLabel.textContent = settings.label;
                measurementInput.placeholder = 'Enter a measurement';
                measurementInput.min = settings.min;
                measurementInput.max = settings.max;
                measurementInput.step = settings.step;
                measurementHint.textContent = settings.hint;
            } else {
                measurementLabel.textContent = 'Measurement';
                measurementInput.placeholder = 'Choose a parameter first';
                measurementHint.textContent = 'Choose a parameter to see the expected unit and range.';
            }

            measurementInput.value = '';
            systolicInput.value = '';
            diastolicInput.value = '';
        });

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (!form.reportValidity()) {
                return;
            }

            const category = categoryInput.value;
            const values = new FormData(form);
            entryToSave = {
                category,
                date: values.get('date')
            };

            if (category === 'blood_pressure') {
                entryToSave.systolic = Number(values.get('systolic'));
                entryToSave.diastolic = Number(values.get('diastolic'));
                if (
                    entryToSave.systolic <= entryToSave.diastolic
                    || entryToSave.systolic < 50
                    || entryToSave.systolic > 300
                    || entryToSave.diastolic < 30
                    || entryToSave.diastolic > 200
                ) {
                    showMessage('Check the values: systolic must be higher than diastolic (50–300 / 30–200 mmHg).');
                    return;
                }
            } else {
                entryToSave.measurement = Number(values.get('measurement'));
                const settings = measurementSettings[category];
                if (
                    !Number.isFinite(entryToSave.measurement)
                    || entryToSave.measurement < Number(settings.min)
                    || entryToSave.measurement > Number(settings.max)
                    || (category === 'steps' && !Number.isInteger(entryToSave.measurement))
                ) {
                    showMessage('The measurement is outside the valid range for this parameter.');
                    return;
                }
            }

            message.hidden = true;
            await saveEntry(editingEntryId !== null);
        });

        undoButton.addEventListener('click', async () => {
            if (!undoEntry) {
                return;
            }

            const undoAction = undoEntry;
            const entryToUndo = undoAction.entry;
            undoButton.disabled = true;
            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(undoAction.type === 'delete'
                        ? { action: 'delete_entry', id: Number(entryToUndo.id) }
                        : entryToUndo.category === 'blood_pressure'
                            ? {
                                action: 'update_entry',
                                id: Number(entryToUndo.id),
                                category: entryToUndo.category,
                                systolic: Number(entryToUndo.measurement.split('/')[0]),
                                diastolic: Number(entryToUndo.measurement.split('/')[1]),
                                date: entryToUndo.date
                            }
                            : {
                                action: 'update_entry',
                                id: Number(entryToUndo.id),
                                category: entryToUndo.category,
                                measurement: Number(entryToUndo.measurement),
                                date: entryToUndo.date
                            })
                });
                const data = await readResponse(response);
                if (undoAction.type === 'delete') {
                    entries = entries.filter((entry) => Number(entry.id) !== Number(data.id));
                } else {
                    entries = entries.map((entry) => Number(entry.id) === Number(data.entry.id) ? data.entry : entry);
                }
                undoEntry = null;
                undoButton.hidden = true;
                renderEntries(entries);
                showMessage(undoAction.type === 'delete' ? 'Your entry has been removed.' : 'Your changes have been reverted.', true);
            } catch (error) {
                showMessage(error.message || 'Unable to revert the changes. Please try again.', false, true);
            } finally {
                undoButton.disabled = false;
            }
        });

        cancelEditButton.addEventListener('click', resetEntryForm);

        const today = new Date();
        const localToday = new Date(today.getTime() - today.getTimezoneOffset() * 60000)
            .toISOString()
            .slice(0, 10);
        document.getElementById('date').max = localToday;
        document.getElementById('date').value = localToday;

        fetch('api.php?resource=health_markers')
            .then(readResponse)
            .then((data) => {
                entries = data.entries;
                renderEntries(entries);
                loadingMessage.hidden = true;
                page.hidden = false;
            })
            .catch((error) => {
                loadingMessage.textContent = error.message || 'Unable to load your health entries.';
            });
    </script>
<?php include __DIR__ . '/includes/footer.php'; ?>
