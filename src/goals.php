<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f4f6f1">
    <title>Your health goals</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main id="goals-page" class="card goal-page-card" hidden>
        <div class="brand"><span class="brand-mark" aria-hidden="true">+</span> WELLBEING</div>
        <div class="goal-page-header">
            <div>
                <h1>Health goals</h1>
                <p class="intro">Choose a health parameter and set a goal to work toward.</p>
            </div>
            <a class="back-link" href="index.php">Back to account</a>
        </div>

        <div id="goal-message" class="message" role="alert" hidden></div>

        <form id="goal-form" class="goal-form">
            <div class="field">
                <label for="category">Health parameter</label>
                <select id="category" name="category" required>
                    <option value="">Choose a parameter</option>
                    <option value="weight">Weight (kg)</option>
                    <option value="sleep">Sleep (h)</option>
                    <option value="steps">Daily steps</option>
                    <option value="blood_pressure">Blood pressure (mmHg)</option>
                </select>
            </div>
            <div class="field">
                <label for="goal">Target value <span id="goal-unit"></span></label>
                <input id="goal" name="goal" type="number" min="0.01" max="99999999.99"
                       step="0.01" inputmode="decimal" placeholder="Enter a number" required>
                <span id="goal-hint" class="field-hint">Choose a health parameter to see its unit.</span>
            </div>
            <div class="field">
                <label for="date">Target date</label>
                <input id="date" name="date" type="date" required>
            </div>
            <div class="goal-form-actions">
                <button id="review-goal-button" type="submit">Review goal</button>
                <button id="cancel-edit" class="secondary-button" type="button" hidden>Cancel edit</button>
            </div>
        </form>

        <section id="goal-review" class="goal-review" aria-labelledby="review-heading" hidden>
            <h2 id="review-heading">Review your goal</h2>
            <p><strong>Health parameter:</strong> <span id="review-category"></span></p>
            <p><strong>Goal:</strong> <span id="review-goal"></span></p>
            <p><strong>Target date:</strong> <span id="review-date"></span></p>
            <div class="goal-review-actions">
                <button id="edit-goal" class="secondary-button" type="button">Make changes</button>
                <button id="confirm-goal" type="button">Confirm and save</button>
            </div>
        </section>

        <section aria-labelledby="saved-goals-heading">
            <h2 id="saved-goals-heading">Your saved goals</h2>
            <p id="goal-empty" class="goal-empty" hidden>You haven't added any goals yet.</p>
            <ul id="goal-list" class="goal-list"></ul>
        </section>
    </main>
    <p id="loading-message" class="intro">Checking your sign-in...</p>

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
        const goalInput = document.getElementById('goal');
        const goalUnit = document.getElementById('goal-unit');
        const goalHint = document.getElementById('goal-hint');
        const reviewHeading = document.getElementById('review-heading');
        const reviewButton = document.getElementById('review-goal-button');
        const cancelEditButton = document.getElementById('cancel-edit');
        const categoryLabels = {
            weight: 'Weight (kg)',
            sleep: 'Sleep (h)',
            steps: 'Daily steps (steps/day)',
            blood_pressure: 'Blood pressure (mmHg)'
        };
        const goalUnits = {
            weight: { label: ' (kg)', unit: 'kg', step: '0.01', hint: 'Enter your target weight in kilograms.' },
            sleep: { label: ' (h)', unit: 'h', step: '0.01', hint: 'Enter your target sleep duration in hours.' },
            steps: { label: ' (steps/day)', unit: 'steps/day', min: '1', step: '1', hint: 'Enter a whole number of steps per day.' },
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
            message.classList.toggle('success', success);
            message.hidden = false;
        }

        function addGoalToList(goal) {
            const existing = goalList.querySelector(`[data-goal-id="${goal.id}"]`);
            const item = existing || document.createElement('li');
            item.className = 'goal-item';
            item.dataset.goalId = goal.id;

            const title = document.createElement('strong');
            title.textContent = categoryLabels[goal.category] || goal.category;
            const description = document.createElement('p');
            description.textContent = `${goal.goal} ${goalUnits[goal.category]?.unit || ''}`.trim();
            const date = document.createElement('time');
            date.dateTime = goal.date;
            date.textContent = `Target date: ${goal.date}`;

            const actions = document.createElement('div');
            actions.className = 'goal-item-actions';
            const editButton = document.createElement('button');
            editButton.type = 'button';
            editButton.className = 'secondary-button';
            editButton.dataset.goalAction = 'edit';
            editButton.textContent = 'Modify';
            const deleteButton = document.createElement('button');
            deleteButton.type = 'button';
            deleteButton.className = 'delete-button';
            deleteButton.dataset.goalAction = 'delete';
            deleteButton.textContent = 'Delete';
            actions.append(editButton, deleteButton);

            item.replaceChildren(title, description, date, actions);
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

        function stopEditing() {
            editingGoalId = null;
            reviewedGoal = null;
            review.hidden = true;
            form.hidden = false;
            form.reset();
            setGoalCategory('');
            reviewButton.textContent = 'Review goal';
            cancelEditButton.hidden = true;
            reviewHeading.textContent = 'Review your goal';
        }

        function beginEditing(goal) {
            editingGoalId = goal.id;
            form.reset();
            setGoalCategory(goal.category);
            goalInput.value = goal.goal;
            document.getElementById('date').value = goal.date;
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

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            if (!form.reportValidity()) {
                return;
            }

            const values = new FormData(form);
            const goalValue = Number(values.get('goal'));
            reviewedGoal = {
                category: values.get('category'),
                goal: goalValue,
                date: values.get('date')
            };
            if (!Number.isFinite(goalValue) || goalValue <= 0) {
                goalInput.setCustomValidity('Enter a number greater than zero.');
                goalInput.reportValidity();
                goalInput.setCustomValidity('');
                return;
            }

            document.getElementById('review-category').textContent = categoryLabels[reviewedGoal.category];
            document.getElementById('review-goal').textContent =
                `${reviewedGoal.goal} ${goalUnits[reviewedGoal.category].unit}`;
            document.getElementById('review-date').textContent = reviewedGoal.date;
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
