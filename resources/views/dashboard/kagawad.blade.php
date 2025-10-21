<!-- Kagawad Dashboard - Focused on projects and community programs -->

<main class="main-content">
    <div class="header-section">
        <div class="header-title">Projects & Programs</div>
        <div class="header-subtitle">Community Development & Project Monitoring</div>
        <div class="date-badge">{{ now()->format('m/d/Y') }}</div>
    </div>

    <!-- Projects Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['active_projects']) }}</h3>
                <p>Active Projects</p>
            </div>
            <div class="stat-icon icon-blue">
                <i class="fas fa-folder-open"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['registered_residents']) }}</h3>
                <p>Total Residents</p>
            </div>
            <div class="stat-icon icon-green">
                <i class="fas fa-users"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['active_households']) }}</h3>
                <p>Active Households</p>
            </div>
            <div class="stat-icon icon-orange">
                <i class="fas fa-home"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3>8</h3>
                <p>Community Programs</p>
            </div>
            <div class="stat-icon icon-pink">
                <i class="fas fa-handshake"></i>
            </div>
        </div>
    </div>

    <!-- Project Details -->
    <div class="activities-grid">
        <div class="activity-card">
            <div class="activity-header blue">
                <i class="fas fa-project-diagram"></i>
                <h3>Active Projects</h3>
            </div>
            <div class="activity-item">
                <div class="activity-icon"></div>
                <div class="activity-content">
                    <div class="activity-title">Road Rehabilitation - 85% Complete</div>
                    <div class="activity-meta">Expected completion: Nov 30, 2025</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon"></div>
                <div class="activity-content">
                    <div class="activity-title">Community Hall - 45% Complete</div>
                    <div class="activity-meta">Expected completion: Jan 15, 2026</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon"></div>
                <div class="activity-content">
                    <div class="activity-title">Street Lighting - 60% Complete</div>
                    <div class="activity-meta">Expected completion: Dec 20, 2025</div>
                </div>
            </div>
        </div>

        <div class="activity-card">
            <div class="activity-header orange">
                <i class="fas fa-calendar-check"></i>
                <h3>Community Activities</h3>
            </div>
            <div class="event-item">
                <div class="event-date">
                    <div class="event-day">15</div>
                    <div class="event-month">DEC</div>
                </div>
                <div class="event-details">
                    <div class="event-title">Barangay Assembly</div>
                    <div class="event-time">2:00 PM</div>
                </div>
            </div>
            <div class="event-item">
                <div class="event-date">
                    <div class="event-day">25</div>
                    <div class="event-month">DEC</div>
                </div>
                <div class="event-details">
                    <div class="event-title">Christmas Celebration</div>
                    <div class="event-time">4:00 PM</div>
                </div>
            </div>
            <div class="event-item">
                <div class="event-date">
                    <div class="event-day">20</div>
                    <div class="event-month">JAN</div>
                </div>
                <div class="event-details">
                    <div class="event-title">Sports Festival</div>
                    <div class="event-time">9:00 AM</div>
                </div>
            </div>
        </div>
    </div>
</main>