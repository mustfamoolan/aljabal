/*
 * Dynamic Categories Loading
 * Loads subcategories when main category is selected
 */
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category_id');
    const subcategorySelect = document.getElementById('subcategory_id');
    
    if (!categorySelect || !subcategorySelect) {
        return;
    }

    // Get old values from window or select
    const oldCategoryId = categorySelect.value || (window.oldCategoryId || null);
    const oldSubcategoryId = window.oldSubcategoryId || null;

    // Load subcategories when category changes
    categorySelect.addEventListener('change', function() {
        const categoryId = this.value;
        
        // Reset subcategory
        subcategorySelect.innerHTML = '<option value="">اختر الفئة الفرعية</option>';
        subcategorySelect.disabled = true;
        
        if (!categoryId) {
            return;
        }

        // Fetch subcategories
        fetch(`/admin/inventory/categories/${categoryId}/subcategories`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            subcategorySelect.innerHTML = '<option value="">اختر الفئة الفرعية</option>';
            
            if (data && Array.isArray(data) && data.length > 0) {
                data.forEach(subcategory => {
                    const option = document.createElement('option');
                    option.value = subcategory.id;
                    option.textContent = subcategory.name;
                    
                    // Select if it matches old subcategory
                    if (oldSubcategoryId && subcategory.id == oldSubcategoryId) {
                        option.selected = true;
                    }
                    
                    subcategorySelect.appendChild(option);
                });
                subcategorySelect.disabled = false;
            } else {
                subcategorySelect.innerHTML = '<option value="">لا توجد فئات فرعية</option>';
            }
        })
        .catch(error => {
            console.error('Error loading subcategories:', error);
            subcategorySelect.innerHTML = '<option value="">حدث خطأ في تحميل الفئات الفرعية</option>';
        });
    });

    // Trigger change if category is pre-selected (for edit page or after validation error)
    if (oldCategoryId && categorySelect.value === oldCategoryId) {
        categorySelect.dispatchEvent(new Event('change'));
    }
});

