// Proposal Functions
function openCreateProposalModal() {
  document.getElementById("create-proposal-modal").style.display = "block";
  // Initialize with one empty row
  addProposalItem();
  // Update the product options
  const productSelects = document.querySelectorAll(
    "#proposalItemsList .product-select"
  );
  productSelects.forEach((select) => {
    select.innerHTML = `
            <option value="">Select Product</option>
            ${getProductOptions()}
        `;
  });
}

function closeCreateProposalModal() {
  document.getElementById("create-proposal-modal").style.display = "none";
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
  const selectedOption = select.options[select.selectedIndex];
  priceInput.value = selectedOption.dataset.price || 0;
  updateProposalItemTotal(row.querySelector(".quantity-input"));
}

function updateProposalItemTotal(input) {
  const row = input.closest("tr");
  const quantity = parseFloat(row.querySelector(".quantity-input").value) || 0;
  const price = parseFloat(row.querySelector(".price-input").value) || 0;
  row.querySelector(".amount-input").value = (quantity * price).toFixed(2);
  updateProposalTotal();
}

function updateProposalTotal() {
  const total = Array.from(document.querySelectorAll("#proposalItemsList .amount-input"))
    .reduce((sum, input) => sum + parseFloat(input.value || 0), 0);
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
    // Validate required fields
    const requiredFields = {
      "proposalCustomerName": "Customer Name",
      "proposalCustomerAddress": "Customer Address",
      "proposalContactNumber": "Contact Number",
      "validityDate": "Validity Date",
      "terms": "Terms and Conditions"
    };

    for (const [id, label] of Object.entries(requiredFields)) {
      const element = document.getElementById(id);
      if (!element || !element.value.trim()) {
        throw new Error(`${label} is required`);
      }
    }

    // Validate items
    const items = Array.from(document.querySelectorAll("#proposalItemsList tr"));
    if (items.length === 0) {
      throw new Error("At least one item is required");
    }

    const formData = {
      customerName: document.getElementById("proposalCustomerName").value.trim(),
      customerAddress: document.getElementById("proposalCustomerAddress").value.trim(),
      contactNumber: document.getElementById("proposalContactNumber").value.trim(),
      validityDate: document.getElementById("validityDate").value,
      terms: document.getElementById("terms").value.trim(),
      items: items.map(row => {
        const productSelect = row.querySelector(".product-select");
        if (!productSelect.value) {
          throw new Error("Please select a product for all items");
        }

        const quantity = parseFloat(row.querySelector(".quantity-input").value);
        if (isNaN(quantity) || quantity <= 0) {
          throw new Error("Please enter a valid quantity for all items");
        }

        const unitPrice = parseFloat(row.querySelector(".price-input").value);
        if (isNaN(unitPrice) || unitPrice <= 0) {
          throw new Error("Invalid unit price for one or more items");
        }

        return {
          product_id: productSelect.value,
          quantity: quantity,
          unit_price: unitPrice,
          amount: parseFloat(row.querySelector(".amount-input").value)
        };
      })
    };

    console.log("Sending proposal data:", formData);

    const response = await fetch("create_proposal.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(formData),
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const result = await response.json();
    console.log("Server response:", result);

    if (result.success) {
      alert("Proposal created successfully!");
      closeCreateProposalModal();
      location.reload();
    } else {
      const errorMsg = result.error_details 
        ? `Error: ${result.message}\nFile: ${result.error_details.file}\nLine: ${result.error_details.line}`
        : `Error: ${result.message}`;
      throw new Error(errorMsg);
    }
  } catch (error) {
    alert("Error creating proposal: " + error.message);
    // console.error("Error creating proposal:", error);
  }
}

// Add the close view modal function
function closeViewProposalModal() {
    document.getElementById("view-proposal-modal").style.display = "none";
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
                            <tbody>
            `;

            proposal.items.forEach(item => {
                detailsHtml += `
                    <tr>
                        <td>${item.product_name}</td>
                        <td>${item.quantity}</td>
                        <td>Rs. ${parseFloat(item.unit_price).toFixed(2)}</td>
                        <td>Rs. ${parseFloat(item.amount).toFixed(2)}</td>
                    </tr>
                `;
            });

            detailsHtml += `
                            </tbody>
                        </table>
                    </div>
                    <div class="details-section">
                        <h4>Total Amount: Rs. ${parseFloat(proposal.total_amount).toFixed(2)}</h4>
                    </div>
                    <div class="details-section">
                        <h4>Terms and Conditions</h4>
                        <p>${proposal.terms}</p>
                    </div>
                </div>
            `;

            document.getElementById("proposal-details").innerHTML = detailsHtml;
            document.getElementById("view-proposal-modal").style.display = "block";
        } else {
            alert('Error loading proposal: ' + data.message);
        }
    } catch (error) {
        alert('Error loading proposal: ' + error.message);
    }
}

// Edit proposal function
async function editProposal(proposalId) {
    try {
        const response = await fetch(`get_proposal.php?id=${proposalId}`);
        const data = await response.json();

        if (data.success) {
            const proposal = data.proposal;
            
            // Populate the form with existing proposal data
            document.getElementById("proposalCustomerName").value = proposal.customer_name;
            document.getElementById("proposalCustomerAddress").value = proposal.customer_address;
            document.getElementById("proposalContactNumber").value = proposal.contact_number;
            document.getElementById("validityDate").value = proposal.validity_date;
            document.getElementById("terms").value = proposal.terms;

            // Clear existing items
            document.getElementById("proposalItemsList").innerHTML = '';

            // Add existing items
            proposal.items.forEach(item => {
                addProposalItem();
                const lastRow = document.querySelector("#proposalItemsList tr:last-child");
                const productSelect = lastRow.querySelector(".product-select");
                productSelect.value = item.product_id;
                lastRow.querySelector(".quantity-input").value = item.quantity;
                lastRow.querySelector(".price-input").value = item.unit_price;
                lastRow.querySelector(".amount-input").value = item.amount;
            });

            // Update total
            updateProposalTotal();

            // Add hidden input for proposal ID
            if (!document.getElementById('editProposalId')) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.id = 'editProposalId';
                document.getElementById('proposal-form').appendChild(hiddenInput);
            }
            document.getElementById('editProposalId').value = proposal.proposal_id;

            // Change button text and action
            const submitBtn = document.querySelector('#create-proposal-modal .submit-btnbox');
            submitBtn.textContent = 'Save Changes';
            submitBtn.onclick = saveProposalChanges;

            // Show modal
            document.getElementById('create-proposal-modal').style.display = 'block';
        } else {
            alert('Error loading proposal: ' + data.message);
        }
    } catch (error) {
        alert('Error loading proposal: ' + error.message);
    }
}

// Save edited proposal
async function saveProposalChanges() {
    try {
        const proposalId = document.getElementById('editProposalId').value;
        const formData = {
            proposalId: proposalId,
            customerName: document.getElementById("proposalCustomerName").value.trim(),
            customerAddress: document.getElementById("proposalCustomerAddress").value.trim(),
            contactNumber: document.getElementById("proposalContactNumber").value.trim(),
            validityDate: document.getElementById("validityDate").value,
            terms: document.getElementById("terms").value.trim(),
            items: Array.from(document.querySelectorAll("#proposalItemsList tr")).map(row => ({
                product_id: row.querySelector(".product-select").value,
                quantity: parseFloat(row.querySelector(".quantity-input").value),
                unit_price: parseFloat(row.querySelector(".price-input").value),
                amount: parseFloat(row.querySelector(".amount-input").value)
            }))
        };

        const response = await fetch("update_proposal.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(formData),
        });

        const result = await response.json();

        if (result.success) {
            alert("Proposal updated successfully!");
            closeCreateProposalModal();
            location.reload();
        } else {
            throw new Error(result.message || 'Unknown error occurred');
        }
    } catch (error) {
        alert("Error updating proposal: " + error.message);
    }
}

// Download proposal function
function downloadProposal(proposalId) {
    window.location.href = `download_proposal.php?id=${proposalId}`;
}