import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['table', 'pagination', 'spinner', 'search'];

    static values = {
        datatableId: String,
        page: Number,
        sort: String,
        order: String,
        search: String,
    };

    connect() {
        this.state = {
            page: this.pageValue || 1,
            sort: this.sortValue || null,
            order: this.orderValue || 'asc',
            search: this.searchValue || '',
        };
    }

    async updateTable() {
        this.showSpinner();

        const url = new URL(`/datatable/${this.datatableIdValue}/data`, window.location.origin);
        url.searchParams.set('page', this.state.page);
        url.searchParams.set('sort', this.state.sort);
        url.searchParams.set('order', this.state.order);
        url.searchParams.set('search', this.state.search);

        try {
            const response = await fetch(url);
            if (response.ok) {
                const json = await response.json();
                this.updateTableContent(json.rows);
                this.updatePagination(json.pagination);
            } else {
                console.error('Failed to fetch datatable content:', response.statusText);
            }
        } catch (error) {
            console.error('Error while updating datatable:', error);
        } finally {
            this.hideSpinner();
        }
    }

    updateTableContent(rowsHtml) {
        this.tableTarget.innerHTML = rowsHtml;
    }

    updatePagination(paginationHtml) {
        this.paginationTarget.innerHTML = paginationHtml;
    }

    sort(event) {
        const column = event.target.dataset.sortField;
        if (!column) return;

        this.state.sort = column;
        this.state.order = this.state.order === 'asc' ? 'desc' : 'asc';
        this.updateTable();
    }

    search(event) {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            this.state.search = event.target.value;
            this.updateTable();
        }, 300); // Debounce
    }

    changePage(event) {
        this.state.page = parseInt(event.target.dataset.page, 10);
        this.updateTable();
    }

    showSpinner() {
        this.spinnerTarget.classList.remove('hidden');
    }

    hideSpinner() {
        this.spinnerTarget.classList.add('hidden');
    }
}
