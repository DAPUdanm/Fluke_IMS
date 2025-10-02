// Report Generation Functions
let lastGeneratedReport = null;

// Initialize date inputs with defaults
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());
    
    document.getElementById('start-date').value = formatDate(lastMonth);
    document.getElementById('end-date').value = formatDate(today);
    
    // Add event listeners
    document.querySelector('.generate-report-btn').addEventListener('click', generateReport);
    document.querySelector('.download-pdf-btn').addEventListener('click', () => downloadReport('pdf'));
    document.querySelector('.download-excel-btn').addEventListener('click', () => downloadReport('excel'));
    
    // Handle report type change
    document.getElementById('report-type').addEventListener('change', function() {
        const dateInputs = document.querySelectorAll('.date-range input');
        if (this.value === 'inventory') {
            dateInputs.forEach(input => input.disabled = true);
        } else {
            dateInputs.forEach(input => input.disabled = false);
        }
    });
});

// Helper function to format date as YYYY-MM-DD
function formatDate(date) {
    return date.toISOString().split('T')[0];
}

// Function to generate report
async function generateReport() {
    const type = document.getElementById('report-type').value;
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    
    // Validate dates if not inventory report
    if (type !== 'inventory' && (!startDate || !endDate)) {
        alert('Please select both start and end dates');
        return;
    }
    
    if (type !== 'inventory' && new Date(endDate) < new Date(startDate)) {
        alert('End date cannot be earlier than start date');
        return;
    }
    
    try {
        const response = await fetch('report_functions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'preview_report',
                type: type,
                start_date: startDate,
                end_date: endDate
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Store the parameters for download
            lastGeneratedReport = {
                type: type,
                startDate: startDate,
                endDate: endDate
            };
            
            // Enable download buttons
            document.querySelector('.download-pdf-btn').disabled = false;
            document.querySelector('.download-excel-btn').disabled = false;
            
            // Show preview
            displayReportPreview(result.data, type);
        } else {
            throw new Error(result.message || 'Failed to generate report');
        }
    } catch (error) {
        alert('Error generating report: ' + error.message);
        console.error('Error generating report:', error);
    }
}

// Function to download report
function downloadReport(format) {
    if (!lastGeneratedReport) {
        alert('Please generate a report first');
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'report_functions.php';
    
    // Add necessary fields
    const fields = {
        type: lastGeneratedReport.type,
        start_date: lastGeneratedReport.startDate,
        end_date: lastGeneratedReport.endDate,
        action: 'download_report',
        format: format
    };
    
    for (const [key, value] of Object.entries(fields)) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        form.appendChild(input);
    }
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Function to display report preview
function displayReportPreview(data, type) {
    const previewDiv = document.getElementById('report-preview');
    let html = '<div class="report-container">';
    
    switch (type) {
        case 'inventory':
            html += generateInventoryReportHTML(data);
            break;
        case 'sales':
            html += generateSalesReportHTML(data);
            break;
        case 'orders':
            html += generateOrdersReportHTML(data);
            break;
        default:
            html += '<p>Invalid report type</p>';
    }
    
    html += '</div>';
    previewDiv.innerHTML = html;
}

// Helper function to generate inventory report HTML
function generateInventoryReportHTML(data) {
    let html = '<h3>Inventory Report</h3><table class="report-table">';
    html += '<thead><tr><th>ID</th><th>Product Name</th><th>Category</th><th>Quantity</th><th>Price</th></tr></thead><tbody>';
    
    for (const item of data) {
        html += `<tr>
            <td>${item.id}</td>
            <td>${item.product_name}</td>
            <td>${item.category}</td>
            <td>${item.quantity}</td>
            <td>Rs. ${Number(item.price).toFixed(2)}</td>
        </tr>`;
    }
    
    html += '</tbody></table>';
    return html;
}

// Helper function to generate sales report HTML
function generateSalesReportHTML(data) {
    let html = '<h3>Sales Report</h3><table class="report-table">';
    html += '<thead><tr><th>Date</th><th>Invoice ID</th><th>Customer</th><th>Amount</th><th>Status</th></tr></thead><tbody>';
    
    for (const sale of data) {
        html += `<tr>
            <td>${sale.sale_date}</td>
            <td>${sale.invoice_id}</td>
            <td>${sale.customer_name}</td>
            <td>Rs. ${Number(sale.total_amount).toFixed(2)}</td>
            <td>${sale.status}</td>
        </tr>`;
    }
    
    html += '</tbody></table>';
    return html;
}

// Helper function to generate orders report HTML
function generateOrdersReportHTML(data) {
    let html = '<h3>Orders Report</h3><table class="report-table">';
    html += '<thead><tr><th>Order ID</th><th>Customer</th><th>Date</th><th>Amount</th><th>Status</th></tr></thead><tbody>';
    
    for (const order of data) {
        html += `<tr>
            <td>${order.order_id}</td>
            <td>${order.customer_name}</td>
            <td>${order.order_date}</td>
            <td>Rs. ${Number(order.total_amount).toFixed(2)}</td>
            <td>${order.status}</td>
        </tr>`;
    }
    
    html += '</tbody></table>';
    return html;
}
