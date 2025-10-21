<!-- Health Worker Dashboard - Focused on health programs and residents -->

<main class="main-content">
    <div class="header-section">
        <div class="header-title">Health & Wellness</div>
        <div class="header-subtitle">Health Programs & Community Services</div>
        <div class="date-badge">{{ now()->format('m/d/Y') }}</div>
    </div>

    <!-- Health Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['health_programs']) }}</h3>
                <p>Health Programs</p>
                <div class="stat-trend">
                    <i class="fas fa-heartbeat"></i>
                    <span>{{ $stats['ongoing_programs'] }} ongoing</span>
                </div>
            </div>
            <div class="stat-icon icon-pink">
                <i class="fas fa-heart"></i>
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
                <h3>245</h3>
                <p>Beneficiaries Served</p>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                    <span>This month</span>
                </div>
            </div>
            <div class="stat-icon icon-green">
                <i class="fas fa-hospital-user"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3>12</h3>
                <p>Scheduled Activities</p>
            </div>
            <div class="stat-icon icon-orange">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>
    </div>

    <!-- Health Programs -->
    <div class="activities-grid">
        <div class="activity-card">
            <div class="activity-header blue">
                <i class="fas fa-clinic-medical"></i>
                <h3>Ongoing Programs</h3>
            </div>
            <div class="activity-item">
                <div class="activity-icon"></div>
                <div class="activity-content">
                    <div class="activity-title">COVID-19 Vaccination - 320/500</div>
                    <div class="activity-meta">64% completion rate</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon"></div>
                <div class="activity-content">
                    <div class="activity-title">Nutrition Program - 145/150</div>
                    <div class="activity-meta">96% completion rate</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon"></div>
                <div class="activity-content">
                    <div class="activity-title">Blood Pressure Monitoring</div>
                    <div class="activity-meta">Every Tuesday & Thursday</div>
                </div>
            </div>
        </div>

        <div class="activity-card">
            <div class="activity-header orange">
                <i class="fas fa-calendar-check"></i>
                <h3>Scheduled Missions</h3>
            </div>
            <div class="event-item">
                <div class="event-date">
                    <div class="event-day">20</div>
                    <div class="event-month">DEC</div>
                </div>
                <div class="event-details">
                    <div class="event-title">Medical Mission</div>
                    <div class="event-time">8:00 AM at Barangay Hall</div>
                </div>
            </div>
            <div class="event-item">
                <div class="event-date">
                    <div class="event-day">28</div>
                    <div class="event-month">DEC</div>
                </div>
                <div class="event-details">
                    <div class="event-title">Dental Clinic</div>
                    <div class="event-time">10:00 AM</div>
                </div>
            </div>
            <div class="event-item">
                <div class="event-date">
                    <div class="event-day">15</div>
                    <div class="event-month">JAN</div>
                </div>
                <div class="event-details">
                    <div class="event-title">Health Awareness Drive</div>
                    <div class="event-time">2:00 PM</div>
                </div>
            </div>
        </div>
    </div>
</main>