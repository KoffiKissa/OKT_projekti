<?php
$pageTitle = 'Your health dashboard';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navigation.php';
?>
    <main id="dashboard-page" class="container mx-auto my-0 w-full max-w-6xl" hidden>
        <div class="mb-7">
            <h1 class="m-0 text-[30px] font-bold tracking-[-0.04em]">Your dashboard</h1>
            <p class="mb-0 mt-[9px] text-sm leading-[1.6] text-[#68756c]">See your progress across all your health goals.</p>
        </div>

        <div id="dashboard-message" class="mb-[18px] rounded-[9px] border border-[#f0d2cf] bg-[#fff5f4] px-[14px] py-3 text-[13px] leading-[1.5] text-[#832e28]" role="alert" hidden></div>

        <section class="mb-10 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4" aria-label="Goal and health summary">
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <p class="m-0 text-[12px] font-semibold text-[#68756c]">Total goals</p>
                <p id="total-goals" class="mb-0 mt-1 text-[25px] font-bold text-[#193d2d]">0</p>
            </div>
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <p class="m-0 text-[12px] font-semibold text-[#516c58]">Completed</p>
                <p id="completed-goals" class="mb-0 mt-1 text-[25px] font-bold text-[#245c36]">0</p>
            </div>
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <p class="m-0 text-[12px] font-semibold text-[#68756c]">In progress</p>
                <p id="active-goals" class="mb-0 mt-1 text-[25px] font-bold text-[#193d2d]">0</p>
            </div>
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <p class="m-0 text-[12px] font-semibold text-[#68756c]">BMI</p>
                <p id="current-bmi" class="mb-0 mt-1 text-[25px] font-bold text-[#193d2d]">Not available</p>
            </div>
        </section>

        <section aria-labelledby="dashboard-goals-heading">
            <div class="mb-[14px] flex flex-wrap items-center justify-between gap-3">
                <h2 id="dashboard-goals-heading" class="m-0 text-lg font-bold tracking-[-0.02em]">Your goals</h2>
                <a class="text-[13px] font-bold text-[#28603e] no-underline hover:underline" href="goals.php">Add or edit goals</a>
            </div>
            <p id="dashboard-empty" class="rounded-[10px] bg-[#f4f6f1] p-[14px] text-sm leading-[1.5] text-[#68756c]" hidden>You haven't added any goals yet. <a class="font-bold text-[#28603e]" href="goals.php">Create your first goal</a>.</p>
            <ul id="dashboard-goal-list" class="m-0 grid list-none gap-6 p-0 md:grid-cols-2 xl:grid-cols-3"></ul>
        </section>
    </main>
    <p id="dashboard-loading" class="mx-auto mb-7 mt-[9px] max-w-[1000px] text-sm leading-[1.6] text-[#68756c]">Loading your goals...</p>

<?php include __DIR__ . '/includes/footer.php'; ?>