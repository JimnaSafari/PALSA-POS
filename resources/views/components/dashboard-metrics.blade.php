<!-- Modern Dashboard Metrics Component -->
<div class="dashboard-metrics">
    <div class="row g-4 mb-4">
        <!-- Sales Today -->
        <div class="col-xl-3 col-md-6">
            <div class="metric-card sales-card">
                <div class="metric-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value" id="todaySales">
                        KSh {{ number_format($salesData['today'] ?? 0, 2) }}
                    </div>
                    <div class="metric-label">Today's Sales</div>
                    <div class="metric-change positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>+12.5%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Today -->
        <div class="col-xl-3 col-md-6">
            <div class="metric-card orders-card">
                <div class="metric-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value" id="todayOrders">
                        {{ $ordersData['today_total'] ?? 0 }}
                    </div>
                    <div class="metric-label">Orders Today</div>
                    <div class="metric-change positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>+8.2%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="col-xl-3 col-md-6">
            <div class="metric-card pending-card">
                <div class="metric-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value" id="pendingOrders">
                        {{ $ordersData['pending'] ?? 0 }}
                    </div>
                    <div class="metric-label">Pending Orders</div>
                    @if(($ordersData['pending'] ?? 0) > 0)
                        <div class="metric-change warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Needs attention</span>
                        </div>
                    @else
                        <div class="metric-change positive">
                            <i class="fas fa-check"></i>
                            <span>All clear</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Low Stock Items -->
        <div class="col-xl-3 col-md-6">
            <div class="metric-card stock-card">
                <div class="metric-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value" id="lowStockItems">
                        {{ $productsData['low_stock'] ?? 0 }}
                    </div>
                    <div class="metric-label">Low Stock Items</div>
                    @if(($productsData['low_stock'] ?? 0) > 0)
                        <div class="metric-change warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Reorder needed</span>
                        </div>
                    @else
                        <div class="metric-change positive">
                            <i class="fas fa-check"></i>
                            <span>Stock healthy</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Sales Chart -->
        <div class="col-xl-8">
            <div class="chart-card">
                <div class="chart-header">
                    <h5 class="chart-title">
                        <i class="fas fa-chart-line me-2"></i>
                        Sales Trend (Last 7 Days)
                    </h5>
                    <div class="chart-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="refreshSalesChart()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="chart-body">
                    <canvas id="salesChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="col-xl-4">
            <div class="chart-card">
                <div class="chart-header">
                    <h5 class="chart-title">
                        <i class="fas fa-star me-2"></i>
                        Top Products
                    </h5>
                </div>
                <div class="chart-body">
                    <div class="top-products-list">
                        @if(isset($topProducts) && count($topProducts) > 0)
                            @foreach($topProducts as $index => $product)
                                <div class="top-product-item">
                                    <div class="product-rank">{{ $index + 1 }}</div>
                                    <div class="product-info">
                                        <div class="product-name">{{ $product['name'] }}</div>
                                        <div class="product-sales">{{ $product['sold'] }} sold</div>
                                    </div>
                                    <div class="product-trend">
                                        <i class="fas fa-arrow-up text-success"></i>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-box-open fa-2x mb-2"></i>
                                <p>No sales data available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row g-4">
        <div class="col-12">
            <div class="activity-card">
                <div class="activity-header">
                    <h5 class="activity-title">
                        <i class="fas fa-history me-2"></i>
                        Recent Activity
                    </h5>
                    <a href="{{ route('orderListPage') }}" class="btn btn-sm btn-outline-primary">
                        View All Orders
                    </a>
                </div>
                <div class="activity-body">
                    @if(isset($recentOrders) && count($recentOrders) > 0)
                        <div class="activity-list">
                            @foreach($recentOrders as $order)
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-shopping-bag"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title">
                                            New order #{{ $order['order_code'] }}
                                        </div>
                                        <div class="activity-details">
                                            {{ $order['customer'] }} ordered {{ $order['product'] }}
                                        </div>
                                        <div class="activity-time">{{ $order['created_at'] }}</div>
                                    </div>
                                    <div class="activity-amount">
                                        KSh {{ number_format($order['amount'], 2) }}
                                    </div>
                                    <div class="activity-status">
                                        <span class="badge bg-{{ $order['status'] === 'Pending' ? 'warning' : 'success' }}">
                                            {{ $order['status'] }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                            <p>No recent orders</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: @json($chartData['daily_sales'] ?? []),
            datasets: [{
                label: 'Sales (KSh)',
                data: @json(array_column($chartData['daily_sales'] ?? [], 'sales')),
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'KSh ' + value.toLocaleString();
                        }
                    }
                }
            },
            elements: {
                point: {
                    radius: 4,
                    hoverRadius: 6
                }
            }
        }
    });

    // Real-time updates every 30 seconds
    setInterval(updateMetrics, 30000);
    
    function updateMetrics() {
        fetch('/api/admin/dashboard')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update metric values
                    document.getElementById('todaySales').textContent = 
                        'KSh ' + parseFloat(data.data.sales.today).toLocaleString();
                    document.getElementById('todayOrders').textContent = 
                        data.data.orders.today_total;
                    document.getElementById('pendingOrders').textContent = 
                        data.data.orders.pending;
                    document.getElementById('lowStockItems').textContent = 
                        data.data.products.low_stock;
                }
            })
            .catch(error => console.error('Error updating metrics:', error));
    }
});

function refreshSalesChart() {
    // Add refresh functionality
    location.reload();
}
</script>

<style>
.dashboard-metrics {
    padding: 0;
}

.metric-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 1.5rem;
    color: white;
    position: relative;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.metric-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.metric-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    transform: translate(30px, -30px);
}

.sales-card {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.orders-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.pending-card {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.stock-card {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.metric-icon {
    position: absolute;
    top: 1rem;
    right: 1rem;
    font-size: 2rem;
    opacity: 0.3;
}

.metric-content {
    position: relative;
    z-index: 2;
}

.metric-value {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.metric-label {
    font-size: 0.9rem;
    opacity: 0.9;
    margin-bottom: 0.5rem;
}

.metric-change {
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.metric-change.positive {
    color: #d4edda;
}

.metric-change.warning {
    color: #fff3cd;
}

.chart-card, .activity-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    overflow: hidden;
}

.chart-header, .activity-header {
    padding: 1.5rem 1.5rem 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chart-title, .activity-title {
    margin: 0;
    color: #2c3e50;
    font-weight: 600;
}

.chart-body, .activity-body {
    padding: 1.5rem;
}

.top-products-list {
    max-height: 300px;
    overflow-y: auto;
}

.top-product-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.top-product-item:last-child {
    border-bottom: none;
}

.product-rank {
    width: 30px;
    height: 30px;
    background: #667eea;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.8rem;
    margin-right: 1rem;
}

.product-info {
    flex: 1;
}

.product-name {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.product-sales {
    font-size: 0.8rem;
    color: #6c757d;
}

.activity-list {
    max-height: 400px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    background: #667eea;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
}

.activity-content {
    flex: 1;
}

.activity-content .activity-title {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.activity-details {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.activity-time {
    color: #adb5bd;
    font-size: 0.8rem;
}

.activity-amount {
    font-weight: bold;
    color: #28a745;
    margin-right: 1rem;
}

.activity-status {
    margin-left: auto;
}

@media (max-width: 768px) {
    .metric-card {
        margin-bottom: 1rem;
    }
    
    .metric-value {
        font-size: 1.5rem;
    }
    
    .chart-header, .activity-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .activity-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .activity-amount, .activity-status {
        margin-left: 0;
    }
}
</style>