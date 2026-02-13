<?php

namespace App\Services;

use App\Models\POS\Invoice;
use Illuminate\Support\Facades\DB;

class InvoiceNumberService
{
    /**
     * Generate next invoice number
     */
    public static function generateInvoiceNumber()
    {
        // Get the current year
        $year = date('Y');
        $prefix = 'INV-' . $year . '-';
        
        // Get the last invoice number for this year
        $lastInvoice = Invoice::where('invoice_no', 'like', $prefix . '%')
            ->orderBy('invoice_no', 'desc')
            ->first();
        
        if ($lastInvoice) {
            // Extract the serial number from last invoice
            $lastSerial = (int) str_replace($prefix, '', $lastInvoice->invoice_no);
            $nextSerial = $lastSerial + 1;
        } else {
            // First invoice of the year
            $nextSerial = 1;
        }
        
        // Format with leading zeros (4 digits)
        return $prefix . str_pad($nextSerial, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get next invoice number preview
     */
    public static function getNextInvoiceNumber()
    {
        $year = date('Y');
        $prefix = 'INV-' . $year . '-';
        
        $lastInvoice = Invoice::where('invoice_no', 'like', $prefix . '%')
            ->orderBy('invoice_no', 'desc')
            ->first();
        
        if ($lastInvoice) {
            $lastSerial = (int) str_replace($prefix, '', $lastInvoice->invoice_no);
            $nextSerial = $lastSerial + 1;
        } else {
            $nextSerial = 1;
        }
        
        return $prefix . str_pad($nextSerial, 4, '0', STR_PAD_LEFT);
    }
}
