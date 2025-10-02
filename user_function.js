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

/////////////////////////////////////////////////// Inventory section //////////////////////////////////////////////

//search-bar
function filterInventoryTable() {
  const input = document.getElementById("inventorySearchBar");
  const filter = input.value.toLowerCase();
  const table = document.querySelector(".inventory-table");
  const trs = table.getElementsByTagName("tr");
  // Skip the header row (i=1)
  for (let i = 1; i < trs.length; i++) {
    let row = trs[i];
    let text = row.textContent.toLowerCase();
    row.style.display = text.includes(filter) ? "" : "none";
  }
}

// Function to open Add Product popup
function openAddProductPopup() {
  document.getElementById("addProductModal").style.display = "block";
}

// Function to close Add Product popup
function closeAddProductPopup() {
  document.getElementById("addProductModal").style.display = "none";
}

// Function to submit new product
async function submitProduct(event) {
  event.preventDefault();

  const productName = document.getElementById("productName").value.trim();
  const quantity = document.getElementById("quantity").value.trim();
  const price = document.getElementById("price").value.trim();
  const category = document.getElementById("category").value.trim();

  // Validate inputs
  if (
    productName === "" ||
    quantity === "" ||
    price === "" ||
    category === ""
  ) {
    alert("Please fill out all fields before adding the product.");
    return;
  }

  try {
    const response = await fetch("user_add_product.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        productName,
        quantity,
        price,
        category,
      }),
    });

    const data = await response.json();

    if (data.success) {
      alert(data.message);
      closeAddProductPopup();
      location.reload(); // Reload to see the new product
    } else {
      alert(data.message);
    }
  } catch (error) {
    console.error("Error:", error);
    alert("Error adding product. Please try again.");
  }
}
