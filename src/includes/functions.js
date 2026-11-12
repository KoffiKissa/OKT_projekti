(function initializeNavigation() {
    if (!document.getElementById('site-navigation-links')) {
        return;
    }

    const siteNavigationLinks = document.getElementById('site-navigation-links');
    const siteNavigationUser = document.getElementById('site-navigation-user');
    let navigationCheckVersion = 0;

    window.setSiteNavigationVisible = (isVisible, username = '') => {
        navigationCheckVersion += 1;
        siteNavigationLinks.style.display = isVisible ? 'flex' : 'none';
        siteNavigationUser.style.display = isVisible ? 'block' : 'none';
        siteNavigationUser.textContent = isVisible && username ? `Logged in as ${username}` : '';
    };

    const currentNavigationCheck = navigationCheckVersion;
    fetch('api.php')
        .then(async (response) => {
            if (response.status === 401) {
                return false;
            }
            if (!response.ok) {
                throw new Error('Unable to verify sign-in status for navigation.');
            }
            const data = await response.json();
            return data.user || null;
        })
        .then((user) => {
            if (currentNavigationCheck === navigationCheckVersion) {
                window.setSiteNavigationVisible(Boolean(user), user ? user.username : '');
            }
        })
        .catch((error) => console.error(error));
})();

(function initializeAccountPage() {
    if (!document.getElementById('login-form')) {
        return;
    }

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
        registerForm.hidden = true;
        message.hidden = true;
        window.setSiteNavigationVisible(true, user.username);
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
        registerForm.querySelectorAll('input, select').forEach((input) => {
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
})();

(function initializeEntriesPage() {
    if (!document.getElementById('entries-page')) {
        return;
    }

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
        const isWeightEntry = entry.category === 'weight';
        const isEditing = Number(entry.id) === editingEntryId;
        item.className = isWeightEntry
            ? `grid items-center gap-3 rounded-lg border ${isEditing ? 'border-[#3a7752] ring-2 ring-[#9fcba9]' : 'border-[#E5E7EB] transition hover:shadow-md'} bg-white p-6 shadow-sm`
            : isEditing
                ? 'flex items-start justify-between gap-3 rounded-lg border border-[#3a7752] bg-white p-6 shadow-sm ring-2 ring-[#9fcba9]'
                : 'flex items-start justify-between gap-3 rounded-lg border border-[#E5E7EB] bg-white p-6 shadow-sm transition hover:shadow-md';
        if (isWeightEntry) {
            item.style.gridTemplateColumns = 'minmax(0, 1fr) auto minmax(0, 1fr)';
        }
        const details = document.createElement('div');
        details.className = 'min-w-0';
        const title = document.createElement('strong');
        title.className = 'mb-[5px] block text-sm font-bold text-[#26372d]';
        title.textContent = categoryLabels[entry.category] || entry.category;
        const value = document.createElement('p');
        value.className = 'm-0 text-[13px] leading-[1.5] text-[#68756c]';
        value.textContent = formatMeasurement(entry);
        const bmi = isWeightEntry ? document.createElement('div') : null;
        if (bmi) {
            bmi.className = 'self-start text-center';
            const bmiLabel = document.createElement('strong');
            bmiLabel.className = 'mb-[5px] block text-sm font-bold text-[#26372d]';
            bmiLabel.textContent = 'BMI';
            const bmiValue = document.createElement('p');
            bmiValue.className = 'm-0 text-[13px] leading-[1.5] text-[#68756c]';
            bmiValue.textContent = entry.bmi === null || entry.bmi === undefined
                ? 'Not available'
                : Number(entry.bmi).toFixed(1);
            bmi.append(bmiLabel, bmiValue);
        }
        const date = document.createElement('time');
        date.className = 'mt-[5px] block text-[13px] leading-[1.5] text-[#68756c]';
        date.dateTime = entry.date;
        date.textContent = `Measured: ${entry.date}`;
        const editButton = document.createElement('button');
        editButton.className = 'min-h-[38px] shrink-0 cursor-pointer rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-2 text-[13px] font-bold text-[#26372d] hover:bg-[#f4f6f1] focus-visible:outline focus-visible:outline-[3px] focus-visible:outline-[#9fcba9] focus-visible:outline-offset-[3px]';
        editButton.type = 'button';
        editButton.textContent = 'Edit entry';
        if (isWeightEntry) {
            editButton.style.justifySelf = 'end';
        }
        editButton.addEventListener('click', () => startEditingEntry(entry));
        details.append(title, value, date);
        item.append(details, ...(bmi ? [bmi] : []), editButton);
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
})();

(function initializeGoalsPage() {
    if (!document.getElementById('goals-page')) {
        return;
    }

    const page = document.getElementById('goals-page');
    const loadingMessage = document.getElementById('loading-message');
    const message = document.getElementById('goal-message');
    const cancelSavedGoalButton = document.getElementById('cancel-saved-goal');
    const form = document.getElementById('goal-form');
    const goalList = document.getElementById('goal-list');
    const emptyMessage = document.getElementById('goal-empty');
    const categoryInput = document.getElementById('category');
    const nameInput = document.getElementById('goal-name');
    const startDateInput = document.getElementById('start-date');
    const doneByDateInput = document.getElementById('done-by-date');
    const goalInput = document.getElementById('goal');
    const goalUnit = document.getElementById('goal-unit');
    const goalHint = document.getElementById('goal-hint');
    const saveButton = document.getElementById('save-goal-button');
    const cancelEditButton = document.getElementById('cancel-edit');
    const categoryLabels = {
        weight: 'Weight (kg)',
        sleep: 'Sleep (h)',
        steps: 'Average daily steps (steps/day)',
        blood_pressure: 'Blood pressure (mmHg)'
    };
    const goalUnits = {
        weight: { label: ' (kg)', unit: 'kg', step: '0.01', hint: 'Enter your target weight in kilograms.' },
        sleep: { label: ' (h)', unit: 'h', step: '0.01', hint: 'Enter your target sleep duration in hours.' },
        steps: { label: ' (steps/day average)', unit: 'steps/day', min: '1', step: '1', hint: 'Enter the average daily steps you want to achieve during this goal period.' },
        blood_pressure: { label: ' (systolic mmHg)', unit: 'systolic mmHg', min: '1', step: '1', hint: 'Enter a whole-number systolic blood pressure target in mmHg.' }
    };
    let goalToSave = null;
    let editingGoalId = null;
    let cancelSavedGoal = null;
    const savedGoals = new Map();

    async function readResponse(response) {
        const data = await response.json();
        if (!response.ok) {
            if (response.status === 401) {
                window.location.href = 'index.php';
            }
            throw new Error(data.error || 'Unable to load your goals.');
        }
        return data;
    }

    function showMessage(text, success = false, showCancel = false) {
        message.textContent = text;
        cancelSavedGoalButton.hidden = !showCancel;
        if (showCancel) {
            message.append(cancelSavedGoalButton);
        } else {
            cancelSavedGoal = null;
        }
        message.classList.toggle('border-[#cce5d1]', success);
        message.classList.toggle('bg-[#f1faf3]', success);
        message.classList.toggle('text-[#245c36]', success);
        message.classList.toggle('border-[#f0d2cf]', !success);
        message.classList.toggle('bg-[#fff5f4]', !success);
        message.classList.toggle('text-[#832e28]', !success);
        message.hidden = false;
    }

    function addGoalToList(goal) {
        const existing = goalList.querySelector(`[data-goal-id="${goal.id}"]`);
        const item = existing || document.createElement('li');
        item.className = 'rounded-lg border border-[#E5E7EB] bg-white p-6 shadow-sm transition hover:shadow-md';
        item.dataset.goalId = goal.id;

        const title = document.createElement('strong');
        title.className = 'mb-[5px] block text-sm font-bold text-[#26372d]';
        title.textContent = goal.goal_name || categoryLabels[goal.category] || goal.category;
        const description = document.createElement('p');
        description.className = 'm-0 text-[13px] leading-[1.5] text-[#68756c]';
        description.textContent = `${categoryLabels[goal.category] || goal.category}: ${goal.goal} ${goalUnits[goal.category]?.unit || ''}`.trim();
        const stepsAverage = document.createElement('p');
        stepsAverage.className = 'mt-[5px] text-[13px] leading-[1.5] text-[#68756c]';
        if (goal.category === 'steps') {
            stepsAverage.textContent = goal.steps_average === null
                ? 'Average steps/day: no entries during this goal period yet.'
                : `Average steps/day: ${Number(goal.steps_average.toFixed(2))}`;
        }
        const startDate = document.createElement('time');
        startDate.className = 'mt-[5px] block text-[13px] leading-[1.5] text-[#68756c]';
        startDate.dateTime = goal.start_date;
        startDate.textContent = `Start date: ${goal.start_date}`;
        const doneByDate = document.createElement('time');
        doneByDate.className = 'mt-[5px] block text-[13px] leading-[1.5] text-[#68756c]';
        doneByDate.dateTime = goal.done_by_date;
        doneByDate.textContent = `End date: ${goal.done_by_date}`;
        const status = document.createElement('p');
        status.className = `mt-[7px] text-[13px] font-semibold ${goal.goal_done ? 'text-[#245c36]' : 'text-[#68756c]'}`;
        status.textContent = goal.goal_done ? 'Completed' : 'In progress';

        const actions = document.createElement('div');
        actions.className = 'mt-3 flex gap-[9px]';
        const editButton = document.createElement('button');
        editButton.type = 'button';
        editButton.className = 'min-h-[38px] w-auto cursor-pointer rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-2 text-[13px] font-bold text-[#26372d] hover:bg-[#f4f6f1] focus-visible:outline focus-visible:outline-[3px] focus-visible:outline-[#9fcba9] focus-visible:outline-offset-[3px] disabled:cursor-wait disabled:opacity-70';
        editButton.dataset.goalAction = 'edit';
        editButton.textContent = 'Modify';
        const deleteButton = document.createElement('button');
        deleteButton.type = 'button';
        deleteButton.className = 'min-h-[38px] w-auto cursor-pointer rounded-[9px] border border-[#f0d2cf] bg-[#fff5f4] px-[13px] py-2 text-[13px] font-bold text-[#832e28] hover:bg-[#fce9e7] focus-visible:outline focus-visible:outline-[3px] focus-visible:outline-[#9fcba9] focus-visible:outline-offset-[3px] disabled:cursor-wait disabled:opacity-70';
        deleteButton.dataset.goalAction = 'delete';
        deleteButton.textContent = 'Delete';
        actions.append(editButton, deleteButton);

        if (!goal.goal_done) {
            const completeButton = document.createElement('button');
            completeButton.type = 'button';
            completeButton.className = 'min-h-[38px] w-auto cursor-pointer rounded-[9px] border border-[#cce5d1] bg-[#f1faf3] px-[13px] py-2 text-[13px] font-bold text-[#245c36] hover:bg-[#e4f4e7] focus-visible:outline focus-visible:outline-[3px] focus-visible:outline-[#9fcba9] focus-visible:outline-offset-[3px] disabled:cursor-wait disabled:opacity-70';
            completeButton.dataset.goalAction = 'complete';
            completeButton.textContent = 'Mark complete';
            actions.prepend(completeButton);
        } else {
            const now = new Date();
            const today = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
            if (goal.done_by_date >= today) {
                const reopenButton = document.createElement('button');
                reopenButton.type = 'button';
                reopenButton.className = 'min-h-[38px] w-auto cursor-pointer rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-2 text-[13px] font-bold text-[#26372d] hover:bg-[#f4f6f1] focus-visible:outline focus-visible:outline-[3px] focus-visible:outline-[#9fcba9] focus-visible:outline-offset-[3px] disabled:cursor-wait disabled:opacity-70';
                reopenButton.dataset.goalAction = 'reopen';
                reopenButton.textContent = 'Mark unfinished';
                actions.prepend(reopenButton);
            }
        }

        item.replaceChildren(
            title,
            description,
            ...(goal.category === 'steps' ? [stepsAverage] : []),
            startDate,
            doneByDate,
            status,
            actions
        );
        if (!existing) {
            goalList.prepend(item);
        }
        savedGoals.set(String(goal.id), goal);
        emptyMessage.hidden = true;
    }

    function renderGoals(goals) {
        goalList.replaceChildren();
        savedGoals.clear();
        goals.forEach(addGoalToList);
        emptyMessage.hidden = goals.length !== 0;
    }

    function setGoalCategory(category) {
        categoryInput.value = category;
        categoryInput.dispatchEvent(new Event('change'));
    }

    function updateDateRangeConstraints() {
        startDateInput.max = doneByDateInput.value;
        doneByDateInput.min = startDateInput.value;
    }

    function stopEditing() {
        editingGoalId = null;
        goalToSave = null;
        form.hidden = false;
        form.reset();
        updateDateRangeConstraints();
        setGoalCategory('');
        saveButton.textContent = 'Save goal';
        cancelEditButton.hidden = true;
    }

    function beginEditing(goal) {
        editingGoalId = goal.id;
        cancelSavedGoal = null;
        cancelSavedGoalButton.hidden = true;
        form.reset();
        nameInput.value = goal.goal_name || '';
        setGoalCategory(goal.category);
        goalInput.value = goal.goal;
        startDateInput.value = goal.start_date;
        doneByDateInput.value = goal.done_by_date;
        updateDateRangeConstraints();
        form.hidden = false;
        saveButton.textContent = 'Save changes';
        cancelEditButton.hidden = false;
        message.hidden = true;
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    categoryInput.addEventListener('change', () => {
        const settings = goalUnits[categoryInput.value];
        goalUnit.textContent = settings?.label || '';
        goalHint.textContent = settings?.hint || 'Choose a health parameter to see its unit.';
        goalInput.min = settings?.min || '0.01';
        goalInput.step = settings?.step || '0.01';
        goalInput.value = '';
    });

    startDateInput.addEventListener('change', updateDateRangeConstraints);
    doneByDateInput.addEventListener('change', updateDateRangeConstraints);

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!form.reportValidity()) {
            return;
        }

        const values = new FormData(form);
        const goalValue = Number(values.get('goal'));
        goalToSave = {
            goal_name: values.get('goal_name').trim(),
            category: values.get('category'),
            goal: goalValue,
            start_date: values.get('start_date'),
            done_by_date: values.get('done_by_date')
        };
        if (!Number.isFinite(goalValue) || goalValue <= 0) {
            goalInput.setCustomValidity('Enter a number greater than zero.');
            goalInput.reportValidity();
            goalInput.setCustomValidity('');
            return;
        }

        if (goalToSave.start_date > goalToSave.done_by_date) {
            doneByDateInput.setCustomValidity('End date must be on or after the start date.');
            doneByDateInput.reportValidity();
            doneByDateInput.setCustomValidity('');
            return;
        }

        message.hidden = true;
        saveButton.disabled = true;
        try {
            const action = editingGoalId === null ? 'create_goal' : 'update_goal';
            const response = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action,
                    ...(editingGoalId === null ? {} : { id: editingGoalId }),
                    ...goalToSave
                })
            });
            const data = await readResponse(response);
            addGoalToList(data.goal);
            stopEditing();
            if (action === 'create_goal') {
                cancelSavedGoal = data.goal;
                showMessage('Your goal has been saved.', true, true);
            } else {
                showMessage('Your goal has been updated.', true);
            }
        } catch (error) {
            showMessage(error.message || 'Unable to save your goal. Please try again.');
        } finally {
            saveButton.disabled = false;
        }
    });

    cancelEditButton.addEventListener('click', stopEditing);

    cancelSavedGoalButton.addEventListener('click', async () => {
        if (!cancelSavedGoal) {
            return;
        }

        const goal = cancelSavedGoal;
        cancelSavedGoalButton.disabled = true;
        try {
            const response = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete_goal', id: goal.id })
            });
            const data = await readResponse(response);
            goalList.querySelector(`[data-goal-id="${data.id}"]`)?.remove();
            savedGoals.delete(String(data.id));
            emptyMessage.hidden = goalList.children.length !== 0;
            stopEditing();
            nameInput.value = goal.goal_name || '';
            setGoalCategory(goal.category);
            goalInput.value = goal.goal;
            startDateInput.value = goal.start_date;
            doneByDateInput.value = goal.done_by_date;
            updateDateRangeConstraints();
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
            showMessage('Goal canceled. Update the details and save again.', true);
        } catch (error) {
            showMessage(error.message || 'Unable to cancel the saved goal. Please try again.', false, true);
        } finally {
            cancelSavedGoalButton.disabled = false;
        }
    });

    goalList.addEventListener('click', async (event) => {
        const button = event.target.closest('button[data-goal-action]');
        if (!button) {
            return;
        }

        const goalId = button.closest('[data-goal-id]').dataset.goalId;
        const goal = savedGoals.get(goalId);
        if (!goal) {
            showMessage('That goal is no longer available. Reload the page and try again.');
            return;
        }

        if (button.dataset.goalAction === 'edit') {
            beginEditing(goal);
            return;
        }

        if (button.dataset.goalAction === 'complete') {
            button.disabled = true;
            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'mark_goal_done', id: goal.id })
                });
                const data = await readResponse(response);
                goal.goal_done = data.goal.goal_done;
                addGoalToList(goal);
                showMessage('Goal marked as complete.', true);
            } catch (error) {
                showMessage(error.message || 'Unable to complete this goal. Please try again.');
            } finally {
                button.disabled = false;
            }
            return;
        }

        if (button.dataset.goalAction === 'reopen') {
            button.disabled = true;
            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'mark_goal_not_done', id: goal.id })
                });
                const data = await readResponse(response);
                goal.goal_done = data.goal.goal_done;
                addGoalToList(goal);
                showMessage('Goal marked as unfinished.', true);
            } catch (error) {
                showMessage(error.message || 'Unable to mark this goal unfinished. Please try again.');
            } finally {
                button.disabled = false;
            }
            return;
        }

        if (!window.confirm('Delete this goal? This cannot be undone.')) {
            return;
        }

        button.disabled = true;
        try {
            const response = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete_goal', id: goal.id })
            });
            await readResponse(response);
            button.closest('[data-goal-id]').remove();
            savedGoals.delete(goalId);
            emptyMessage.hidden = goalList.children.length !== 0;
            showMessage('Goal deleted.', true);
            if (editingGoalId !== null && String(editingGoalId) === goalId) {
                stopEditing();
            }
        } catch (error) {
            showMessage(error.message || 'Unable to delete this goal. Please try again.');
        } finally {
            button.disabled = false;
        }
    });

    fetch('api.php?resource=goals')
        .then(readResponse)
        .then((data) => {
            renderGoals(data.goals);
            loadingMessage.hidden = true;
            page.hidden = false;
        })
        .catch((error) => {
            loadingMessage.textContent = error.message || 'Unable to load your goals.';
        });
})();

(function initializeDashboardPage() {
    if (!document.getElementById('dashboard-page')) {
        return;
    }

    const page = document.getElementById('dashboard-page');
    const loadingMessage = document.getElementById('dashboard-loading');
    const message = document.getElementById('dashboard-message');
    const goalList = document.getElementById('dashboard-goal-list');
    const emptyMessage = document.getElementById('dashboard-empty');
    const categoryLabels = {
        weight: 'Weight',
        sleep: 'Sleep',
        steps: 'Average daily steps',
        blood_pressure: 'Blood pressure'
    };
    const units = {
        weight: 'kg',
        sleep: 'h',
        steps: 'steps/day',
        blood_pressure: 'systolic mmHg'
    };

    async function readResponse(response) {
        const data = await response.json();
        if (!response.ok) {
            if (response.status === 401) {
                window.location.href = 'index.php';
            }
            throw new Error(data.error || 'Unable to load your dashboard.');
        }
        return data;
    }

    function parseEntryValue(entry) {
        if (entry.category === 'blood_pressure') {
            const match = /^(\d+)\/\d+$/.exec(entry.measurement);
            return match ? Number(match[1]) : null;
        }
        const value = Number(entry.measurement);
        return Number.isFinite(value) ? value : null;
    }

    function calculateProgress(goal, entries, today) {
        const matchingEntries = entries.filter((entry) =>
            entry.category === goal.category
            && entry.date >= goal.start_date
            && entry.date <= goal.done_by_date
            && (goal.category !== 'steps' || entry.date <= today)
            && (goal.category !== 'weight' || entry.date <= today)
        );
        if (goal.category === 'weight') {
            const weightEntries = matchingEntries
                .map((entry) => ({
                    date: entry.date,
                    id: Number(entry.id),
                    value: parseEntryValue(entry)
                }))
                .filter((entry) => entry.value !== null)
                .sort((first, second) => first.date.localeCompare(second.date) || first.id - second.id);
            const latestWeight = weightEntries[weightEntries.length - 1];
            return { value: latestWeight ? latestWeight.value : 0, recorded: matchingEntries.length };
        }

        const total = matchingEntries.reduce((sum, entry) => {
            const value = parseEntryValue(entry);
            return value === null ? sum : sum + value;
        }, 0);

        if (goal.category !== 'steps') {
            return { value: total, recorded: matchingEntries.length };
        }

        const start = new Date(`${goal.start_date}T00:00:00Z`);
        const elapsedEnd = goal.done_by_date < today ? goal.done_by_date : today;
        const end = new Date(`${elapsedEnd}T00:00:00Z`);
        const days = elapsedEnd >= goal.start_date
            ? Math.floor((end - start) / 86400000) + 1
            : 0;
        return { value: days > 0 ? total / days : 0, recorded: matchingEntries.length };
    }

    function formatNumber(value) {
        return new Intl.NumberFormat(undefined, { maximumFractionDigits: 2 }).format(value);
    }

    function goalStatus(goal, today) {
        if (goal.goal_done) {
            return { label: 'Completed', className: 'bg-[#e4f4e7] text-[#245c36]' };
        }
        if (goal.done_by_date < today) {
            return { label: 'Past due', className: 'bg-[#fff0ed] text-[#832e28]' };
        }
        if (goal.start_date > today) {
            return { label: 'Not started', className: 'bg-[#f4f6f1] text-[#68756c]' };
        }
        return { label: 'In progress', className: 'bg-[#f4f6f1] text-[#516c58]' };
    }

    function shiftDateByMonth(dateString, monthOffset) {
        const date = new Date(`${dateString}T00:00:00Z`);
        const targetMonth = new Date(Date.UTC(date.getUTCFullYear(), date.getUTCMonth() + monthOffset, 1));
        const lastDay = new Date(Date.UTC(
            targetMonth.getUTCFullYear(),
            targetMonth.getUTCMonth() + 1,
            0
        )).getUTCDate();
        targetMonth.setUTCDate(Math.min(date.getUTCDate(), lastDay));
        return targetMonth.toISOString().slice(0, 10);
    }

    function svgElement(name, attributes = {}) {
        const element = document.createElementNS('http://www.w3.org/2000/svg', name);
        Object.entries(attributes).forEach(([attribute, value]) => {
            element.setAttribute(attribute, String(value));
        });
        return element;
    }

    function renderWeightChart(goal, entries, today) {
        const isActiveGoal = !goal.goal_done
            && goal.start_date <= today
            && goal.done_by_date >= today;
        const startDate = isActiveGoal ? goal.start_date : shiftDateByMonth(today, -1);
        const endDate = isActiveGoal ? goal.done_by_date : today;
        const startTime = new Date(`${startDate}T00:00:00Z`).getTime();
        const endTime = new Date(`${endDate}T00:00:00Z`).getTime();
        const measurements = entries
            .filter((entry) => entry.category === 'weight'
                && entry.date >= startDate
                && entry.date <= endDate
                && entry.date <= today)
            .map((entry) => ({ date: entry.date, value: parseEntryValue(entry), id: Number(entry.id) }))
            .filter((entry) => entry.value !== null)
            .sort((first, second) => first.date.localeCompare(second.date) || first.id - second.id);
        const target = Number(goal.goal);
        const maxMeasurement = measurements.reduce(
            (maximum, entry) => Math.max(maximum, entry.value),
            target
        );
        const yPadding = Math.max((maxMeasurement - target) * 0.12, target * 0.08, 1);
        const chartTop = 12;
        const chartBottom = 104;
        const chartLeft = 34;
        const chartRight = 306;
        const chartMax = maxMeasurement + yPadding;
        const xForDate = (date) => endTime === startTime
            ? (chartLeft + chartRight) / 2
            : chartLeft + ((new Date(`${date}T00:00:00Z`).getTime() - startTime) / (endTime - startTime))
                * (chartRight - chartLeft);
        const yForValue = (value) => chartBottom
            - ((Math.max(value, target) - target) / (chartMax - target)) * (chartBottom - chartTop);

        const figure = document.createElement('figure');
        figure.className = 'mb-0 mt-5 rounded-lg bg-[#F9FAFB] p-4';
        const heading = document.createElement('figcaption');
        heading.className = 'mb-2 flex items-center justify-between gap-2 text-xs font-semibold text-[#26372d]';
        const headingLabel = document.createElement('span');
        headingLabel.textContent = 'Weight trend';
        const dateRange = document.createElement('span');
        dateRange.className = 'text-right text-[10px] font-normal text-[#68756c]';
        dateRange.textContent = isActiveGoal ? 'Active goal period' : 'Last month';
        heading.append(headingLabel, dateRange);

        const chart = svgElement('svg', {
            viewBox: '0 0 320 122',
            class: 'h-32 w-full overflow-visible',
            role: 'img',
            'aria-label': `Weight trend from ${startDate} to ${endDate}. Goal: ${formatNumber(target)} kg.`
        });
        [chartTop, (chartTop + chartBottom) / 2].forEach((y) => {
            chart.append(svgElement('line', {
                x1: chartLeft,
                y1: y,
                x2: chartRight,
                y2: y,
                stroke: '#E5E7EB',
                'stroke-width': 1
            }));
        });
        chart.append(svgElement('line', {
            x1: chartLeft,
            y1: chartBottom,
            x2: chartRight,
            y2: chartBottom,
            stroke: '#16A34A',
            'stroke-width': 1.5,
            'stroke-dasharray': '4 4'
        }));

        const points = measurements.map((entry) => ({
            ...entry,
            x: xForDate(entry.date),
            y: yForValue(entry.value)
        }));
        for (let index = 1; index < points.length; index += 1) {
            const previous = points[index - 1];
            const current = points[index];
            const color = current.value > previous.value
                ? '#DC4C4C'
                : current.value < previous.value
                    ? '#16A34A'
                    : '#94A3B8';
            chart.append(svgElement('line', {
                x1: previous.x,
                y1: previous.y,
                x2: current.x,
                y2: current.y,
                stroke: color,
                'stroke-width': 3,
                'stroke-linecap': 'round'
            }));
        }
        points.forEach((point, index) => {
            const direction = index === 0 ? 0 : point.value - points[index - 1].value;
            const marker = svgElement('circle', {
                cx: point.x,
                cy: point.y,
                r: 4,
                fill: direction > 0 ? '#DC4C4C' : direction < 0 ? '#16A34A' : '#64748B',
                stroke: '#FFFFFF',
                'stroke-width': 2
            });
            const tooltip = svgElement('title');
            tooltip.textContent = `${point.date}: ${formatNumber(point.value)} kg`;
            marker.append(tooltip);
            chart.append(marker);
        });

        const goalLabel = document.createElement('p');
        goalLabel.className = 'mb-0 mt-1 text-[10px] font-semibold text-[#15803D]';
        goalLabel.textContent = `Goal · ${formatNumber(target)} kg`;

        const legend = document.createElement('div');
        legend.className = 'mt-2 flex flex-wrap gap-x-4 gap-y-1 text-[10px] text-[#68756c]';
        [
            ['#DC4C4C', '↑ Increasing'],
            ['#16A34A', '↓ Decreasing']
        ].forEach(([color, label]) => {
            const item = document.createElement('span');
            item.className = 'inline-flex items-center gap-1';
            const marker = document.createElement('span');
            marker.className = 'h-2 w-2 rounded-full';
            marker.style.backgroundColor = color;
            marker.setAttribute('aria-hidden', 'true');
            item.append(marker, document.createTextNode(label));
            legend.append(item);
        });

        const dateLabels = document.createElement('div');
        dateLabels.className = 'mt-1 flex justify-between text-[10px] text-[#68756c]';
        const startLabel = document.createElement('time');
        startLabel.dateTime = startDate;
        startLabel.textContent = startDate;
        const endLabel = document.createElement('time');
        endLabel.dateTime = endDate;
        endLabel.textContent = endDate;
        dateLabels.append(startLabel, endLabel);

        figure.append(heading, chart, goalLabel, legend, dateLabels);
        if (measurements.length === 0) {
            const emptyChartMessage = document.createElement('p');
            emptyChartMessage.className = 'mb-0 mt-2 text-[11px] text-[#68756c]';
            emptyChartMessage.textContent = 'No weight entries recorded during this period yet.';
            figure.append(emptyChartMessage);
        }
        return figure;
    }

    function renderStepsChart(goal, entries, today) {
        const isActiveGoal = !goal.goal_done
            && goal.start_date <= today
            && goal.done_by_date >= today;
        const startDate = isActiveGoal ? goal.start_date : shiftDateByMonth(today, -1);
        const endDate = isActiveGoal ? goal.done_by_date : today;
        const dailyTotals = new Map();
        entries
            .filter((entry) => entry.category === 'steps'
                && entry.date >= startDate
                && entry.date <= endDate
                && entry.date <= today)
            .forEach((entry) => {
                const value = parseEntryValue(entry);
                if (value !== null) {
                    dailyTotals.set(entry.date, (dailyTotals.get(entry.date) || 0) + value);
                }
            });
        const measurements = [...dailyTotals.entries()]
            .map(([date, value]) => ({ date, value }))
            .sort((first, second) => first.date.localeCompare(second.date));
        const target = Number(goal.goal);
        const chartTop = 12;
        const chartBottom = 104;
        const chartLeft = 34;
        const chartRight = 306;
        const startTime = new Date(`${startDate}T00:00:00Z`).getTime();
        const endTime = new Date(`${endDate}T00:00:00Z`).getTime();
        const chartMax = measurements.reduce(
            (maximum, entry) => Math.max(maximum, entry.value),
            target
        ) * 1.1;
        const xForDate = (date) => endTime === startTime
            ? (chartLeft + chartRight) / 2
            : chartLeft + ((new Date(`${date}T00:00:00Z`).getTime() - startTime) / (endTime - startTime))
                * (chartRight - chartLeft);
        const yForValue = (value) => chartBottom
            - (value / chartMax) * (chartBottom - chartTop);

        const figure = document.createElement('figure');
        figure.className = 'mb-0 mt-5 rounded-lg bg-[#F9FAFB] p-4';
        const heading = document.createElement('figcaption');
        heading.className = 'mb-2 flex items-center justify-between gap-2 text-xs font-semibold text-[#26372d]';
        const headingLabel = document.createElement('span');
        headingLabel.textContent = 'Daily steps trend';
        const dateRange = document.createElement('span');
        dateRange.className = 'text-right text-[10px] font-normal text-[#68756c]';
        dateRange.textContent = isActiveGoal ? 'Active goal period' : 'Last month';
        heading.append(headingLabel, dateRange);

        const chart = svgElement('svg', {
            viewBox: '0 0 320 122',
            class: 'h-32 w-full overflow-visible',
            role: 'img',
            'aria-label': `Daily steps from ${startDate} to ${endDate}. Goal: ${formatNumber(target)} steps per day.`
        });
        [chartTop, (chartTop + chartBottom) / 2, chartBottom].forEach((y) => {
            chart.append(svgElement('line', {
                x1: chartLeft,
                y1: y,
                x2: chartRight,
                y2: y,
                stroke: '#E5E7EB',
                'stroke-width': 1
            }));
        });
        const goalY = yForValue(target);
        chart.append(svgElement('line', {
            x1: chartLeft,
            y1: goalY,
            x2: chartRight,
            y2: goalY,
            stroke: '#16A34A',
            'stroke-width': 1.5,
            'stroke-dasharray': '4 4'
        }));

        const points = measurements.map((entry) => ({
            ...entry,
            x: xForDate(entry.date),
            y: yForValue(entry.value)
        }));
        for (let index = 1; index < points.length; index += 1) {
            const previous = points[index - 1];
            const current = points[index];
            chart.append(svgElement('line', {
                x1: previous.x,
                y1: previous.y,
                x2: current.x,
                y2: current.y,
                stroke: '#64748B',
                'stroke-width': 3,
                'stroke-linecap': 'round'
            }));
        }
        points.forEach((point) => {
            const marker = svgElement('circle', {
                cx: point.x,
                cy: point.y,
                r: 4,
                fill: '#64748B',
                stroke: '#FFFFFF',
                'stroke-width': 2
            });
            const tooltip = svgElement('title');
            tooltip.textContent = `${point.date}: ${formatNumber(point.value)} steps`;
            marker.append(tooltip);
            chart.append(marker);
        });

        const goalLabel = document.createElement('p');
        goalLabel.className = 'mb-0 mt-1 text-[10px] font-semibold text-[#15803D]';
        goalLabel.textContent = `Daily goal · ${formatNumber(target)} steps`;

        const legend = document.createElement('div');
        legend.className = 'mt-2 flex flex-wrap gap-x-4 gap-y-1 text-[10px] text-[#68756c]';
        [
            { color: '#64748B', label: 'Daily steps', isLine: false },
            { color: '#16A34A', label: 'Daily goal', isLine: true }
        ].forEach(({ color, label, isLine }) => {
            const item = document.createElement('span');
            item.className = 'inline-flex items-center gap-1';
            const marker = document.createElement('span');
            marker.className = isLine ? 'h-0 w-3 border-t-2 border-dashed' : 'h-2 w-2 rounded-full';
            if (isLine) {
                marker.style.borderTopColor = color;
            } else {
                marker.style.backgroundColor = color;
            }
            marker.setAttribute('aria-hidden', 'true');
            item.append(marker, document.createTextNode(label));
            legend.append(item);
        });

        const dateLabels = document.createElement('div');
        dateLabels.className = 'mt-1 flex justify-between text-[10px] text-[#68756c]';
        const startLabel = document.createElement('time');
        startLabel.dateTime = startDate;
        startLabel.textContent = startDate;
        const endLabel = document.createElement('time');
        endLabel.dateTime = endDate;
        endLabel.textContent = endDate;
        dateLabels.append(startLabel, endLabel);

        figure.append(heading, chart, goalLabel, legend, dateLabels);
        if (measurements.length === 0) {
            const emptyChartMessage = document.createElement('p');
            emptyChartMessage.className = 'mb-0 mt-2 text-[11px] text-[#68756c]';
            emptyChartMessage.textContent = 'No step entries recorded during this period yet.';
            figure.append(emptyChartMessage);
        }
        return figure;
    }

    function renderGoal(goal, entries, today) {
        const progress = calculateProgress(goal, entries, today);
        const target = Number(goal.goal);
        const status = goalStatus(goal, today);
        const item = document.createElement('li');
        item.className = 'overflow-hidden rounded-lg border border-[#E5E7EB] bg-white p-7 shadow-sm transition hover:shadow-md';

        const topRow = document.createElement('div');
        topRow.className = 'flex items-start justify-between gap-3';
        const titleGroup = document.createElement('div');
        const title = document.createElement('h3');
        title.className = 'm-0 text-base font-bold text-[#26372d]';
        title.textContent = goal.goal_name || categoryLabels[goal.category] || goal.category;
        const category = document.createElement('p');
        category.className = 'mb-0 mt-1 text-[12px] text-[#68756c]';
        category.textContent = categoryLabels[goal.category] || goal.category;
        titleGroup.append(title, category);

        const badge = document.createElement('span');
        badge.className = `shrink-0 rounded-full px-3 py-1 text-[11px] font-bold ${status.className}`;
        badge.textContent = status.label;
        topRow.append(titleGroup, badge);

        const value = document.createElement('p');
        value.className = 'mb-0 mt-5 text-[13px] text-[#68756c]';
        value.textContent = goal.category === 'steps'
            ? `Average: ${formatNumber(progress.value)} ${units[goal.category]} / ${formatNumber(target)} ${units[goal.category]} goal`
            : goal.category === 'weight'
                ? (progress.recorded > 0
                    ? `Latest: ${formatNumber(progress.value)} ${units[goal.category]} / ${formatNumber(target)} ${units[goal.category]} goal`
                    : `No weight entries yet / ${formatNumber(target)} ${units[goal.category]} goal`)
            : `Recorded: ${formatNumber(progress.value)} ${units[goal.category]} / ${formatNumber(target)} ${units[goal.category]} goal`;

        const weightChart = goal.category === 'weight'
            ? renderWeightChart(goal, entries, today)
            : null;
        const stepsChart = goal.category === 'steps'
            ? renderStepsChart(goal, entries, today)
            : null;

        const progressSection = document.createElement('div');
        if (goal.category === 'weight') {
            const remaining = document.createElement('p');
            remaining.className = 'mb-0 mt-3 text-right text-[13px] font-semibold text-[#68756c]';
            remaining.textContent = progress.recorded > 0
                ? `${formatNumber(Math.max(progress.value - target, 0))} ${units.weight} to go`
                : 'Record a weight to see how many kg are left.';
            progressSection.append(remaining);
        } else {
            const percent = target > 0 ? Math.min((progress.value / target) * 100, 100) : 0;
            const progressTrack = document.createElement('div');
            progressTrack.className = 'mt-3 h-2 overflow-hidden rounded-full bg-[#E5E7EB]';
            progressTrack.setAttribute('role', 'progressbar');
            progressTrack.setAttribute('aria-label', `${title.textContent} progress`);
            progressTrack.setAttribute('aria-valuemin', '0');
            progressTrack.setAttribute('aria-valuemax', '100');
            progressTrack.setAttribute('aria-valuenow', String(Math.round(percent)));
            const progressFill = document.createElement('div');
            progressFill.className = `h-full rounded-full ${goal.goal_done ? 'bg-[#3a7752]' : 'bg-[#76a87b]'}`;
            progressFill.style.width = `${percent}%`;
            progressTrack.append(progressFill);

            const progressCaption = document.createElement('p');
            progressCaption.className = 'mb-0 mt-2 text-right text-[11px] font-semibold text-[#68756c]';
            progressCaption.textContent = `${formatNumber(percent)}%`;
            progressSection.append(progressTrack, progressCaption);
        }

        const dates = document.createElement('p');
        dates.className = 'mb-0 mt-4 text-[12px] text-[#68756c]';
        dates.textContent = `Dates: ${goal.start_date} – ${goal.done_by_date}`;

        const footer = document.createElement('div');
        footer.className = 'mt-4 flex items-center justify-between gap-3 border-t border-[#E5E7EB] pt-3';
        const entryCount = document.createElement('span');
        entryCount.className = 'text-[11px] text-[#68756c]';
        entryCount.textContent = `${progress.recorded} ${progress.recorded === 1 ? 'entry' : 'entries'} in period`;
        const manageLink = document.createElement('a');
        manageLink.className = 'text-[12px] font-bold text-[#28603e] no-underline hover:underline';
        manageLink.href = 'goals.php';
        manageLink.textContent = 'Manage';
        footer.append(entryCount, manageLink);

        item.append(
            topRow,
            value,
            ...(weightChart ? [weightChart] : []),
            ...(stepsChart ? [stepsChart] : []),
            progressSection,
            dates,
            footer
        );
        return item;
    }

    function localToday() {
        const now = new Date();
        return `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
    }

    async function loadDashboard() {
        try {
            const [goalResponse, entryResponse, bmiResponse] = await Promise.all([
                fetch('api.php?resource=goals'),
                fetch('api.php?resource=health_markers'),
                fetch('api.php?resource=bmi')
            ]);
            const [goalData, entryData, bmiData] = await Promise.all([
                readResponse(goalResponse),
                readResponse(entryResponse),
                readResponse(bmiResponse)
            ]);
            const goals = goalData.goals;
            const entries = entryData.entries;
            document.getElementById('current-bmi').textContent = bmiData.bmi === null
                ? 'Not available'
                : Number(bmiData.bmi).toFixed(1);
            const completed = goals.filter((goal) => goal.goal_done).length;
            document.getElementById('total-goals').textContent = goals.length;
            document.getElementById('completed-goals').textContent = completed;
            document.getElementById('active-goals').textContent = goals.length - completed;
            const today = localToday();
            goalList.replaceChildren(...goals.map((goal) => renderGoal(goal, entries, today)));
            emptyMessage.hidden = goals.length !== 0;
            page.hidden = false;
            loadingMessage.hidden = true;
        } catch (error) {
            loadingMessage.hidden = true;
            message.textContent = error.message || 'Unable to load your dashboard.';
            message.hidden = false;
            page.hidden = false;
        }
    }

    loadDashboard();
})();
