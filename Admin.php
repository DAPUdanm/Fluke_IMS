<?php
session_start();

// Session Timeout (e.g., 40 minutes)
$timeout_duration = 2400; // 2400 seconds = 40 minutes
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
  session_unset();
  session_destroy();
  header('Location: Login.php?timeout=1');
  exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

// Role-Based Access Control
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
  header('Location: Login.php?unauthorized=1');
  exit();
}

// CSRF Protection: Generate token if not set
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Logout Functionality
if (isset($_GET['logout'])) {
  session_unset();
  session_destroy();
  header('Location: Login.php?logout=1');
  exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');
include('dbconnection.php');
include('Admin_function.php');

$users = getUsers();

// Handle User Management POST actions securely
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // CSRF Protection
  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Invalid CSRF token.');
  }
  // Example: Handle update and delete actions (add your logic here)
  if (isset($_POST['update'])) {
    // Update user role logic here
    // Use prepared statements for DB updates
  }
  if (isset($_POST['delete'])) {
    // Delete user logic here
    // Use prepared statements for DB deletes
  }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="CSS/admin-style.css" />
  <!-- <link rel="stylesheet" href="CSS/proposal.css" /> -->
  <link rel="stylesheet" href="CSS/night-mode.css" />
  <link rel="stylesheet" href="CSS/order-modal.css" />
  <link rel="stylesheet" href="CSS/order-modal.css" />
  <link rel="stylesheet" href="CSS/product-search.css" />
  <!-- Add Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="assets/admin/js/admin_function.js"></script>
  <script src="assets/admin/js/order_management.js"></script>
  <script src="assets/admin/js/proposal_management.js"></script>
  <script src="assets/admin/js/invoice_management.js"></script>
  <script src="assets/admin/js/sales_trends.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <link rel="icon" href="image/Logo.png">
  <title>Admin Dashboard</title>
</head>

<body>

  <div class="sidebar">
    <div class="image-align">
      <img
        class="sysimage"
        src="images/system logo 3.png"
        alt="System Logo" />
    </div>

    <div class="font">Admin Panel</div>

    <ul>
      <li><a href="#" id="123" onclick="showSection('dashboard')">Dashboard</a></li>
      <li><a href="#" onclick="showSection('user-management')">User Management</a></li>
      <li><a href="#" onclick="showSection('inventory-management')">Inventory Management</a></li>
      <li><a href="#" onclick="showSection('supplier-management')">Supplier Management</a></li>
      <li><a href="#" onclick="showSection('order-management')"> Order Management</a></li>
      <li><a href="#" onclick="showSection('invoice-management')">Invoice History</a></li>
      <li><a href="#" onclick="showSection('report-generation')"> Report Generation</a></li>
      <li><a href="#" onclick="showSection('product-search')">Product Search and Filtering</a></li>
      <li>
        <div class="logoutSB"><a href="javascript:void(0)" onclick="confirmLogout()">Log Out</a></div>
        <!-- Logout Modal -->
        <div id="logoutModal" class="modal">
          <div class="logoutmodal-content1">
            <span class="close-btn" onclick="closeLogoutModal()">&times;</span>
            <div class="h3box1">Logout Confirmation</div>
            <div class="pbox">Are you sure you want to logout?</div>
            <button type="button" class="delete-btnbox" onclick="confirmLogoutAction()">Yes, Logout</button>
            <button type="button" class="reverse-btnbox" onclick="closeLogoutModal()">No, Cancel</button>
          </div>
        </div>
      </li>
    </ul>
  </div>
  <!-- /////////////////////////////////////////////// dashboard section /////////////////////////////////////////////// -->
  <div class="main-content">
    <div id="dashboard" class="content-section">
      <div class="h1">Dashboard</div>
      <div class="dashboard-container">
        <div class="dashboard-content">
          <div class="overview-section">
            <div class="card">
              <h3>Total Products</h3>
              <p><?php
                  include 'dbconnection.php';
                  $query = "SELECT COUNT(*) as total FROM inventory";
                  $result = $conn->query($query);
                  $row = $result->fetch_assoc();
                  echo $row['total'];
                  ?></p>
            </div>
            <div class="card">
              <h3>Stocks are to run out</h3>
              <p><?php
                  include 'dbconnection.php';
                  $query = "SELECT COUNT(*) as total FROM inventory WHERE quantity <= 7";
                  $result = $conn->query($query);
                  $row = $result->fetch_assoc();
                  echo $row['total'];
                  ?></p>
            </div>
            <div class="card">
              <h3>Total Complete Orders</h3>
              <p><?php
                  include 'dbconnection.php';
                  $query = "SELECT COUNT(*) as total FROM orders WHERE status IN ('Completed', 'Modified')";
                  $result = $conn->query($query);
                  $row = $result->fetch_assoc();
                  echo $row['total'];
                  ?></p>
            </div>
            <div class="card">
              <h3>Pending Orders</h3>
              <p><?php
                  include 'dbconnection.php';
                  $query = "SELECT COUNT(*) as total FROM orders WHERE status = 'Pending'";
                  $result = $conn->query($query);
                  $row = $result->fetch_assoc();
                  echo $row['total'];
                  ?></p>
            </div>
          </div>

          <div class="middle-section">
            <div class="sales-reports">
              <h2>Sales Reports</h2>
              <div class="sales-stat">
                <?php
                require_once 'dbconnection.php';

                try {
                  // Get today's total sales (from midnight to current time)
                  $query = "SELECT COALESCE(SUM(total_amount), 0) as total_sales 
                      FROM sales 
                      WHERE DATE(sale_date) = CURDATE() 
                      AND status = 'completed'";

                  $result = $conn->query($query);
                  if (!$result) {
                    throw new Exception($conn->error);
                  }

                  $row = $result->fetch_assoc();
                  $todaysSales = $row['total_sales'];

                  // Format and display today's sales
                  echo "<div class='daily-sales'>";
                  echo "<h3>Today's Sales</h3>";
                  echo "<p class='sales-trend-item'>Rs. " . number_format($todaysSales, 2) . "</p>";

                  // Get current time
                  date_default_timezone_set('Asia/Colombo');
                  $currentTime = date('h:i A');
                  echo "<p class='sales-time'>As of $currentTime</p>";
                  echo "</div>";
                } catch (Exception $e) {
                  echo "<p class='error'>Error loading sales data: " . htmlspecialchars($e->getMessage()) . "</p>";
                }
                ?>
              </div>
            </div>
          </div>

          <div class="bottom-section">
            <div class="chart">
              <h2>Sales Trends</h2>
              <canvas id="salesChart"></canvas>
            </div>
            <div class="quick-actions">
              <h2>Quick Actions</h2>
              <button onclick="showSection('inventory-management')">Add New Product</button>
              <button onclick="showSection('order-management')">Create Order</button>
              <button onclick="showSection('report-generation')">Generate Report</button>

              <!-- Logout Modal Button-->
              <a href="javascript:void(0)" class="logoutB" onclick="confirmLogout()">Login Out</a>

              <!-- Night Mode Toggle -->
              <div class="night-mode-toggle">
                <button id="nightModeBtn" onclick="toggleNightMode()">
                  <i class="fas fa-moon"></i> Night Mode
                </button>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- /////////////////////////////////////////////// user management section /////////////////////////////////////////////// -->
    <div id="user-management" class="content-section">
      <h1>User Management</h1>

      <div class="user-management">
        <div class="top-bar">
          <input type="text" placeholder="Search Users..." class="search-bar" id="userSearchInput" onkeyup="filterUserTable()">

          <!-- <button class="add-user-btn" onclick="openAddUserModal()">
              Add New User
            </button> -->
        </div>

        <!-- User List -->
        <table class="user-table">
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
          </tr>

          <?php while ($row = mysqli_fetch_assoc($users)) { ?>
            <tr>
              <td><?php echo $row['id']; ?></td>
              <td><?php echo $row['username']; ?></td>
              <td><?php echo $row['email']; ?></td>
              <td><?php echo $row['role']; ?></td>
              <td>
                <!-- Add CSRF token to forms -->
                <form action="Admin.php#user-management" method="POST" style="display:inline;">
                  <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                  <select name="new_role" class="role-select">
                    <option value="User" <?php if ($row['role'] == 'User') echo 'selected'; ?>>User</option>
                    <option value="Admin" <?php if ($row['role'] == 'Admin') echo 'selected'; ?>>Admin</option>
                  </select>
                  <button class="edit-btn" type="submit" name="update">Update</button>
                </form>
                <form action="Admin.php#user-management" method="POST" style="display:inline;">
                  <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                  <button class="delete-btn" type="submit" name="delete">Delete</button>
                </form>
              </td>
            </tr>
          <?php
          } ?>

        </table>
      </div>
    </div>





    <!-- ///////////////////////////////////////////////////////Inventory Management/////////////////////////////////////////////////-->

    <div id="inventory-management" class="content-section">
      <h1>Inventory Management</h1>

      <div class="inventory-management">
        <div class="top-bar">
          <input
            type="text"
            placeholder="Search Products..."
            class="search-bar"
            id="inventorySearchInput"
            onkeyup="filterInventoryTable()" />
          <button class="add-product-btn" onclick="openAddProductPopup()">
            Add New Product
          </button>
        </div>
        <?php
        $sql = "SELECT * FROM inventory";
        $result = $conn->query($sql);
        ?>
        <table class="inventory-table">
          <thead>
            <tr>
              <th>Product ID</th>
              <th>Product Name</th>
              <th>Quantity</th>
              <th>Price</th>
              <th>Category</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="inventory-list">
            <?php
            // Connect to the database and fetch the inventory records
            include 'dbconnection.php';
            $query = "SELECT * FROM inventory";
            $result = $conn->query($query);

            while ($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $row['id'] . "</td>";
              echo "<td>" . $row['product_name'] . "</td>";
              echo "<td>" . $row['quantity'] . "</td>";
              echo "<td>" . $row['price'] . "</td>";
              echo "<td>" . $row['category'] . "</td>";
              echo "<td>";
              echo "<button onclick=\"openEditModal(" . $row['id'] . ", '" . addslashes($row['product_name']) . "', " . $row['quantity'] . ", " . $row['price'] . ", '" . addslashes($row['category']) . "')\" class=\"edit-btn\">Edit</button>";
              echo "<button onclick=\"openDeleteModal(" . $row['id'] . ", '" . addslashes($row['product_name']) . "')\" class=\"delete-btn\">Delete</button>";

              "</td>";
              echo "</tr>";
            }
            $conn->close();
            ?>
          </tbody>
        </table>
      </div>

      <!--///////////////////////////// Add New Product /////////////////////////////-->
      <div id="add-product-popup" class="modal">
        <div class="modal-content">
          <span class="close-btn" onclick="closeAddProductPopup()">&times;</span>
          <div class="h3box">Add New Product</div>
          <div class="input-group">
            <label>Product Name:</label>
            <input type="text" id="productName" required />
          </div>
          <div class="input-group">
            <label>Quantity:</label>
            <input type="number" id="quantity" required />
          </div>
          <div class="input-group">
            <label>Price:</label>
            <input type="number" id="price" required />
          </div>
          <div class="input-group">
            <label>Category:</label>
            <select id="category" class="input-groupbox">
              <option value="Laptop">Laptop</option>
              <option value="Desktop">Desktop</option>
              <option value="Peripheral Device">Peripheral Device</option>
            </select>
          </div>
          <button onclick="addProduct()" id="addproduct" class="submit-btnbox">Add Product</button>
          <button onclick="closeAddProductPopup()" class="cancel-btnbox">Cancel</button>
        </div>
      </div>

      <!--///////////////////////////// Edit Product /////////////////////////////-->
      <div id="editModal" class="modal">
        <div class="modal-content">
          <span class="close-btn" onclick="closeEditProductPopup()">&times;</span>
          <div class="h3box">Edit Product</div>
          <form id="editForm">
            <!-- <div class="input-group">
            <label>Product ID:</label>
            <input type="hidden" id="editProductId" />
          </div> -->
            <div class="input-group">
              <label>Product Name:</label>
              <input type="text" id="editProductName" />
            </div>
            <div class="input-group">
              <label>Quantity:</label>
              <input type="number" id="editQuantity" />
            </div>
            <div class="input-group">
              <label>Price:</label>
              <input type="number" id="editPrice" />
            </div>
            <div class="input-group">
              <label>Category:</label>
              <select id="editCategory">
                <option value="laptop">Laptop</option>
                <option value="desktop">Desktop</option>
                <option value="Peripheral Device">Peripheral Device</option>
              </select>
            </div>
            <input type="hidden" id="editProductId">
            <button type="button" onclick="submitEditForm()" class="submit-btnbox">Save Changes</button>
            <button type="button" onclick="closeEditProductPopup()" class="cancel-btnbox">Cancel</button>
            <!-- <button onclick="saveChanges()" class="submit-btnbox">Save Changes</button>
          <button onclick="closeAddProductPopup()" class="cancel-btnbox">Cancel</button> -->
          </form>
        </div>
      </div>



      <!--///////////////////////////// delete Product /////////////////////////////-->
      <div id="deleteModal" class="modal">
        <div class="modal-content">
          <span class="close-btn" onclick="closeproductDeleteModal()">&times;</span>
          <div class="h3box">Delete Product</div>
          <div class="delete-para">
            <div>Are you sure you want to delete this product?</div>
            <div id="deleteProductName"></div>
            <div class="input-group">
              <input type="number" id="deleteProductId">
            </div>
            <button type="button" onclick="confirmproductDelete()" class="delete-fuction-cancel-btn">Confirm Delete</button>
            <button type="button" onclick="closeproductDeleteModal()" class="cancel-btnbox">Cancel</button>
          </div>
        </div>
      </div>
    </div>








    <!-- ///////////////////////////////////////////////////////Supplier Management/////////////////////////////////////////////////-->



    <div id="supplier-management" class="content-section">
      <h1>Supplier Management</h1>
      <div class="supplier-management">
        <div class="top-bar">
          <input type="text" placeholder="Search Suppliers..." class="search-bar" id="supplierSearchInput" onkeyup="filterSupplierTable()" />
          <button class="add-product-btn" onclick="openAddSupplierPopup()">Add New Supplier</button>
        </div>
        <table class="supplier-table">
          <thead>
            <tr>
              <th>Supplier ID</th>
              <th>Supplier Name</th>
              <th>Contact</th>
              <th>Email</th>
              <th>Address</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="supplier-list">
            <?php
            include 'supplier_functions.php';
            $result = getSuppliers();

            while ($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $row['id'] . "</td>";
              echo "<td>" . $row['supplier_name'] . "</td>";
              echo "<td>" . $row['contact'] . "</td>";
              echo "<td>" . $row['email'] . "</td>";
              echo "<td>" . $row['address'] . "</td>";
              echo "<td>";
              echo "<button onclick=\"openEditSupplierModal(" . $row['id'] . ", '" . addslashes($row['supplier_name']) . "', '" . addslashes($row['contact']) . "', '" . addslashes($row['email']) . "', '" . addslashes($row['address']) . "')\" class=\"edit-btn\">Edit</button>";
              echo "<button onclick=\"openDeleteSupplierModal(" . $row['id'] . ", '" . addslashes($row['supplier_name']) . "')\" class=\"delete-btn\">Delete</button>";
              echo "</td>";
              echo "</tr>";
            }
            ?>
          </tbody>
        </table>
      </div>

      <!--///////////////////////////// Add New Supplier /////////////////////////////-->
      <div id="add-supplier-popup" class="modal">
        <div class="modal-content">
          <span class="close-btn" onclick="closeAddSupplierPopup()">&times;</span>
          <div class="h3box">Add New Supplier</div>
          <div class="input-group">
            <label>Supplier Name:</label>
            <input type="text" id="supplierName" required />
          </div>
          <div class="input-group">
            <label>Contact:</label>
            <input type="text" id="contact" required />
          </div>
          <div class="input-group">
            <label>Email:</label>
            <input type="email" id="email" required />
          </div>
          <div class="input-group">
            <label>Address:</label>
            <input id="address" required />
            <!-- <textarea id="address" required></textarea> -->
          </div>
          <button onclick="addSupplier()" class="submit-btnbox">Add Supplier</button>
          <button onclick="closeAddSupplierPopup()" class="cancel-btnbox">Cancel</button>
        </div>
      </div>

      <!--///////////////////////////// Edit Supplier /////////////////////////////-->

      <div id="editSupplierModal" class="modal">
        <div class="modal-content">
          <span class="close-btn" onclick="closeEditSupplierPopup()">&times;</span>
          <div class="h3box">Edit Supplier</div>
          <input type="hidden" id="editSupplierId">
          <div class="input-group">
            <label>Supplier Name:</label>
            <input type="text" id="editSupplierName" required />
          </div>
          <div class="input-group">
            <label>Contact:</label>
            <input type="text" id="editContact" required />
          </div>
          <div class="input-group">
            <label>Email:</label>
            <input type="email" id="editEmail" required />
          </div>
          <div class="input-group">
            <label>Address:</label>
            <input id="editAddress" required />
            <!-- <textarea id="editAddress" required></textarea> -->
          </div>
          <button onclick="updateSupplier()" class="submit-btnbox">Save Changes</button>
          <button onclick="closeEditSupplierPopup()" class="cancel-btnbox">Cancel</button>
        </div>
      </div>

      <!--///////////////////////////// Delete Supplier /////////////////////////////-->

      <div id="deleteSupplierModal" class="modal">
        <div class="modal-content">
          <span class="close-btn" onclick="closeDeleteSupplierPopup()">&times;</span>
          <div class="h3box">Delete Supplier</div>
          <div class="delete-para">
            <div>Are you sure you want to delete this supplier?</div>
            <div id="deleteSupplierName"></div>
            <input type="hidden" id="deleteSupplierId">
            <button onclick="deleteSupplier()" class="delete-fuction-cancel-btn">Confirm Delete</button>
            <button onclick="closeDeleteSupplierPopup()" class="cancel-btnbox">Cancel</button>
          </div>
        </div>
      </div>
    </div>





    <!-- //////////////////////////////////////////////////// order-management ///////////////////////////////////////////////-->
    <div id="order-management" class="content-section">
      <h1>Order Management</h1>

      <!-- Tab Navigation -->
      <div class="order-tabs">
        <button class="tab-btn active" onclick="showOrderTab('orders')">Orders</button>
        <button class="tab-btn" onclick="showOrderTab('proposals')">Proposals</button>
      </div>

      <!-- Orders Tab Content -->
      <div id="orders-tab" class="tab-content active">
        <div class="order-management">
          <div class="top-bar">
            <input type="text" placeholder="Search Orders..." class="search-bar" id="orderSearchInput" onkeyup="filterOrderTable()" />
            <button class="create-order-btn" onclick="openCreateOrderModal()">Create New Order</button>
          </div>

          <table class="order-table">
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Date</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="order-list"> <?php
                                    try {
                                      require_once 'order_functions.php';
                                      $orders = getOrders();

                                      if ($orders && $orders->num_rows > 0) {
                                        while ($order = $orders->fetch_assoc()) {
                                          echo "<tr>";
                                          echo "<td>" . htmlspecialchars($order['order_id']) . "</td>";
                                          echo "<td>" . htmlspecialchars($order['customer_name']) . "</td>";
                                          echo "<td>" . htmlspecialchars($order['order_date']) . "</td>";
                                          echo "<td>Rs. " . number_format($order['total_amount'], 2) . "</td>";
                                          echo "<td class='ord-status-" . strtolower($order['status']) . "'>" . ucfirst(htmlspecialchars($order['status'])) . "</td>";
                                          echo "<td>";
                                          echo "<button onclick=\"viewOrder('" . htmlspecialchars($order['order_id']) . "')\" class=\"view-btn\">View</button>";
                                          if ($order['status'] !== 'canceled') {
                                            echo "<button onclick=\"editOrder('" . htmlspecialchars($order['order_id']) . "')\" class=\"edit-btn\">Edit</button>";
                                            echo "<button onclick=\"cancelOrder('" . htmlspecialchars($order['order_id']) . "')\" class=\"ord-cancel-btn\">Cancel</button>";
                                          }
                                          echo "</td>";
                                          echo "</tr>";
                                        }
                                      } else {
                                        echo "<tr><td colspan='6'>No orders found</td></tr>";
                                      }
                                    } catch (Exception $e) {
                                      echo "<tr><td colspan='6'>Error loading orders: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                                    }
                                    ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Proposals Tab Content -->
      <div id="proposals-tab" class="tab-content">
        <div class="order-management">
          <div class="top-bar">
            <input type="text" placeholder="Search Proposals..." class="search-bar" id="proposalSearchInput" onkeyup="filterProposalTable()" />
            <button class="create-order-btn" onclick="openCreateProposalModal()">Create New Proposal</button>
          </div>

          <table class="proposal-table">
            <thead>
              <tr>
                <th>Proposal ID</th>
                <th>Customer Name</th>
                <th>Date Created</th>
                <th>Valid Until</th>
                <th>Total Amount</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="proposal-list">
              <?php
              include 'proposal_functions.php';
              $proposals = getProposals();
              while ($proposal = $proposals->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $proposal['proposal_id'] . "</td>";
                echo "<td>" . $proposal['customer_name'] . "</td>";
                echo "<td>" . $proposal['date_created'] . "</td>";
                echo "<td>" . $proposal['validity_date'] . "</td>";
                echo "<td>Rs. " . number_format($proposal['total_amount'], 2) . "</td>";
                echo "<td>";
                echo "<button onclick=\"viewProposal('" . $proposal['proposal_id'] . "')\" class=\"view-btn\">View</button>";
                echo "<button onclick=\"editProposal('" . $proposal['proposal_id'] . "')\" class=\"edit-btn\">Edit</button>";
                echo "<button onclick=\"downloadProposal('" . $proposal['proposal_id'] . "')\" class=\"download-btn\">Download</button>";
                echo "</td>";
                echo "</tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Create Order Modal -->
      <div id="create-order-modal" class="modal">
        <div class="modal-content large-modal">
          <span class="close-btn" onclick="closeCreateOrderModal()">&times;</span>
          <div class="h3box">Create New Order</div>
          <form id="order-form">
            <div class="form-section">
              <div class="input-group">
                <label>Customer Name:</label>
                <input type="text" id="customerName" required />
              </div>
              <div class="input-group">
                <label>Contact Number:</label>
                <input type="text" id="contactNumber" required />
              </div>
              <div class="input-group">
                <label>Address:</label>
                <input type=" text" id="customerAddress" required />
                <!-- <textarea id="customerAddress" required></textarea> -->
              </div>
              <div class="input-group">
                <label>Payment Method:</label>
                <select id="paymentMethod" required>
                  <option value="Cash">Cash</option>
                  <option value="Card">Card</option>
                  <option value="Bank Transfer">Bank Transfer</option>
                </select>
              </div>
            </div>

            <div class="form-section">
              <h4>Order Items</h4>
              <div id="order-items">
                <table class="items-table">
                  <thead>
                    <tr>
                      <th>Product</th>
                      <th>Quantity</th>
                      <th>Unit Price</th>
                      <th>Amount</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="orderItemsList">
                    <!-- Items will be added here dynamically -->
                  </tbody>
                </table>
                <button type="button" onclick="addOrderItem()" class="add-item-btn">Add Item</button>
              </div>
            </div>

            <div class="form-section">
              <div class="total-amount">
                <h4>Total Amount: Rs. <span id="orderTotal">0.00</span></h4>
              </div>
            </div>

            <div class="form-actions">
              <button type="button" class="submit-btnbox" onclick="createOrder()">Create Order</button>
              <button type="button" onclick="closeCreateOrderModal()" class="cancel-btnbox">Cancel</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Create Proposal Modal -->
      <div id="create-proposal-modal" class="modal">
        <div class="modal-content large-modal">
          <span class="close-btn" onclick="closeCreateProposalModal()">&times;</span>
          <div class="h3box">Create New Proposal</div>
          <form id="proposal-form">
            <div class="form-section">
              <div class="input-group">
                <label>Customer Name:</label>
                <input type="text" id="proposalCustomerName" required />
              </div>
              <div class="input-group">
                <label>Contact Number:</label>
                <input type="text" id="proposalContactNumber" required />
              </div>
              <div class="input-group">
                <label>Address:</label>
                <input type="text" id="proposalCustomerAddress" required />
                <!-- <textarea id="proposalCustomerAddress" required></textarea> -->
              </div>
              <div class="input-group">
                <label>Validity Date:</label>
                <input type="date" id="validityDate" required />
              </div>
            </div>

            <div class="form-section">
              <h4>Proposal Items</h4>
              <div id="proposal-items">
                <table class="items-table">
                  <thead>
                    <tr>
                      <th>Product</th>
                      <th>Quantity</th>
                      <th>Unit Price</th>
                      <th>Amount</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="proposalItemsList">
                    <!-- Items will be added here dynamically -->
                  </tbody>
                </table>
                <button type="button" onclick="addProposalItem()" class="add-item-btn">Add Item</button>
              </div>
            </div>

            <div class="form-section">
              <div class="input-group">
                <label>Terms and Conditions:</label>
                <input type="text" id="terms" required />
                <!-- <textarea id="terms" required></textarea> -->
              </div>
              <div class="total-amount">
                <h4>Total Amount: Rs. <span id="proposalTotal">0.00</span></h4>
              </div>
            </div>
            <div class="form-actions">
              <button type="submit" class="submit-btnbox">Create Proposal</button>
              <button type="button" onclick="closeCreateProposalModal()" class="cancel-btnbox">Cancel</button>
            </div>
          </form>
          <script>
            document.getElementById('proposal-form').addEventListener('submit', createProposal);
          </script>
        </div>
      </div>

      <!-- View Order Modal -->
      <div id="view-order-modal" class="modal">
        <div class="modal-content large-modal">
          <span class="close-btn" onclick="closeViewOrderModal()">&times;</span>
          <div class="h3box">Order Details</div>
          <hr>
          <div id="order-details">
            <!-- Order details will be loaded here -->
          </div>
          <button onclick="closeViewOrderModal()" class="cancel-btnbox">close</button>
        </div>
      </div>

      <!-- View Proposal Modal -->
      <div id="view-proposal-modal" class="modal">
        <div class="modal-content large-modal">
          <span class="close-btn" onclick="closeViewProposalModal()">&times;</span>
          <div class="h3box">Proposal Details</div>
          <hr>
          <div id="proposal-details">
            <!-- Proposal details will be loaded here -->
          </div>
          <hr>
          <button onclick="closeViewProposalModal()" class="cancel-btnbox">Close</button>
        </div>
      </div>
    </div>

    <!-- //////////////////////////////////////////////////// Invoice History ///////////////////////////////////////////////-->
    <div id="invoice-management" class="content-section">
      <h1>Invoice History</h1>
      <div class="invoice-management">
        <div class="top-bar">
          <input type="text" placeholder="Search Invoices..." class="search-bar" id="invoiceSearchInput" onkeyup="filterInvoiceTable()" />
        </div>

        <table class="invoice-table">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Customer Name</th>
              <th>Date</th>
              <th>Total Amount</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="invoice-list"> <?php
                                    require_once 'order_functions.php';
                                    $result = $conn->query("SELECT * FROM invoices ORDER BY created_at DESC");
                                    while ($row = $result->fetch_assoc()) {
                                      echo "<tr>";
                                      echo "<td>" . $row['order_id'] . "</td>";
                                      echo "<td>" . $row['customer_name'] . "</td>";
                                      echo "<td>" . $row['invoice_date'] . "</td>";
                                      echo "<td>Rs. " . number_format($row['total_amount'], 2) . "</td>";
                                      echo "<td class=\"inv-status-" . strtolower($row['invoice_status']) . "\">" . ucfirst($row['invoice_status']) . "</td>";

                                      echo "<td>";
                                      echo "<button onclick=\"viewInvoice('" . $row['invoice_id'] . "')\" class=\"view-btn\">View</button>";
                                      if ($row['invoice_status'] !== 'cancelled') {
                                        echo "<button onclick=\"downloadInvoice('" . $row['invoice_id'] . "')\" class=\"download-btn\">Download</button>";
                                      }
                                      echo "</td>";
                                      echo "</tr>";
                                    }
                                    ?>
          </tbody>
        </table>

        <!-- View Invoice Modal -->
        <div id="view-invoice-modal" class="modal">
          <div class="modal-content large-modal">
            <span class="close-btn" onclick="closeViewInvoiceModal()">&times;</span>
            <div class="h3box">Invoice Details</div>
            <hr>
            <div id="invoice-details">
              <!-- Invoice details will be loaded here -->
            </div>
            <hr>
            <button onclick="closeViewInvoiceModal()" class="cancel-btnbox">Close</button>
          </div>
        </div>
      </div>
    </div>




    <!-- //////////////////////////////////////////////////// Report Generation ///////////////////////////////////////////////-->
    <div id="report-generation" class="content-section">
      <h1>Report Generation</h1>
      <div class="report-generation">
        <div class="h3box">Report Generation</div>

        <div class="report-selection">
          <label for="report-type">Select Report Type:</label>
          <select id="report-type">
            <option value="inventory">Inventory Report</option>
            <option value="sales">Sales Report</option>
            <option value="orders">Order Report</option>
          </select>
        </div>

        <div class="date-range">
          <label for="start-date">Start Date:</label>
          <input type="date" id="start-date" />
          <label for="end-date">End Date:</label>
          <input type="date" id="end-date" />
          <small>(Date range is not applicable for Inventory reports)</small>
        </div>

        <button class="generate-report-btn">Generate Report</button>

        <div class="download-options">
          <h3>Download Options:</h3>
          <div class="download-btns">
            <button class="download-pdf-btn" disabled>Download as PDF</button>
            <button class="download-excel-btn" disabled>Download as Excel</button>
          </div>
          <small>Please generate a report first before downloading</small>
        </div>

        <div id="report-preview">
          <!-- Report preview will be shown here -->
        </div>
      </div>
    </div>

    <!-- Include the report management JavaScript file -->
    <script src="assets/admin/js/report_management.js"></script>





    <!-- //////////////////////////////////////////////////// product-search //////////////////////////////////////////////-->
    <div id="product-search" class="content-section">
      <h1>Product Search and Filtering</h1>
      <div class="product-search">
        <div class="search-filter-bar">
          <input
            type="text"
            placeholder="Search by Product Name"
            class="product-search-bar"
            id="productSearchInput"
            onkeyup="filterProductTable()" />
          <select class="category-filter">
            <option value="">All Categories</option>
            <option value="peripheral">Peripheral Device</option>
            <option value="desktop">Desktop</option>
            <option value="laptop">Laptop</option>
          </select>
          <input type="number" placeholder="Min Price" class="price-filter" />
          <input type="number" placeholder="Max Price" class="price-filter" />
          <button class="filter-btn">Filter</button>
          <button class="clear-filters-btn" value="reset">Clear Filters</button>
        </div>

        <table class="product-table">
          <!-- Table content will be dynamically populated by JavaScript -->
        </table>
      </div>
    </div>
  </div>

  <!-- Include product search JavaScript -->
  <script src="assets/admin/js/product_search.js"></script>
</body>

</html>