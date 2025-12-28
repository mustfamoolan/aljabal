/*
 * Tags Management for Product Forms
 * Allows creating new tags on the fly from Choices.js
 */
document.addEventListener('DOMContentLoaded', function() {
    const tagsSelect = document.querySelector('#tags');
    
    if (!tagsSelect || typeof Choices === 'undefined') {
        return;
    }

    const choices = new Choices(tagsSelect, {
        removeItemButton: true,
        searchEnabled: true,
        placeholder: true,
        placeholderValue: 'اختر التاغات أو اكتب لإضافة جديد',
        searchPlaceholderValue: 'ابحث عن تاغ أو اكتب لإضافة جديد',
        addItems: true,
        editItems: false,
        duplicateItemsAllowed: false,
    });

    // Track new tags that need to be created
    const newTags = new Set();

    // Handle adding new items
    choices.passedElement.element.addEventListener('addItem', function(event) {
        const value = event.detail.value;
        
        // Skip if it's a number (existing tag ID)
        if (!isNaN(value)) {
            return;
        }

        // Check if tag exists
        fetch('/admin/tags/check-duplicate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ name: value })
        })
        .then(response => response.json())
        .then(data => {
            if (data.exists && data.tag) {
                // Tag exists, replace with ID
                choices.removeActiveItemsByValue(value);
                choices.setChoiceByValue(data.tag.id.toString());
            } else {
                // New tag, mark for creation
                newTags.add(value);
            }
        })
        .catch(error => {
            console.error('Error checking tag:', error);
        });
    });

    // Before form submit, create new tags
    const form = tagsSelect.closest('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (newTags.size === 0) {
                return; // No new tags to create
            }

            e.preventDefault();

            // Create all new tags
            const createPromises = Array.from(newTags).map(tagName => {
                return fetch('/admin/tags', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ name: tagName })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.tag) {
                        // Replace text value with ID
                        choices.removeActiveItemsByValue(tagName);
                        choices.setChoiceByValue(data.tag.id.toString());
                        return data.tag.id;
                    }
                    throw new Error('Failed to create tag');
                });
            });

            Promise.all(createPromises)
                .then(() => {
                    newTags.clear();
                    // Re-enable form and submit
                    form.submit();
                })
                .catch(error => {
                    console.error('Error creating tags:', error);
                    alert('حدث خطأ أثناء إنشاء التاغات الجديدة. يرجى المحاولة مرة أخرى.');
                    // Re-enable form
                });
        });
    }
});

