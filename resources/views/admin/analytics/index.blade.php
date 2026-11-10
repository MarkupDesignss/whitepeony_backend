@extends('admin.layouts.app')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="container-fluid py-4">
    <h2 class="mb-4 fw-bold">📊 Analytics Dashboard</h2>

    {{-- Summary Stats --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="card-title text-uppercase fw-bold">Total Sales</h6>
                        <p class="fs-4 fw-bold">&euro;{{ number_format($totalSales, 2) }}</p>

                    </div>
                    <div>
                        {{-- Money Bag Icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="white" class="bi bi-cash-stack" viewBox="0 0 16 16">
                            <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v1H0V4z"/>
                            <path d="M0 7v5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7H0zm3 2a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="card-title text-uppercase fw-bold">Orders</h6>
                        <p class="display-6 fw-bold">{{ $ordersCount }}</p>
                    </div>
                    <div>
                        {{-- Shopping Cart Icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="white" class="bi bi-cart-check" viewBox="0 0 16 16">
                            <path d="M10.854 6.854a.5.5 0 0 0-.708-.708L8.5 7.793 7.354 6.646a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0l2-2z"/>
                            <path d="M0 1.5A.5.5 0 0 1 .5 1h1.11l.401 1.607L2.89 5H14.5a.5.5 0 0 1 .49.598l-1.5 7A.5.5 0 0 1 13 13H4a.5.5 0 0 1-.491-.408L1.01 2H.5a.5.5 0 0 1-.5-.5z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="card-title text-uppercase fw-bold">Active Users</h6>
                        <p class="display-6 fw-bold">{{ $activeUsers }}</p>
                    </div>
                    <div>
                        {{-- People Icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="white" class="bi bi-people" viewBox="0 0 16 16">
                            <path d="M13 7c0-1.105-.672-2-1.5-2S10 5.895 10 7s.672 2 1.5 2S13 8.105 13 7zM5.5 5C4.672 5 4 5.895 4 7s.672 2 1.5 2S7 8.105 7 7 6.328 5 5.5 5z"/>
                            <path d="M0 13s1-1 4-1 4 1 4 1v1H0v-1zm9 0s1-1 4-1 4 1 4 1v1H9v-1z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-dark shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="card-title text-uppercase fw-bold">Total Users</h6>
                        <p class="display-6 fw-bold">{{ $totalUsers }}</p>
                    </div>
                    <div>
                        {{-- User Icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="black" class="bi bi-person-circle" viewBox="0 0 16 16">
                            <path d="M11 10a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.09 11.21 5.788 10 8 10s4.91 1.21 5.468 2.37A7 7 0 0 0 8 1z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sales by Month Chart --}}
    <div class="d-flex gap-2 col-12">
        <div class="card shadow-sm border-0 col-md-6">
            <div class="card-header bg-light fw-bold">Sales (Last 6 Months)</div>
            <div class="card-body">
                <canvas id="salesChart" height="120"></canvas>
            </div>
        </div>

        {{-- Top Products --}}
        <div class="card col-md-6 shadow-sm border-0">
            <div class="card-header bg-light fw-bold">Top 5 Products</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Total Quantity Sold</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->product->name ?? 'N/A' }}</td>
                                    <td class="fw-bold">{{ $item->total_quantity }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No product data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

<!-- All your HTML content goes here (cards, canvas, tables, etc.) -->

<!-- Place scripts at the bottom of the page -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    const salesData = @json($salesByMonth);
    const labels = salesData.map(item => item.month);
    const totals = salesData.map(item => parseFloat(item.total));

    const ctx = document.getElementById('salesChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Sales (\u20AC)',
                data: totals,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '\u20AC' + value;
                        }
                    }
                }
            }
        }
    });
});

</script>

