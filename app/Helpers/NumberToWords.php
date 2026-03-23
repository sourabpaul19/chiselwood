<?php

namespace App\Helpers;

class NumberToWords
{
    public static function convert($number)
    {
        $number = round($number);
        $no     = floor($number);
        $point  = round($number - $no, 2) * 100;

        $words = [
            0 => '',
            1 => 'One',
            2 => 'Two',
            3 => 'Three',
            4 => 'Four',
            5 => 'Five',
            6 => 'Six',
            7 => 'Seven',
            8 => 'Eight',
            9 => 'Nine',
            10 => 'Ten',
            11 => 'Eleven',
            12 => 'Twelve',
            13 => 'Thirteen',
            14 => 'Fourteen',
            15 => 'Fifteen',
            16 => 'Sixteen',
            17 => 'Seventeen',
            18 => 'Eighteen',
            19 => 'Nineteen',
            20 => 'Twenty',
            30 => 'Thirty',
            40 => 'Forty',
            50 => 'Fifty',
            60 => 'Sixty',
            70 => 'Seventy',
            80 => 'Eighty',
            90 => 'Ninety'
        ];

        $digits = ['', 'Hundred', 'Thousand', 'Lakh', 'Crore'];
        $str = [];
        $i = 0;

        while ($no > 0) {
            $divider = ($i == 1) ? 10 : 100;
            $numberPart = $no % $divider;
            $no = floor($no / $divider);

            if ($numberPart) {
                $plural = ($numberPart > 9 && $i) ? 's' : null;
                $hundred = ($i == 1 && $str[0]) ? 'and ' : null;

                if ($numberPart < 21) {
                    $str[] = $words[$numberPart] . ' ' . $digits[$i] . ' ' . $hundred;
                } else {
                    $str[] = $words[floor($numberPart / 10) * 10] . ' ' .
                             $words[$numberPart % 10] . ' ' .
                             $digits[$i] . ' ' . $hundred;
                }
            } else {
                $str[] = null;
            }
            $i++;
        }

        $str = array_reverse($str);
        $result = trim(implode('', $str));

        if ($point > 0) {
            $result .= ' and ' . $words[$point / 10] . ' ' . $words[$point % 10] . ' Paise';
        }

        return $result;
    }
}
