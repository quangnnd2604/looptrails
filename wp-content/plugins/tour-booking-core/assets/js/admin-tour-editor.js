(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		document.addEventListener('click', function (e) {
			// Add Row
			if (e.target && e.target.classList.contains('tbc-add-row')) {
				e.preventDefault();
				const repeaterId = e.target.getAttribute('data-repeater');
				const table = document.getElementById(repeaterId);
				if (!table) return;

				const tbody = table.querySelector('tbody');
				if (!tbody) return;

				const template = table.querySelector('template.tbc-row-template');
				let newRow = null;

				if (template) {
					const clone = template.content.cloneNode(true);
					newRow = clone.querySelector('tr');
				} else {
					const lastRow = tbody.querySelector('tr.tbc-repeater-row');
					if (lastRow) {
						newRow = lastRow.cloneNode(true);
					}
				}

				if (!newRow) return;

				// Determine unique next index
				const newIndex = 'new_' + Date.now() + '_' + Math.floor(Math.random() * 1000);

				newRow.querySelectorAll('input, textarea, select').forEach(function (el) {
					if (el.name) {
						el.name = el.name.replace(/\[(?:template|\d+|new_[^\]]+)\]/, '[' + newIndex + ']');
					}
					if (el.type === 'checkbox') {
						el.checked = false;
					} else if (el.name && el.name.indexOf('[post_id]') !== -1) {
						el.value = '0';
					} else {
						el.value = el.getAttribute('data-default') || '';
					}
				});

				newRow.style.display = '';
				newRow.classList.remove('is-deleted');
				tbody.appendChild(newRow);
			}

			// Remove / Delete Row Toggle
			if (e.target && e.target.classList.contains('tbc-remove-row-btn')) {
				e.preventDefault();
				const row = e.target.closest('tr.tbc-repeater-row');
				if (!row) return;

				const deleteInput = row.querySelector('input.tbc-delete-flag');
				const isNew = row.getAttribute('data-is-new') === '1' || (row.querySelector('input[name*="[post_id]"]') && row.querySelector('input[name*="[post_id]"]').value === '0');

				if (isNew) {
					row.remove();
				} else {
					if (deleteInput) {
						deleteInput.value = '1';
					}
					row.classList.add('is-deleted');
					row.style.opacity = '0.4';
					row.style.textDecoration = 'line-through';
					e.target.style.display = 'none';
					const undoBtn = row.querySelector('.tbc-undo-row-btn');
					if (undoBtn) undoBtn.style.display = 'inline-block';
				}
			}

			// Undo Remove Row
			if (e.target && e.target.classList.contains('tbc-undo-row-btn')) {
				e.preventDefault();
				const row = e.target.closest('tr.tbc-repeater-row');
				if (!row) return;

				const deleteInput = row.querySelector('input.tbc-delete-flag');
				if (deleteInput) {
					deleteInput.value = '0';
				}
				row.classList.remove('is-deleted');
				row.style.opacity = '1';
				row.style.textDecoration = 'none';
				e.target.style.display = 'none';
				const removeBtn = row.querySelector('.tbc-remove-row-btn');
				if (removeBtn) removeBtn.style.display = 'inline-block';
			}
		});
	});
})();
