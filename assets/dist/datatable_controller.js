import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['table', 'pagination', 'spinner', 'search', 'error', 'footer', 'header', 'actions'];

    static values = {
        id: String,
        page: Number,
        search: String,
        mode: String,
        pagesize: Number,
        url: String,
    };

    connect() {
        this.state = {
            page: this.pageValue || 1,
            search: this.searchValue || '',
            multiSort: []
        };

        this.updateTable();
    }

    async updateTable() {
        this.showSpinner();

        const url = new URL(this.urlValue, window.location.origin);
        url.searchParams.set('page', this.state.page);
        url.searchParams.set('search', this.state.search);
        this.state.multiSort.forEach((sort, index) => {
            url.searchParams.set(`multiSort[${index}][field]`, sort.field);
            url.searchParams.set(`multiSort[${index}][order]`, sort.order);
        });

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
                const sortIndex = this.state.multiSort.findIndex(sort => sort.field === field);

                if (sortIndex >= 0) {
                    // Colonne triée : affiche l'icône avec l'ordre et la priorité
                    const sort = this.state.multiSort[sortIndex];
                    iconContainer.innerHTML = sort.order === 'asc' ? icons.icon_sort_asc : icons.icon_sort_desc;
                    iconContainer.dataset.priority = sortIndex + 1;
                } else {
                    // Colonne non triée : affiche l'icône neutre
                    iconContainer.innerHTML = icons.icon_sort_neutral;
                    delete iconContainer.dataset.priority;
                }
            }
        });
    }

    sort(event) {
        const column = event.target.dataset.field;
        if (!column) return;

        const field = column;

        if (event.shiftKey || event.ctrlKey) {
            // Ajoute ou met à jour cette colonne dans l'ordre des tris
            const existingIndex = this.state.multiSort.findIndex(col => col.field === field);
            if (existingIndex >= 0) {
                this.state.multiSort[existingIndex].order = this.state.multiSort[existingIndex].order === 'asc' ? 'desc' : 'asc';
            } else {
                this.state.multiSort.push({ field, order: 'asc' });
            }
        } else {
            // Mono-colonne
            if (this.state.multiSort[0] && this.state.multiSort[0].order && this.state.multiSort[0].field === field) {
                this.state.multiSort = [{field, order: this.state.multiSort[0].order === 'asc' ? 'desc' : 'asc'}];
            } else {
                this.state.multiSort = [{field, order: 'asc'}];
            }
        }

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

    exportCsv() {
        const url = new URL(this.urlValue.replace('fetch_data', 'export_csv'), window.location.origin);

        // Ajouter les paramètres de tri, de recherche et de page
        url.searchParams.set('page', this.state.page);
        url.searchParams.set('search', this.state.search);
        this.state.multiSort.forEach((sort, index) => {
            url.searchParams.set(`multiSort[${index}][field]`, sort.field);
            url.searchParams.set(`multiSort[${index}][order]`, sort.order);
        });

        // Rediriger l'utilisateur vers la route d'export CSV
        window.location.href = url.toString();
    }

}
