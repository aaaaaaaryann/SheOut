<?php
include "conn.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales and Orders Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
</head>
<body>
    <?php
    // PHP Data Fetching
    $year = date("Y");
    $data = array();
    $orderData = array();

    // Fetch sales data
    $query_sales = mysqli_query($conn, "SELECT DATE_FORMAT(co.order_date, '%Y-%m-%d') as order_date, SUM(product_price) as amount 
                                  FROM customer_order co 
                                  LEFT JOIN customer_order_product cop ON co.order_id = cop.order_id 
                                  LEFT JOIN product p ON cop.product_id = p.product_id 
                                  WHERE co.order_status = 'Completed'
                                  GROUP BY order_date 
                                  ORDER BY co.order_date");

    while ($rowsale = mysqli_fetch_assoc($query_sales)) {
        $data[] = $rowsale;
    }

    // Fetch orders data
    $query_orders = mysqli_query($conn, "SELECT DATE_FORMAT(order_date, '%Y-%m') as month, COUNT(order_id) as orders 
                                     FROM customer_order 
                                     WHERE order_status = 'Completed' 
                                     GROUP BY month 
                                     ORDER BY month");

    while ($roworder = mysqli_fetch_assoc($query_orders)) {
        $orderData[$roworder['month']] = $roworder['orders'];
    }
    ?>

    <select id="yearSelect">
        <!-- Options will be populated dynamically -->
    </select>

    <select id="monthSelect">
        <!-- Options will be populated dynamically -->
    </select>

    <p>Total Sales: ₱<span id="totalSales">0.00</span></p>

    <canvas id="salesChart" width="80%" height="20%"></canvas>

    <p>Total Orders: <span id="totalOrders">0</span></p>

    <canvas id="ordersChart" width="80%" height="20%"></canvas>

    <script>
        const salesData = <?php echo json_encode($data); ?>;
        const orderData = <?php echo json_encode($orderData); ?>;
        const currentYear = <?php echo json_encode($year); ?>;

        // Group sales data by year, month, and day
        const groupedSalesData = salesData.reduce((acc, cur) => {
            const [year, month, day] = cur.order_date.split('-');
            if (!acc[year]) acc[year] = {};
            if (!acc[year][month]) acc[year][month] = {};
            acc[year][month][day] = cur.amount;
            return acc;
        }, {});

        const years = Object.keys(groupedSalesData);
        const months = Array.from({length: 12}, (_, i) => String(i + 1).padStart(2, '0'));

        const yearSelect = document.getElementById('yearSelect');
        const monthSelect = document.getElementById('monthSelect');
        const totalSalesElement = document.getElementById('totalSales');
        const totalOrdersElement = document.getElementById('totalOrders');

        // Populate year options
        years.forEach(year => {
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;
            yearSelect.appendChild(option);
        });

        // Populate month options
        months.forEach(month => {
            const option = document.createElement('option');
            option.value = month;
            option.textContent = month;
            monthSelect.appendChild(option);
        });

        // Function to get days in a month
        function getDaysInMonth(year, month) {
            const date = new Date(year, month, 0);
            return date.getDate();
        }

        // Function to update sales chart
        function updateSalesChart() {
            const selectedYear = yearSelect.value;
            const selectedMonth = monthSelect.value;
            const daysInMonth = getDaysInMonth(selectedYear, selectedMonth);
            const salesChartData = Array(daysInMonth).fill(0);
            const salesChartLabels = Array.from({length: daysInMonth}, (_, i) => String(i + 1).padStart(2, '0'));
            let totalSales = 0;

            if (groupedSalesData[selectedYear] && groupedSalesData[selectedYear][selectedMonth]) {
                const daysData = groupedSalesData[selectedYear][selectedMonth];
                Object.keys(daysData).forEach(day => {
                    const dayIndex = parseInt(day) - 1;
                    salesChartData[dayIndex] = daysData[day];
                    totalSales += parseFloat(daysData[day]);
                });
            }

            totalSalesElement.textContent = totalSales.toFixed(2);

            if (window.salesChart !== undefined) {
                window.salesChart.destroy();
            }

            const salesChartCtx = document.getElementById('salesChart').getContext('2d');
            window.salesChart = new Chart(salesChartCtx, {
                type: 'bar',
                data: {
                    labels: salesChartLabels,
                    datasets: [{
                        label: 'Daily Sales Amount',
                        data: salesChartData,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Function to update orders chart
        function updateOrdersChart() {
            const selectedYear = yearSelect.value;
            const selectedMonth = monthSelect.value;
            const ordersData = Array(12).fill(0);
            let totalOrders = 0;

            if (orderData[selectedYear]) {
                ordersData[selectedMonth - 1] = orderData[`${selectedYear}-${selectedMonth}`] || 0;
                totalOrders = parseInt(orderData[`${selectedYear}-${selectedMonth}`]) || 0;
            }

            totalOrdersElement.textContent = totalOrders;

            if (window.ordersChart !== undefined) {
                window.ordersChart.destroy();
            }

            const ordersChartCtx = document.getElementById('ordersChart').getContext('2d');
            window.ordersChart = new Chart(ordersChartCtx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Monthly Orders',
                        data: ordersData,
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Event listeners for changes in year and month selects
        yearSelect.addEventListener('change', () => {
            updateSalesChart();
            updateOrdersChart();
        });

        monthSelect.addEventListener('change', () => {
            updateSalesChart();
            updateOrdersChart();
        });

        // Initialize with the current year and month
        yearSelect.value = currentYear;
        updateSalesChart();
        updateOrdersChart();
    </script>
</body>
</html>

