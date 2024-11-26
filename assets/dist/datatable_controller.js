import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['table', 'pagination', 'spinner', 'search', 'error', 'footer', 'header'];

    static values = {
        id: String,
        page: Number,
        sort: String,
        order: String,
        search: String,
        mode: String,
        pagesize: Number,
        url: String,
    };

    connect() {
        this.state = {
            page: this.pageValue || 1,
            sort: this.sortValue || null,
            order: this.orderValue || 'asc',
            search: this.searchValue || '',
        };

        this.updateTable();
    }

    async updateTable() {
        this.showSpinner();

        const url = new URL(this.urlValue, window.location.origin);
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
                this.updateIcons(json.icons);
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

    updateIcons(icons) {
        this.headerTarget.querySelectorAll('[data-field]').forEach(col => {
            const iconContainer = col.querySelector('.datatable-sort-icon');
            if (iconContainer) {
                const field = col.dataset.field;
                let iconHtml = icons.icon_sort_neutral; // Icône par défaut
                if (field === this.state.sort) {
                    iconHtml = this.state.order === 'asc' ? icons.icon_sort_asc : icons.icon_sort_desc;
                }
                iconContainer.innerHTML = iconHtml;
            }
        });
    }

    sort(event) {
        const column = event.target.dataset.field;
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
        // Trouve l'élément parent ayant l'attribut `data-page`
        const pageElement = event.target.closest('[data-page]');
        if (!pageElement) return; // Si aucun parent trouvé, on sort

        const page = parseInt(pageElement.dataset.page, 10);
        if (isNaN(page)) return; // Si la page n'est pas un entier valide, on sort

        this.state.page = page;
        this.updateTable();
    }

    showSpinner() {
        this.spinnerTarget.classList.remove(this.getHiddenClass());
    }

    hideSpinner() {
        this.spinnerTarget.classList.add(this.getHiddenClass());
    }

    getHiddenClass() {
        if (this.modeValue == 'bootstrap') {
            return 'd-none';
        }

        if (this.modeValue == 'tailwind') {
            return 'hidden';
        }

        return 'hidden';
    }
}
