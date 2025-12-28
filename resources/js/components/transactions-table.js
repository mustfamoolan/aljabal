/*
Transactions Table with GridJS
*/
import gridjs from 'gridjs/dist/gridjs.umd.js'

document.addEventListener('DOMContentLoaded', function() {
    if (!document.getElementById("transactions-table") || !window.transactionsUrl) return;

    const transactionsUrl = window.transactionsUrl;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    new gridjs.Grid({
        columns: [
            {
                name: 'النوع',
                formatter: (cell) => {
                    const typeLabels = {
                        'deposit': 'إيداع',
                        'withdrawal': 'سحب',
                        'commission': 'عمولة',
                        'bonus': 'مكافأة',
                        'deduction': 'خصم',
                        'refund': 'استرداد'
                    };
                    const badges = {
                        'deposit': 'bg-success',
                        'withdrawal': 'bg-danger',
                        'commission': 'bg-info',
                        'bonus': 'bg-warning',
                        'deduction': 'bg-secondary',
                        'refund': 'bg-primary'
                    };
                    return gridjs.html(`<span class="badge ${badges[cell] || 'bg-secondary'}">${typeLabels[cell] || cell}</span>`);
                }
            },
            {
                name: 'المبلغ',
                formatter: (cell) => {
                    // Remove trailing zeros
                    const num = parseFloat(cell);
                    const formatted = num % 1 === 0 ? num.toString() : num.toFixed(2).replace(/\.?0+$/, '');
                    
                    // Format with thousand separators (keep English numerals)
                    const parts = formatted.split('.');
                    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    const withSeparators = parts.join('.');
                    
                    return withSeparators + ' د.ع';
                }
            },
            {
                name: 'الحالة',
                formatter: (cell) => {
                    const statusLabels = {
                        'pending': 'معلق',
                        'approved': 'موافق عليه',
                        'rejected': 'مرفوض',
                        'completed': 'مكتمل'
                    };
                    const badges = {
                        'pending': 'bg-warning',
                        'approved': 'bg-success',
                        'rejected': 'bg-danger',
                        'completed': 'bg-primary'
                    };
                    return gridjs.html(`<span class="badge ${badges[cell] || 'bg-secondary'}">${statusLabels[cell] || cell}</span>`);
                }
            },
            {
                name: 'التاريخ',
                formatter: (cell) => {
                    return new Date(cell).toLocaleDateString('ar-SA', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
            },
            {
                name: 'الوصف',
                formatter: (cell) => cell || '-'
            }
        ],
        server: {
            url: transactionsUrl,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            then: (data) => {
                console.log('Transactions API Response:', data);
                if (!data) {
                    console.error('No data received from API');
                    return [];
                }
                
                // Handle different response formats
                let transactions = [];
                if (data.data && Array.isArray(data.data)) {
                    transactions = data.data;
                } else if (Array.isArray(data)) {
                    transactions = data;
                }
                
                // GridJS expects an array of rows directly
                return transactions.map(item => {
                    const type = item.type?.value || item.type || '';
                    const status = item.status?.value || item.status || '';
                    const amount = parseFloat(item.amount || 0);
                    const createdAt = item.created_at || '';
                    const description = item.description || '-';
                    
                    return [type, amount, status, createdAt, description];
                });
            },
            total: (data) => {
                // GridJS expects a function that returns the total count
                if (!data) return 0;
                return data.total || (data.data && Array.isArray(data.data) ? data.data.length : 0);
            },
            handle: async (res) => {
                console.log('Transactions API Response Status:', res.status);
                if (!res.ok) {
                    const errorText = await res.text();
                    console.error('Transactions API Error:', errorText);
                    throw new Error(`Failed to load transactions: ${res.status} ${res.statusText}`);
                }
                try {
                    const data = await res.json();
                    console.log('Transactions API Data:', data);
                    return data;
                } catch (e) {
                    console.error('Failed to parse JSON:', e);
                    throw new Error('Failed to parse transactions data');
                }
            }
        },
        search: true,
        pagination: {
            limit: 15
        },
        sort: true,
        language: {
            'search': {
                'placeholder': 'ابحث...'
            },
            'pagination': {
                'previous': 'السابق',
                'next': 'التالي',
                'showing': 'عرض',
                'results': () => 'نتائج'
            }
        }
    }).render(document.getElementById('transactions-table'));
});

