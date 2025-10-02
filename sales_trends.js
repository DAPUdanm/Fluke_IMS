// Initialize the sales chart
let salesChart = null;

// Function to load and display sales trends
async function loadSalesTrends() {
    try {
        const response = await fetch('get_sales_trends.php');
        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message || 'Failed to load sales data');
        }

        const salesData = data.data;
        const dates = salesData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        });
        const sales = salesData.map(item => item.sales);
        const orders = salesData.map(item => item.orders);

        // Destroy existing chart if it exists
        if (salesChart) {
            salesChart.destroy();
        }

        // Get the canvas context
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        // Create the new chart
        salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Sales (Rs.)',
                        data: sales,
                        backgroundColor: 'rgba(255, 135, 0, 0.5)',
                        borderColor: '#ff8700',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Orders',
                        data: orders,
                        type: 'line',
                        backgroundColor: 'rgba(53, 162, 235, 0.5)',
                        borderColor: 'rgb(53, 162, 235)',
                        borderWidth: 2,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Last 7 Days Sales & Orders'
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Sales Amount (Rs.)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Number of Orders'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });

    } catch (error) {
        console.error('Error loading sales trends:', error);
        document.getElementById('salesChart').innerHTML = 'Error loading sales data';
    }
}

// Load sales trends when the page loads
document.addEventListener('DOMContentLoaded', () => {
    loadSalesTrends();
    // Refresh sales trends every 5 minutes
    setInterval(loadSalesTrends, 5 * 60 * 1000);
});
