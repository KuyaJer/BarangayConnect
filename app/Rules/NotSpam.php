<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NotSpam implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $v = trim((string) $value);

        // At least 2 real words (≥2 chars each)
        $words = array_filter(preg_split('/\s+/', $v), fn($w) => mb_strlen($w) >= 2);
        if (count($words) < 2) {
            $fail('The :attribute must contain meaningful content (at least 2 words).');
            return;
        }

        // Repeated character check (ignoring spaces/punctuation)
        $letters = preg_replace('/[^a-zA-Z0-9]/u', '', strtolower($v));
        if (strlen($letters) >= 5) {
            $counts   = array_count_values(str_split($letters));
            $maxCount = max($counts);
            if ($maxCount / strlen($letters) >= 0.55) {
                $fail('The :attribute appears to contain repeated characters. Please provide meaningful content.');
                return;
            }
        }

        // Keyboard-mash detection
        $alpha = preg_replace('/[^a-z]/i', '', strtolower($v));
        $mashPatterns = ['asdfg', 'qwert', 'zxcvb', 'uiop', 'hjkl', 'bnm',
                         'fffff', 'aaaaa', 'sssss', 'ddddd', 'jjjjj', 'kkkkk',
                         'asdf', 'qwer', 'zxcv'];
        // Only flag short strings so that legitimate text isn't blocked
        if (strlen($alpha) <= 30) {
            foreach ($mashPatterns as $p) {
                if (str_contains($alpha, $p)) {
                    $fail('The :attribute appears to contain gibberish. Please describe your issue clearly.');
                    return;
                }
            }
        }
    }
}
