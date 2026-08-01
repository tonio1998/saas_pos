const Utils = {

    formatCurrency(value, currency = 'PHP') {

        if (value === null || value === undefined || value === '') {
            return '₱0.00';
        }

        return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency
        }).format(value);

    },

    formatDate(value) {

        if (!value) {
            return '-';
        }

        const date = new Date(value);

        if (isNaN(date.getTime())) {
            return value;
        }

        return date.toLocaleDateString('en-PH', {
            year: 'numeric',
            month: 'short',
            day: '2-digit'
        });

    },

    formatDateTime(value) {

        if (!value) {
            return '-';
        }

        const date = new Date(value);

        if (isNaN(date.getTime())) {
            return value;
        }

        return date.toLocaleString('en-PH', {
            year: 'numeric',
            month: 'short',
            day: '2-digit',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });

    },

    formatNumber(value, decimals = 2) {

        if (value === null || value === undefined || value === '') {
            return '0.00';
        }

        return Number(value).toLocaleString('en-PH', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });

    }

};

window.Utils = Utils;
