<header class="container mx-auto mb-8 flex max-w-6xl flex-wrap items-center justify-between gap-5">
    <a class="inline-flex items-center gap-[10px] text-sm font-bold tracking-[0.06em] text-[#193d2d] no-underline" href="index.php">
        <span class="grid h-[34px] w-[34px] place-items-center rounded-[11px] bg-[#c9e6c6] text-[21px] text-[#193d2d]" aria-hidden="true">+</span>
        WELLBEING
    </a>
    <nav id="site-navigation-links" class="flex flex-wrap gap-x-6 gap-y-3 text-[13px] font-bold text-[#28603e]" aria-label="Main navigation" style="display: none">
        <a class="no-underline hover:underline" href="index.php">Account</a>
        <a class="no-underline hover:underline" href="dashboard.php">Dashboard</a>
        <a class="no-underline hover:underline" href="goals.php">Goals</a>
        <a class="no-underline hover:underline" href="entries.php">Health entries</a>
    </nav>
</header>
<script>
    const siteNavigationLinks = document.getElementById('site-navigation-links');
    let navigationCheckVersion = 0;

    window.setSiteNavigationVisible = (isVisible) => {
        navigationCheckVersion += 1;
        siteNavigationLinks.style.display = isVisible ? 'flex' : 'none';
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
            return Boolean(data.user);
        })
        .then((isAuthenticated) => {
            if (currentNavigationCheck === navigationCheckVersion) {
                siteNavigationLinks.style.display = isAuthenticated ? 'flex' : 'none';
            }
        })
        .catch((error) => console.error(error));
</script>
