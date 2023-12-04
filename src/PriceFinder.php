<?php
namespace dynoser\textworks;

class PriceFinder
{
    public $prefixesArr = [
        'Цена', 'Ціна', 'Price', 'Precio', 'Preis', 'Prix', 'Preço', '価格', '价格', 'قیمت', 'मूल्य',
        '₽', '₴', '$', '€', '¥', '£', 'A$', 'C$', 'CHF', '元', 'kr', '₹'
    ];
    public $suffixesArr = [
        'руб', 'грн', 'грив',
        '₽', '₴', '$', '€', '¥', '£', 'A$', 'C$', '元', 'kr', '₹', 
        'RUB', 'UAH', 'USD', 'EUR', 'JPY', 'GBP', 'AUD', 'CAD', 'CHF', 'CNY', 'SEK', 'INR', 
    ];

    public $escapedPrefixes = '';
    public $escapedSuffixes = '';

    public $middleReg = '(\d{1,3}(?:[\s,\.]\d{3})*|\d+)(\.|\,)?(\d{2})?';
    
    public function setPrefixes($prefixesArr) {
        assert(\is_array($prefixesArr));
        $this->prefixesArr = $prefixesArr;
        $this->escapedPrefixes = '';
    }

    public function setSuffixes($suffixesArr) {
        assert(\is_array($suffixesArr));
        $this->suffixesArr = $suffixesArr;
        $this->escapedSuffixes = '';
    }

    function findPrices($string) {
        if (!$this->escapedPrefixes) {
            $this->escapedPrefixes = \array_map(function($item) { return \preg_quote($item, '/'); }, $this->prefixesArr);
        }
        if (!$this->escapedSuffixes) {
            $this->escapedSuffixes = \array_map(function($item) { return \preg_quote($item, '/'); }, $this->suffixesArr);
        }

        $pattern = '/(' . implode('|', $this->escapedPrefixes) . ')?[:]?\s*'. $this->middleReg .'\s*(' . implode('|', $this->escapedSuffixes) . ')?/';

        $matches = [];
        \preg_match_all($pattern, $string, $matches, \PREG_SET_ORDER | \PREG_OFFSET_CAPTURE);

        $results = [];
        foreach ($matches as $match) {
            $results[] = [
                'full_match' => $match[0][0],
                'match_position' => $match[0][1],
                'digits' => \preg_replace('/\D/', '', $match[0][0]),
//                'prefix' => $match[1][0],
//                'price' => $match[2][0],
//                'suffix' => end($match)[0]
            ];
        }

        return $results;
    }
}
