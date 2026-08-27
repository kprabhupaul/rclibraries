(function (window) {
    'use strict';

    const Simal = {

        fire(options = {}) {
			
			// Allow simple usage:
    		// simal.fire('Hello world')
    		if (typeof options === 'string') {
        		options = {
            		text: options
        		};
    		}

            return new Promise((resolve) => {

                const config = {
                    title: '',
                    text: '',
                    html: '',
                    icon: '',
                    showCancelButton: false,
                    showDenyButton: false,
                    confirmButtonText: 'OK',
                    cancelButtonText: 'Cancel',
                    denyButtonText: 'Deny',
                    allowOutsideClick: true,
                    allowEscapeKey: true,
                    ...options
                };

                const dialog = document.createElement('dialog');

                dialog.className = 'simal-dialog';

                dialog.innerHTML = `
                    <div class="simal-container">

                        ${
                            config.icon
                                ? `<div class="simal-icon simal-icon-${config.icon}">
                                       ${this.getIcon(config.icon)}
                                   </div>`
                                : ''
                        }

                        ${
                            config.title
                                ? `<div class="simal-title">${config.title}</div>`
                                : ''
                        }

                        ${
                            config.html
                                ? `<div class="simal-content">${config.html}</div>`
                                : config.text
                                    ? `<div class="simal-content">${config.text}</div>`
                                    : ''
                        }

                        <div class="simal-actions">

                            ${
                                config.showDenyButton
                                    ? `<button type="button"
                                               class="simal-button simal-deny-button">
                                           ${config.denyButtonText}
                                       </button>`
                                    : ''
                            }

                            ${
                                config.showCancelButton
                                    ? `<button type="button"
                                               class="simal-button simal-cancel-button">
                                           ${config.cancelButtonText}
                                       </button>`
                                    : ''
                            }

                            <button type="button"
                                    class="simal-button simal-confirm-button">
                                ${config.confirmButtonText}
                            </button>

                        </div>

                    </div>
                `;

                document.body.appendChild(dialog);

                let completed = false;

                const finish = (result) => {

                    if (completed) {
                        return;
                    }

                    completed = true;

                    dialog.close();

                    dialog.remove();

                    resolve({
                        isConfirmed: result === 'confirmed',
                        isCancelled: result === 'cancelled',
                        isDenied: result === 'denied'
                    });
                };

                const confirmButton =
                    dialog.querySelector('.simal-confirm-button');

                const cancelButton =
                    dialog.querySelector('.simal-cancel-button');

                const denyButton =
                    dialog.querySelector('.simal-deny-button');

                confirmButton.addEventListener('click', () => {
                    finish('confirmed');
                });

                if (cancelButton) {
                    cancelButton.addEventListener('click', () => {
                        finish('cancelled');
                    });
                }

                if (denyButton) {
                    denyButton.addEventListener('click', () => {
                        finish('denied');
                    });
                }

                dialog.addEventListener('cancel', (event) => {

                    if (config.allowEscapeKey) {
                        finish('cancelled');
                    } else {
                        event.preventDefault();
                    }

                });

                dialog.addEventListener('click', (event) => {

                    if (
                        config.allowOutsideClick &&
                        event.target === dialog
                    ) {
                        finish('cancelled');
                    }

                });

                dialog.showModal();
            });
        },

        getIcon(type) {

            const icons = {

                success: `
                    <span class="simal-icon-symbol">✓</span>
                `,

                error: `
                    <span class="simal-icon-symbol">×</span>
                `,

                warning: `
                    <span class="simal-icon-symbol">!</span>
                `,

                info: `
                    <span class="simal-icon-symbol">i</span>
                `,

                question: `
                    <span class="simal-icon-symbol">?</span>
                `
            };

            return icons[type] || '';
        }

    };

    window.simal = Simal;

})(window);