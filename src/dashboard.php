<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f4f6f1">
    <title>Your health dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen min-w-[320px] bg-gradient-to-br from-[#e1eee2] via-[#f4f6f1] to-[#e8eee1] p-6 font-['Segoe_UI',Arial,sans-serif] text-[#1b2b21] [font-synthesis:none] [text-rendering:optimizeLegibility] max-[480px]:p-[14px]">
    <main id="dashboard-page" class="mx-auto my-6 w-full max-w-[1000px] rounded-[22px] border border-[#e6e9e1] bg-white p-11 shadow-[0_24px_70px_#253c2c14] max-[480px]:my-0 max-[480px]:rounded-[17px] max-[480px]:px-[26px] max-[480px]:py-8" hidden>
        <div class="mb-[26px] inline-flex items-center gap-[10px] text-sm font-bold tracking-[0.06em] text-[#193d2d]"><span class="grid h-[34px] w-[34px] place-items-center rounded-[11px] bg-[#c9e6c6] text-[21px] text-[#193d2d]" aria-hidden="true">+</span> WELLBEING</div>
        <div class="mb-7 flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="m-0 text-[30px] font-bold tracking-[-0.04em]">Your dashboard</h1>
                <p class="mb-0 mt-[9px] text-sm leading-[1.6] text-[#68756c]">See your progress across all your health goals.</p>
            </div>
            <nav class="flex flex-wrap gap-4 text-[13px] font-bold text-[#28603e]" aria-label="Account navigation">
                <a class="mt-2 no-underline hover:underline" href="index.php">Account</a>
                <a class="mt-2 no-underline hover:underline" href="goals.php">Manage goals</a>
                <a class="mt-2 no-underline hover:underline" href="entries.php">Add entry</a>
            </nav>
        </div>

        <div id="dashboard-message" class="mb-[18px] rounded-[9px] border border-[#f0d2cf] bg-[#fff5f4] px-[14px] py-3 text-[13px] leading-[1.5] text-[#832e28]" role="alert" hidden></div>

        <section class="mb-7 grid grid-cols-3 gap-3 max-[600px]:grid-cols-1" aria-label="Goal summary">
            <div class="rounded-[10px] bg-[#f4f6f1] p-4">
                <p class="m-0 text-[12px] font-semibold text-[#68756c]">Total goals</p>
                <p id="total-goals" class="mb-0 mt-1 text-[25px] font-bold text-[#193d2d]">0</p>
            </div>
            <div class="rounded-[10px] bg-[#f1faf3] p-4">
                <p class="m-0 text-[12px] font-semibold text-[#516c58]">Completed</p>
                <p id="completed-goals" class="mb-0 mt-1 text-[25px] font-bold text-[#245c36]">0</p>
            </div>
            <div class="rounded-[10px] bg-[#f4f6f1] p-4">
                <p class="m-0 text-[12px] font-semibold text-[#68756c]">In progress</p>
                <p id="active-goals" class="mb-0 mt-1 text-[25px] font-bold text-[#193d2d]">0</p>
            </div>
        </section>

        <section aria-labelledby="dashboard-goals-heading">
            <div class="mb-[14px] flex flex-wrap items-center justify-between gap-3">
                <h2 id="dashboard-goals-heading" class="m-0 text-lg font-bold tracking-[-0.02em]">Your goals</h2>
                <a class="text-[13px] font-bold text-[#28603e] no-underline hover:underline" href="goals.php">Add or edit goals</a>
            </div>
            <p id="dashboard-empty" class="rounded-[10px] bg-[#f4f6f1] p-[14px] text-sm leading-[1.5] text-[#68756c]" hidden>You haven't added any goals yet. <a class="font-bold text-[#28603e]" href="goals.php">Create your first goal</a>.</p>
            <ul id="dashboard-goal-list" class="m-0 grid list-none gap-4 p-0 md:grid-cols-2"></ul>
        </section>
    </main>
    <p id="dashboard-loading" class="mx-auto mb-7 mt-[9px] max-w-[1000px] text-sm leading-[1.6] text-[#68756c]">Loading your goals...</p>

    <script>
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

        function calculateProgress(goal, entries) {
            const matchingEntries = entries.filter((entry) =>
                entry.category === goal.category
                && entry.date >= goal.start_date
                && entry.date <= goal.done_by_date
            );
            const total = matchingEntries.reduce((sum, entry) => {
                const value = parseEntryValue(entry);
                return value === null ? sum : sum + value;
            }, 0);

            if (goal.category !== 'steps') {
                return { value: total, recorded: matchingEntries.length };
            }

            const start = new Date(`${goal.start_date}T00:00:00Z`);
            const end = new Date(`${goal.done_by_date}T00:00:00Z`);
            const days = Math.floor((end - start) / 86400000) + 1;
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

        function renderGoal(goal, entries, today) {
            const progress = calculateProgress(goal, entries);
            const target = Number(goal.goal);
            const percent = target > 0 ? Math.min((progress.value / target) * 100, 100) : 0;
            const status = goalStatus(goal, today);
            const item = document.createElement('li');
            item.className = 'rounded-[12px] border border-[#e6e9e1] bg-[#fbfcfa] p-5';

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
                : `Recorded: ${formatNumber(progress.value)} ${units[goal.category]} / ${formatNumber(target)} ${units[goal.category]} goal`;

            const progressTrack = document.createElement('div');
            progressTrack.className = 'mt-3 h-2 overflow-hidden rounded-full bg-[#e6e9e1]';
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

            const dates = document.createElement('p');
            dates.className = 'mb-0 mt-4 text-[12px] text-[#68756c]';
            dates.textContent = `Dates: ${goal.start_date} – ${goal.done_by_date}`;

            const footer = document.createElement('div');
            footer.className = 'mt-4 flex items-center justify-between gap-3 border-t border-[#e6e9e1] pt-3';
            const entryCount = document.createElement('span');
            entryCount.className = 'text-[11px] text-[#68756c]';
            entryCount.textContent = `${progress.recorded} ${progress.recorded === 1 ? 'entry' : 'entries'} in period`;
            const manageLink = document.createElement('a');
            manageLink.className = 'text-[12px] font-bold text-[#28603e] no-underline hover:underline';
            manageLink.href = 'goals.php';
            manageLink.textContent = 'Manage';
            footer.append(entryCount, manageLink);

            item.append(topRow, value, progressTrack, progressCaption, dates, footer);
            return item;
        }

        function localToday() {
            const now = new Date();
            return `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
        }

        async function loadDashboard() {
            try {
                const [goalResponse, entryResponse] = await Promise.all([
                    fetch('api.php?resource=goals'),
                    fetch('api.php?resource=health_markers')
                ]);
                const [goalData, entryData] = await Promise.all([
                    readResponse(goalResponse),
                    readResponse(entryResponse)
                ]);
                const goals = goalData.goals;
                const entries = entryData.entries;
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
    </script>
</body>
</html>