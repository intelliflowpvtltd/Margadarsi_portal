@extends('layouts.app')

@section('title', 'Departments')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Departments</h2>
                @can('projects.create')
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDepartmentModal">
                    <i class="bi bi-plus-circle me-2"></i>Add Department
                </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Project</label>
                    <select id="filterProject" class="form-select">
                        <option value="">All Projects</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select id="filterStatus" class="form-select">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" id="searchInput" class="form-control" placeholder="Search departments...">
                </div>
            </div>
        </div>
    </div>

    <!-- Departments Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="departmentsTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Project</th>
                            <th>Type</th>
                            <th>Roles</th>
                            <th>Users</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="departmentsTableBody">
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Department Modal -->
<div class="modal fade" id="createDepartmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createDepartmentForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Project <span class="text-danger">*</span></label>
                        <select name="project_id" class="form-select" required>
                            <option value="">Select Project</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" name="slug" class="form-control" required>
                        <small class="text-muted">Use: management, sales, or pre_sales</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" checked>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Department</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let departments = [];
    let projects = [];

    // Load initial data
    loadProjects();
    loadDepartments();

    // Load projects for filter and form
    function loadProjects() {
        $.get('/projects', function(response) {
            projects = response.data;
            const options = projects.map(p => 
                `<option value="${p.id}">${p.name}</option>`
            ).join('');
            $('#filterProject, select[name="project_id"]').append(options);
        });
    }

    // Load departments
    function loadDepartments() {
        const params = {
            project_id: $('#filterProject').val(),
            is_active: $('#filterStatus').val(),
            search: $('#searchInput').val()
        };

        $.get('/departments', params, function(response) {
            departments = response.data;
            renderTable();
        }).fail(function(xhr) {
            showError('Failed to load departments');
            $('#departmentsTableBody').html('<tr><td colspan="7" class="text-center text-danger">Error loading data</td></tr>');
        });
    }

    // Render table
    function renderTable() {
        if (departments.length === 0) {
            $('#departmentsTableBody').html('<tr><td colspan="7" class="text-center">No departments found</td></tr>');
            return;
        }

        const html = departments.map(dept => {
            const statusBadge = dept.is_active 
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-secondary">Inactive</span>';
            
            const typeBadge = dept.slug === 'management' 
                ? '<span class="badge bg-primary">Management</span>'
                : dept.slug === 'sales'
                ? '<span class="badge bg-info">Sales</span>'
                : '<span class="badge bg-warning">Pre-Sales</span>';

            return `
                <tr>
                    <td><strong>${dept.name}</strong></td>
                    <td>${dept.project?.name || 'N/A'}</td>
                    <td>${typeBadge}</td>
                    <td>${dept.roles_count || 0}</td>
                    <td>${dept.users_count || 0}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <a href="/departments/${dept.id}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                        @can('projects.update')
                        <button class="btn btn-sm btn-outline-warning" onclick="editDepartment(${dept.id})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        @endcan
                        @can('projects.delete')
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteDepartment(${dept.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                        @endcan
                    </td>
                </tr>
            `;
        }).join('');

        $('#departmentsTableBody').html(html);
    }

    // Filters
    $('#filterProject, #filterStatus').on('change', loadDepartments);
    $('#searchInput').on('keyup', debounce(loadDepartments, 500));

    // Create department
    $('#createDepartmentForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serializeArray().reduce((obj, item) => {
            obj[item.name] = item.value;
            return obj;
        }, {});
        formData.is_active = $('input[name="is_active"]').is(':checked');

        $.post('/departments', formData, function(response) {
            $('#createDepartmentModal').modal('hide');
            $('#createDepartmentForm')[0].reset();
            showSuccess('Department created successfully');
            loadDepartments();
        }).fail(function(xhr) {
            showError(xhr.responseJSON?.message || 'Failed to create department');
        });
    });

    // Helper functions
    function showSuccess(message) {
        alert(message); // Replace with toast notification
    }

    function showError(message) {
        alert(message); // Replace with toast notification
    }

    function debounce(func, wait) {
        let timeout;
        return function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, arguments), wait);
        };
    }
});
</script>
@endpush
@endsection
