<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Analytics Dashboard</h1>

        <div class="bg-white rounded shadow p-4 mb-6">
            <form id="filters" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium">Date From</label>
                    <input type="date" id="date_from" class="mt-1 w-full border rounded p-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Date To</label>
                    <input type="date" id="date_to" class="mt-1 w-full border rounded p-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Transaction Status</label>
                    <select id="status" class="mt-1 w-full border rounded p-2">
                        <option value="">All</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="failed">Failed</option>
                        <option value="refunded">Refunded</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="button" id="applyFilters" class="bg-blue-600 text-white px-4 py-2 rounded">Apply</button>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6" id="metrics">
            <div class="bg-white rounded shadow p-4">
                <h2 class="text-sm text-gray-600">Payments</h2>
                <div class="text-3xl font-bold" id="payments_count">0</div>
                <div class="text-green-600" id="payments_amount">$0.00</div>
            </div>
            <div class="bg-white rounded shadow p-4">
                <h2 class="text-sm text-gray-600">Affiliate Commission</h2>
                <div class="text-3xl font-bold" id="affiliates_count">0</div>
                <div class="text-blue-600" id="affiliates_amount">$0.00</div>
            </div>
            <div class="bg-white rounded shadow p-4">
                <h2 class="text-sm text-gray-600">Coupon Discounts</h2>
                <div class="text-3xl font-bold" id="coupons_count">0</div>
                <div class="text-purple-600" id="coupons_amount">$0.00</div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded shadow p-4">
                <h2 class="text-lg font-semibold mb-2">Transactions</h2>
                <canvas id="transactionsChart" height="120"></canvas>
            </div>
            <div class="bg-white rounded shadow p-4">
                <h2 class="text-lg font-semibold mb-2">Affiliate Performance</h2>
                <canvas id="affiliateChart" height="120"></canvas>
            </div>
            <div class="bg-white rounded shadow p-4">
                <h2 class="text-lg font-semibold mb-2">Coupon Usage</h2>
                <canvas id="couponChart" height="120"></canvas>
            </div>
            <div class="bg-white rounded shadow p-4">
                <h2 class="text-lg font-semibold mb-2">Loyalty Points</h2>
                <canvas id="pointsChart" height="120"></canvas>
            </div>
        </div>
    </div>

    <script>
        const toMoney = (n) => new Intl.NumberFormat(undefined, { style: 'currency', currency: 'USD' }).format(Number(n || 0));

        const getFilters = () => {
            const dateFrom = document.getElementById('date_from').value || '';
            const dateTo = document.getElementById('date_to').value || '';
            const status = document.getElementById('status').value || '';
            return { date_from: dateFrom, date_to: dateTo, status };
        };

        const buildQuery = (obj) => {
            const params = new URLSearchParams();
            Object.entries(obj).forEach(([k, v]) => { if (v) params.append(k, v); });
            return params.toString();
        };

        let charts = {};

        const loadMetrics = async () => {
            const q = buildQuery(getFilters());
            const res = await fetch(`/api/analytics/metrics?${q}`);
            const data = await res.json();
            if (!data.success) return;
            document.getElementById('payments_count').textContent = data.metrics.payments.count;
            document.getElementById('payments_amount').textContent = toMoney(data.metrics.payments.total_net);
            document.getElementById('affiliates_count').textContent = data.metrics.affiliates.count;
            document.getElementById('affiliates_amount').textContent = toMoney(data.metrics.affiliates.total_commission);
            document.getElementById('coupons_count').textContent = data.metrics.coupons.count;
            document.getElementById('coupons_amount').textContent = toMoney(data.metrics.coupons.total_discount);
        };

        const loadChart = async (id, url, labelsKey = 'labels', datasetsKey = 'datasets') => {
            const q = buildQuery(getFilters());
            const res = await fetch(`/api/analytics/${url}?${q}`);
            const data = await res.json();
            if (!data.success) return;

            const ctx = document.getElementById(id).getContext('2d');
            if (charts[id]) charts[id].destroy();
            charts[id] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data[labelsKey],
                    datasets: data[datasetsKey].map((ds, i) => ({
                        ...ds,
                        borderColor: ['#2563eb', '#16a34a', '#f59e0b', '#7c3aed'][i % 4],
                        backgroundColor: ['#93c5fd55', '#86efac55', '#fde68a55', '#c4b5fd55'][i % 4],
                        tension: 0.3,
                        fill: true,
                    }))
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true } }
                }
            });
        };

        const apply = () => {
            loadMetrics();
            loadChart('transactionsChart', 'transactions');
            loadChart('affiliateChart', 'affiliates');
            loadChart('couponChart', 'coupons');
            loadChart('pointsChart', 'points');
        };

        document.getElementById('applyFilters').addEventListener('click', apply);
        // Initialize with default range (last 30 days)
        const d = new Date();
        const toISODate = (dt) => dt.toISOString().split('T')[0];
        const from = new Date(); from.setDate(d.getDate() - 30);
        document.getElementById('date_from').value = toISODate(from);
        document.getElementById('date_to').value = toISODate(d);
        apply();
    </script>
</body>
</html>