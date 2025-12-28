/*
Representative Orders Create Page
Handles categories slider, dynamic filtering, and product updates
*/
document.addEventListener('DOMContentLoaded', function() {
    // Categories Slider
    const categoriesSlider = document.querySelector('.categories-slider');
    const categoryFilterBtns = document.querySelectorAll('.category-filter-btn');

    if (categoriesSlider && categoryFilterBtns.length > 0) {
        // Category filter buttons - update URL with category filter
        categoryFilterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                categoryFilterBtns.forEach(b => b.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');

                // Get current URL parameters
                const url = new URL(window.location.href);
                const categoryId = this.dataset.categoryId;
                
                // Update or remove category_id parameter
                if (categoryId) {
                    url.searchParams.set('category_id', categoryId);
                } else {
                    url.searchParams.delete('category_id');
                }
                
                // Remove subcategory_id when changing main category
                url.searchParams.delete('subcategory_id');
                
                // Reload page with new filters
                window.location.href = url.toString();
            });
        });
    }

    // Dynamic subcategory loading for modal
    const modalCategorySelect = document.getElementById('modal_category_id');
    const modalSubcategorySelect = document.getElementById('modal_subcategory_id');

    if (modalCategorySelect && modalSubcategorySelect) {
        modalCategorySelect.addEventListener('change', function() {
            const categoryId = this.value;

            if (!categoryId) {
                modalSubcategorySelect.innerHTML = '<option value="">جميع الأقسام الفرعية</option>';
                modalSubcategorySelect.disabled = true;
                return;
            }

            // Fetch subcategories
            const url = `/admin/inventory/categories/${categoryId}/subcategories`;
            fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => response.json())
            .then(data => {
                modalSubcategorySelect.innerHTML = '<option value="">جميع الأقسام الفرعية</option>';
                if (data.length > 0) {
                    data.forEach(subcategory => {
                        const option = document.createElement('option');
                        option.value = subcategory.id;
                        option.textContent = subcategory.name;
                        modalSubcategorySelect.appendChild(option);
                    });
                    modalSubcategorySelect.disabled = false;
                } else {
                    modalSubcategorySelect.innerHTML = '<option value="">لا توجد أقسام فرعية</option>';
                    modalSubcategorySelect.disabled = true;
                }
            })
            .catch(error => {
                console.error('Error loading subcategories:', error);
                modalSubcategorySelect.innerHTML = '<option value="">حدث خطأ في تحميل الأقسام الفرعية</option>';
                modalSubcategorySelect.disabled = true;
            });
        });
    }

    // Initialize Choices.js for tags in modal if available
    if (typeof Choices !== 'undefined') {
        const modalTagsSelect = document.getElementById('modal_filter_tags');
        if (modalTagsSelect) {
            new Choices(modalTagsSelect, {
                removeItemButton: true,
                searchEnabled: true,
                placeholder: true,
                placeholderValue: 'اختر التاغات',
                searchPlaceholderValue: 'ابحث عن تاغ...',
            });
        }
    }
});

