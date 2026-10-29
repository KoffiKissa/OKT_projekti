<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f4f6f1">
    <title>New health entry</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main id="entries-page" class="card goal-page-card" hidden>
        <div class="brand"><span class="brand-mark" aria-hidden="true">+</span> WELLBEING</div>
        <div class="goal-page-header">
            <div>
                <h1>New entry</h1>
                <p class="intro">Record a health measurement and check it before saving.</p>
            </div>
            <a class="back-link" href="index.php">Back to account</a>
        </div>

        <div id="entry-message" class="message" role="status" hidden></div>

        <form id="entry-form">
            <div class="field">
                <label for="category">Health parameter</label>
                <select id="category" name="category" required>
                    <option value="">Choose a parameter</option>
                    <option value="weight">Weight (kg)</option>
                    <option value="sleep">Sleep (h)</option>
                    <option value="steps">Daily steps (steps/day)</option>
                    <option value="blood_pressure">Blood pressure (mmHg)</option>
                </select>
            </div>

            <div id="single-measurement-field" class="field">
                <label id="measurement-label" for="measurement">Measurement</label>
                <input id="measurement" name="measurement" type="number" min="0" max="500"
                       step="0.01" inputmode="decimal" placeholder="Choose a parameter first" disabled required>
                <span id="measurement-hint" class="field-hint">Choose a parameter to see the expected unit and range.</span>
            </div>

            <div id="blood-pressure-fields" class="entry-number-row" hidden>
                <div class="field">
                    <label for="systolic">Systolic (mmHg)</label>
                    <input id="systolic" name="systolic" type="number" min="50" max="300"
                           step="1" inputmode="numeric" placeholder="e.g. 120" disabled required>
                </div>
                <div class="field">
                    <label for="diastolic">Diastolic (mmHg)</label>
                    <input id="diastolic" name="diastolic" type="number" min="30" max="200"
                           step="1" inputmode="numeric" placeholder="e.g. 80" disabled required>
                </div>
                <span class="field-hint">Enter whole numbers; systolic must be higher than diastolic.</span>
            </div>

            <div class="field">
                <label for="date">Measurement date</label>
                <input id="date" name="date" type="date" required>
            </div>
            <button type="submit">Review entry</button>
        </form>

        <section id="entry-review" class="goal-review" aria-labelledby="review-heading" hidden>
            <h2 id="review-heading">Review your entry</h2>
            <p><strong>Health parameter:</strong> <span id="review-category"></span></p>
            <p><strong>Measurement:</strong> <span id="review-measurement"></span></p>
            <p><strong>Date:</strong> <span id="review-date"></span></p>
            <div class="goal-review-actions">
                <button id="edit-entry" class="secondary-button" type="button">Make changes</button>
                <button id="confirm-entry" type="button">Confirm and save</button>
            </div>
        </section>

        <section aria-labelledby="entry-history-heading">
            <h2 id="entry-history-heading">Your recent entries</h2>
            <p id="entry-empty" class="goal-empty" hidden>You haven't recorded any measurements yet.</p>
            <ul id="entry-list" class="goal-list"></ul>
        </section>
    </main>
    <p id="loading-message" class="intro">Checking your sign-in...</p>

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
            message.classList.toggle('success', success);
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
            item.className = 'goal-item';
            const title = document.createElement('strong');
            title.textContent = categoryLabels[entry.category] || entry.category;
            const value = document.createElement('p');
            value.textContent = formatMeasurement(entry);
            const date = document.createElement('time');
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
