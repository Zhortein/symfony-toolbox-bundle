import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['table', 'pagination', 'spinner', 'search', 'builder', 'filters', 'error', 'footer', 'header', 'actions'];

    static values = {
        id: String,
        page: Number,
        search: String,
        mode: String,
        pagesize: Number,
        url: String,
        download: String,
        filters: String,
    };

    connect() {
        this.filters = [];
        this.filterId = 0;

        this.state = {
            page: this.pageValue || 1,
            search: this.searchValue || '',
            multiSort: []
        };

        this.loadFilterInfo();
        this.updateTable();
    }

    async loadFilterInfo() {
        try {
            const url = new URL(this.filtersValue, window.location.origin);
            const response = await fetch(url);
            if (response.ok) {
                const data = await response.json();
                this.columns = data.columns;
                this.hideErrorPanel();
            } else {
                throw new Error(`Failed to load filter info: ${response.statusText}`);
            }
        } catch (error) {
            console.error(error);
            this.displayError('Unable to load filters. Please try again later.');
        }
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

        // Ajout des filtres
        const filterData = this.filters.map(filter => ({
            column: filter.column,
            type: filter.type,
            value1: this.getInputValue(`filters[${filter.id}][value1]`),
            value2: this.getInputValue(`filters[${filter.id}][value2]`),
            values: this.getInputValues(`filters[${filter.id}][values][]`)
        }));

        filterData.forEach((filter, index) => {
            url.searchParams.set(`filters[${index}][column]`, filter.column);
            url.searchParams.set(`filters[${index}][type]`, filter.type);
            url.searchParams.set(`filters[${index}][value1]`, filter.value1 || '');
            url.searchParams.set(`filters[${index}][value2]`, filter.value2 || '');
            url.searchParams.set(`filters[${index}][values]`, filter.values || []);
        });

        try {
            const response = await fetch(url);
            if (response.ok) {
                const json = await response.json();
                this.updateTableContent(json.rows);
                this.updatePagination(json.pagination);
                this.updateIcons(json.icons);
            } else {
                throw new Error(`Failed to fetch datatable content: ${response.statusText}`);
            }
        } catch (error) {
            console.error('Error while updating datatable:', error);
            this.displayError('Unable to update data. Please try again later.');
        } finally {
            this.hideSpinner();
        }
    }

    getInputValue(name) {
        const input = document.querySelector(`[name="${name}"]`);
        return input ? input.value : '';
    }

    getInputValues(name) {
        const inputs = document.querySelectorAll(`[name="${name}"]`);
        return Array.from(inputs).map(input => input.value);
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
        const url = new URL(this.downloadValue, window.location.origin);

        // Ajouter les paramètres de tri, de recherche et de page
        url.searchParams.set('type', 'csv');
        url.searchParams.set('search', this.state.search);
        this.state.multiSort.forEach((sort, index) => {
            url.searchParams.set(`multiSort[${index}][field]`, sort.field);
            url.searchParams.set(`multiSort[${index}][order]`, sort.order);
        });

        // Rediriger l'utilisateur vers la route d'export CSV
        window.location.href = url.toString();
    }

    exportExcel() {
        const url = new URL(this.downloadValue, window.location.origin);

        // Ajouter les paramètres de tri, de recherche et de page
        url.searchParams.set('type', 'excel');
        url.searchParams.set('search', this.state.search);
        this.state.multiSort.forEach((sort, index) => {
            url.searchParams.set(`multiSort[${index}][field]`, sort.field);
            url.searchParams.set(`multiSort[${index}][order]`, sort.order);
        });

        // Rediriger l'utilisateur vers la route d'export CSV
        window.location.href = url.toString();
    }

    exportPdf() {
        const url = new URL(this.downloadValue, window.location.origin);

        // Ajouter les paramètres de tri, de recherche et de page
        url.searchParams.set('type', 'pdf');
        url.searchParams.set('search', this.state.search);
        this.state.multiSort.forEach((sort, index) => {
            url.searchParams.set(`multiSort[${index}][field]`, sort.field);
            url.searchParams.set(`multiSort[${index}][order]`, sort.order);
        });

        // Rediriger l'utilisateur vers la route d'export CSV
        window.location.href = url.toString();
    }

    addFilter(event) {
        event.preventDefault();
        this.filterId++;

        const filterData = {
            id: this.filterId,
            column: '',
            type: '',
            filterType: '',
            value1: '',
            value2: '',
            operator: 'AND'
        };

        this.filters.push(filterData);
        this.renderFilters();
    }

    clearFilters(event) {
        event.preventDefault();
        this.filters = [];
        this.renderFilters();
    }

    deleteFilter(event) {
        const filterId = event.target.dataset.filterId;
        this.filters = this.filters.filter(f => f.id !== parseInt(filterId));
        this.renderFilters();
    }

    renderFilters() {
        const filtersHtml = this.filters.map(filter => this.renderFilter(filter)).join('');
        this.filtersTarget.innerHTML = filtersHtml;
    }

    renderFilter(filter) {
        // On va devoir rendre ces options dynamiques, à adapter
        const columnOptions = this.getColumnOptions(filter);
        const filterTypeOptions = this.getFilterTypeOptions(filter);
        const inputFields = this.getInputFields(filter);

        return `
        <div class="filter" data-filter-id="${filter.id}">
            <select name="filters[${filter.id}][column]" data-action="change->zhortein--symfony-toolbox-bundle--datatable#changeColumn">
                <option>---</option>
                ${columnOptions}
            </select>

            <select name="filters[${filter.id}][type]" data-action="change->zhortein--symfony-toolbox-bundle--datatable#changeFilterType">
                <option>---</option>
                ${filterTypeOptions}
            </select>

            <div data-filter-inputs>
                ${inputFields}
            </div>
            
            <button 
                data-filter-id="${filter.id}" 
                data-action="click->zhortein--symfony-toolbox-bundle--datatable#deleteFilter"
            >Supprimer</button>
        </div>
    `;
    }

    getColumnOptions(filter) {
        return this.columns.map(col => `<option value="${col.name}" ${filter.column === col.name ? 'selected' : ''}>${col.label}</option>`).join('');
    }

    getEnumOptions(columnName) {
        const column = this.columns.find(col => col.name === columnName);
        if (!column || !Array.isArray(column.values)) return '';

        return column.values.map(value =>
            `<option value="${value.key}">${value.label}</option>`
        ).join('');
    }

    getFilterTypeOptions(filter) {
        const column = this.columns.find(col => col.name === filter.column);
        if (!column) return '';

        return column.filters.map(f => {
            return `<option value="${f.id}" ${filter.type === f.id ? 'selected' : ''}>${f.label}</option>`;
        }).join('');
    }

    getInputFields(filter) {
        const filterType = filter.type;
        if (!filterType) return '';

        switch (filterType) {
            case 'before':
            case 'after':
                return `<input required type="date" name="filters[${filter.id}][value1]" value="${filter.value1 || ''}" />`;
            case 'between':
            case 'not_between':
                return `
                <input required type="text" name="filters[${filter.id}][value1]" value="${filter.value1 || ''}" />
                <input required type="text" name="filters[${filter.id}][value2]" value="${filter.value2 || ''}" />
            `;

            case 'in':
            case 'not_in':
                return `<select required multiple name="filters[${filter.id}][values][]">${this.getEnumOptions(filter.column)}</select>`;

            case 'is_true':
            case 'is_false':
            case 'is_null':
            case 'is_not_null':
                return ``; // Aucun champ nécessaire pour ces filtres

            default:
                return `<input required type="text" name="filters[${filter.id}][value1]" value="${filter.value1 || ''}" />`;
        }
    }

    displayError(message) {
        this.errorTarget.innerHTML = message;
        this.errorTarget.classList.remove(this.getHiddenClass());
    }

    hideErrorPanel() {
        this.errorTarget.innerHTML = '';
        this.errorTarget.classList.add(this.getHiddenClass());
    }

    changeColumn(event) {
        const filterId = this.getFilterIdFromElement(event.target);
        const selectedColumnName = event.target.value;

        // Trouver la colonne correspondante
        const column = this.columns.find(col => col.name === selectedColumnName);
        if (!column) {
            console.warn(`Colonne "${selectedColumnName}" non trouvée dans la liste des colonnes disponibles.`);
            return;
        }

        // Mettre à jour l'état du filtre pour la colonne sélectionnée
        const filterData = this.filters.find(f => f.id === parseInt(filterId));
        if (filterData) {
            filterData.column = selectedColumnName;
            filterData.type = ''; // On réinitialise le type de filtre
            filterData.value1 = '';
            filterData.value2 = '';
            filterData.values = [];
        }

        // Mettre à jour les options de type de filtre
        const filterTypeSelect = event.target.closest('.filter').querySelector(`select[name="filters[${filterId}][type]"]`);
        if (filterTypeSelect) {
            filterTypeSelect.innerHTML = `
            <option value="">---</option>
            ${column.filters.map(filter => `<option value="${filter.id}">${filter.label}</option>`).join('')}
        `;
        }

        // Re-render les champs de saisie pour le filtre
        this.renderFilters();
    }

    changeFilterType(event) {
        const filterId = this.getFilterIdFromElement(event.target);
        const selectedFilterType = event.target.value;

        // Mettre à jour l'état du filtre pour le type sélectionné
        const filterData = this.filters.find(f => f.id === parseInt(filterId));
        if (filterData) {
            filterData.type = selectedFilterType;
            filterData.value1 = '';
            filterData.value2 = '';
            filterData.values = [];
        }

        // Re-render uniquement cette partie
        this.renderFilterFields(filterId);
    }

    renderFilterFields(filterId) {
        const filterData = this.filters.find(f => f.id === parseInt(filterId));
        if (!filterData) return;

        const filterContainer = document.querySelector(`.filter[data-filter-id="${filterId}"]`);
        if (!filterContainer) return;

        const fieldsContainer = filterContainer.querySelector('[data-filter-inputs]');
        if (!fieldsContainer) return;

        fieldsContainer.innerHTML = this.getInputFields(filterData);
    }

    getFilterIdFromElement(element) {
        const filterContainer = element.closest('.filter');
        return filterContainer ? filterContainer.dataset.filterId : null;
    }

    applyFilters(event) {
        event.preventDefault();
        this.updateTable();
    }
}
