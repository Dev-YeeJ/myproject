<!-- Treasurer Dashboard - Focused on financial management -->

<main class="main-content">
    <div class="header-section">
        <div class="header-title">Financial Dashboard</div>
        <div class="header-subtitle">Budget & Financial Management</div>
        <div class="date-badge">{{ now()->format('m/d/Y') }}</div>
    </div>

    <!-- Financial Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h3>₱{{ number_format($stats['total_revenue']) }}</h3>
                <p>Total Revenue</p>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                    <span>Monthly</span>
                </div>
            </div>
            <div class="stat-icon icon-green">
                <i class="fas fa-arrow-trending-up"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3>₱{{ number_format($stats['total_expenses']) }}</h3>
                <p>Total Expenses</p>
                <div class="stat-trend">
                    <i class="fas fa-arrow-down"></i>
                    <span>This month</span>
                </div>
            </div>
            <div class="stat-icon icon-orange">
                <i class="fas fa-arrow-trending-down"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3>₱{{ number_format($stats['monthly_budget'] - $stats['total_expenses']) }}</h3>
                <p>Available Balance</p>
            </div>
            <div class="stat-icon icon-blue">
                <i class="fas fa-wallet"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3>₱{{ number_format($stats['monthly_budget']) }}</h3>
                <p>Monthly Budget</p>
            </div>
            <div class="stat-icon icon-purple">
                <i class="fas fa-chart-pie"></i>
            </div>
        </div>
    </div>

    <!-- Financial Reports -->
    <div class="activities-grid">
        <div class="activity-card">
            <div class="activity-header blue">
                <i class="fas fa-file-pdf"></i>
                <h3>Financial Reports</h3>
            </div>
            <div class="activity-item">
                <div class="activity-icon"></div>
                <div class="activity-content">
                    <div class="activity-title">Monthly Budget Report - October</div>
                    <div class="activity-meta">Generated 2 days ago</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon"></div>
                <div class="activity-content">
                    <div class="activity-title">Expenditure Summary</div>
                    <div class="activity-meta">Generated 5 days ago</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon"></div>
                <div class="activity-content">
                    <div class="activity-title">Fund Allocation Report</div>
                    <div class="activity-meta">Generated 1 week ago</div>
                </div>
            </div>
        </div>

        <div class="activity-card">
            <div class="activity-header orange">
                <i class="fas fa-money-bill-wave"></i>
                <h3>Recent Transactions</h3>
            </div>
            <div class="activity-item">
                <div class="activity-icon"></div>
                <div class="activity-content">
                    <div class="activity-title">Road Maintenance - ₱25,000</div>
                    <div class="activity-meta">Released 2 hours ago</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon"></div>
                <div class="activity-content">
                    <div class="activity-title">Utilities - ₱15,000</div>
                    <div class="activity-meta">Released yesterday</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon"></div>
                <div class="activity-content">
                    <div class="activity-title">Office Supplies - ₱8,500</div>
                    <div class="activity-meta">Released 2 days ago</div>
                </div>
            </div>
        </div>
    </div>
</main>