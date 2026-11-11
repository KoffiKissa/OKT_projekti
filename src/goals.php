<?php
$pageTitle = 'Your health goals';
include __DIR__ . '/includes/header.html';
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
                    <option value="sleep">Sleep (h)</option>
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

    <script>
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
    </script>
<?php include __DIR__ . '/includes/footer.php'; ?>
