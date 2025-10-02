// Function to show a specific section
function showSection(sectionId) {
  console.log("Showing section:", sectionId);

  const sections = document.querySelectorAll(".content-section");
  sections.forEach((section) => {
    section.style.display = "none";
  });

  const targetSection = document.getElementById(sectionId);
  if (targetSection) {
    targetSection.style.display = "block";
    console.log("Successfully showed section:", sectionId);
  } else {
    console.error(`Section ${sectionId} not found`);
  }
}

// show dashboard section
document.addEventListener("DOMContentLoaded", function () {
  showSection("dashboard");
});

// Logout confirmation function
function confirmLogout() {
  const confirmLogout = confirm("Are you sure you want to logout?");
  if (confirmLogout) {
    window.location.href = "logout.php";
  }
}

// Add event listener to the sidebar logout button as well
document.addEventListener("DOMContentLoaded", function () {
  const sidebarLogoutBtn = document.querySelector(".logoutSB a");
  if (sidebarLogoutBtn) {
    sidebarLogoutBtn.onclick = function (e) {
      e.preventDefault();
      confirmLogout();
    };
  }
});

// Logout functions
function confirmLogout() {
  document.getElementById("logoutModal").style.display = "block";
}

function closeLogoutModal() {
  document.getElementById("logoutModal").style.display = "none";
}

function confirmLogoutAction() {
  window.location.href = "logout.php";
}

// Add event listener to the sidebar logout button
document.addEventListener("DOMContentLoaded", function () {
  const sidebarLogoutBtn = document.querySelector(".logoutSB a");
  if (sidebarLogoutBtn) {
    sidebarLogoutBtn.onclick = function (e) {
      e.preventDefault();
      confirmLogout();
    };
  }
});

/////////////////////////////////////////////////// Night mood //////////////////////////////////////////////

function toggleNightMode() {
  document.body.classList.toggle("night-mode");

  // Save preference to localStorage
  const isNightMode = document.body.classList.contains("night-mode");
  localStorage.setItem("nightMode", isNightMode);

  // Update button icon and text
  const button = document.getElementById("nightModeBtn");
  if (isNightMode) {
    button.innerHTML = '<i class="fas fa-sun"></i> Light Mode';
  } else {
    button.innerHTML = '<i class="fas fa-moon"></i> Night Mode';
  }
}

// Check for saved preference when page loads
document.addEventListener("DOMContentLoaded", () => {
  const isNightMode = localStorage.getItem("nightMode") === "true";
  if (isNightMode) {
    document.body.classList.add("night-mode");
    document.getElementById("nightModeBtn").innerHTML =
      '<i class="fas fa-sun"></i> Light Mode';
  }
});

/////////////////////////////////////////////////// User Management (search-bar) //////////////////////////////////////////////
function filterUserTable() {
  var input = document.getElementById("userSearchInput");
  var filter = input.value.toLowerCase();
  var table = document.querySelector(".user-table");
  var tr = table.getElementsByTagName("tr");
  for (var i = 1; i < tr.length; i++) {
    // skip header row
    var tds = tr[i].getElementsByTagName("td");
    var found = false;
    for (var j = 0; j < tds.length - 1; j++) {
      // exclude actions column
      if (tds[j] && tds[j].textContent.toLowerCase().indexOf(filter) > -1) {
        found = true;
        break;
      }
    }
    tr[i].style.display = found ? "" : "none";
  }
}

/////////////////////////////////////////////////// Add Product model //////////////////////////////////////////////
//product search function
function filterInventoryTable() {
  const input = document.getElementById("inventorySearchInput");
  const filter = input.value.toLowerCase();
  const table = document.querySelector(".inventory-table");
  const trs = table.getElementsByTagName("tr");

  // Skip the header row (i = 1)
  for (let i = 1; i < trs.length; i++) {
    let tds = trs[i].getElementsByTagName("td");
    let found = false;
    for (let j = 0; j < tds.length - 1; j++) {
      // Exclude Actions column
      if (tds[j] && tds[j].textContent.toLowerCase().indexOf(filter) > -1) {
        found = true;
        break;
      }
    }
    trs[i].style.display = found ? "" : "none";
  }
}

// Function to open the add product popup
function openAddProductPopup() {
  document.getElementById("add-product-popup").style.display = "block";
}

// Function to close the add product popup
function closeAddProductPopup() {
  document.getElementById("add-product-popup").style.display = "none";
}

// Function to add a new product
function addProduct() {
  var productName = document.getElementById("productName").value.trim();
  var quantity = document.getElementById("quantity").value.trim();
  var price = document.getElementById("price").value.trim();
  var category = document.getElementById("category").value.trim();

  // Check if any fields are empty
  if (
    productName === "" ||
    quantity === "" ||
    price === "" ||
    category === ""
  ) {
    alert("Please fill out all fields before adding the product.");
    return; // Stop execution if fields are empty
  }

  const xhr = new XMLHttpRequest();
  xhr.open("POST", "Admin_function.php", true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      alert("Product Add Successfull"); // Show response message
      closeAddProductPopup(); // Close the popup

      location.reload("inventory-management"); // Reload the page to see the new product (Optional)
    }
  };

  const data = `productName=${productName}&quantity=${quantity}&price=${price}&category=${category}`;
  xhr.send(data); // Send data to PHP script

  document.getElementById("productName").value = "";
  document.getElementById("quantity").value = "";
  document.getElementById("price").value = "";
  document.getElementById("category").value = "laptop";
}

/////////////////////////////////////////////////// Edit Product model //////////////////////////////////////////////
function openEditModal(id, productName, quantity, price, category) {
  // Display the modal
  document.getElementById("editModal").style.display = "block";

  // Populate the form with current product details
  document.getElementById("editProductId").value = id;
  document.getElementById("editProductName").value = productName;
  document.getElementById("editQuantity").value = quantity;
  document.getElementById("editPrice").value = price;
  document.getElementById("editCategory").value = category;
}

function closeEditProductPopup() {
  document.getElementById("editModal").style.display = "none";
}

function submitEditForm() {
  // Retrieve updated values from the form
  const id = document.getElementById("editProductId").value;
  const productName = document.getElementById("editProductName").value;
  const quantity = document.getElementById("editQuantity").value;
  const price = document.getElementById("editPrice").value;
  const category = document.getElementById("editCategory").value;

  // Send AJAX request to update the product details
  fetch(`editProduct.php`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      id,
      productName,
      quantity,
      price,
      category,
    }),
  })
    .then((response) => response.text())
    .then((data) => {
      alert("Product updated successfully!");
      closeModal();
      location.reload("inventory-management"); // Reload to see updated data
    })
    .catch((error) => console.error("Error:", error));
}

/////////////////////////////////////////////////// Delete Product model //////////////////////////////////////////////
function openDeleteModal(id, productName) {
  // Display the delete confirmation modal
  document.getElementById("deleteModal").style.display = "block";

  // Set product details in the modal
  document.getElementById("deleteProductId").value = id;
  document.getElementById("deleteProductName").textContent =
    "Product: " + productName;
}

function closeproductDeleteModal() {
  document.getElementById("deleteModal").style.display = "none";
}

function confirmproductDelete() {
  // Retrieve the product ID from the modal
  const id = document.getElementById("deleteProductId").value;

  // Send AJAX request to delete the product
  fetch(`deleteProduct.php`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      id,
    }),
  })
    .then((response) => response.text())
    .then((data) => {
      alert("Product deleted successfully!");
      closeproductDeleteModal();
      location.reload("inventory-management"); // Reload the page to see updated list
    })
    .catch((error) => console.error("Error:", error));
}

/////////////////////////////////////////////////// Supplier management (search-bar) //////////////////////////////////////////////
function filterSupplierTable() {
  var input = document.getElementById("supplierSearchInput");
  var filter = input.value.toLowerCase();
  var table = document.getElementById("supplier-list");
  var tr = table.getElementsByTagName("tr");
  for (var i = 0; i < tr.length; i++) {
    var tds = tr[i].getElementsByTagName("td");
    var found = false;
    for (var j = 0; j < tds.length - 1; j++) {
      // exclude actions column
      if (tds[j] && tds[j].textContent.toLowerCase().indexOf(filter) > -1) {
        found = true;
        break;
      }
    }
    tr[i].style.display = found ? "" : "none";
  }
}

/////////////////////////////////////////////////// Add Supplier //////////////////////////////////////////////
function openAddSupplierPopup() {
  document.getElementById("add-supplier-popup").style.display = "block";
}

function closeAddSupplierPopup() {
  document.getElementById("add-supplier-popup").style.display = "none";
}

function addSupplier() {
  const supplierName = document.getElementById("supplierName").value.trim();
  const contact = document.getElementById("contact").value.trim();
  const email = document.getElementById("email").value.trim();
  const address = document.getElementById("address").value.trim();

  // Validate inputs
  if (supplierName === "" || contact === "" || email === "" || address === "") {
    alert("Please fill out all fields before adding the supplier.");
    return;
  }

  // Create form data
  const formData = new FormData();
  formData.append("action", "add");
  formData.append("supplierName", supplierName);
  formData.append("contact", contact);
  formData.append("email", email);
  formData.append("address", address);

  // Send AJAX request
  fetch("supplier_functions.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.text())
    .then((data) => {
      if (data === "success") {
        alert("Supplier added successfully!");
        closeAddSupplierPopup();
        location.reload();
      } else {
        alert("Error adding supplier. Please try again.");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Error adding supplier. Please try again.");
    });
}

/////////////////////////////////////////////////// Edit Supplier //////////////////////////////////////////////
function openEditSupplierModal(id, supplierName, contact, email, address) {
  document.getElementById("editSupplierModal").style.display = "block";
  document.getElementById("editSupplierId").value = id;
  document.getElementById("editSupplierName").value = supplierName;
  document.getElementById("editContact").value = contact;
  document.getElementById("editEmail").value = email;
  document.getElementById("editAddress").value = address;
}

function closeEditSupplierPopup() {
  document.getElementById("editSupplierModal").style.display = "none";
}

function updateSupplier() {
  const id = document.getElementById("editSupplierId").value;
  const supplierName = document.getElementById("editSupplierName").value.trim();
  const contact = document.getElementById("editContact").value.trim();
  const email = document.getElementById("editEmail").value.trim();
  const address = document.getElementById("editAddress").value.trim();

  const xhr = new XMLHttpRequest();
  xhr.open("POST", "supplier_functions.php", true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      closeEditSupplierPopup();
      location.reload();
    }
  };

  const data = `action=update&id=${id}&supplierName=${supplierName}&contact=${contact}&email=${email}&address=${address}`;
  xhr.send(data);
}

/////////////////////////////////////////////////// Delete Supplier //////////////////////////////////////////////
function openDeleteSupplierModal(id, supplierName) {
  document.getElementById("deleteSupplierModal").style.display = "block";
  document.getElementById("deleteSupplierId").value = id;
  document.getElementById("deleteSupplierName").textContent =
    "Supplier: " + supplierName;
}

function closeDeleteSupplierPopup() {
  document.getElementById("deleteSupplierModal").style.display = "none";
}

function deleteSupplier() {
  const id = document.getElementById("deleteSupplierId").value;

  const xhr = new XMLHttpRequest();
  xhr.open("POST", "supplier_functions.php", true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      closeDeleteSupplierPopup();
      location.reload();
    }
  };

  const data = `action=delete&id=${id}`;
  xhr.send(data);
}

/////////////////////////////////////////////////// Order management //////////////////////////////////////////////

//order_management.js

/////////////////////////////////////////////////// Order management (search-bar) //////////////////////////////////////////////
function filterOrderTable() {
  var input = document.getElementById("orderSearchInput");
  var filter = input.value.toLowerCase();
  var table = document.getElementById("order-list");
  if (!table) return;
  var tr = table.getElementsByTagName("tr");
  for (var i = 0; i < tr.length; i++) {
    var tds = tr[i].getElementsByTagName("td");
    var found = false;
    for (var j = 0; j < tds.length - 1; j++) {
      // exclude actions column
      if (tds[j] && tds[j].textContent.toLowerCase().indexOf(filter) > -1) {
        found = true;
        break;
      }
    }
    tr[i].style.display = found ? "" : "none";
  }
}

/////////////////////////////////////////////////// proposal management (search-bar) //////////////////////////////////////////////
function filterProposalTable() {
  var input = document.getElementById("proposalSearchInput");
  var filter = input.value.toLowerCase();
  var table = document.getElementById("proposal-list");
  if (!table) return;
  var tr = table.getElementsByTagName("tr");
  for (var i = 0; i < tr.length; i++) {
    var tds = tr[i].getElementsByTagName("td");
    var found = false;
    for (var j = 0; j < tds.length - 1; j++) {
      // exclude actions column
      if (tds[j] && tds[j].textContent.toLowerCase().indexOf(filter) > -1) {
        found = true;
        break;
      }
    }
    tr[i].style.display = found ? "" : "none";
  }
}

/////////////////////////////////////////////////// invoice management //////////////////////////////////////////////

//Invoice_management.js

/////////////////////////////////////////////////// invoice management (search-bar) //////////////////////////////////////////////
function filterInvoiceTable() {
  var input = document.getElementById("invoiceSearchInput");
  var filter = input.value.toLowerCase();
  var table = document.getElementById("invoice-list");
  if (!table) return;
  var tr = table.getElementsByTagName("tr");
  for (var i = 0; i < tr.length; i++) {
    var tds = tr[i].getElementsByTagName("td");
    var found = false;
    for (var j = 0; j < tds.length - 1; j++) {
      // exclude actions column
      if (tds[j] && tds[j].textContent.toLowerCase().indexOf(filter) > -1) {
        found = true;
        break;
      }
    }
    tr[i].style.display = found ? "" : "none";
  }
}

// Function to open the modal for creating a new invoice
function showCreateInvoiceForm() {
  document.getElementById("create-invoice-modal").style.display = "block";
}

// Function to open the modal for editing an existing invoice
function showEditInvoiceForm(invoiceId) {
  const invoiceRow = document.querySelector(`[data-invoice-id="${invoiceId}"]`);
  if (invoiceRow) {
    document.getElementById("edit-invoice-id").value = invoiceId;
    document.getElementById("edit-customer-name").value =
      invoiceRow.querySelector(".customer-name").innerText;
    document.getElementById("edit-invoice-date").value =
      invoiceRow.querySelector(".invoice-date").innerText;
    document.getElementById("edit-total-amount").value =
      invoiceRow.querySelector(".total-amount").innerText;
    document.getElementById("edit-invoice-modal").style.display = "block";
  }
}

// Function to show delete confirmation modal
function showDeleteInvoiceForm(invoiceId) {
  document.getElementById("delete-invoice-modal").style.display = "block";
  // Store invoice ID for deletion
  window.deleteInvoiceId = invoiceId;
}

// Function to create invoice
function createInvoice(event) {
  event.preventDefault();
  const customerName = document.getElementById("create-customer-name").value;
  const invoiceDate = document.getElementById("create-invoice-date").value;
  const totalAmount = document.getElementById("create-total-amount").value;

  const invoiceId = Date.now(); // Use timestamp as a unique ID
  const newRow = document.createElement("tr");
  newRow.setAttribute("data-invoice-id", invoiceId); // Assigning data-invoice-id

  newRow.innerHTML = `
                        <td>#${invoiceId}</td>
                        <td class="customer-name">${customerName}</td>
                        <td class="invoice-date">${invoiceDate}</td>
                        <td class="total-amount">${totalAmount}</td>
                        <td>Unpaid</td>
                        <td>
                            <button class="modify" onclick="viewInvoice(${invoiceId})">View</button>
                            <button class="edit-btn" onclick="showEditInvoiceForm(${invoiceId})">Edit</button>
                            <button class="delete-btn" onclick="showDeleteInvoiceForm(${invoiceId})">Delete</button>
                        </td>
    `;
  document.getElementById("invoice-list").appendChild(newRow);

  document.getElementById("create-customer-name").value = "";
  document.getElementById("create-invoice-date").value = "";
  document.getElementById("create-total-amount").value = "";

  closeModal("create-invoice-modal");
}

// Function to view invoice details
function viewInvoice(invoiceId) {
  const invoiceRow = document.querySelector(`[data-invoice-id="${invoiceId}"]`);
  if (invoiceRow) {
    const customerName = invoiceRow.querySelector(".customer-name").innerText;
    const invoiceDate = invoiceRow.querySelector(".invoice-date").innerText;
    const totalAmount = invoiceRow.querySelector(".total-amount").innerText;
    alert(
      `Invoice Details:\nCustomer: ${customerName}\nDate: ${invoiceDate}\nTotal: $${totalAmount}`
    );
  }
}

// Function to edit invoice
function editInvoice(event) {
  event.preventDefault();
  const invoiceId = document.getElementById("edit-invoice-id").value;
  const customerName = document.getElementById("edit-customer-name").value;
  const invoiceDate = document.getElementById("edit-invoice-date").value;
  const totalAmount = document.getElementById("edit-total-amount").value;

  const invoiceRow = document.querySelector(`[data-invoice-id="${invoiceId}"]`);
  if (invoiceRow) {
    invoiceRow.querySelector(".customer-name").innerText = customerName;
    invoiceRow.querySelector(".invoice-date").innerText = invoiceDate;
    invoiceRow.querySelector(".total-amount").innerText = totalAmount;
  }

  closeModal("edit-invoice-modal");
}

// Function to delete invoice
function deleteInvoice() {
  const invoiceRow = document.querySelector(
    `[data-invoice-id="${window.deleteInvoiceId}"]`
  );
  if (invoiceRow) {
    invoiceRow.remove();
  }
  closeModal("delete-invoice-modal");
}

// Close modal function
function closeModal(modalId) {
  document.getElementById(modalId).style.display = "none";
}

// Function to view invoice details in the modal
function viewInvoice(invoiceId) {
  const invoiceRow = document.querySelector(`[data-invoice-id="${invoiceId}"]`);
  if (invoiceRow) {
    const customerName = invoiceRow.querySelector(".customer-name").innerText;
    const invoiceDate = invoiceRow.querySelector(".invoice-date").innerText;
    const totalAmount = invoiceRow.querySelector(".total-amount").innerText;
    const status = invoiceRow.querySelector("td:nth-child(5)").innerText; // 5th column is status

    // Update modal content
    document.getElementById(
      "view-invoice-title"
    ).innerText = `View Invoice #${invoiceId}`;
    document.getElementById("view-invoice-id").innerText = invoiceId;
    document.getElementById("view-customer-name").innerText = customerName;
    document.getElementById("view-invoice-date").innerText = invoiceDate;
    document.getElementById("view-total-amount").innerText = totalAmount;
    document.getElementById("view-status").innerText = status;

    // Show the modal
    document.getElementById("view-invoice-modal").style.display = "block";
  }
}

// Function to close any modal
function closeModal(modalId) {
  document.getElementById(modalId).style.display = "none";
}

/////////////////////////////////////// report generation ///////////////////////////////////////
