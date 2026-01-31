@extends('layouts.app')

@section('title', $company->name)

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('companies.index') }}">Companies</a></li>
<li class="breadcrumb-item active">{{ $company->name }}</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-start">
        <div class="d-flex align-items-center gap-3">
            @if($company->logo)
            <img src="{{ $company->logo }}" alt="{{ $company->name }}" class="company-logo-large">
            @else
            <div class="company-logo-placeholder-large">
                {{ strtoupper(substr($company->name, 0, 1)) }}
            </div>
            @endif
            <div>
                <h1 class="page-title mb-1">{{ $company->name }}</h1>
                @if($company->tagline)
                <p class="text-muted mb-2">{{ $company->tagline }}</p>
                @endif
                <span class="badge-{{ $company->is_active ? 'active' : 'inactive' }}">
                    {{ $company->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('companies.edit', $company) }}" class="btn btn-maroon">
                <i class="bi bi-pencil me-2"></i>
                Edit Company
            </a>
            <button class="btn btn-outline-danger" onclick="deleteCompany()">
                <i class="bi bi-trash me-2"></i>
                Delete
            </button>
        </div>
    </div>
</div>

<!-- Company Details Cards -->
<div class="row g-4">
    <!-- Basic Information -->
    <div class="col-md-6">
        <div class="info-card">
            <h5 class="card-title">
                <i class="bi bi-info-circle me-2 text-gold"></i>
                Basic Information
            </h5>
            <div class="info-grid">
                <div class="info-item">
                    <label>Company Name</label>
                    <span>{{ $company->name }}</span>
                </div>
                <div class="info-item">
                    <label>Legal Name</label>
                    <span>{{ $company->legal_name ?? '-' }}</span>
                </div>
                @if($company->description)
                <div class="info-item full-width">
                    <label>Description</label>
                    <span>{{ $company->description }}</span>
                </div>
                @endif
                <div class="info-item">
                    <label>Created At</label>
                    <span>{{ $company->created_at->format('d M Y, h:i A') }}</span>
                </div>
                <div class="info-item">
                    <label>Last Updated</label>
                    <span>{{ $company->updated_at->format('d M Y, h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Registration Details -->
    <div class="col-md-6">
        <div class="info-card">
            <h5 class="card-title">
                <i class="bi bi-file-text me-2 text-gold"></i>
                Registration Details
            </h5>
            <div class="info-grid">
                <div class="info-item">
                    <label>PAN Number</label>
                    <span class="copyable" onclick="copyToClipboard('{{ $company->pan_number }}')">
                        {{ $company->pan_number ?? '-' }}
                        @if($company->pan_number)
                        <i class="bi bi-clipboard ms-2"></i>
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <label>GSTIN</label>
                    <span class="copyable" onclick="copyToClipboard('{{ $company->gstin }}')">
                        {{ $company->gstin ?? '-' }}
                        @if($company->gstin)
                        <i class="bi bi-clipboard ms-2"></i>
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <label>CIN</label>
                    <span class="copyable" onclick="copyToClipboard('{{ $company->cin }}')">
                        {{ $company->cin ?? '-' }}
                        @if($company->cin)
                        <i class="bi bi-clipboard ms-2"></i>
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <label>RERA Number</label>
                    <span>{{ $company->rera_number ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <label>Incorporation Date</label>
                    <span>{{ $company->incorporation_date ? $company->incorporation_date->format('d M Y') : '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="col-md-6">
        <div class="info-card">
            <h5 class="card-title">
                <i class="bi bi-telephone me-2 text-gold"></i>
                Contact Information
            </h5>
            <div class="info-grid">
                <div class="info-item">
                    <label>Email</label>
                    <span class="copyable" onclick="copyToClipboard('{{ $company->email }}')">
                        <a href="mailto:{{ $company->email }}" class="text-gold">{{ $company->email ?? '-' }}</a>
                        @if($company->email)
                        <i class="bi bi-clipboard ms-2"></i>
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <label>Phone</label>
                    <span class="copyable" onclick="copyToClipboard('{{ $company->phone }}')">
                        <a href="tel:{{ $company->phone }}" class="text-gold">{{ $company->phone ?? '-' }}</a>
                        @if($company->phone)
                        <i class="bi bi-clipboard ms-2"></i>
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <label>Alternate Phone</label>
                    <span>{{ $company->alternate_phone ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <label>WhatsApp</label>
                    <span>{{ $company->whatsapp ?? '-' }}</span>
                </div>
                <div class="info-item full-width">
                    <label>Website</label>
                    <span>
                        @if($company->website)
                        <a href="{{ $company->website }}" target="_blank" class="text-gold">
                            {{ $company->website }} <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                        @else
                        -
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Addresses -->
    <div class="col-md-6">
        <div class="info-card">
            <h5 class="card-title">
                <i class="bi bi-geo-alt me-2 text-gold"></i>
                Addresses
            </h5>

            <!-- Registered Address -->
            <div class="mb-3">
                <h6 class="text-maroon mb-2">Registered Office</h6>
                @if($company->registered_full_address)
                <p class="mb-0">{{ $company->registered_full_address }}</p>
                @else
                <p class="text-muted mb-0">Not provided</p>
                @endif
            </div>

            <!-- Corporate Address -->
            <div>
                <h6 class="text-maroon mb-2">Corporate Office</h6>
                @if($company->corporate_full_address)
                <p class="mb-0">{{ $company->corporate_full_address }}</p>
                @else
                <p class="text-muted mb-0">Not provided</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Social Media -->
    @if($company->facebook_url || $company->instagram_url || $company->linkedin_url || $company->twitter_url || $company->youtube_url)
    <div class="col-12">
        <div class="info-card">
            <h5 class="card-title">
                <i class="bi bi-share me-2 text-gold"></i>
                Social Media
            </h5>
            <div class="social-links">
                @if($company->facebook_url)
                <a href="{{ $company->facebook_url }}" target="_blank" class="social-link facebook">
                    <i class="bi bi-facebook"></i>
                    <span>Facebook</span>
                </a>
                @endif
                @if($company->instagram_url)
                <a href="{{ $company->instagram_url }}" target="_blank" class="social-link instagram">
                    <i class="bi bi-instagram"></i>
                    <span>Instagram</span>
                </a>
                @endif
                @if($company->linkedin_url)
                <a href="{{ $company->linkedin_url }}" target="_blank" class="social-link linkedin">
                    <i class="bi bi-linkedin"></i>
                    <span>LinkedIn</span>
                </a>
                @endif
                @if($company->twitter_url)
                <a href="{{ $company->twitter_url }}" target="_blank" class="social-link twitter">
                    <i class="bi bi-twitter"></i>
                    <span>Twitter</span>
                </a>
                @endif
                @if($company->youtube_url)
                <a href="{{ $company->youtube_url }}" target="_blank" class="social-link youtube">
                    <i class="bi bi-youtube"></i>
                    <span>YouTube</span>
                </a>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Related Data -->
    <div class="col-12">
        <div class="info-card">
            <h5 class="card-title">
                <i class="bi bi-diagram-3 me-2 text-gold"></i>
                Related Data
            </h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="stat-card">
                        <i class="bi bi-briefcase stat-icon"></i>
                        <div>
                            <h3>{{ $company->projects()->count() }}</h3>
                            <p>Projects</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <i class="bi bi-people stat-icon"></i>
                        <div>
                            <h3>{{ $company->users()->count() }}</h3>
                            <p>Users</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <i class="bi bi-shield-check stat-icon"></i>
                        <div>
                            <h3>{{ $company->roles()->count() }}</h3>
                            <p>Roles</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Company Logo Large */
    .company-logo-large {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 12px;
        border: 2px solid rgba(184, 149, 106, 0.3);
    }

    .company-logo-placeholder-large {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--color-coffee-gold), var(--color-coffee-gold-dark));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 2rem;
    }

    /* Custom Dark Maroon Button */
    .btn-maroon {
        background: linear-gradient(135deg, var(--color-dark-maroon), var(--color-maroon-light));
        color: white;
        border: none;
        padding: 0.625rem 1.25rem;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(128, 0, 32, 0.2);
    }

    .btn-maroon:hover {
        background: linear-gradient(135deg, #6B001B, var(--color-dark-maroon));
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(128, 0, 32, 0.3);
        color: white;
    }

    .btn-maroon:active {
        transform: translateY(0);
        box-shadow: 0 2px 6px rgba(128, 0, 32, 0.2);
    }

    /* Info Card */
    .info-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(184, 149, 106, 0.2);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        height: 100%;
    }

    .card-title {
        color: var(--color-dark-maroon);
        font-family: var(--font-primary);
        font-size: 1.25rem;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid rgba(184, 149, 106, 0.2);
    }

    /* Info Grid */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.25rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .info-item.full-width {
        grid-column: 1 / -1;
    }

    .info-item label {
        font-size: 0.8125rem;
        color: var(--color-text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-item span {
        color: var(--color-text-dark);
        font-size: 0.9375rem;
        font-weight: 500;
    }

    .copyable {
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
    }

    .copyable:hover {
        color: var(--color-coffee-gold) !important;
    }

    .copyable i {
        opacity: 0.5;
        font-size: 0.875rem;
    }

    .copyable:hover i {
        opacity: 1;
    }

    /* Social Links */
    .social-links {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .social-link {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        border-radius: 8px;
        text-decoration: none;
        color: white;
        transition: all 0.2s ease;
        font-weight: 600;
    }

    .social-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        color: white;
    }

    .social-link.facebook {
        background: linear-gradient(135deg, #1877F2, #0C63D4);
    }

    .social-link.instagram {
        background: linear-gradient(135deg, #E4405F, #C13584);
    }

    .social-link.linkedin {
        background: linear-gradient(135deg, #0A66C2, #004182);
    }

    .social-link.twitter {
        background: linear-gradient(135deg, #1DA1F2, #0C85D0);
    }

    .social-link.youtube {
        background: linear-gradient(135deg, #FF0000, #CC0000);
    }

    /* Stat Card */
    .stat-card {
        background: linear-gradient(135deg, rgba(128, 0, 32, 0.05), rgba(184, 149, 106, 0.05));
        border: 1px solid rgba(184, 149, 106, 0.2);
        border-radius: 10px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(184, 149, 106, 0.15);
    }

    .stat-icon {
        font-size: 2.5rem;
        color: var(--color-coffee-gold);
    }

    .stat-card h3 {
        color: var(--color-dark-maroon);
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
    }

    .stat-card p {
        color: var(--color-text-secondary);
        font-size: 0.875rem;
        font-weight: 600;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .text-gold {
        color: var(--color-coffee-gold) !important;
    }

    .text-gold:hover {
        color: var(--color-coffee-gold-dark) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    function copyToClipboard(text) {
        if (!text || text === '-') return;

        navigator.clipboard.writeText(text).then(() => {
            // Show a temporary success message
            const alert = document.createElement('div');
            alert.className = 'alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3';
            alert.style.zIndex = '9999';
            alert.textContent = 'Copied to clipboard!';
            document.body.appendChild(alert);

            setTimeout(() => {
                alert.remove();
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy:', err);
        });
    }

    function deleteCompany() {
        if (!confirm('Are you sure you want to delete this company? This action cannot be undone.')) {
            return;
        }

        fetch('{{ route("companies.destroy", $company) }}', {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                window.location.href = '{{ route("companies.index") }}';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to delete company');
            });
    }
</script>
@endpush