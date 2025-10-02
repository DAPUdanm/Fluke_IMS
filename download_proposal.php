<?php
require_once 'dbconnection.php';
require_once 'proposal_functions.php';

try {
    if (!isset($_GET['id'])) {
        throw new Exception('Proposal ID is required');
    }
    
    $pdfContent = generateProposalPDF($_GET['id']);
    
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="proposal.pdf"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    
    echo $pdfContent;
} catch (Exception $e) {
    die('Error generating PDF: ' . $e->getMessage());
}
