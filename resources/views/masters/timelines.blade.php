@extends('layouts.app')

@section('title', 'Timelines - Master Data')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold">Timelines</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('masters.index') }}">Masters</a></li>
                    <li class="breadcrumb-item active">Timelines</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-primary" id="btnAdd"><i class="bi bi-plus-lg me-2"></i>Add Timeline</button>
    </div>

    <div class="card premium-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable">
                    <thead><tr><th>#</th><th>Name</th><th>Min Days</th><th>Max Days</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody id="dataBody"></tbody>
                </table>
            </div>
            <div id="loadingState" class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2 text-muted">Loading...</p></div>
            <div id="emptyState" class="text-center py-5 d-none"><i class="bi bi-calendar-range display-4 text-muted"></i><p class="mt-2 text-muted">No timelines found</p></div>
        </div>
    </div>
</div>

<div class="modal fade" id="itemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="modalTitle">Add Timeline</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form id="itemForm">
                    <input type="hidden" id="itemId">
                    <div class="mb-3"><label class="form-label">Name <span class="text-danger">*</span></label><input type="text" class="form-control" name="name" id="itemName" placeholder="e.g., Immediate, 1-3 Months" required></div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Min Days</label><input type="number" class="form-control" name="min_days" id="itemMin" placeholder="0"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Max Days</label><input type="number" class="form-control" name="max_days" id="itemMax" placeholder="90"></div>
                    </div>
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
const API_BASE='/admin/masters/timelines';let itemsData=[];
document.addEventListener('DOMContentLoaded',()=>{loadItems();document.getElementById('btnAdd').addEventListener('click',openCreateModal);document.getElementById('btnSave').addEventListener('click',saveItem)});
async function loadItems(){showLoading();try{const r=await fetch(API_BASE,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},credentials:'same-origin'});const d=await r.json();itemsData=d.data||d;renderItems()}catch(e){showEmpty()}}
function renderItems(){const tbody=document.getElementById('dataBody');if(!itemsData||!itemsData.length){showEmpty();return}tbody.innerHTML=itemsData.map((i,x)=>`<tr><td>${x+1}</td><td><strong>${i.name}</strong></td><td>${i.min_days||'-'}</td><td>${i.max_days||'-'}</td><td>${i.sort_order||0}</td><td><span class="badge ${i.is_active?'badge-active':'badge-inactive'}">${i.is_active?'Active':'Inactive'}</span></td><td><button class="btn btn-sm btn-outline-primary btn-action me-1" onclick="editItem(${i.id})"><i class="bi bi-pencil"></i></button><button class="btn btn-sm btn-outline-danger btn-action" onclick="deleteItem(${i.id})"><i class="bi bi-trash"></i></button></td></tr>`).join('');hideLoading();document.getElementById('dataTable').classList.remove('d-none')}
function openCreateModal(){document.getElementById('modalTitle').textContent='Add Timeline';document.getElementById('itemForm').reset();document.getElementById('itemId').value='';document.getElementById('itemActive').checked=true;new bootstrap.Modal(document.getElementById('itemModal')).show()}
function editItem(id){const i=itemsData.find(x=>x.id===id);if(!i)return;document.getElementById('modalTitle').textContent='Edit Timeline';document.getElementById('itemId').value=i.id;document.getElementById('itemName').value=i.name;document.getElementById('itemMin').value=i.min_days||'';document.getElementById('itemMax').value=i.max_days||'';document.getElementById('itemOrder').value=i.sort_order||0;document.getElementById('itemActive').checked=i.is_active;new bootstrap.Modal(document.getElementById('itemModal')).show()}
async function saveItem(){const id=document.getElementById('itemId').value;const data={name:document.getElementById('itemName').value,min_days:document.getElementById('itemMin').value||null,max_days:document.getElementById('itemMax').value||null,sort_order:parseInt(document.getElementById('itemOrder').value)||0,is_active:document.getElementById('itemActive').checked};try{const r=await fetch(id?`${API_BASE}/${id}`:API_BASE,{method:id?'PUT':'POST',headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'X-Requested-With':'XMLHttpRequest'},body:JSON.stringify(data),credentials:'same-origin'});const res=await r.json();if(!r.ok)throw new Error(res.message);bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();loadItems();alert(res.message||'Saved!')}catch(e){alert(e.message||'Error')}}
async function deleteItem(id){if(!confirm('Delete?'))return;try{const r=await fetch(`${API_BASE}/${id}`,{method:'DELETE',headers:{'Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'X-Requested-With':'XMLHttpRequest'},credentials:'same-origin'});const res=await r.json();if(!r.ok)throw new Error(res.message);loadItems();alert(res.message||'Deleted!')}catch(e){alert(e.message||'Error')}}
function showLoading(){document.getElementById('loadingState').classList.remove('d-none');document.getElementById('emptyState').classList.add('d-none');document.getElementById('dataTable').classList.add('d-none')}function hideLoading(){document.getElementById('loadingState').classList.add('d-none')}function showEmpty(){hideLoading();document.getElementById('emptyState').classList.remove('d-none');document.getElementById('dataTable').classList.add('d-none')}
</script>
@endpush
