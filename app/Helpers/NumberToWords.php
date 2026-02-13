<?php

namespace App\Helpers;

class NumberToWords
{
    public static function convert($number)
    {
        // Remove decimal part for words conversion
        $number = str_replace(',', '', $number);
        $parts = explode('.', $number);
        
        $wholeNumber = (int)($parts[0]);
        $decimalNumber = isset($parts[1]) ? (int)($parts[1]) : 0;
        
        $words = self::convertNumber($wholeNumber);
        
        if ($decimalNumber > 0) {
            $words .= ' and ' . self::convertNumber($decimalNumber) . ' ' . ($decimalNumber == 1 ? 'Paisa' : 'Paisas');
        }
        
        return $words;
    }
    
    private static function convertNumber($number)
    {
        if ($number == 0) {
            return 'Zero';
        }
        
        $words = [];
        
        // Millions
        if ($number >= 1000000) {
            $millions = floor($number / 1000000);
            $words[] = self::convertNumber($millions) . ' Million';
            $number %= 1000000;
        }
        
        // Thousands
        if ($number >= 1000) {
            $thousands = floor($number / 1000);
            $words[] = self::convertNumber($thousands) . ' Thousand';
            $number %= 1000;
        }
        
        // Hundreds
        if ($number >= 100) {
            $hundreds = floor($number / 100);
            $words[] = self::convertNumber($hundreds) . ' Hundred';
            $number %= 100;
        }
        
        // Tens and units
        if ($number > 0) {
            $words[] = self::convertTensAndUnits($number);
        }
        
        return implode(' ', $words);
    }
    
    private static function convertTensAndUnits($number)
    {
        $units = ['One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
        $teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        
        if ($number < 10) {
            return $units[$number - 1] ?? '';
        } elseif ($number >= 10 && $number < 20) {
            return $teens[$number - 10] ?? '';
        } else {
            $ten = floor($number / 10);
            $unit = $number % 10;
            $word = $tens[$ten - 2] ?? '';
            if ($unit > 0) {
                $word .= ' ' . $units[$unit - 1];
            }
            return $word;
        }
    }
}
