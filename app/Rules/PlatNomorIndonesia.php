<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PlatNomorIndonesia implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Normalize: uppercase and trim
        $plat = strtoupper(trim($value));
        
        // Indonesian license plate format:
        // [1-2 letters] [SPACE] [1-4 numbers] [SPACE] [1-3 letters]
        // Examples: B 1234 ABC, DK 567 XY, D 1 A
        
        $pattern = '/^[A-Z]{1,2}\s\d{1,4}\s[A-Z]{1,3}$/';
        
        if (!preg_match($pattern, $plat)) {
            $fail('Format plat nomor tidak valid. Contoh format yang benar: B 1234 ABC, DK 567 XY');
        }
    }
    
    /**
     * Static method untuk normalize plat nomor
     */
    public static function normalize(string $plat): string
    {
        // Remove all spaces
        $plat = strtoupper(str_replace(' ', '', trim($plat)));
        
        // Parse components
        if (!preg_match('/^([A-Z]{1,2})(\d{1,4})([A-Z]{1,3})$/', $plat, $matches)) {
            return strtoupper(trim($plat)); // Return as-is if doesn't match
        }
        
        // Reconstruct with proper spacing: AREA NUMBER CODE
        return $matches[1] . ' ' . $matches[2] . ' ' . $matches[3];
    }
}