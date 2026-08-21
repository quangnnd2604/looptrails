/**
 * Top Destinations & Essentials Tab Switching
 */
document.addEventListener('DOMContentLoaded', function () {
	const tabButtons = document.querySelectorAll('.destinations-tabs-nav .tab-btn');
	const tabPanels = document.querySelectorAll('.destinations-tab-panel');

	if (!tabButtons.length || !tabPanels.length) {
		return;
	}

	tabButtons.forEach(function (button) {
		button.addEventListener('click', function () {
			const targetTab = this.getAttribute('data-tab');

			tabButtons.forEach(function (btn) {
				btn.classList.remove('is-active');
			});
			this.classList.add('is-active');

			tabPanels.forEach(function (panel) {
				if (panel.getAttribute('data-panel') === targetTab) {
					panel.style.display = 'grid';
					panel.classList.add('is-active');
				} else {
					panel.style.display = 'none';
					panel.classList.remove('is-active');
				}
			});
		});
	});
});
