/*
Permissions Table with GridJS
*/
import gridjs from 'gridjs/dist/gridjs.umd.js'

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById("permissions-table") && window.permissionsData) {
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const permissionsData = window.permissionsData.map(permission => {
            // Format roles badges
            let rolesHtml = '';
            if (permission.roles.length > 0) {
                const rolesToShow = permission.roles.slice(0, 3);
                rolesHtml = rolesToShow.map(role =>
                    `<span class="badge bg-primary-subtle text-primary py-1 px-2 mb-1">${role}</span>`
                ).join('');
                if (permission.roles.length > 3) {
                    rolesHtml += `<span class="badge bg-light-subtle text-muted py-1 px-2 mb-1">+${permission.roles.length - 3}</span>`;
                }
            } else {
                rolesHtml = '<span class="text-muted">لا توجد أدوار</span>';
            }

            // Format actions buttons
            const editBtn = `<a href="${permission.edit_url}" class="btn btn-soft-primary btn-sm" title="تعديل"><iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon></a>`;
            const deleteBtn = `<button type="button" class="btn btn-soft-danger btn-sm delete-permission-btn" data-url="${permission.delete_url}" data-token="${csrfToken}" title="حذف"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></button>`;
            const actionsHtml = `<div class="d-flex gap-2">${editBtn}${deleteBtn}</div>`;

            return [
                // Name column (Arabic + English)
                gridjs.html(`
                    <div>
                        <p class="fs-15 mb-0 fw-semibold">${permission.name_ar}</p>
                        <small class="text-muted">${permission.name}</small>
                    </div>
                `),
                // Description column
                gridjs.html(`
                    <span class="badge bg-secondary-subtle text-secondary py-1 px-2">
                        ${permission.description}
                    </span>
                `),
                // Roles column
                gridjs.html(`<div class="d-flex flex-wrap gap-1">${rolesHtml}</div>`),
                // Actions
                gridjs.html(actionsHtml)
            ];
        });

        const grid = new gridjs.Grid({
            columns: [
                {
                    name: 'اسم الصلاحية',
                    width: '30%',
                    sort: true,
                },
                {
                    name: 'الوصف',
                    width: '20%',
                    sort: true,
                },
                {
                    name: 'مُسندة إلى',
                    width: '30%',
                },
                {
                    name: 'الإجراءات',
                    width: '20%',
                    sort: false,
                }
            ],
            pagination: {
                limit: 15
            },
            search: true,
            sort: true,
            data: permissionsData.length > 0 ? permissionsData : [
                [gridjs.html('<span class="text-muted">لا توجد صلاحيات</span>'), '', '', '']
            ]
        }).render(document.getElementById("permissions-table"));

        // Handle delete button clicks using event delegation
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-permission-btn')) {
                e.preventDefault();
                const btn = e.target.closest('.delete-permission-btn');
                if (confirm('هل أنت متأكد من حذف هذه الصلاحية؟')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = btn.getAttribute('data-url');

                    const tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = '_token';
                    tokenInput.value = btn.getAttribute('data-token');

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';

                    form.appendChild(tokenInput);
                    form.appendChild(methodInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        });
    }
});
