<?php
session_start();

// Session Timeout (e.g., 20 minutes)
$timeout_duration = 1200; // 1200 seconds = 20 minutes
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header('Location: Login.php?timeout=1');
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

// Role-Based Access Control for User
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'user') {
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

// Handle sensitive POST actions securely (example for server-side forms)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Invalid CSRF token.');
    }
    // Add your POST handling logic here (e.g., add product, update order, etc.)
    // Use prepared statements for DB operations
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Interface</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/user-style.css">
    <link rel="stylesheet" href="CSS/admin-style.css">
    <link rel="stylesheet" href="CSS/product-search.css" />
    <script src="assets/user/js/user_function.js"></script>
    <script src="assets/user/js/order_management.js"></script>
    <link rel="website icon" type="png" href="image/Logo.png" />
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="image-align">
            <img class="sysimage" src="images/system logo 3.png" alt="System Logo">
        </div>
        <div class="font">User Panel</div>
        <ul>
            <li><a href="#" onclick="showSection('dashboard')">Dashboard</a></li>
            <li><a href="#" onclick="showSection('inventory-viewing')">Inventory</a></li>
            <li><a href="#" onclick="showSection('order-processing')">Order Process</a></li>
            <li><a href="#" onclick="showSection('invoice-generation')">Invoice History</a></li>
            <li><a href="#" onclick="showSection('barcode-scanning')">Barcode Scanner</a></li>
            <li><a href="#" onclick="showSection('report-generation')">Report Generation</a></li>
            <li><a href="#" onclick="showSection('product-search')">Product Search and Filter</a></li>
            <li>
                <button class="logoutSB" onclick="confirmLogout()">Log Out</button>
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


    <!-- Main Content Area -->
    <div class="main-content">
        <div id="dashboard" class="content-section">

            <!-- Main Dashboard Section -->
            <div class="dashboard-container">
                <h1>Dashboard</h1>

                <!-- Overview Cards Section -->
                <div class="overview-section">
                    <div class="card">
                        <h3>Current Stock Levels</h3>
                        <p><?php
                            include 'dbconnection.php';
                            $query = "SELECT COUNT(*) as total FROM inventory";
                            $result = $conn->query($query);
                            $row = $result->fetch_assoc();
                            echo $row['total'];
                            ?></p>
                    </div>
                    <div class="card">
                        <h3>Complete Orders</h3>
                        <p><?php
                            include 'dbconnection.php';
                            $query = "SELECT COUNT(*) as total FROM orders WHERE status IN ('Completed', 'Modified')";
                            $result = $conn->query($query);
                            $row = $result->fetch_assoc();
                            echo $row['total'];
                            ?></p>
                    </div>
                    <div class="card">
                        <h3>Low Stock Alerts</h3>
                        <p><?php
                            include 'dbconnection.php';
                            $query = "SELECT COUNT(*) as total FROM inventory WHERE quantity <= 7";
                            $result = $conn->query($query);
                            $row = $result->fetch_assoc();
                            echo $row['total'];
                            ?></p>
                    </div>
                </div>

                <!-- Recent Activities Section -->
                <div class="recent-activities">
                    <h2>Recent Activities</h2>
                    <ul class="activity-list">
                        <?php
                        // Show the 5 most recent orders as recent activities
                        $recentOrdersQuery = "SELECT order_id, customer_name, order_date, status FROM orders ORDER BY order_date DESC LIMIT 5";
                        $result = $conn->query($recentOrdersQuery);
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<li>Order <strong>#' . htmlspecialchars($row['order_id']) . '</strong> for ' . htmlspecialchars($row['customer_name']) . ' on ' . date('M d, Y', strtotime($row['order_date'])) . ' <span class=\'activity-status\'>(' . htmlspecialchars($row['status']) . ')</span></li>';
                            }
                        } else {
                            echo '<li>No recent orders found.</li>';
                        }
                        ?>
                    </ul>
                </div>

                <!-- Quick Actions Section -->
                <div class="quick-actions">
                    <h2>Quick Actions</h2>
                    <button class="action-btn" onclick="showSection('inventory-viewing')">Add New Product</button>
                    <button class="action-btn" onclick="showSection('order-processing')">Process Order</button>
                    <button class="action-btn" onclick="showSection('barcode-scanning')">Barcode Scanner</button>
                    <button class="action-btn" onclick="showSection('report-generation')">Generate Report</button><br>
                    <button href="javascript:void(0)" style="background-color: #ff4444;" onmouseover="this.style.backgroundColor='#cc0000'" onmouseout="this.style.backgroundColor='#ff4444'" onclick="confirmLogout()">Logout</button>
                </div>
            </div>
        </div>


        <div id="inventory-viewing" class="content-section">
            <h1>Inventory</h1>

            <div class="inventory-management">
                <div class="top-bar">
                    <input
                        type="text"
                        placeholder="Search Products..."
                        class="search-bar"
                        id="inventorySearchBar"
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
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Add Product Modal -->
            <div id="addProductModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeAddProductPopup()">&times;</span>
                    <h2>Add New Product</h2>
                    <form id="addProductForm" onsubmit="submitProduct(event)" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <div class="form-group">
                            <label for="productName">Product Name:</label>
                            <input type="text" id="productName" name="productName" required>
                        </div>
                        <div class="form-group">
                            <label for="quantity">Quantity:</label>
                            <input type="number" id="quantity" name="quantity" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="price">Price:</label>
                            <input type="number" id="price" name="price" min="0" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="category">Category:</label>
                            <select id="category" name="category" class="input-groupbox" required>
                                <option value="Laptop">Laptop</option>
                                <option value="Desktop">Desktop</option>
                                <option value="Peripheral Device">Peripheral Device</option>
                            </select>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="submit-btn">Add Product</button>
                            <button type="button" onclick="closeAddProductPopup()" class="cancel-btnbox">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- /////////////////////////////////////////////////// Order section ////////////////////////////////////////////// -->
        <div id="order-processing" class="content-section">
            <h1>Order Process</h1>

            <!-- Tab Navigation -->
            <div class="order-tabs">
                <button class="tab-btn active" onclick="showOrderTab('orders')">Orders</button>
                <button class="tab-btn" onclick="showOrderTab('proposals')">Proposals</button>
            </div>

            <!-- Orders Tab Content -->
            <div id="orders-tab" class="tab-content active">
                <div class="order-management">
                    <div class="top-bar">
                        <input type="text" placeholder="Search Orders..." class="search-bar" id="orderSearchBar" onkeyup="filterOrderTable()" />
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
                        <tbody id="order-list"> <?php try {
                                                    require_once 'dbconnection.php';
                                                    require_once 'order_functions.php';

                                                    // Ensure the connection is open
                                                    if (!isset($conn) || $conn->connect_error) {
                                                        throw new Exception("Database connection failed");
                                                    }

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
                                                            echo "</td>";
                                                            echo "</tr>";
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='6'>No orders found</td></tr>";
                                                    }
                                                } catch (Exception $e) {
                                                    error_log("Error in user.php order list: " . $e->getMessage());
                                                    echo "<tr><td colspan='6'>Unable to load orders. Please try again later.</td></tr>";
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
                        <input type="text" placeholder="Search Proposals..." class="search-bar" id="proposalSearchBar" onkeyup="filterProposalTable()" />
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
                </div>
            </div>

            <!-- View Order Modal -->
            <script src="assets/user/js/order_management.js"></script>
            <script>
                document.getElementById('proposal-form').addEventListener('submit', createProposal);
            </script>
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



        <div id="invoice-generation" class="content-section">
            <h1>Invoice History</h1>
            <!-- Main Invoice Generation Section -->
            <div class="invoice-management">

                <div class="top-bar">
                    <input type="text" placeholder="Search Invoices..." class="search-bar" id="invoiceSearchBar" onkeyup="filterInvoiceTable()" />
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



        <div class="main-content1">
            <div id="barcode-scanning" class="content-section">
                <h1 class="h1box">Barcode Scanner</h1>
                <!-- Main Barcode Scanning Section -->
                <div class="barcode-container">

                    <!-- Barcode Input Field -->
                    <div class="barcode-input-section">
                        <label for="barcode">Scan or Enter Barcode</label>
                        <input type="text" id="barcode" placeholder="Scan or type barcode" class="barcode-input">
                        <button class="scan-btn" onclick="fetchProduct()">Search Product</button>
                    </div>

                    <!-- Product Details Display -->
                    <div class="product-details" id="product-details" style="display: none;">
                        <h2>Product Details</h2>
                        <p><strong>Product Name:</strong> <span id="product-name">Laptop</span></p>
                        <p><strong>SKU:</strong> <span id="product-sku">SKU12345</span></p>
                        <p><strong>Category:</strong> <span id="product-category">Electronics</span></p>
                        <p><strong>Price:</strong> <span id="product-price">$1200.00</span></p>
                        <p><strong>Stock Level:</strong> <span id="product-stock">30</span></p>

                        <!-- Stock Management -->
                        <div class="stock-management">
                            <button class="add-stock-btn" onclick="updateStock('add')">Add Stock</button>
                            <button class="remove-stock-btn" onclick="updateStock('remove')">Remove Stock</button>
                        </div>
                    </div>

                    <!-- Alerts/Notifications -->
                    <div class="alerts" id="alerts" style="display: none;">
                        <p id="alert-message">Invalid Barcode</p>
                    </div>
                </div>
            </div>
        </div>



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


        <div id="product-search" class="content-section">
            <h1>Product Search and Filtering</h1>
            <div class="product-search">
                <div class="search-filter-bar">
                    <input
                        type="text"
                        placeholder="Search by Product Name"
                        class="product-search-bar" />
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
    <script src="assets/user/js/product_search.js"></script>
    </div>

    <!-- Include the report management JavaScript file -->
    <script src="assets/user/js/report_management.js"></script>


    <script src="assets/user/js/order_management.js"></script>
    <script src="assets/user/js/invoice_management.js"></script>
    <script>
        // Function to show selected section
        function showSection(sectionId) {
            const sections = document.querySelectorAll('.content-section');
            sections.forEach(section => section.style.display = 'none');
            document.getElementById(sectionId).style.display = 'block';
        }

        // Initially display dashboard
        showSection('dashboard');

        // Add event listener for proposal form
        document.getElementById('proposal-form').addEventListener('submit', createProposal);
    </script>

</body>

</html>