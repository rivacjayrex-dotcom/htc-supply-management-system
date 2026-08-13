<x-app-layout>
    <x-slot name="header">Institutional Analytics & Reports</x-slot>

    <!-- Load Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="container-fluid py-2">
        <!-- ROW 1: QUICK ANALYTICS -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                    <small class="text-muted fw-bold uppercase tracking-widest" style="font-size: 9px;">Total Released (Year)</small>
                    <h3 class="fw-black mt-2 mb-0">{{ $stats['year'] }}</h3>
                    <p class="text-success small mb-0 mt-1"><i data-lucide="trending-up" style="width:12px"></i> Institutional Growth</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-success">
                    <small class="text-muted fw-bold uppercase tracking-widest" style="font-size: 9px;">Total Expenditure</small>
                    <h3 class="fw-black mt-2 mb-0 text-success">₱{{ number_format($costs['total_spent'], 2) }}</h3>
                    <p class="text-muted small mb-0 mt-1">Released Supply Value</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                    <small class="text-muted fw-bold uppercase tracking-widest" style="font-size: 9px;">Avg Requisition Cost</small>
                    <h3 class="fw-black mt-2 mb-0">₱{{ number_format($costs['avg_per_req'], 2) }}</h3>
                    <p class="text-muted small mb-0 mt-1">Per Transaction</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-dark text-white h-100">
                    <small class="text-white-50 fw-bold uppercase tracking-widest" style="font-size: 9px;">Completed this Month</small>
                    <h3 class="fw-black mt-2 mb-0">{{ $stats['month'] }}</h3>
                    <div class="progress mt-2" style="height: 4px; background: rgba(255,255,255,0.1);">
                        <div class="progress-bar bg-success" style="width: 65%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- CHART 1: MONTHLY VOLUME -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                    <h6 class="fw-bold mb-4">Fulfillment Volume ({{ date('Y') }})</h6>
                    <canvas id="monthlyChart" style="max-height: 300px;"></canvas>
                </div>
            </div>

            <!-- CHART 2: DEPT SPENDING -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                    <h6 class="fw-bold mb-4">Spending by Dept.</h6>
                    <canvas id="deptChart"></canvas>
                </div>
            </div>

            <!-- TOP ITEMS LIST -->
            <div class="col-lg-12 mt-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                    <div class="card-header bg-white border-0 p-4 pb-0">
                        <h6 class="fw-bold">Top 5 Highly Requested Items</h6>
                    </div>
                    <div class="table-responsive p-4 pt-2">
                        <table class="table table-sm align-middle">
                            <thead class="bg-light small uppercase">
                                <tr>
                                    <th>Item Description</th>
                                    <th class="text-center">Total Qty Distributed</th>
                                    <th>Inventory Impact</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topItems as $item)
                                <tr>
                                    <td class="fw-bold">{{ $item->item_name }}</td>
                                    <td class="text-center">{{ $item->total_qty }}</td>
                                    <td>
                                        <div class="progress" style="height: 6px; width: 100px;">
                                            <div class="progress-bar bg-primary" style="width: {{ ($item->total_qty / 100) * 100 }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // DATA PREP FROM PHP
        const monthlyLabels = @json($monthlyData->pluck('month'));
        const monthlyValues = @json($monthlyData->pluck('count'));
        const deptLabels = @json($deptSpending->pluck('department'));
        const deptValues = @json($deptSpending->pluck('total'));

        // MONTHLY LINE CHART
        new Chart(document.getElementById('monthlyChart'), {
            type: 'line',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Requisitions Released',
                    data: monthlyValues,
                    borderColor: '#185b3b',
                    backgroundColor: 'rgba(24, 91, 59, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { plugins: { legend: { display: false } } }
        });

        // DEPT DOUGHNUT CHART
        new Chart(document.getElementById('deptChart'), {
            type: 'doughnut',
            data: {
                labels: deptLabels,
                datasets: [{
                    data: deptValues,
                    backgroundColor: ['#185b3b', '#28a745', '#ffc107', '#17a2b8', '#6c757d']
                }]
            },
            options: { cutout: '70%' }
        });
    </script>
</x-app-layout>
