@extends('layouts.app')

@section('title', 'States - Master Data')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold">States</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('masters.index') }}">Masters</a></li>
                    <li class="breadcrumb-item active">States</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-primary" id="btnAdd">
            <i class="bi bi-plus-lg me-2"></i>Add State
        </button>
    </div>

    <!-- Filter -->
    <div class="card premium-card mb-4">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <label class="form-label mb-1 small">Filter by Country</label>
                    <select class="form-select" id="filterCountry">
                        <option value="">All Countries</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card premium-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable">
                    <thead>
                        <tr>
                            <th width="60">#</th>
                            <th>Name</th>
                            <th>Country</th>
                            <th>Code</th>
                            <th width="100">Status</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="dataBody"></tbody>
                </table>
            </div>
            
            <div id="loadingState" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 text-muted">Loading states...</p>
            </div>
            
            <div id="emptyState" class="text-center py-5 d-none">
                <i class="bi bi-map display-4 text-muted"></i>
                <p class="mt-2 text-muted">No states found</p>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="itemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add State</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="itemForm">
                    <input type="hidden" id="itemId">
                    <div class="mb-3">
                        <label class="form-label">Country <span class="text-danger">*</span></label>
                        <select class="form-select" name="country_id" id="countrySelect" required>
                            <option value="">Select Country</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="itemName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" class="form-control" name="code" id="itemCode" placeholder="e.g., MH, DL">
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="itemActive" name="is_active" checked>
                            <label class="form-check-label" for="itemActive">Active</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnSave">
                    <i class="bi bi-check-lg me-2"></i>Save
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .premium-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(184, 149, 106, 0.2);
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
    }
    .badge-active { background: linear-gradient(135deg, #10b981, #059669); color: white; }
    .badge-inactive { background: #e5e7eb; color: #6b7280; }
    .btn-action { padding: 0.375rem 0.5rem; border-radius: 6px; }
</style>
@endpush

@push('scripts')
<script>
    const API_BASE = '/admin/masters/states';
    const COUNTRIES_API = '/admin/masters/countries';
    let itemsData = [];
    let countriesData = [];
    
    document.addEventListener('DOMContentLoaded', function() {
        loadCountries();
        loadItems();
        
        document.getElementById('btnAdd').addEventListener('click', openCreateModal);
        document.getElementById('btnSave').addEventListener('click', saveItem);
        document.getElementById('filterCountry').addEventListener('change', loadItems);
    });
    
    async function loadCountries() {
        try {
            const response = await fetch(COUNTRIES_API, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            });
            const data = await response.json();
            countriesData = data.data || data;
            
            const options = countriesData.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
            document.getElementById('filterCountry').innerHTML = '<option value="">All Countries</option>' + options;
            document.getElementById('countrySelect').innerHTML = '<option value="">Select Country</option>' + options;
        } catch (error) {
            console.error('Error loading countries:', error);
        }
    }
    
    async function loadItems() {
        showLoading();
        try {
            let url = API_BASE;
            const countryFilter = document.getElementById('filterCountry').value;
            if (countryFilter) {
                url = `/admin/masters/states/by-country/${countryFilter}`;
            }
            
            const response = await fetch(url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            });
            const data = await response.json();
            itemsData = data.data || data;
            renderItems();
        } catch (error) {
            console.error('Error loading states:', error);
            showEmpty();
        }
    }
    
    function renderItems() {
        const tbody = document.getElementById('dataBody');
        if (!itemsData || itemsData.length === 0) { showEmpty(); return; }
        
        tbody.innerHTML = itemsData.map((item, index) => `
            <tr>
                <td>${index + 1}</td>
                <td><strong>${item.name}</strong></td>
                <td>${item.country?.name || '-'}</td>
                <td><code>${item.code || '-'}</code></td>
                <td><span class="badge ${item.is_active ? 'badge-active' : 'badge-inactive'}">${item.is_active ? 'Active' : 'Inactive'}</span></td>
                <td>
                    <button class="btn btn-sm btn-outline-primary btn-action me-1" onclick="editItem(${item.id})"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-outline-danger btn-action" onclick="deleteItem(${item.id})"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
        `).join('');
        
        hideLoading();
        document.getElementById('dataTable').classList.remove('d-none');
    }
    
    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Add State';
        document.getElementById('itemForm').reset();
        document.getElementById('itemId').value = '';
        document.getElementById('itemActive').checked = true;
        new bootstrap.Modal(document.getElementById('itemModal')).show();
    }
    
    function editItem(id) {
        const item = itemsData.find(i => i.id === id);
        if (!item) return;
        
        document.getElementById('modalTitle').textContent = 'Edit State';
        document.getElementById('itemId').value = item.id;
        document.getElementById('countrySelect').value = item.country_id;
        document.getElementById('itemName').value = item.name;
        document.getElementById('itemCode').value = item.code || '';
        document.getElementById('itemActive').checked = item.is_active;
        
        new bootstrap.Modal(document.getElementById('itemModal')).show();
    }
    
    async function saveItem() {
        const id = document.getElementById('itemId').value;
        const isEdit = !!id;
        
        const data = {
            country_id: document.getElementById('countrySelect').value,
            name: document.getElementById('itemName').value,
            code: document.getElementById('itemCode').value || null,
            is_active: document.getElementById('itemActive').checked
        };
        
        try {
            const response = await fetch(isEdit ? `${API_BASE}/${id}` : API_BASE, {
                method: isEdit ? 'PUT' : 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data),
                credentials: 'same-origin'
            });
            
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Failed to save');
            
            bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();
            loadItems();
            alert(result.message || 'Saved successfully!');
        } catch (error) {
            alert(error.message || 'Failed to save.');
        }
    }
    
    async function deleteItem(id) {
        if (!confirm('Are you sure you want to delete this state?')) return;
        
        try {
            const response = await fetch(`${API_BASE}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Failed to delete');
            
            loadItems();
            alert(result.message || 'Deleted successfully!');
        } catch (error) {
            alert(error.message || 'Failed to delete.');
        }
    }
    
    function showLoading() {
        document.getElementById('loadingState').classList.remove('d-none');
        document.getElementById('emptyState').classList.add('d-none');
        document.getElementById('dataTable').classList.add('d-none');
    }
    function hideLoading() { document.getElementById('loadingState').classList.add('d-none'); }
    function showEmpty() { hideLoading(); document.getElementById('emptyState').classList.remove('d-none'); document.getElementById('dataTable').classList.add('d-none'); }
</script>
@endpush
