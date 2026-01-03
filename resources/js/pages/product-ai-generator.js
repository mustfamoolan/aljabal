/**
 * Product AI Generator
 * Handles AI-powered product description generation using Gemini API
 */

class ProductAIGenerator {
    constructor() {
        this.apiEndpoint = '/api/admin/ai/generate-product-description';
        this.init();
    }

    init() {
        // Bind event listeners
        const generateShortBtn = document.getElementById('generate-short-description-btn');
        const generateLongBtn = document.getElementById('generate-long-description-btn');

        if (generateShortBtn) {
            generateShortBtn.addEventListener('click', () => this.generateDescription('short'));
        }

        if (generateLongBtn) {
            generateLongBtn.addEventListener('click', () => this.generateDescription('long'));
        }
    }

    /**
     * Collect product data from form fields
     */
    collectProductData() {
        const categorySelect = document.getElementById('category_id');
        const categoryId = categorySelect?.value || '';
        const categoryName = categorySelect ? this.getSelectedText('category_id') : '';

        // Get is_original checkbox value
        const isOriginalCheckbox = document.getElementById('is_original');
        const isOriginal = isOriginalCheckbox ? isOriginalCheckbox.checked : false;

        const data = {
            name: document.getElementById('name')?.value || '',
            author: document.getElementById('author')?.value || '',
            publisher: document.getElementById('publisher')?.value || '',
            is_original: isOriginal,
            product_type: isOriginal ? 'original' : 'normal', // For backward compatibility with AI
            category_id: categoryId || null,
            category_name: categoryName,
            tags: this.getSelectedTags(),
            sku: document.getElementById('sku')?.value || '',
            retail_price: document.getElementById('retail_price')?.value || '',
        };

        // Remove empty values
        Object.keys(data).forEach(key => {
            if (data[key] === '' || data[key] === null) {
                delete data[key];
            }
        });

        return data;
    }

    /**
     * Get selected option text
     */
    getSelectedText(selectId) {
        const select = document.getElementById(selectId);
        if (!select || !select.value) return '';

        const selectedOption = select.options[select.selectedIndex];
        return selectedOption ? selectedOption.text.trim() : '';
    }

    /**
     * Get selected tags
     */
    getSelectedTags() {
        const tagsSelect = document.getElementById('tags');
        if (!tagsSelect) return [];

        // If using Choices.js (check multiple ways)
        if (window.Choices) {
            // Method 1: Check if element has Choices instance
            const choicesInstance = tagsSelect._choicesjs ||
                                   (tagsSelect.closest('.choices')?._choicesjs) ||
                                   Choices.getInstance(tagsSelect);

            if (choicesInstance) {
                const values = choicesInstance.getValue(true);
                if (Array.isArray(values)) {
                    // Get text labels instead of values
                    const selectedItems = choicesInstance.store.activeItems || [];
                    return selectedItems.map(item => item.label || item.value).filter(Boolean);
                }
            }
        }

        // Fallback to standard select
        const selectedOptions = Array.from(tagsSelect.selectedOptions);
        return selectedOptions.map(option => {
            // Try to get text, fallback to value
            return option.text?.trim() || option.value;
        }).filter(Boolean);
    }

    /**
     * Generate description (short or long)
     */
    async generateDescription(type) {
        const data = this.collectProductData();

        // Validate required fields
        if (!data.name || data.name.trim() === '') {
            this.showError('يرجى إدخال اسم المنتج أولاً');
            return;
        }

        const buttonId = type === 'short' ? 'generate-short-description-btn' : 'generate-long-description-btn';
        const button = document.getElementById(buttonId);
        const targetFieldId = type === 'short' ? 'short_description' : 'long_description';

        if (!button) {
            console.error(`Button with id ${buttonId} not found`);
            return;
        }

        // Show loading state
        this.setLoadingState(button, true);

        try {
            const response = await this.callAIAPI({ ...data, type });

            if (response.success) {
                const field = document.getElementById(targetFieldId);
                if (field) {
                    const description = type === 'short' ? response.short_description : response.long_description;
                    field.value = description || '';
                    this.showSuccess('تم توليد الوصف بنجاح');
                } else {
                    this.showError('حقل الوصف غير موجود');
                }
            } else {
                this.showError(response.message || 'فشل في توليد الوصف');
            }
        } catch (error) {
            console.error('Error generating description:', error);
            this.showError('حدث خطأ أثناء توليد الوصف. يرجى المحاولة مرة أخرى.');
        } finally {
            this.setLoadingState(button, false);
        }
    }

    /**
     * Call AI API endpoint
     */
    async callAIAPI(data) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const response = await fetch(this.apiEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
            },
            credentials: 'same-origin',
            body: JSON.stringify(data),
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({ message: 'خطأ في التواصل مع الخادم' }));
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }

        return await response.json();
    }

    /**
     * Set loading state for button
     */
    setLoadingState(button, isLoading) {
        if (isLoading) {
            button.disabled = true;
            button.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                جاري التوليد...
            `;
        } else {
            button.disabled = false;
            const originalText = button.getAttribute('data-original-text') || 'توليد نص تلقائي';
            button.innerHTML = `
                <iconify-icon icon="solar:magic-stick-3-bold-duotone" class="fs-18 me-2"></iconify-icon>
                ${originalText}
            `;
        }
    }

    /**
     * Show success message
     */
    showSuccess(message) {
        // Use Toastify if available, otherwise alert
        if (window.Toastify) {
            Toastify({
                text: message,
                duration: 3000,
                gravity: 'top',
                position: 'left',
                style: {
                    background: 'linear-gradient(to right, #00b09b, #96c93d)',
                },
            }).showToast();
        } else {
            alert(message);
        }
    }

    /**
     * Show error message
     */
    showError(message) {
        // Use Toastify if available, otherwise alert
        if (window.Toastify) {
            Toastify({
                text: message,
                duration: 4000,
                gravity: 'top',
                position: 'left',
                style: {
                    background: 'linear-gradient(to right, #ff5f6d, #ffc371)',
                },
            }).showToast();
        } else {
            alert(message);
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Only initialize if we're on product create/edit page
    if (document.getElementById('generate-short-description-btn') ||
        document.getElementById('generate-long-description-btn')) {
        new ProductAIGenerator();
    }
});

export default ProductAIGenerator;
