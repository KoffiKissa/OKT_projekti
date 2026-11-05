<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f4f6f1">
    <title>Your health goals</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen min-w-[320px] bg-gradient-to-br from-[#e1eee2] via-[#f4f6f1] to-[#e8eee1] p-6 font-['Segoe_UI',Arial,sans-serif] text-[#1b2b21] [font-synthesis:none] [text-rendering:optimizeLegibility] max-[480px]:p-[14px]">
    <main id="goals-page" class="mx-auto my-6 w-full max-w-[1000px] rounded-[22px] border border-[#e6e9e1] bg-white p-11 shadow-[0_24px_70px_#253c2c14] max-[480px]:my-0 max-[480px]:rounded-[17px] max-[480px]:px-[26px] max-[480px]:py-8" hidden>
        <div class="mb-[26px] inline-flex items-center gap-[10px] text-sm font-bold tracking-[0.06em] text-[#193d2d]"><span class="grid h-[34px] w-[34px] place-items-center rounded-[11px] bg-[#c9e6c6] text-[21px] text-[#193d2d]" aria-hidden="true">+</span> WELLBEING</div>
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="m-0 text-[30px] font-bold tracking-[-0.04em]">Health goals</h1>
                <p class="mb-7 mt-[9px] text-sm leading-[1.6] text-[#68756c]">Choose a health parameter and set a goal to work toward.</p>
            </div>
            <nav class="flex flex-wrap gap-4">
                <a class="mt-2 whitespace-nowrap text-[13px] font-bold text-[#28603e] no-underline hover:underline" href="dashboard.php">Dashboard</a>
                <a class="mt-2 whitespace-nowrap text-[13px] font-bold text-[#28603e] no-underline hover:underline" href="index.php">Back to account</a>
            </nav>
        </div>

        <div id="goal-message" class="mb-[18px] rounded-[9px] border border-[#f0d2cf] bg-[#fff5f4] px-[14px] py-3 text-[13px] leading-[1.5] text-[#832e28]" role="alert" hidden></div>

        <form id="goal-form" class="grid grid-cols-2 gap-x-5 max-[700px]:grid-cols-1">
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
                <button id="review-goal-button" type="submit" class="mt-0 min-h-[38px] w-auto flex-1 cursor-pointer rounded-[9px] border-0 bg-[#28603e] px-[13px] py-2 text-[13px] font-bold text-white hover:bg-[#1d4d31] focus-visible:outline focus-visible:outline-[3px] focus-visible:outline-[#9fcba9] focus-visible:outline-offset-[3px] disabled:cursor-wait disabled:opacity-70 max-[480px]:w-full">Review goal</button>
                <button id="cancel-edit" type="button" class="mt-0 min-h-[38px] w-auto cursor-pointer rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-2 text-[13px] font-bold text-[#26372d] hover:bg-[#f4f6f1] focus-visible:outline focus-visible:outline-[3px] focus-visible:outline-[#9fcba9] focus-visible:outline-offset-[3px] disabled:cursor-wait disabled:opacity-70 max-[480px]:w-full" hidden>Cancel edit</button>
            </div>
        </form>

        <section id="goal-review" class="mb-[18px] rounded-[10px] border border-[#cce5d1] bg-[#f1faf3] p-4" aria-labelledby="review-heading" hidden>
            <h2 id="review-heading" class="mb-3 mt-0 text-[17px] font-bold text-[#245c36]">Review your goal</h2>
            <p class="mt-[7px] text-sm leading-[1.5] text-[#516c58]"><strong>Name:</strong> <span id="review-name"></span></p>
            <p class="mt-[7px] text-sm leading-[1.5] text-[#516c58]"><strong>Health parameter:</strong> <span id="review-category"></span></p>
            <p class="mt-[7px] text-sm leading-[1.5] text-[#516c58]"><strong>Goal:</strong> <span id="review-goal"></span></p>
            <p class="mt-[7px] text-sm leading-[1.5] text-[#516c58]"><strong>Start date:</strong> <span id="review-start-date"></span></p>
            <p class="mt-[7px] text-sm leading-[1.5] text-[#516c58]"><strong>End date:</strong> <span id="review-done-by-date"></span></p>
            <div class="mt-4 grid grid-cols-2 gap-[10px] max-[480px]:grid-cols-1">
                <button id="edit-goal" class="mt-0 min-h-12 w-auto cursor-pointer rounded-[9px] border border-[#dce2da] bg-white px-[13px] py-2 text-sm font-bold text-[#26372d] hover:bg-[#f4f6f1] focus-visible:outline focus-visible:outline-[3px] focus-visible:outline-[#9fcba9] focus-visible:outline-offset-[3px] disabled:cursor-wait disabled:opacity-70" type="button">Make changes</button>
                <button id="confirm-goal" type="button" class="mt-0 min-h-12 w-full cursor-pointer rounded-[9px] border-0 bg-[#28603e] text-sm font-bold text-white hover:bg-[#1d4d31] focus-visible:outline focus-visible:outline-[3px] focus-visible:outline-[#9fcba9] focus-visible:outline-offset-[3px] disabled:cursor-wait disabled:opacity-70">Confirm and save</button>
            </div>
        </section>

        <section aria-labelledby="saved-goals-heading">
            <h2 id="saved-goals-heading" class="mb-[14px] mt-[26px] text-lg font-bold tracking-[-0.02em]">Your saved goals</h2>
            <p id="goal-empty" class="rounded-[10px] bg-[#f4f6f1] p-[14px] text-sm leading-[1.5] text-[#68756c]" hidden>You haven't added any goals yet.</p>
            <ul id="goal-list" class="m-0 grid list-none gap-3 p-0 md:grid-cols-2"></ul>
        </section>
    </main>
    <p id="loading-message" class="mb-7 mt-[9px] text-sm leading-[1.6] text-[#68756c]">Checking your sign-in...</p>

    <script>
        const page = document.getElementById('goals-page');
        const loadingMessage = document.getElementById('loading-message');
        const message = document.getElementById('goal-message');
        const form = document.getElementById('goal-form');
        const review = document.getElementById('goal-review');
        const goalList = document.getElementById('goal-list');
        const emptyMessage = document.getElementById('goal-empty');
        const confirmButton = document.getElementById('confirm-goal');
        const categoryInput = document.getElementById('category');
        const nameInput = document.getElementById('goal-name');
        const startDateInput = document.getElementById('start-date');
        const doneByDateInput = document.getElementById('done-by-date');
        const goalInput = document.getElementById('goal');
        const goalUnit = document.getElementById('goal-unit');
        const goalHint = document.getElementById('goal-hint');
        const reviewHeading = document.getElementById('review-heading');
        const reviewButton = document.getElementById('review-goal-button');
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
        let reviewedGoal = null;
        let editingGoalId = null;
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

        function addGoalToList(goal) {
            const existing = goalList.querySelector(`[data-goal-id="${goal.id}"]`);
            const item = existing || document.createElement('li');
            item.className = 'rounded-[10px] border border-[#e6e9e1] bg-[#fbfcfa] p-[14px]';
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
            reviewedGoal = null;
            review.hidden = true;
            form.hidden = false;
            form.reset();
            updateDateRangeConstraints();
            setGoalCategory('');
            reviewButton.textContent = 'Review goal';
            cancelEditButton.hidden = true;
            reviewHeading.textContent = 'Review your goal';
        }

        function beginEditing(goal) {
            editingGoalId = goal.id;
            form.reset();
            nameInput.value = goal.goal_name || '';
            setGoalCategory(goal.category);
            goalInput.value = goal.goal;
            startDateInput.value = goal.start_date;
            doneByDateInput.value = goal.done_by_date;
            updateDateRangeConstraints();
            review.hidden = true;
            form.hidden = false;
            reviewButton.textContent = 'Review changes';
            cancelEditButton.hidden = false;
            reviewHeading.textContent = 'Review your changes';
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

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            if (!form.reportValidity()) {
                return;
            }

            const values = new FormData(form);
            const goalValue = Number(values.get('goal'));
            reviewedGoal = {
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

            if (reviewedGoal.start_date > reviewedGoal.done_by_date) {
                doneByDateInput.setCustomValidity('End date must be on or after the start date.');
                doneByDateInput.reportValidity();
                doneByDateInput.setCustomValidity('');
                return;
            }

            document.getElementById('review-name').textContent = reviewedGoal.goal_name;
            document.getElementById('review-category').textContent = categoryLabels[reviewedGoal.category];
            document.getElementById('review-goal').textContent =
                `${reviewedGoal.goal} ${goalUnits[reviewedGoal.category].unit}`;
            document.getElementById('review-start-date').textContent = reviewedGoal.start_date;
            document.getElementById('review-done-by-date').textContent = reviewedGoal.done_by_date;
            reviewHeading.textContent = editingGoalId === null ? 'Review your goal' : 'Review your changes';
            confirmButton.textContent = editingGoalId === null ? 'Confirm and save' : 'Confirm changes';
            form.hidden = true;
            review.hidden = false;
            message.hidden = true;
        });

        document.getElementById('edit-goal').addEventListener('click', () => {
            review.hidden = true;
            form.hidden = false;
        });

        cancelEditButton.addEventListener('click', stopEditing);

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

        confirmButton.addEventListener('click', async () => {
            if (!reviewedGoal) {
                showMessage('Review your goal before saving it.');
                return;
            }

            confirmButton.disabled = true;
            try {
                const action = editingGoalId === null ? 'create_goal' : 'update_goal';
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action,
                        ...(editingGoalId === null ? {} : { id: editingGoalId }),
                        ...reviewedGoal
                    })
                });
                const data = await readResponse(response);
                addGoalToList(data.goal);
                stopEditing();
                showMessage(action === 'create_goal' ? 'Your goal has been verified and saved.' : 'Your goal has been updated.', true);
            } catch (error) {
                showMessage(error.message || 'Unable to save your goal. Please try again.');
            } finally {
                confirmButton.disabled = false;
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
</body>
</html>
