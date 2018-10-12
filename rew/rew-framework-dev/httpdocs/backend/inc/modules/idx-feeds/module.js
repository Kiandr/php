const toggleFeedSwitcher = (() => { // eslint-disable-line no-unused-vars
    const feedSwitcher = document.querySelector('.bar--feeds');
    const feedsList = document.querySelector('.menu__list--feeds');
    if (!feedSwitcher || !feedsList) return;
    const targetId = 'feedSwitcher';
    // show/hide the feeds list
    feedSwitcher.addEventListener('click', (e) => {
        if (e.target.id == targetId) {
            feedsList.classList.toggle('hidden');
        }
    });
    // hide feeds list when clicked outside feedSwitcher
    document.addEventListener('click', (e) => {
        if (e.target.id !== targetId && feedSwitcher.contains(e.target) == false) {
            feedsList.classList.add('hidden');
        }
    });
})();
