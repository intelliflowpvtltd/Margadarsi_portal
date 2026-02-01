@extends('layouts.app')

@section('title', 'Countries - Master Data')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold">Countries</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('masters.index') }}">Masters</a></li>
                    <li class="breadcrumb-item active">Countries</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-primary" id="btnAddCountry">
            <i class="bi bi-plus-lg me-2"></i>Add Country
        </button>
    </div>

    <!-- Data Table Card -->
    <div class="card premium-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="countriesTable">
                    <thead>
                        <tr>
                            <th width="60">#</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Phone Code</th>
                            <th width="100">Status</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="countriesBody">
                        <!-- Populated via JS -->
                    </tbody>
                </table>
            </div>
            
            <!-- Loading State -->
            <div id="loadingState" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading countries...</p>
            </div>
            
            <!-- Empty State -->
            <div id="emptyState" class="text-center py-5 d-none">
                <i class="bi bi-globe display-4 text-muted"></i>
                <p class="mt-2 text-muted">No countries found</p>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="countryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Country</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="countryForm">
                    <input type="hidden" id="countryId">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="countryName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="code" id="countryCode" maxlength="3" placeholder="e.g., IN, US, UK" required>
                        <small class="text-muted">2-3 letter country code</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Code</label>
                        <input type="text" class="form-control" name="phone_code" id="countryPhoneCode" placeholder="e.g., +91, +1">
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="countryActive" name="is_active" checked>
                            <label class="form-check-label" for="countryActive">Active</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnSaveCountry">
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
    
    .badge-active {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }
    
    .badge-inactive {
        background: #e5e7eb;
        color: #6b7280;
    }
    
    .btn-action {
        padding: 0.375rem 0.5rem;
        border-radius: 6px;
    }
</style>
@endpush

@push('scripts')
<script>
    const API_BASE = '/admin/masters/countries';
    let countriesData = [];
    
    document.addEventListener('DOMContentLoaded', function() {
        loadCountries();
        
        document.getElementById('btnAddCountry').addEventListener('click', openCreateModal);
        document.getElementById('btnSaveCountry').addEventListener('click', saveCountry);
    });
    
    async function loadCountries() {
        showLoading();
        try {
            const response = await fetch(API_BASE, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) throw new Error('Failed to load countries');
            
            const data = await response.json();
            countriesData = data.data || data;
            renderCountries();
        } catch (error) {
            console.error('Error loading countries:', error);
            showEmpty();
        }
    }
    
    function renderCountries() {
        const tbody = document.getElementById('countriesBody');
        
        if (!countriesData || countriesData.length === 0) {
            showEmpty();
            return;
        }
        
        tbody.innerHTML = countriesData.map((country, index) => `
            <tr>
                <td>${index + 1}</td>
                <td><strong>${country.name}</strong></td>
                <td><code>${country.code}</code></td>
                <td>${country.phone_code || '-'}</td>
                <td>
                    <span class="badge ${country.is_active ? 'badge-active' : 'badge-inactive'}">
                        ${country.is_active ? 'Active' : 'Inactive'}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-primary btn-action me-1" onclick="editCountry(${country.id})">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger btn-action" onclick="deleteCountry(${country.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
        
        hideLoading();
        document.getElementById('countriesTable').classList.remove('d-none');
    }
    
    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Add Country';
        document.getElementById('countryForm').reset();
        document.getElementById('countryId').value = '';
        document.getElementById('countryActive').checked = true;
        new bootstrap.Modal(document.getElementById('countryModal')).show();
    }
    
    async function editCountry(id) {
        const country = countriesData.find(c => c.id === id);
        if (!country) return;
        
        document.getElementById('modalTitle').textContent = 'Edit Country';
        document.getElementById('countryId').value = country.id;
        document.getElementById('countryName').value = country.name;
        document.getElementById('countryCode').value = country.code;
        document.getElementById('countryPhoneCode').value = country.phone_code || '';
        document.getElementById('countryActive').checked = country.is_active;
        
        new bootstrap.Modal(document.getElementById('countryModal')).show();
    }
    
    async function saveCountry() {
        const id = document.getElementById('countryId').value;
        const isEdit = !!id;
        
        const data = {
            name: document.getElementById('countryName').value,
            code: document.getElementById('countryCode').value.toUpperCase(),
            phone_code: document.getElementById('countryPhoneCode').value || null,
            is_active: document.getElementById('countryActive').checked
        };
        
        try {
            const url = isEdit ? `${API_BASE}/${id}` : API_BASE;
            const method = isEdit ? 'PUT' : 'POST';
            
            const response = await fetch(url, {
                method: method,
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
            
            if (!response.ok) {
                throw new Error(result.message || 'Failed to save country');
            }
            
            bootstrap.Modal.getInstance(document.getElementById('countryModal')).hide();
            loadCountries();
            alert(result.message || 'Country saved successfully!');
        } catch (error) {
            console.error('Error saving country:', error);
            alert(error.message || 'Failed to save country.');
        }
    }
    
    async function deleteCountry(id) {
        if (!confirm('Are you sure you want to delete this country?')) return;
        
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
            
            if (!response.ok) {
                throw new Error(result.message || 'Failed to delete country');
            }
            
            loadCountries();
            alert(result.message || 'Country deleted successfully!');
        } catch (error) {
            console.error('Error deleting country:', error);
            alert(error.message || 'Failed to delete country.');
        }
    }
    
    function showLoading() {
        document.getElementById('loadingState').classList.remove('d-none');
        document.getElementById('emptyState').classList.add('d-none');
        document.getElementById('countriesTable').classList.add('d-none');
    }
    
    function hideLoading() {
        document.getElementById('loadingState').classList.add('d-none');
    }
    
    function showEmpty() {
        hideLoading();
        document.getElementById('emptyState').classList.remove('d-none');
        document.getElementById('countriesTable').classList.add('d-none');
    }
</script>
@endpush
