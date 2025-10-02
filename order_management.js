//search-bar
function filterOrderTable() {
  const input = document.getElementById("orderSearchBar");
  const filter = input.value.toLowerCase();
  const table = document.querySelector(".order-table");
  if (!table) return;
  const trs = table.getElementsByTagName("tr");
  // Skip the header row (i=1)
  for (let i = 1; i < trs.length; i++) {
    let row = trs[i];
    let text = row.textContent.toLowerCase();
    row.style.display = text.includes(filter) ? "" : "none";
  }
}

// Order Management Tab Functions
function showOrderTab(tab) {
  const ordersTabs = document.querySelectorAll(".tab-content");
  ordersTabs.forEach((t) => t.classList.remove("active"));
  document.getElementById(`${tab}-tab`).classList.add("active");

  const tabBtns = document.querySelectorAll(".tab-btn");
  tabBtns.forEach((btn) => btn.classList.remove("active"));
  event.target.classList.add("active");
}

// Order Functions
function openCreateOrderModal() {
  document.getElementById("create-order-modal").style.display = "block";

  // Clear existing items
  const itemsTable = document.getElementById("orderItemsList");
  if (itemsTable) {
    itemsTable.innerHTML = "";
  }

  // Initialize with one empty row
  addOrderItem();

  // Reset total
  document.getElementById("orderTotal").textContent = "0.00";
}

function closeCreateOrderModal() {
  document.getElementById("create-order-modal").style.display = "none";
}

function closeViewOrderModal() {
  document.getElementById("view-order-modal").style.display = "none";
}

// Create order function
async function createOrder() {
  try {
    const formData = {
      customerName: document.getElementById("customerName").value,
      customerAddress: document.getElementById("customerAddress").value,
      contactNumber: document.getElementById("contactNumber").value,
      paymentMethod: document.getElementById("paymentMethod").value,
      items: Array.from(document.querySelectorAll("#orderItemsList tr")).map(
        (row) => ({
          product_id: row.querySelector(".product-select").value,
          quantity: parseFloat(row.querySelector(".quantity-input").value),
          unit_price: parseFloat(row.querySelector(".price-input").value),
          amount: parseFloat(row.querySelector(".amount-input").value),
        })
      ),
    };

    const response = await fetch("create_order.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(formData),
    });
    const result = await response.json();
    if (result.success) {
      alert(`Order created successfully!\nOrder ID: ${result.order_id}`);
      closeCreateOrderModal();
      location.reload(); // Refresh the page to show the new order
    } else {
      alert("Error creating order: " + (result.message || "Unknown error"));
    }
  } catch (error) {
    alert("Error creating order: " + error.message);
  }
}

// View order function
async function viewOrder(orderId) {
  if (!orderId) {
    alert("Invalid Order ID");
    return;
  }

  try {
    const modalContent = document.getElementById("order-details");
    modalContent.innerHTML =
      '<div class="loading">Loading order details...</div>';
    document.getElementById("view-order-modal").style.display = "block";

    const response = await fetch(
      `get_order.php?id=${encodeURIComponent(orderId)}`
    );
    const data = await response.json();

    if (!data.success) {
      throw new Error(data.message || "Failed to load order details");
    }

    const orderDetails = data.order;

    // Build items HTML with additional product details
    const itemsHtml = orderDetails.items
      .map(
        (item) => `
        <tr>
          <td>
            ${htmlEscape(item.product_name)}
            ${
              item.description
                ? `<br><small>${htmlEscape(item.description)}</small>`
                : ""
            }
            ${
              item.category
                ? `<br><small>Category: ${htmlEscape(item.category)}</small>`
                : ""
            }
          </td>
          <td>${item.quantity}</td>
          <td>Rs. ${parseFloat(item.unit_price).toFixed(2)}</td>
          <td>Rs. ${parseFloat(item.amount).toFixed(2)}</td>
        </tr>
      `
      )
      .join("");

    const statusClass = orderDetails.status.toLowerCase();
    const orderActions =
      orderDetails.status === "pending"
        ? `<div class="order-actions" style="margin-top: 20px;">
          <button onclick="completeOrder('${htmlEscape(
            orderDetails.order_id
          )}')" class="complete-btn">Complete Order</button>
         </div>`
        : "";

    modalContent.innerHTML = `
      <div class="order-info">
        <div class="info-group">
          <p><strong>Order ID:</strong> ${htmlEscape(orderDetails.order_id)}</p>
          <p><strong>Date:</strong> ${htmlEscape(orderDetails.order_date)}</p>
          <p><strong>Status:</strong> <span class="ord-status-${statusClass}">${
      orderDetails.status
    }</span></p>
        </div>
        <div class="info-group">
          <p><strong>Customer Name:</strong> ${htmlEscape(
            orderDetails.customer_name
          )}</p>
          <p><strong>Contact Number:</strong> ${htmlEscape(
            orderDetails.contact_number
          )}</p>
          <p><strong>Address:</strong> ${htmlEscape(
            orderDetails.customer_address
          )}</p>
        </div>
        <div class="info-group">
          <p><strong>Payment Method:</strong> ${htmlEscape(
            orderDetails.payment_method
          )}</p>
        </div>
      </div>
      <div class="order-items">
        <h4>Order Items</h4>
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
                <td colspan="3" style="text-align: right;"><strong>Total Amount:</strong></td>
                <td>Rs. ${parseFloat(orderDetails.total_amount || 0).toFixed(
                  2
                )}</td>
              </tr>
            </tfoot>
          </table>
          ${orderActions}
        </div>
      </div>`;
  } catch (error) {
    const modalContent = document.getElementById("order-details");
    modalContent.innerHTML = `<div class="error">Error: ${htmlEscape(
      error.message
    )}</div>`;
  }
}

// Complete order function
async function completeOrder(orderId) {
  if (!orderId) {
    alert("Invalid Order ID");
    return;
  }

  if (
    !confirm(
      "Are you sure you want to complete this order?\n\nThis will:\n- Mark the order as completed\n- Deduct items from inventory\n- Generate an invoice\n\nThis action cannot be undone."
    )
  ) {
    return;
  }

  try {
    const response = await fetch("order_functions.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        action: "complete_order",
        order_id: orderId,
      }),
    });

    const result = await response.json();
    if (result.success) {
      let message = "Order completed successfully!";
      if (result.invoice_id) {
        message += `\n\nInvoice ID: ${result.invoice_id}`;
        // If there's a download URL for the invoice, offer to open it
        if (result.invoice_url) {
          if (
            confirm(message + "\n\nWould you like to download the invoice?")
          ) {
            window.open(result.invoice_url, "_blank");
          }
        } else {
          alert(message);
        }
      } else {
        alert(message);
      }
      location.reload(); // Refresh the page to show updated status
    } else {
      throw new Error(result.message || "Failed to complete order");
    }
  } catch (error) {
    console.error("Error completing order:", error);
    alert("Error completing order: " + error.message);
  }
}

// Order Items Management
function addOrderItem() {
  const itemsTable = document.getElementById("orderItemsList");
  if (!itemsTable) {
    console.error("Order items table not found");
    return;
  }

  const newRow = document.createElement("tr");
  newRow.innerHTML = `
        <td>
            <select class="product-select" onchange="updateOrderItemPrice(this)" required>
                <option value="">Select Product</option>
                ${getProductOptions()}
            </select>
        </td>
        <td>
            <input type="number" 
                class="quantity-input" 
                onchange="updateOrderItemTotal(this)" 
                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                min="1" 
                value="1" 
                required>
        </td>
        <td><input type="number" class="price-input" readonly></td>
        <td><input type="number" class="amount-input" readonly></td>
        <td><button type="button" onclick="removeOrderItem(this)" class="delete-btn">Remove</button></td>
    `;
  itemsTable.appendChild(newRow);
  updateOrderTotal();
}

function updateOrderItemPrice(select) {
  console.log("Selected value:", select.value); // Debug log
  const row = select.closest("tr");
  const priceInput = row.querySelector(".price-input");
  const quantityInput = row.querySelector(".quantity-input");
  const amountInput = row.querySelector(".amount-input");

  if (!select.value) {
    // Clear values if no product is selected
    priceInput.value = "";
    quantityInput.max = "";
    amountInput.value = "";
    updateOrderTotal();
    return;
  }

  // Convert select.value to string for comparison if it's a number
  const selectedId = select.value.toString();
  console.log("Looking for product with id:", selectedId); // Debug log
  const product = window.productsList?.find(
    (p) => p.id.toString() === selectedId
  );
  console.log("Found product:", product); // Debug log

  if (product) {
    // Set the price
    priceInput.value = parseFloat(product.price).toFixed(2);

    // Update max quantity based on stock
    quantityInput.max = product.quantity;

    // If current quantity is more than stock, adjust it
    if (parseFloat(quantityInput.value) > product.quantity) {
      quantityInput.value = product.quantity;
    }

    // Calculate amount
    const amount = parseFloat(quantityInput.value) * parseFloat(product.price);
    amountInput.value = amount.toFixed(2);

    // Update the total
    updateOrderTotal();
  } else {
    console.error("Product not found for id:", selectedId); // Debug log
    priceInput.value = "";
    quantityInput.max = "";
    amountInput.value = "";
    updateOrderTotal();
  }
}

function updateOrderItemTotal(input) {
  const row = input.closest("tr");
  const quantity = parseFloat(input.value) || 0;
  const price = parseFloat(row.querySelector(".price-input").value) || 0;
  row.querySelector(".amount-input").value = (quantity * price).toFixed(2);
  updateOrderTotal();
}

function updateOrderTotal() {
  const total = Array.from(document.querySelectorAll(".amount-input")).reduce(
    (sum, input) => sum + (parseFloat(input.value) || 0),
    0
  );
  document.getElementById("orderTotal").textContent = total.toFixed(2);
}

function removeOrderItem(button) {
  button.closest("tr").remove();
  updateOrderTotal();
}

//search-bar
function filterProposalTable() {
  const input = document.getElementById("proposalSearchBar");
  const filter = input.value.toLowerCase();
  const table = document.querySelector(".proposal-table");
  if (!table) return;
  const trs = table.getElementsByTagName("tr");
  // Skip the header row (i=1)
  for (let i = 1; i < trs.length; i++) {
    let row = trs[i];
    let text = row.textContent.toLowerCase();
    row.style.display = text.includes(filter) ? "" : "none";
  }
}

// Proposal Functions
function openCreateProposalModal() {
  document.getElementById("create-proposal-modal").style.display = "block";
  addProposalItem();
}

function closeCreateProposalModal() {
  document.getElementById("create-proposal-modal").style.display = "none";
}

function closeViewProposalModal() {
  document.getElementById("view-proposal-modal").style.display = "none";
}

function addProposalItem() {
  const itemsTable = document.getElementById("proposalItemsList");
  const newRow = document.createElement("tr");
  newRow.innerHTML = `
        <td>
            <select class="product-select" onchange="updateProposalItemPrice(this)">
                <option value="">Select Product</option>
                ${getProductOptions()}
            </select>
        </td>
        <td><input type="number" class="quantity-input" onchange="updateProposalItemTotal(this)" min="1" value="1"></td>
        <td><input type="number" class="price-input" readonly></td>
        <td><input type="number" class="amount-input" readonly></td>
        <td><button onclick="removeProposalItem(this)" class="delete-btn">Remove</button></td>
    `;
  itemsTable.appendChild(newRow);
}

function updateProposalItemPrice(select) {
  const row = select.closest("tr");
  const priceInput = row.querySelector(".price-input");
  const product = window.productsList?.find((p) => p.id === select.value);
  if (product) {
    priceInput.value = product.price;
    updateProposalItemTotal(row.querySelector(".quantity-input"));
  } else {
    priceInput.value = "";
  }
}

function updateProposalItemTotal(input) {
  const row = input.closest("tr");
  const quantity = parseFloat(input.value) || 0;
  const price = parseFloat(row.querySelector(".price-input").value) || 0;
  row.querySelector(".amount-input").value = (quantity * price).toFixed(2);
  updateProposalTotal();
}

function updateProposalTotal() {
  const total = Array.from(
    document.querySelectorAll("#proposalItemsList .amount-input")
  ).reduce((sum, input) => sum + (parseFloat(input.value) || 0), 0);
  document.getElementById("proposalTotal").textContent = total.toFixed(2);
}

function removeProposalItem(button) {
  button.closest("tr").remove();
  updateProposalTotal();
}

// Create proposal function
async function createProposal(event) {
  event.preventDefault();
  try {
    const formData = {
      customerName: document.getElementById("proposalCustomerName").value,
      customerAddress: document.getElementById("proposalCustomerAddress").value,
      contactNumber: document.getElementById("proposalContactNumber").value,
      validityDate: document.getElementById("validityDate").value,
      terms: document.getElementById("terms").value,
      items: Array.from(document.querySelectorAll("#proposalItemsList tr")).map(
        (row) => ({
          product_id: row.querySelector(".product-select").value,
          quantity: parseFloat(row.querySelector(".quantity-input").value),
          unit_price: parseFloat(row.querySelector(".price-input").value),
          amount: parseFloat(row.querySelector(".amount-input").value),
        })
      ),
    };

    const response = await fetch("create_proposal.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(formData),
    });
    const result = await response.json();
    if (result.success) {
      alert(
        `Proposal created successfully!\nProposal ID: ${result.proposal_id}`
      );
      closeCreateProposalModal();
      location.reload();
    } else {
      alert("Error creating proposal: " + (result.message || "Unknown error"));
    }
  } catch (error) {
    alert("Error creating proposal: " + error.message);
  }
}

// View proposal function
async function viewProposal(proposalId) {
  try {
    const response = await fetch(`get_proposal.php?id=${proposalId}`);
    const data = await response.json();

    if (data.success) {
      const proposal = data.proposal;
      let detailsHtml = `
                <div class="proposal-details">
                    <div class="details-section">
                        <h4>Proposal Information</h4>
                        <p><strong>Proposal ID:</strong> ${proposal.proposal_id}</p>
                        <p><strong>Date Created:</strong> ${proposal.date_created}</p>
                        <p><strong>Valid Until:</strong> ${proposal.validity_date}</p>
                    </div>
                    <div class="details-section">
                        <h4>Customer Information</h4>
                        <p><strong>Name:</strong> ${proposal.customer_name}</p>
                        <p><strong>Address:</strong> ${proposal.customer_address}</p>
                        <p><strong>Contact:</strong> ${proposal.contact_number}</p>
                    </div>
                    <div class="details-section">
                        <h4>Items</h4>
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>`;

      proposal.items.forEach((item) => {
        detailsHtml += `
                    <tr>
                        <td>${item.product_name}</td>
                        <td>${item.quantity}</td>
                        <td>Rs. ${parseFloat(item.unit_price).toFixed(2)}</td>
                        <td>Rs. ${parseFloat(item.amount).toFixed(2)}</td>
                    </tr>`;
      });

      detailsHtml += `
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" style="text-align: right;"><strong>Total Amount:</strong></td>
                                    <td>Rs. ${parseFloat(
                                      proposal.total_amount
                                    ).toFixed(2)}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="details-section">
                        <h4>Terms and Conditions</h4>
                        <p>${proposal.terms}</p>
                    </div>
                </div>`;

      document.getElementById("proposal-details").innerHTML = detailsHtml;
      document.getElementById("view-proposal-modal").style.display = "block";
    } else {
      alert("Error loading proposal: " + data.message);
    }
  } catch (error) {
    alert("Error loading proposal: " + error.message);
  }
}

// Download proposal function
function downloadProposal(proposalId) {
  window.location.href = `download_proposal.php?id=${proposalId}`;
}

// Helper Functions
function getProductOptions() {
  if (!window.productsList || !Array.isArray(window.productsList)) {
    return "";
  }
  return window.productsList
    .filter(
      (product) =>
        product && product.id && product.product_name && product.quantity > 0
    )
    .map(
      (product) =>
        `<option value="${product.id}">${product.product_name}</option>`
    )
    .join("");
}

// Helper function to escape HTML and prevent XSS
function htmlEscape(str) {
  if (!str) return "";
  return str
    .toString()
    .replace(/&/g, "&amp;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;");
}

// Initialize products list on page load
document.addEventListener("DOMContentLoaded", async function () {
  try {
    const response = await fetch("get_products.php");
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();

    if (!data.success) {
      throw new Error(data.message || "Failed to load products");
    }

    window.productsList = data.products;

    // Initialize product selects if they exist
    document.querySelectorAll(".product-select").forEach((select) => {
      select.innerHTML = `<option value="">Select Product</option>${getProductOptions()}`;
    });
  } catch (error) {
    console.error("Error loading products:", error);
    alert(
      "Failed to load product list. Please refresh the page or contact support."
    );
  }
});
