@extends('admin.layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="min-vh-100 bg-light p-4">
    @if(session('success'))
        <div class="alert alert-success shadow-sm" id="success">{{ session('success') }}</div>
    @endif

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-0">Dashboard</h3>
                <p class="text-muted small mb-0">Overview of your store performance</p>
            </div>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
        </div>

        {{-- Dashboard Cards --}}
        <div class="row g-4">

            <!-- Total Orders -->
            <div class="col-lg-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 dashboard-card text-white bg-gradient-primary position-relative overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase fw-semibold mb-2">Total Orders</h6>
                                <h3 class="fw-bold mb-0" id="orders-count">{{ $orders }}</h3>
                            </div>
                            <div class="icon-lg bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="bi bi-bag fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Customers -->
            <div class="col-lg-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 dashboard-card text-white bg-gradient-success position-relative overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase fw-semibold mb-2">Active Customers</h6>
                                <h3 class="fw-bold mb-0" id="active-customers">{{ $activecustomer }}</h3>
                            </div>
                            <div class="icon-lg bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="bi bi-people fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Blocked Customers -->
            <div class="col-lg-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 dashboard-card text-dark bg-warning bg-opacity-75 position-relative overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase fw-semibold mb-2">Blocked Customers</h6>
                                <h3 class="fw-bold mb-0" id="blocked-customers">{{ $blockcustomer }}</h3>
                            </div>
                            <div class="icon-lg bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="bi bi-person-x fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products -->
            <div class="col-lg-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 dashboard-card text-white bg-gradient-danger position-relative overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase fw-semibold mb-2">Products</h6>
                                <h3 class="fw-bold mb-0" id="products-count">{{ $products }}</h3>
                            </div>
                            <div class="icon-lg bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="bi bi-box-seam fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>


<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
}
.bg-gradient-success {
    background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
}
.bg-gradient-danger {
    background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
}
.dashboard-card {
    transition: all 0.3s ease;
}
.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}
.icon-lg {
    width: 50px;
    height: 50px;
    display: flex;
    justify-content: center;
    align-items: center;
}
</style>
@endsection

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                const element = document.getElementById("success");
                if(element) {
                    element.style.display = "none"; 
                }
            }, 3000); // 3000ms = 3 seconds
        });
</script>