<?php
require_once 'dbconnection.php';
require_once 'proposal_functions.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['id'])) {
        throw new Exception('Proposal ID is required');
    }
    
    $proposal = getProposal($_GET['id']);
    if (!$proposal) {
        throw new Exception('Proposal not found');
    }
    
    $items = getProposalItems($_GET['id']);
    $proposal['items'] = array();
    while ($item = $items->fetch_assoc()) {
        $proposal['items'][] = $item;
    }
    
    echo json_encode(['success' => true, 'proposal' => $proposal]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
