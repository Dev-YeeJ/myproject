{{-- resources/views/dashboards/captain-household-view.blade.php --}}

@extends('layouts.dashboard-layout')

@section('title', 'View Household - ' . $household->household_name)

{{-- STYLES MOVED FROM @push TO @section('content') --}}

@section('nav-items')
    {{-- Copied nav items from resident-profiling for consistency --}}
    <li class="nav-item">
        <a href="{{ route('dashboard.captain') }}" class="nav-link ">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="nav-item">
    <a href="{{ route('captain.resident-profiling') }}" class="nav-link active">
        <i class="fas fa-users"></i>
        <span>Resident Profiling</span>
    </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('captain.document-services') }}" class="nav-link">
            <i class="far fa-file-alt"></i>
            <span>Documents Services</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="fas fa-dollar-sign"></i>
            <span>Financial Management</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('captain.health-services') }}" class="nav-link ">
            <i class="fas fa-heart"></i>
            <span>Health & Social Services</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Incident & Blotter</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="fas fa-flag"></i>
            <span>Project Monitoring</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="fas fa-bell"></i>
            <span>Announcements</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="fas fa-check-circle"></i>
            <span>SK Module</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    </li>
@endsection

@section('content')

{{-- STYLES ARE NOW HERE --}}
<style>
    .details-container {
        max-width: 1000px;
        margin: 0 auto;
    }

    .details-header {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white;
        padding: 30px 40px;
        border-radius: 12px;
        margin-bottom: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .profile-avatar {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        border: 3px solid rgba(255, 255, 255, 0.3);
    }

    .header-info h1 {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0 0 8px 0;
    }

    .header-badges {
        display: flex;
        gap: 10px;
    }

    .header-badge {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.2);
    }

    .header-actions {
        display: flex;
        gap: 12px;
    }

    .btn-header {
        padding: 10px 20px;
        border-radius: 8px;
        border: 2px solid white;
        background: transparent;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-header:hover {
        background: white;
        color: #2B5CE6;
    }
    
    .btn-header-green {
        border-color: #10B981;
        background: #10B981;
    }
    .btn-header-green:hover {
        background: white;
        color: #10B981;
        border-color: white;
    }


    .details-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 25px;
        margin-bottom: 25px;
    }

    .details-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    /* Modify card padding for table */
    .details-card.table-card {
        padding: 0;
        overflow: hidden;
    }

    .card-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1F2937;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 15px;
        border-bottom: 2px solid #E5E7EB;
        /* Add padding back for cards that don't contain tables */
        padding-left: 30px;
        padding-right: 30px;
        padding-top: 30px;
        margin: 0;
    }
    
    .details-card.table-card .card-title {
        border-radius: 12px 12px 0 0;
        background: #F9FAFB;
        border-bottom: 2px solid #E5E7EB;
        margin-bottom: 0;
    }
    
    .card-content {
        padding: 30px;
    }

    .info-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    .info-row.single {
        grid-template-columns: 1fr;
    }
    
    /* Remove last row margin */
    .info-row:last-child {
        margin-bottom: 0;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .info-label {
        font-size: 0.85rem;
        color: #6B7280;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 1rem;
        color: #1F2937;
        font-weight: 500;
    }

    .info-value.highlight {
        color: #2B5CE6;
        font-weight: 600;
    }

    .badge-status {
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
        width: fit-content;
    }

    .badge-male {
        background: #DBEAFE;
        color: #1E40AF;
    }

    .badge-female {
        background: #FCE7F3;
        color: #BE185D;
    }
    
    .badge-head {
        background: #1E3A8A;
        color: white;
    }
    .badge-spouse {
        background: #FEF3C7;
        color: #92400E;
    }
    .badge-child {
        background: #E0E7FF;
        color: #3730A3;
    }
    .badge-member {
        background: #F3F4F6;
        color: #4B5563;
    }

    .badge-complete {
        background: #D1FAE5;
        color: #065F46;
    }
    .badge-incomplete {
        background: #FEF3C7;
        color: #92400E;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .stat-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px;
        background: #F9FAFB;
        border-radius: 8px;
    }

    .stat-label {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #6B7280;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .stat-label i {
        width: 32px;
        height: 32px;
        background: white;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2B5CE6;
    }

    .stat-value {
        font-weight: 700;
        color: #1F2937;
        font-size: 1.1rem;
    }

    .checkbox-indicators {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .checkbox-indicator {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: #F9FAFB;
        border-radius: 8px;
    }

    .checkbox-icon {
        width: 24px;
        height: 24px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
    }

    .checkbox-icon.checked {
        background: #10B981;
        color: white;
    }

    .checkbox-icon.unchecked {
        background: #E5E7EB;
        color: #9CA3AF;
    }

    .checkbox-label {
        font-weight: 600;
        color: #374151;
        font-size: 0.9rem;
    }
    
    .checkbox-label .count {
        font-weight: 700;
        color: #1F2937;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #2B5CE6;
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 20px;
        transition: all 0.3s;
    }

    .back-link:hover {
        gap: 12px;
        color: #1E3A8A;
    }
    
    /* Table styles */
    .table-container {
        overflow-x: auto;
    }
    .table { width: 100%; margin: 0; border-collapse: collapse; }
    .table thead { background: #F9FAFB; }
    .table th { padding: 16px 20px; font-weight: 700; color: #1F2937; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; text-align: left; border-bottom: 2px solid #E5E7EB; }
    .table td { padding: 18px 20px; vertical-align: middle; border-bottom: 1px solid #F3F4F6; text-align: left; }
    .table tbody tr:last-child td { border-bottom: none; }
    .table tbody tr:hover { background: #F9FAFB; }
    .resident-name { font-weight: 600; color: #1F2937; margin: 0 0 4px 0; }
    .resident-suffix { color: #6B7280; font-size: 0.9rem; }
    .no-results-found { text-align: center; padding: 60px; }
    .no-results-found i { font-size: 3rem; color: #ccc; margin-bottom: 15px; }
    .no-results-found p { color: #999; font-size: 1.1rem; }
    .action-icons { display: flex; gap: 10px; }
    .action-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s; border: none; background: transparent; color: #6B7280; }
    .action-icon.view { color: #2B5CE6; }
    .action-icon.view:hover { background: #EFF6FF; }
    .action-icon.edit { color: #10B981; }
    .action-icon.edit:hover { background: #ECFDF5; }
    .action-icon.delete { color: #EF4444; }
    .action-icon.delete:hover { background: #FEE2E2; }
    
    /* Modal Styles */
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); align-items: center; justify-content: center; }
    .modal.show { display: flex; }
    .modal-content { background: white; padding: 30px; border-radius: 12px; max-width: 400px; width: 90%; }
    .modal-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
    .modal-icon { width: 48px; height: 48px; background: #FEE2E2; color: #EF4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .modal-title { font-size: 1.3rem; font-weight: 700; color: #1F2937; }
    .modal-body { margin-bottom: 25px; color: #6B7280; line-height: 1.6; }
    .modal-actions { display: flex; gap: 12px; justify-content: flex-end; }
    .btn-cancel { padding: 10px 20px; background: #F3F4F6; color: #4B5563; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
    .btn-cancel:hover { background: #E5E7EB; }
    .btn-confirm-delete { padding: 10px 20px; background: #EF4444; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
    .btn-confirm-delete:hover { background: #DC2626; }


    @media (max-width: 768px) {
        .details-grid {
            grid-template-columns: 1fr;
        }

        .details-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 20px;
        }

        .header-actions {
            width: 100%;
        }

        .btn-header {
            flex: 1;
            justify-content: center;
        }

        .info-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="details-container">
    <a href="{{ route('captain.resident-profiling', ['view' => 'households']) }}" class="back-link">
        <i class="fas fa-arrow-left"></i>
        <span>Back to Household Directory</span>
    </a>

    <div class="details-header">
        <div class="header-left">
            <div class="profile-avatar">
                <i class="fas fa-home"></i>
            </div>
            <div class="header-info">
                <h1>{{ $household->household_name }}</h1>
                <div class="header-badges">
                    <span class="header-badge">{{ $household->household_number }}</span>
                    <span class="header-badge">{{ $household->total_members }} Members</span>
                </div>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('captain.household.edit', $household->id) }}" class="btn-header">
                <i class="fas fa-edit"></i>
                <span>Edit</span>
            </a>
            <a href="{{ route('captain.resident.create', ['household_id' => $household->id]) }}" class="btn-header btn-header-green">
                <i class="fas fa-user-plus"></i>
                <span>Add Member</span>
            </a>
        </div>
    </div>

    <div class="details-grid">
        {{-- Left Column --}}
        <div>
            <div class="details-card" style="margin-bottom: 25px;">
                <div class="card-title" style="padding: 0; border-bottom: none;">
                    <i class="fas fa-info-circle"></i>
                    <span>Household Information</span>
                </div>
                <div class="card-content" style="padding: 30px 0 0 0;">
                    <div class="info-row">
                        <div class="info-item">
                            <div class="info-label">Household Name</div>
                            <div class="info-value">{{ $household->household_name }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Household Number</div>
                            <div class="info-value">{{ $household->household_number }}</div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-item">
                            <div class="info-label">Household Head</div>
                            <div class="info-value highlight">
                                @if($household->head)
                                    {{ $household->head->full_name }}
                                @else
                                    <span style="color: #EF4444; font-weight: 500;">No Head Assigned</span>
                                @endif
                            </div>
                        </div>
                         <div class="info-item">
                            <div class="info-label">Data Status</div>
                            <div class="info-value">
                                <span class="badge-status {{ $household->status === 'complete' ? 'badge-complete' : 'badge-incomplete' }}">
                                    {{ ucfirst($household->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-item">
                            <div class="info-label">Address</div>
                            <div class="info-value">{{ $household->address }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Purok / Zone</div>
                            <div class="info-value">{{ $household->purok ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="details-card table-card">
                <div class="card-title">
                    <i class="fas fa-users"></i>
                    <span>Household Members ({{ $household->activeResidents->count() }})</span>
                </div>
                
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Age</th>
                                <th>Role</th>
                                <th>Contact</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($household->activeResidents as $resident)
                            <tr>
                                <td>
                                    <div class="resident-name">{{ $resident->first_name }} {{ $resident->last_name }}</div>
                                    @if($resident->suffix)
                                    <div class="resident-suffix">{{ $resident->suffix }}</div>
                                    @endif
                                </td>
                                <td>{{ $resident->age }}</td>
                                <td>
                                    @php
                                        $statusClass = 'badge-member'; // Default
                                        if ($resident->household_status === 'Household Head') $statusClass = 'badge-head';
                                        if ($resident->household_status === 'Spouse') $statusClass = 'badge-spouse';
                                        if ($resident->household_status === 'Child') $statusClass = 'badge-child';
                                    @endphp
                                    <span class="badge {{ $statusClass }}">
                                        {{ $resident->household_status }}
                                    </span>
                                </td>
                                <td>{{ $resident->contact_number ?? 'N/A' }}</td>
                                <td>
                                    <div class="action-icons">
                                        <a href="{{ route('captain.resident.show', $resident->id) }}" class="action-icon view" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('captain.resident.edit', $resident->id) }}" class="action-icon edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="action-icon delete" title="Delete" onclick="showDeleteModal({{ $resident->id }}, '{{ $resident->first_name }} {{ $resident->last_name }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">
                                    <div class="no-results-found">
                                        <i class="fas fa-users"></i>
                                        <p>No active residents found for this household.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- Right Column --}}
        <div>
            <div class="details-card" style="margin-bottom: 25px;">
                <div class="card-title">
                    <i class="fas fa-chart-bar"></i>
                    <span>Quick Stats</span>
                </div>
                <div class="card-content">
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-label">
                                <i class="fas fa-users"></i>
                                <span>Total Members</span>
                            </div>
                            <div class="stat-value">{{ $household->total_members }}</div>
                        </div>

                        <div class="stat-item">
                            <div class="stat-label">
                                <i class="fas fa-user-tie"></i>
                                <span>Household Head</span>
                            </div>
                            <div class="stat-value" style="font-size: 0.85rem; text-align: right;">
                                {{ $household->head ? $household->head->first_name . ' ' . $household->head->last_name : 'N/A' }}
                            </div>
                        </div>
                        
                        <div class="stat-item">
                            <div class="stat-label">
                                <i class="fas fa-check-circle"></i>
                                <span>Data Status</span>
                            </div>
                            <div class="stat-value" style="font-size: 0.85rem;">{{ ucfirst($household->status) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="details-card">
                <div class="card-title">
                    <i class="fas fa-tags"></i>
                    <span>Member Demographics</span>
                </div>
                <div class="card-content">
                    @php
                        // Calculate stats from the eager-loaded collection
                        $seniors = $household->activeResidents->where('is_senior_citizen', true)->count();
                        $minors = $household->activeResidents->where('age', '<', 18)->count();
                        $pwd = $household->activeResidents->where('is_pwd', true)->count();
                        $is_4ps = $household->activeResidents->where('is_4ps', true)->count();
                        $voters = $household->activeResidents->where('is_registered_voter', true)->count();
                    @endphp
                    
                    <div class="checkbox-indicators">
                        <div class="checkbox-indicator">
                            <div class="checkbox-icon {{ $voters > 0 ? 'checked' : 'unchecked' }}">
                                <i class="fas {{ $voters > 0 ? 'fa-check' : 'fa-times' }}"></i>
                            </div>
                            <div class="checkbox-label">Registered Voters: <span class="count">{{ $voters }}</span></div>
                        </div>
                        
                        <div class="checkbox-indicator">
                            <div class="checkbox-icon {{ $seniors > 0 ? 'checked' : 'unchecked' }}">
                                <i class="fas {{ $seniors > 0 ? 'fa-check' : 'fa-times' }}"></i>
                            </div>
                            <div class="checkbox-label">Senior Citizens: <span class="count">{{ $seniors }}</span></div>
                        </div>
                        
                        <div class="checkbox-indicator">
                            <div class="checkbox-icon {{ $minors > 0 ? 'checked' : 'unchecked' }}">
                                <i class="fas {{ $minors > 0 ? 'fa-check' : 'fa-times' }}"></i>
                            </div>
                            <div class="checkbox-label">Minors (Under 18): <span class="count">{{ $minors }}</span></div>
                        </div>

                        <div class="checkbox-indicator">
                            <div class="checkbox-icon {{ $pwd > 0 ? 'checked' : 'unchecked' }}">
                                <i class="fas {{ $pwd > 0 ? 'fa-check' : 'fa-times' }}"></i>
                            </div>
                            <div class="checkbox-label">Persons with Disability: <span class="count">{{ $pwd }}</span></div>
                        </div>

                        <div class="checkbox-indicator">
                            <div class="checkbox-icon {{ $is_4ps > 0 ? 'checked' : 'unchecked' }}">
                                <i class="fas {{ $is_4ps > 0 ? 'fa-check' : 'fa-times' }}"></i>
                            </div>
                            <div class="checkbox-label">4Ps Beneficiaries: <span class="count">{{ $is_4ps }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Re-usable Resident Delete Modal --}}
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="modal-title">Delete Resident</div>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to remove <strong id="residentName"></strong> from the system?</p>
            <p>This will only deactivate the resident. This action can be undone by an administrator.</p>
        </div>
        <div classV="modal-actions">
            <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-confirm-delete">Deactivate Resident</button>
            </form>
        </div>
    </div>
</div>

<script>
    // --- Resident Delete Modal ---
    function showDeleteModal(residentId, residentName) {
        document.getElementById('residentName').textContent = residentName;
        document.getElementById('deleteForm').action = `/captain/resident/${residentId}`;
        document.getElementById('deleteModal').classList.add('show');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('show');
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target === modal) {
            closeDeleteModal();
        }
    }
</script>

@endsection