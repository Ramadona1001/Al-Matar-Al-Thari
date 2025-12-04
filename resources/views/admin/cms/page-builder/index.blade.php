@extends('layouts.dashboard')

@section('title', __('Page Builder'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.sections.index') }}">{{ __('Sections') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Page Builder') }}</li>
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    </div>
@endif

<div class="card modern-card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="chart-icon-wrapper bg-primary-subtle">
                    <i class="fas fa-th-large text-primary"></i>
                </div>
                <div>
                    <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Content Management') }}</p>
                    <h5 class="fw-bold mb-0 text-gray-900">{{ __('Page Builder') }}</h5>
                </div>
            </div>
            <div>
                <select id="pageSelector" class="form-select form-select-lg">
                    @foreach($pages as $p)
                        <option value="{{ $p }}" {{ $page === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <div class="row g-4">
            <!-- Available Sections Sidebar -->
            <div class="col-lg-3">
                <div class="card border">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-list me-2"></i>{{ __('Available Sections') }}
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div id="availableSections" class="available-sections-list">
                            @foreach($availableSections as $section)
                                @php
                                    $currentLocale = app()->getLocale();
                                    $translation = $section->translate($currentLocale);
                                    $enTranslation = $section->translate('en');
                                    $title = ($translation && $translation->title) 
                                        ? $translation->title 
                                        : (($enTranslation && $enTranslation->title) ? $enTranslation->title : $section->name);
                                @endphp
                                <div class="available-section-item mb-2 p-2 border rounded cursor-move" 
                                     data-section-id="{{ $section->id }}"
                                     draggable="true"
                                     style="cursor: move; background: #f8f9fa; transition: all 0.2s;"
                                     onmouseover="this.style.background='#e9ecef';"
                                     onmouseout="this.style.background='#f8f9fa';">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-grip-vertical text-muted"></i>
                                        <div class="flex-grow-1">
                                            <strong class="d-block small">{{ $title }}</strong>
                                            <small class="text-muted">{{ $section->type }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Page Builder Canvas -->
            <div class="col-lg-9">
                <div class="card border">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-palette me-2"></i>{{ __('Page Layout') }} - {{ ucfirst($page) }}
                        </h6>
                        <button type="button" class="btn btn-sm btn-primary" id="saveLayoutBtn">
                            <i class="fas fa-save me-1"></i>{{ __('Save Layout') }}
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <div id="pageBuilderCanvas" class="page-builder-canvas">
                            <!-- Rows will be dynamically added here -->
                            <div class="builder-row mb-3" data-row="0">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-secondary me-2">Row 1</span>
                                    <button type="button" class="btn btn-sm btn-outline-danger ms-auto" onclick="removeRow(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="row g-2 builder-row-content" style="min-height: 80px; border: 2px dashed #dee2e6; border-radius: 8px; padding: 10px;">
                                    <!-- Sections will be dropped here -->
                                    <div class="col-12 text-center text-muted py-4 drop-zone-hint">
                                        <i class="fas fa-hand-pointer fa-2x mb-2"></i>
                                        <p class="mb-0">{{ __('Drag sections here or click to add') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-3" id="addRowBtn">
                            <i class="fas fa-plus me-1"></i>{{ __('Add Row') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@push('styles')
<link href="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.css" rel="stylesheet">
<style>
.page-builder-canvas {
    min-height: 400px;
}

.builder-row {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
}

.builder-row-content {
    background: white;
    min-height: 100px;
}

.builder-section-item {
    background: white;
    border: 2px solid #dee2e6;
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 10px;
    cursor: move;
    transition: all 0.2s;
    position: relative;
}

.builder-section-item:hover {
    border-color: #0d6efd;
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.15);
}

.builder-section-item.dragging {
    opacity: 0.5;
}

.section-width-controls {
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px solid #e9ecef;
}

.section-width-input {
    text-align: center;
}

.section-width-input.is-invalid {
    border-color: #dc3545;
}

@media (min-width: 768px) {
    .section-width-controls {
        margin-top: 0;
        padding-top: 0;
        border-top: none;
        margin-left: auto;
    }
}

.available-section-item {
    user-select: none;
}

.drop-zone-hint {
    pointer-events: none;
}

.builder-row-content.sortable-ghost {
    opacity: 0.4;
}

.builder-row-content.sortable-drag {
    opacity: 0.8;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
let rowCounter = 1;

// Available sections data
const availableSectionsData = @json($sectionsData ?? []);

// Initialize Sortable for rows
document.addEventListener('DOMContentLoaded', function() {
    initializePageBuilder();
    loadPageSections();
});

function initializePageBuilder() {
    // Make available sections draggable
    const availableSections = document.getElementById('availableSections');
    if (availableSections) {
        new Sortable(availableSections, {
            group: {
                name: 'sections',
                pull: 'clone',
                put: false
            },
            sort: false,
            animation: 150,
        });
    }
    
    // Initialize rows
    initializeRows();
    
    // Add row button
    const addRowBtn = document.getElementById('addRowBtn');
    if (addRowBtn) {
        addRowBtn.addEventListener('click', addNewRow);
    }
    
    // Save layout button
    const saveBtn = document.getElementById('saveLayoutBtn');
    if (saveBtn) {
        saveBtn.addEventListener('click', saveLayout);
    }
    
    // Page selector
    const pageSelector = document.getElementById('pageSelector');
    if (pageSelector) {
        pageSelector.addEventListener('change', function() {
            window.location.href = '{{ route("admin.page-builder.show", ":page") }}'.replace(':page', this.value);
        });
    }
}

function initializeRows() {
    const rows = document.querySelectorAll('.builder-row-content');
    rows.forEach((row, index) => {
        initializeRowSortable(row, index);
    });
}

function initializeRowSortable(rowElement, rowIndex) {
    // Store sortable instance on the element for later cleanup
    const sortableInstance = new Sortable(rowElement, {
        group: 'sections',
        animation: 150,
        ghostClass: 'sortable-ghost',
        dragClass: 'sortable-drag',
        onAdd: function(evt) {
            const sectionId = evt.item.dataset.sectionId;
            if (sectionId) {
                // Apply default width of 12 when section is added
                applySectionWidth(sectionId, evt.item, 12);
            }
        },
        onEnd: function(evt) {
            updateSectionPositions();
        }
    });
    rowElement.sortableInstance = sortableInstance;
}

function addNewRow() {
    const canvas = document.getElementById('pageBuilderCanvas');
    const newRow = document.createElement('div');
    newRow.className = 'builder-row mb-3';
    newRow.dataset.row = rowCounter;
    newRow.innerHTML = `
        <div class="d-flex align-items-center mb-2">
            <span class="badge bg-secondary me-2">Row ${rowCounter + 1}</span>
            <button type="button" class="btn btn-sm btn-outline-danger ms-auto" onclick="removeRow(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="row g-2 builder-row-content" style="min-height: 80px; border: 2px dashed #dee2e6; border-radius: 8px; padding: 10px;">
            <div class="col-12 text-center text-muted py-4 drop-zone-hint">
                <i class="fas fa-hand-pointer fa-2x mb-2"></i>
                <p class="mb-0">{{ __('Drag sections here or click to add') }}</p>
            </div>
        </div>
    `;
    canvas.appendChild(newRow);
    const rowContent = newRow.querySelector('.builder-row-content');
    initializeRowSortable(rowContent, rowCounter);
    rowCounter++;
}

function removeRow(btn) {
    if (confirm('{{ __("Are you sure you want to remove this row? All sections in this row will be removed.") }}')) {
        const row = btn.closest('.builder-row');
        const rowContent = row.querySelector('.builder-row-content');
        const sections = rowContent.querySelectorAll('.builder-section-item');
        
        // Remove all sections from the row first
        sections.forEach(section => {
            section.remove();
        });
        
        // Then remove the row
        row.remove();
        
        // Reinitialize rows to update row numbers
        reinitializeRows();
        updateSectionPositions();
    }
}

function reinitializeRows() {
    const canvas = document.getElementById('pageBuilderCanvas');
    const rows = canvas.querySelectorAll('.builder-row');
    rows.forEach((row, index) => {
        row.dataset.row = index;
        const badge = row.querySelector('.badge');
        if (badge) {
            badge.textContent = `Row ${index + 1}`;
        }
        // Reinitialize sortable for this row
        const rowContent = row.querySelector('.builder-row-content');
        if (rowContent) {
            // Destroy existing sortable instance if any
            const sortableInstance = rowContent.sortableInstance;
            if (sortableInstance) {
                sortableInstance.destroy();
            }
            // Create new sortable instance
            initializeRowSortable(rowContent, index);
        }
    });
    rowCounter = rows.length;
}

function updateSectionWidth(btn) {
    const element = btn.closest('.builder-section-item');
    if (!element) return;
    
    const widthInput = element.querySelector('.section-width-input');
    if (!widthInput) return;
    
    const width = parseInt(widthInput.value);
    if (width < 1 || width > 12) {
        widthInput.classList.add('is-invalid');
        alert('{{ __("Width must be between 1 and 12") }}');
        return;
    }
    
    widthInput.classList.remove('is-invalid');
    const sectionId = element.dataset.sectionId;
    applySectionWidth(sectionId, element, width);
}

function applySectionWidth(sectionId, element, width) {
    // Remove existing column classes
    element.className = element.className.replace(/col-\d+/g, '').trim();
    
    // Add new column classes separately (classList.add doesn't accept spaces)
    element.classList.add('col-12');
    if (width !== 12) {
        element.classList.add(`col-md-${width}`);
    }
    element.classList.add('builder-section-item');
    
    // Update dataset width
    element.dataset.sectionId = sectionId;
    element.dataset.width = width;
    
    // Check if element already has content (for updates)
    const widthInput = element.querySelector('.section-width-input');
    if (widthInput) {
        // Just update the input value and dataset
        widthInput.value = width;
    } else {
        // Element doesn't have content yet, create it (for new sections)
        const sectionData = availableSectionsData[sectionId];
        if (sectionData) {
            element.innerHTML = `
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <i class="fas fa-grip-vertical text-muted"></i>
                    <div class="flex-grow-1">
                        <strong>${sectionData.title || sectionData.name}</strong>
                        <small class="text-muted d-block">${sectionData.type}</small>
                    </div>
                    <div class="d-flex align-items-center gap-2 section-width-controls">
                        <label class="small text-muted mb-0">{{ __('Width') }}:</label>
                        <input type="number" class="form-control form-control-sm section-width-input" 
                               min="1" max="12" value="${width}" 
                               style="width: 60px;" 
                               onkeypress="if(event.key === 'Enter') updateSectionWidth(this)">
                        <button type="button" class="btn btn-sm btn-primary" onclick="updateSectionWidth(this)" title="{{ __('Update Width') }}">
                            <i class="fas fa-check"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSection(this)" title="{{ __('Remove') }}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
        }
    }
    
    // Remove hint if exists
    const hint = element.parentElement.querySelector('.drop-zone-hint');
    if (hint && element.parentElement.querySelectorAll('.builder-section-item').length > 0) {
        hint.remove();
    }
    
    updateSectionPositions();
}


function removeSection(btn) {
    if (confirm('{{ __("Are you sure you want to remove this section?") }}')) {
        const element = btn.closest('.builder-section-item');
        const rowContent = element.parentElement;
        element.remove();
        
        // Add hint back if row is empty
        if (rowContent.querySelectorAll('.builder-section-item').length === 0) {
            rowContent.innerHTML = `
                <div class="col-12 text-center text-muted py-4 drop-zone-hint">
                    <i class="fas fa-hand-pointer fa-2x mb-2"></i>
                    <p class="mb-0">{{ __('Drag sections here or click to add') }}</p>
                </div>
            `;
        }
        
        updateSectionPositions();
    }
}

function updateSectionPositions() {
    // This will be called to update visual positions
    // Actual save happens on Save Layout button click
}

function saveLayout() {
    const rows = document.querySelectorAll('.builder-row');
    const layout = [];
    const sectionIdsInLayout = new Set();
    let globalOrder = 0;
    
    rows.forEach((row, rowIndex) => {
        const rowContent = row.querySelector('.builder-row-content');
        const sections = rowContent.querySelectorAll('.builder-section-item');
        
        sections.forEach((section, colIndex) => {
            const sectionId = section.dataset.sectionId;
            const width = parseInt(section.dataset.width || 12);
            
            if (sectionId) {
                sectionIdsInLayout.add(parseInt(sectionId));
                layout.push({
                    id: parseInt(sectionId),
                    row: rowIndex,
                    column: colIndex,
                    width: width,
                    order: globalOrder++,
                });
            }
        });
    });
    
    // Show loading
    const saveBtn = document.getElementById('saveLayoutBtn');
    const originalHtml = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>{{ __("Saving...") }}';
    
    // Send to server
    fetch('{{ route("admin.page-builder.save", $page) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({ layout: layout }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || '{{ __("Layout saved successfully!") }}');
        } else {
            alert(data.message || '{{ __("Error saving layout") }}');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('{{ __("Error saving layout") }}');
    })
    .finally(() => {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalHtml;
    });
}

function loadPageSections() {
    fetch('{{ route("admin.page-builder.sections", $page) }}')
        .then(response => response.json())
        .then(sections => {
            // Group sections by row
            const sectionsByRow = {};
            sections.forEach(section => {
                const row = section.builder_data?.row || 0;
                if (!sectionsByRow[row]) {
                    sectionsByRow[row] = [];
                }
                sectionsByRow[row].push(section);
            });
            
            // Clear canvas
            const canvas = document.getElementById('pageBuilderCanvas');
            canvas.innerHTML = '';
            rowCounter = 0;
            
            // Create rows
            const maxRow = Math.max(...Object.keys(sectionsByRow).map(Number), 0);
            for (let i = 0; i <= maxRow; i++) {
                const rowSections = sectionsByRow[i] || [];
                addRowWithSections(i, rowSections);
            }
            
            // If no sections, add one empty row
            if (Object.keys(sectionsByRow).length === 0) {
                addNewRow();
            }
        })
        .catch(error => {
            console.error('Error loading sections:', error);
        });
}

function addRowWithSections(rowIndex, sections) {
    const canvas = document.getElementById('pageBuilderCanvas');
    const row = document.createElement('div');
    row.className = 'builder-row mb-3';
    row.dataset.row = rowIndex;
    
    let sectionsHtml = '';
    if (sections.length > 0) {
        sections.forEach(section => {
            const width = section.builder_data?.width || 12;
            const colClass = width === 12 ? 'col-12' : `col-12 col-md-${width}`;
            sectionsHtml += `
                <div class="${colClass} builder-section-item" data-section-id="${section.id}" data-width="${width}">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <i class="fas fa-grip-vertical text-muted"></i>
                        <div class="flex-grow-1">
                            <strong>${section.title}</strong>
                            <small class="text-muted d-block">${section.type}</small>
                        </div>
                        <div class="d-flex align-items-center gap-2 section-width-controls">
                            <label class="small text-muted mb-0">{{ __('Width') }}:</label>
                            <input type="number" class="form-control form-control-sm section-width-input" 
                                   min="1" max="12" value="${width}" 
                                   style="width: 60px;" 
                                   onkeypress="if(event.key === 'Enter') updateSectionWidth(this)">
                            <button type="button" class="btn btn-sm btn-primary" onclick="updateSectionWidth(this)" title="{{ __('Update Width') }}">
                                <i class="fas fa-check"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSection(this)" title="{{ __('Remove') }}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
    } else {
        sectionsHtml = `
            <div class="col-12 text-center text-muted py-4 drop-zone-hint">
                <i class="fas fa-hand-pointer fa-2x mb-2"></i>
                <p class="mb-0">{{ __('Drag sections here or click to add') }}</p>
            </div>
        `;
    }
    
    row.innerHTML = `
        <div class="d-flex align-items-center mb-2">
            <span class="badge bg-secondary me-2">Row ${rowIndex + 1}</span>
            <button type="button" class="btn btn-sm btn-outline-danger ms-auto" onclick="removeRow(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="row g-2 builder-row-content" style="min-height: 80px; border: 2px dashed #dee2e6; border-radius: 8px; padding: 10px;">
            ${sectionsHtml}
        </div>
    `;
    
    canvas.appendChild(row);
    const rowContent = row.querySelector('.builder-row-content');
    initializeRowSortable(rowContent, rowIndex);
    rowCounter = Math.max(rowCounter, rowIndex + 1);
}
</script>
@endpush
@endsection

