<!-- Tanod Dashboard - Focused on security and incidents -->

<main class="main-content">
    <div class="header-section">
        <div class="header-title">Security & Safety</div>
        <div class="header-subtitle">Incident Reports & Security Management</div>
        <div class="date-badge">{{ now()->format('m/d/Y') }}</div>
    </div>

    <!-- Security Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['recent_incidents']) }}</h3>
                <p>Recent Incidents</p>
                <div class="stat-trend">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ $stats['resolved_incidents'] }} resolved</span>
                </div>
            </div>
            <div class="stat-icon icon-orange">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['registered_residents']) }}</h3>
                <p>Total Residents</p>
            </div>
            <div class="stat-icon icon-blue">
                <i class="fas fa-users"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['active_households']) }}</h3>
                <p>Active Households</p>
            </div>
            <div class="stat-icon icon-green">
                <i class="fas fa-home"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3>98%</h3>
                <p>Resolution Rate</p>
            </div>
            <div class="stat-icon icon-pink">
                <i class="fas fa-shield-alt"></i>
            </div>
        </div>
    </div>

    <!-- Incident Reports -->
    <div class="activities-grid">
        <div class="activity-card">
            <div class="activity-header blue">
                <i class="fas fa-file-alt"></i>
                <h3>Active Incidents</h3>
            </div>
            <div class="activity-item">
                <div class="activity-icon"></div>
                <div class="activity-content">
                    <div class="activity-title">Noise Complaint - Purok 3</div>
                    <div class="activity-meta">Reported 2 hours ago - INVESTIGATING</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon"></div>
                <div class="activity-content">
                    <div class="activity-title">Traffic Incident - Main Road</div>
                    <div class="activity-meta">Reported yesterday - RESOLVED</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon"></div>
                <div class="activity-content">
                    <div class="activity-title">Trespassing - Purok 1</div>
                    <div class="activity-meta">Reported 3 days ago - RESOLVED</div>
                </div>
            </div>
        </div>

        <div class="activity-card">
            <div class="activity-header orange">
                <i class="fas fa-tasks"></i>
                <h3>Patrol Schedule</h3>
            </div>
            <div class="event-item">
                <div class="event-date">
                    <div class="event-day">Today</div>
                    <div class="event-month">6:00 AM</div>
                </div>
                <div class="event-details">
                    <div class="event-title">Morning Patrol - All Puroks</div>
                    <div class="event-time">2 hours</div>
                </div>
            </div>
            <div class="event-item">
                <div class="event-date">
                    <div class="event-day">Today</div>
                    <div class="event-month">2:00 PM</div>
                </div>
                <div class="event-details">
                    <div class="event-title">Afternoon Patrol</div>
                    <div class="event-time">2 hours</div>
                </div>
            </div>
            <div class="event-item">
                <div class="event-date">
                    <div class="event-day">Today</div>
                    <div class="event-month">10:00 PM</div>
                </div>
                <div class="event-details">
                    <div class="event-title">Night Patrol</div>
                    <div class="event-time">4 hours</div>
                </div>
            </div>
        </div>
    </div>
</main>