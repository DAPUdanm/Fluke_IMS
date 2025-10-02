<?php
if (!function_exists('generateOrderId')) {
    require_once 'dbconnection.php';

    // Check if connection exists
    if (!isset($conn)) {
        die("Database connection not established");
    }

    // Function to generate a unique order ID
    function generateOrderId()
    {
        $date = date('Ymd');
        $random = mt_rand(10000, 99999);
        return "ORD-{$date}-{$random}";
    }

    // Function to generate a unique invoice ID
    function generateInvoiceId()
    {
        $date = date('Ymd');
        $random = mt_rand(10000, 99999);
        return "INVO-{$date}-{$random}";
    }

    // Function to get all orders
    function getOrders()
    {
        global $conn;
        $query = "SELECT * FROM orders ORDER BY created_at DESC";
        return $conn->query($query);
    }

    // Function to get a specific order
    function getOrder($orderId)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
        $stmt->bind_param("s", $orderId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Function to get order items
    function getOrderItems($orderId)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT 
            order_id,
            product_id,
            product_name,
            product_category as category,
            product_description as description,
            quantity,
            unit_price,
            amount,
            unit_price as current_price
        FROM order_items
        WHERE order_id = ?
        ORDER BY product_id");
        $stmt->bind_param("s", $orderId);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Function to create a new order
    function createOrder($customerName, $customerAddress, $contactNumber, $paymentMethod, $items)
    {
        global $conn;

        try {
            $conn->begin_transaction();

            // Check if there's enough stock for all items
            foreach ($items as $item) {
                $stmt = $conn->prepare("SELECT quantity FROM inventory WHERE id = ?");
                $stmt->bind_param("i", $item['product_id']);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();

                if ($result['quantity'] < $item['quantity']) {
                    throw new Exception("Insufficient stock for product ID: " . $item['product_id']);
                }
            }

            $orderId = generateOrderId();
            $orderDate = date('Y-m-d');
            $totalAmount = 0;

            // Calculate total amount
            foreach ($items as $item) {
                $totalAmount += $item['amount'];
            }

            // Insert order
            $stmt = $conn->prepare("INSERT INTO orders (order_id, customer_name, customer_address, contact_number, 
                              order_date, total_amount, payment_method, status) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");

            if (!$stmt->bind_param(
                "sssssds",
                $orderId,
                $customerName,
                $customerAddress,
                $contactNumber,
                $orderDate,
                $totalAmount,
                $paymentMethod
            )) {
                throw new Exception("Failed to bind order parameters: " . $stmt->error);
            }

            if (!$stmt->execute()) {
                throw new Exception("Failed to create order: " . $stmt->error);
            }

            error_log("Debug - Order created with ID: " . $orderId);

            // Insert order items
            foreach ($items as $item) {
                // Get product details from inventory
                $stmt = $conn->prepare("SELECT product_name, category FROM inventory WHERE id = ?");
                $stmt->bind_param("i", $item['product_id']);
                $stmt->execute();
                $product = $stmt->get_result()->fetch_assoc();

                if (!$product) {
                    throw new Exception("Product not found with ID: " . $item['product_id']);
                }

                // Insert order item
                $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_category, quantity, unit_price, amount) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?)");

                error_log("Debug - Inserting order item: orderId={$orderId}, productId={$item['product_id']}
                ");

                if (!$stmt->bind_param(
                    "ssssddd",
                    $orderId,
                    $item['product_id'],
                    $product['product_name'],
                    $product['category'],
                    $item['quantity'],
                    $item['unit_price'],
                    $item['amount']
                )) {
                    throw new Exception("Failed to bind order item parameters: " . $stmt->error);
                }

                if (!$stmt->execute()) {
                    throw new Exception("Failed to insert order item: " . $stmt->error);
                }

                error_log("Debug - Order item inserted successfully");
            }

            $conn->commit();
            return ['success' => true, 'order_id' => $orderId];
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Order creation failed: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Function to complete an order
    function completeOrder($orderId)
    {
        global $conn;

        try {
            $conn->begin_transaction();

            // Get order details
            $order = getOrder($orderId);
            if (!$order) {
                throw new Exception("Order not found");
            }

            // Check if order can be completed
            if ($order['status'] !== 'pending') {
                throw new Exception("Order cannot be completed. Current status: " . $order['status']);
            }

            // Check stock availability
            $orderItems = getOrderItems($orderId);
            while ($item = $orderItems->fetch_assoc()) {
                $stmt = $conn->prepare("SELECT quantity FROM inventory WHERE id = ?");
                $stmt->bind_param("i", $item['product_id']);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();

                if ($result['quantity'] < $item['quantity']) {
                    throw new Exception("Insufficient stock for product: " . $item['product_name']);
                }
            }

            // Update order status
            $stmt = $conn->prepare("UPDATE orders SET status = 'completed' WHERE order_id = ?");
            $stmt->bind_param("s", $orderId);
            $stmt->execute();

            // Deduct inventory
            $orderItems->data_seek(0); // Reset pointer to beginning
            while ($item = $orderItems->fetch_assoc()) {
                $stmt = $conn->prepare("UPDATE inventory SET quantity = quantity - ? WHERE id = ?");
                $stmt->bind_param("ii", $item['quantity'], $item['product_id']);
                $stmt->execute();
            }

            // Generate invoice
            $invoiceId = generateInvoiceId();

            // Insert into invoices table
            $stmt = $conn->prepare("INSERT INTO invoices (invoice_id, order_id, invoice_date, customer_name, 
                              contact_number, total_amount, payment_method, product_details, invoice_status) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

            // Get product details as JSON
            $productDetails = [];
            $orderItems->data_seek(0); // Reset pointer to beginning
            while ($item = $orderItems->fetch_assoc()) {
                $productDetails[] = [
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'amount' => $item['amount']
                ];
            }
            $productDetailsJson = json_encode($productDetails);

            $currentDate = date('Y-m-d');
            $status = 'completed';
            $stmt->bind_param(
                "sssssdsss",
                $invoiceId,
                $orderId,
                $currentDate,
                $order['customer_name'],
                $order['contact_number'],
                $order['total_amount'],
                $order['payment_method'],
                $productDetailsJson,
                $status
            );
            $stmt->execute();

            // Insert into sales table
            $stmt = $conn->prepare("INSERT INTO sales (order_id, invoice_id, customer_name, contact_number, sale_date, 
                              total_amount, payment_method, status) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $status = 'completed';
            $currentDate = date('Y-m-d');
            $stmt->bind_param(
                "sssssdss",
                $orderId,
                $invoiceId,
                $order['customer_name'],
                $order['contact_number'],
                $currentDate,
                $order['total_amount'],
                $order['payment_method'],
                $status
            );
            $stmt->execute();

            $saleId = $stmt->insert_id;

            // Insert sales items
            $stmt = $conn->prepare("INSERT INTO sales_products (sale_id, product_name, quantity, unit_price, total) 
                              VALUES (?, ?, ?, ?, ?)");
            $orderItems->data_seek(0);

            // Reset pointer to beginning
            while ($item = $orderItems->fetch_assoc()) {
                $stmt->bind_param(
                    "isids",
                    $saleId,
                    $item['product_name'],
                    $item['quantity'],
                    $item['unit_price'],
                    $item['amount']
                );
                $stmt->execute();
            }

            $conn->commit();
            return ['success' => true, 'invoice_id' => $invoiceId];
        } catch (Exception $e) {
            $conn->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Function to modify an order
    function modifyOrder($orderId, $customerName, $customerAddress, $contactNumber, $paymentMethod, $items)
    {
        global $conn;

        try {
            $conn->begin_transaction();

            // Get original order
            $originalOrder = getOrder($orderId);
            if (!$originalOrder) {
                throw new Exception("Order not found");
            }

            // Check if order can be modified
            if ($originalOrder['status'] !== 'pending' && $originalOrder['status'] !== 'completed') {
                throw new Exception("Order cannot be modified. Current status: " . $originalOrder['status']);
            }

            // Calculate new total
            $totalAmount = 0;
            foreach ($items as $item) {
                $totalAmount += $item['amount'];
            }

            // If order was completed, handle inventory adjustment
            if ($originalOrder['status'] === 'completed') {
                // Restore original inventory quantities
                $originalItems = getOrderItems($orderId);
                while ($item = $originalItems->fetch_assoc()) {
                    $stmt = $conn->prepare("UPDATE inventory SET quantity = quantity + ? WHERE id = ?");
                    $stmt->bind_param("ii", $item['quantity'], $item['product_id']);
                    $stmt->execute();
                }

                // Check and deduct new quantities
                foreach ($items as $item) {
                    $stmt = $conn->prepare("SELECT quantity FROM inventory WHERE id = ?");
                    $stmt->bind_param("i", $item['product_id']);
                    $stmt->execute();
                    $result = $stmt->get_result()->fetch_assoc();

                    if ($result['quantity'] < $item['quantity']) {
                        throw new Exception("Insufficient stock for product ID: " . $item['product_id']);
                    }

                    $stmt = $conn->prepare("UPDATE inventory SET quantity = quantity - ? WHERE id = ?");
                    $stmt->bind_param("ii", $item['quantity'], $item['product_id']);
                    $stmt->execute();
                }

                // Mark old invoice as cancelled
                $stmt = $conn->prepare("UPDATE sales SET status = 'cancelled' WHERE order_id = ?");
                $stmt->bind_param("s", $orderId);
                $stmt->execute();

                // Update invoice status to cancelled
                $stmt = $conn->prepare("UPDATE invoices SET invoice_status = 'cancelled' WHERE order_id = ?");
                $stmt->bind_param("s", $orderId);
                $stmt->execute();

                // Generate new invoice
                $newInvoiceId = generateInvoiceId();

                // Create new invoice record
                $productDetails = [];
                foreach ($items as $item) {
                    $stmt = $conn->prepare("SELECT product_name FROM inventory WHERE id = ?");
                    $stmt->bind_param("i", $item['product_id']);
                    $stmt->execute();
                    $result = $stmt->get_result()->fetch_assoc();

                    $productDetails[] = [
                        'product_name' => $result['product_name'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'amount' => $item['amount']
                    ];
                }
                $productDetailsJson = json_encode($productDetails);

                $stmt = $conn->prepare("INSERT INTO invoices (invoice_id, order_id, invoice_date, customer_name, 
                                  contact_number, total_amount, payment_method, product_details, invoice_status) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $currentDate = date('Y-m-d');
                $status = 'completed';
                $stmt->bind_param(
                    "sssssdsss",
                    $newInvoiceId,
                    $orderId,
                    $currentDate,
                    $customerName,
                    $contactNumber,
                    $totalAmount,
                    $paymentMethod,
                    $productDetailsJson,
                    $status
                );
                $stmt->execute();

                // Create new sales record
                $stmt = $conn->prepare("INSERT INTO sales (order_id, invoice_id, customer_name, contact_number, sale_date, 
                                  total_amount, payment_method, status) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $status = 'completed';
                $currentDate = date('Y-m-d');
                $stmt->bind_param(
                    "sssssdss",
                    $orderId,
                    $newInvoiceId,
                    $customerName,
                    $contactNumber,
                    $currentDate,
                    $totalAmount,
                    $paymentMethod,
                    $status
                );
                $stmt->execute();

                $newSaleId = $stmt->insert_id;

                // Insert new sales items
                $stmt = $conn->prepare("INSERT INTO sales_products (sale_id, product_name, quantity, unit_price, total) 
                                  VALUES (?, ?, ?, ?, ?)");
                foreach ($items as $item) {
                    $stmt = $conn->prepare("SELECT product_name FROM inventory WHERE id = ?");
                    $stmt->bind_param("i", $item['product_id']);
                    $stmt->execute();
                    $result = $stmt->get_result()->fetch_assoc();

                    $stmt = $conn->prepare("INSERT INTO sales_products (sale_id, product_name, quantity, unit_price, total) 
                                      VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param(
                        "isids",
                        $newSaleId,
                        $result['product_name'],
                        $item['quantity'],
                        $item['unit_price'],
                        $item['amount']
                    );
                    $stmt->execute();
                }
            }

            // Update order
            $stmt = $conn->prepare("UPDATE orders SET customer_name = ?, customer_address = ?, 
                              contact_number = ?, total_amount = ?, payment_method = ?, 
                              status = CASE WHEN status = 'completed' THEN 'modified' ELSE status END 
                              WHERE order_id = ?");
            $stmt->bind_param(
                "sssdss",
                $customerName,
                $customerAddress,
                $contactNumber,
                $totalAmount,
                $paymentMethod,
                $orderId
            );
            $stmt->execute();            // Delete old order items
            $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
            $stmt->bind_param("s", $orderId);
            $stmt->execute();

            // Insert new order items with complete product details
            foreach ($items as $item) {
                // Get product details from inventory
                $stmt = $conn->prepare("SELECT product_name, category FROM inventory WHERE id = ?");
                $stmt->bind_param("i", $item['product_id']);
                $stmt->execute();
                $product = $stmt->get_result()->fetch_assoc();

                if (!$product) {
                    throw new Exception("Product not found with ID: " . $item['product_id']);
                }

                // Insert order item with full details
                $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_category, quantity, unit_price, amount) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?)");
                
                $stmt->bind_param(
                    "ssssddd",
                    $orderId,
                    $item['product_id'],
                    $product['product_name'],
                    $product['category'],
                    $item['quantity'],
                    $item['unit_price'],
                    $item['amount']
                );
                $stmt->execute();
            }

            $conn->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $conn->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Function to cancel an order
    function cancelOrder($orderId)
    {
        global $conn;

        try {
            $conn->begin_transaction();

            // Get order details
            $order = getOrder($orderId);
            if (!$order) {
                throw new Exception("Order not found");
            }

            // Update order status to canceled (matching the database enum)
            $stmt = $conn->prepare("UPDATE orders SET status = 'canceled' WHERE order_id = ?");
            $stmt->bind_param("s", $orderId);
            $stmt->execute();

            // Restore inventory if order was completed            
            if ($order['status'] === 'completed' || $order['status'] === 'modified') {
                $orderItems = getOrderItems($orderId);
                while ($item = $orderItems->fetch_assoc()) {
                    $stmt = $conn->prepare("UPDATE inventory SET quantity = quantity + ? WHERE id = ?");
                    $stmt->bind_param("ii", $item['quantity'], $item['product_id']);
                    $stmt->execute();
                }

                // Mark invoice as cancelled (sales table uses different spelling)
                $stmt = $conn->prepare("UPDATE sales SET status = 'cancelled' WHERE order_id = ?");
                $stmt->bind_param("s", $orderId);
                $stmt->execute();

                // Update invoice status to cancelled (invoices table uses different spelling)
                $stmt = $conn->prepare("UPDATE invoices SET invoice_status = 'cancelled' WHERE order_id = ?");
                $stmt->bind_param("s", $orderId);
                $stmt->execute();

                // Delete sales products
                $stmt = $conn->prepare("DELETE FROM sales_products WHERE sale_id IN 
                                  (SELECT id FROM sales WHERE order_id = ?)");
                $stmt->bind_param("s", $orderId);
                $stmt->execute();
            }

            $conn->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $conn->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Handle AJAX requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['action'])) {
            switch ($data['action']) {
                case 'complete_order':
                    try {
                        if (!isset($data['order_id'])) {
                            throw new Exception("Order ID is required");
                        }

                        $result = completeOrder($data['order_id']);
                        echo json_encode($result);
                    } catch (Exception $e) {
                        echo json_encode([
                            'success' => false,
                            'message' => $e->getMessage()
                        ]);
                    }
                    break;

                case 'modify_order':
                    try {
                        if (!isset(
                            $data['order_id'],
                            $data['customer_name'],
                            $data['customer_address'],
                            $data['contact_number'],
                            $data['payment_method'],
                            $data['items']
                        )) {
                            throw new Exception("Missing required fields");
                        }

                        $result = modifyOrder(
                            $data['order_id'],
                            $data['customer_name'],
                            $data['customer_address'],
                            $data['contact_number'],
                            $data['payment_method'],
                            $data['items']
                        );
                        echo json_encode($result);
                    } catch (Exception $e) {
                        echo json_encode([
                            'success' => false,
                            'message' => $e->getMessage()
                        ]);
                    }
                    break;

                case 'cancel_order':
                    try {
                        if (!isset($data['order_id'])) {
                            throw new Exception("Order ID is required");
                        }

                        $result = cancelOrder($data['order_id']);
                        echo json_encode($result);
                    } catch (Exception $e) {
                        echo json_encode([
                            'success' => false,
                            'message' => $e->getMessage()
                        ]);
                    }
                    break;
            }
            exit;
        }
    }
}
