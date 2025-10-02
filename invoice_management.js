//search-bar
function filterInvoiceTable() {
  const input = document.getElementById("invoiceSearchBar");
  const filter = input.value.toLowerCase();
  const table = document.querySelector(".invoice-table");
  if (!table) return;
  const trs = table.getElementsByTagName("tr");
  // Skip the header row (i=1)
  for (let i = 1; i < trs.length; i++) {
    let row = trs[i];
    let text = row.textContent.toLowerCase();
    row.style.display = text.includes(filter) ? "" : "none";
  }
}

// View invoice function
async function viewInvoice(invoiceId) {
  try {
    // Clear existing content first
    const modalContent = document.getElementById("invoice-details");
    modalContent.innerHTML =
      '<div class="loading">Loading invoice details...</div>';

    // Show the modal immediately with loading state
    document.getElementById("view-invoice-modal").style.display = "block";

    const response = await fetch(
      `get_invoice.php?id=${encodeURIComponent(invoiceId)}`
    );
    const data = await response.json();

    if (!data.success) {
      throw new Error(data.message || "Failed to load invoice details");
    }

    const invoice = data.invoice;

    // Generate the invoice details HTML
    const itemsHtml = invoice.items
      .map(
        (item) => `
            <tr>
                <td>${item.product_name}</td>
                <td>${item.quantity}</td>
                <td>Rs. ${Number(item.unit_price).toFixed(2)}</td>
                <td>Rs. ${Number(item.unit_price * item.quantity).toFixed(
                  2
                )}</td>
            </tr>
        `
      )
      .join("");

    // Update the modal content
    modalContent.innerHTML = `
            <div class="invoice-info">
                <div class="info-group">
                    <p><strong>Order ID:</strong> ${invoice.order_id}</p>
                    <p><strong>Invoice ID:</strong> ${invoice.invoice_id}</p>
                    <p><strong>Customer Name:</strong> ${
                      invoice.customer_name
                    }</p>
                    <p><strong>Contact Number:</strong> ${
                      invoice.contact_number
                    }</p>
                </div>
                <div class="info-group">
                    <p><strong>Date:</strong> ${invoice.invoice_date}</p>
                    <p><strong>Payment Method:</strong> ${
                      invoice.payment_method
                    }</p>
                    <p><strong>Status:</strong> <span class="inv-status-${invoice.invoice_status.toLowerCase()}">${
      invoice.invoice_status
    }</span></p>
                </div>
            </div>
            <div class="invoice-items">
                <h4>Invoice Items</h4>
                <div class="table-wrapper">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${itemsHtml}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Total Amount:</strong></td>
                                <td><strong>Rs. ${Number(
                                  invoice.total_amount
                                ).toFixed(2)}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>`;
  } catch (error) {
    document.getElementById("invoice-details").innerHTML = `
            <div class="error-message">
                Error loading invoice details: ${error.message}
            </div>`;
  }
}

// Function to close invoice modal
function closeViewInvoiceModal() {
  document.getElementById("view-invoice-modal").style.display = "none";
}

// Download invoice function
function downloadInvoice(invoiceId) {
  if (!invoiceId) {
    alert("Invalid Invoice ID");
    return;
  }
  window.location.href = `download_invoice.php?id=${encodeURIComponent(
    invoiceId
  )}`;
}
