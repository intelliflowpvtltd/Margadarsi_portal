@extends('layouts.app')

@section('title', 'Generic Masters - Master Data')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold">Generic Masters</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('masters.index') }}">Masters</a></li>
                    <li class="breadcrumb-item active">Generic Masters</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-primary" id="btnAdd"><i class="bi bi-plus-lg me-2"></i>Add Entry</button>
    </div>

    <div class="card premium-card mb-4">
        <div class="card-body py-3">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label mb-1 small">Filter by Type</label>
                    <select class="form-select" id="filterType"><option value="">All Types</option></select>
                </div>
            </div>
        </div>
    </div>

    <div class="card premium-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable">
                    <thead><tr><th>#</th><th>Type</th><th>Value</th><th>Description</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody id="dataBody"></tbody>
                </table>
            </div>
            <div id="loadingState" class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2 text-muted">Loading...</p></div>
            <div id="emptyState" class="text-center py-5 d-none"><i class="bi bi-gear display-4 text-muted"></i><p class="mt-2 text-muted">No entries found</p></div>
        </div>
    </div>
</div>

<div class="modal fade" id="itemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="modalTitle">Add Entry</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form id="itemForm">
                    <input type="hidden" id="itemId">
                    <div class="mb-3"><label class="form-label">Type <span class="text-danger">*</span></label><input type="text" class="form-control" name="type" id="itemType" placeholder="e.g., DOCUMENT_TYPE, OCCUPATION" required></div>
                    <div class="mb-3"><label class="form-label">Value <span class="text-danger">*</span></label><input type="text" class="form-control" name="value" id="itemValue" required></div>
                    <div class="mb-3"><label class="form-label">Description</label><textarea class="form-control" name="description" id="itemDesc" rows="2"></textarea></div>
                    <div class="mb-3"><label class="form-label">Sort Order</label><input type="number" class="form-control" name="sort_order" id="itemOrder" value="0"></div>
                    <div class="mb-3"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="itemActive" checked><label class="form-check-label" for="itemActive">Active</label></div></div>
                </form>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="btnSave"><i class="bi bi-check-lg me-2"></i>Save</button></div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>.premium-card{background:rgba(255,255,255,0.95);backdrop-filter:blur(10px);border:1px solid rgba(184,149,106,0.2);border-radius:12px;box-shadow:0 4px 16px rgba(0,0,0,0.05)}.badge-active{background:linear-gradient(135deg,#10b981,#059669);color:#fff}.badge-inactive{background:#e5e7eb;color:#6b7280}.btn-action{padding:.375rem .5rem;border-radius:6px}</style>
@endpush

@push('scripts')
<script>
const API_BASE='/admin/masters/generic-masters';let itemsData=[],typesData=[];
document.addEventListener('DOMContentLoaded',()=>{loadTypes();loadItems();document.getElementById('btnAdd').addEventListener('click',openCreateModal);document.getElementById('btnSave').addEventListener('click',saveItem);document.getElementById('filterType').addEventListener('change',loadItems)});
async function loadTypes(){try{const r=await fetch('/admin/masters/generic-masters/types/list',{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},credentials:'same-origin'});const d=await r.json();typesData=d.data||d||[];document.getElementById('filterType').innerHTML='<option value="">All Types</option>'+typesData.map(t=>`<option value="${t}">${t}</option>`).join('')}catch(e){}}
async function loadItems(){showLoading();try{let url=API_BASE;const type=document.getElementById('filterType').value;if(type)url=`/admin/masters/generic-masters/by-type/${type}`;const r=await fetch(url,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},credentials:'same-origin'});const d=await r.json();itemsData=d.data||d;renderItems()}catch(e){showEmpty()}}
function renderItems(){const tbody=document.getElementById('dataBody');if(!itemsData||!itemsData.length){showEmpty();return}tbody.innerHTML=itemsData.map((i,x)=>`<tr><td>${x+1}</td><td><code>${i.type}</code></td><td><strong>${i.value}</strong></td><td>${i.description||'-'}</td><td>${i.sort_order||0}</td><td><span class="badge ${i.is_active?'badge-active':'badge-inactive'}">${i.is_active?'Active':'Inactive'}</span></td><td><button class="btn btn-sm btn-outline-primary btn-action me-1" onclick="editItem(${i.id})"><i class="bi bi-pencil"></i></button><button class="btn btn-sm btn-outline-danger btn-action" onclick="deleteItem(${i.id})"><i class="bi bi-trash"></i></button></td></tr>`).join('');hideLoading();document.getElementById('dataTable').classList.remove('d-none')}
function openCreateModal(){document.getElementById('modalTitle').textContent='Add Entry';document.getElementById('itemForm').reset();document.getElementById('itemId').value='';document.getElementById('itemActive').checked=true;new bootstrap.Modal(document.getElementById('itemModal')).show()}
function editItem(id){const i=itemsData.find(x=>x.id===id);if(!i)return;document.getElementById('modalTitle').textContent='Edit Entry';document.getElementById('itemId').value=i.id;document.getElementById('itemType').value=i.type;document.getElementById('itemValue').value=i.value;document.getElementById('itemDesc').value=i.description||'';document.getElementById('itemOrder').value=i.sort_order||0;document.getElementById('itemActive').checked=i.is_active;new bootstrap.Modal(document.getElementById('itemModal')).show()}
async function saveItem(){const id=document.getElementById('itemId').value;const data={type:document.getElementById('itemType').value.toUpperCase(),value:document.getElementById('itemValue').value,description:document.getElementById('itemDesc').value||null,sort_order:parseInt(document.getElementById('itemOrder').value)||0,is_active:document.getElementById('itemActive').checked};try{const r=await fetch(id?`${API_BASE}/${id}`:API_BASE,{method:id?'PUT':'POST',headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'X-Requested-With':'XMLHttpRequest'},body:JSON.stringify(data),credentials:'same-origin'});const res=await r.json();if(!r.ok)throw new Error(res.message);bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();loadItems();loadTypes();alert(res.message||'Saved!')}catch(e){alert(e.message||'Error')}}
async function deleteItem(id){if(!confirm('Delete?'))return;try{const r=await fetch(`${API_BASE}/${id}`,{method:'DELETE',headers:{'Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'X-Requested-With':'XMLHttpRequest'},credentials:'same-origin'});const res=await r.json();if(!r.ok)throw new Error(res.message);loadItems();alert(res.message||'Deleted!')}catch(e){alert(e.message||'Error')}}
function showLoading(){document.getElementById('loadingState').classList.remove('d-none');document.getElementById('emptyState').classList.add('d-none');document.getElementById('dataTable').classList.add('d-none')}function hideLoading(){document.getElementById('loadingState').classList.add('d-none')}function showEmpty(){hideLoading();document.getElementById('emptyState').classList.remove('d-none');document.getElementById('dataTable').classList.add('d-none')}
</script>
@endpush
