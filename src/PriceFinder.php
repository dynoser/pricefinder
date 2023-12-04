<?php
namespace dynoser\textworks;

class PriceFinder
{
    public $currenciesArr = [
        'RUB' => '₽',  // Российский рубль
        'UAH' => '₴',  // Украинская гривна
        'USD' => '$',  // Американский доллар
        'EUR' => '€',  // Евро
        'GBP' => '£',  // Британский фунт
        'JPY' => '¥',  // Японская йена
        'CNY' => '元',  // Китайский юань
        'INR' => '₹',   // Индийская рупия
        'CAD' => 'C$',  // Канадский доллар
        'AUD' => 'A$',  // Австралийский доллар
        'CHF' => 'CHF', // Швейцарский франк
        'KRW' => '₩',  // Южнокорейская вона
        'BRL' => 'R$',  // Бразильский реал
        'MXN' => 'Mex$',// Мексиканский песо
        'ZAR' => 'R',   // Южноафриканский рэнд
        'SEK' => 'kr',  // Шведская крона
        'NOK' => 'kr',  // Норвежская крона
        'DKK' => 'kr',  // Датская крона
        'PLN' => 'zł',  // Польский злотый
        'CZK' => 'Kč',  // Чешская крона
        'HUF' => 'Ft',  // Венгерский форинт
        'THB' => '฿',   // Тайский бат
        'SGD' => 'S$',  // Сингапурский доллар
        'HKD' => 'HK$', // Гонконгский доллар
        'MYR' => 'RM',  // Малайзийский ринггит
        'PHP' => '₱',   // Филиппинское песо
        'IDR' => 'Rp',  // Индонезийская рупия
        'NZD' => 'NZ$', // Новозеландский доллар
        'TRY' => '₺',   // Турецкая лира
        'ILS' => '₪',   // Израильский шекель
        'AED' => 'د.إ', // Дирхам (ОАЭ)
        'SAR' => 'ر.س', // Саудовский риял
        'QAR' => 'ر.ق', // Катарский риал
        'BYN' => 'Br',  // Белорусский рубль
        'KZT' => '₸',   // Казахский тенге
    ];

    
    public $prefixesArr = [
        'Цена', 'Ціна', 'Price', 'Precio', 'Preis', 'Prix', 'Preço', '価格', '价格', 'قیمت', 'मूल्य',
    ];
    public $suffixesArr = [
        'руб', 'грн', 'грив',
    ];

    public $escapedPrefixes = '';
    public $escapedSuffixes = '';
    
    public $lowerSumPrefixes = '';
    public $lowerSumSuffixes = '';

    public $middleReg  = '(\d{1,3}(?:[\s,\.]\d{3})*|\d+)(\.|\,)?(\d{1,2})?';
    
    public function __construct($addPrefixesArr = [], $addSuffixesArr = []) {
        $currCodesArr  = \array_keys($this->currenciesArr);
        $currCharsArr = \array_values($this->currenciesArr);
        $this->prefixesArr = \array_merge($this->prefixesArr, $currCharsArr, $addSuffixesArr);
        $this->suffixesArr = \array_merge($this->suffixesArr, $currCharsArr, $currCodesArr, $addSuffixesArr);
    }
    
    public function setPrefixes($prefixesArr) {
        assert(\is_array($prefixesArr));
        $this->prefixesArr = $prefixesArr;
        $this->escapedPrefixes = '';
        $this->lowerSumPrefixes = '';
    }

    public function setSuffixes($suffixesArr) {
        assert(\is_array($suffixesArr));
        $this->suffixesArr = $suffixesArr;
        $this->escapedSuffixes = '';
        $this->lowerSumSuffixes = '';
    }

    function findPrices($string) {
        if (!$this->escapedPrefixes) {
            $this->escapedPrefixes = \array_map(function($item) { return \preg_quote($item, '/'); }, $this->prefixesArr);
        }
        if (!$this->escapedSuffixes) {
            $this->escapedSuffixes = \array_map(function($item) { return \preg_quote($item, '/'); }, $this->suffixesArr);
        }
        if (!$this->lowerSumPrefixes) {
            $this->lowerSumPrefixes = ' ' . self::mbStrToLower(\implode(' ', $this->prefixesArr)) . ' ';
        }
        if (!$this->lowerSumSuffixes) {
            $this->lowerSumSuffixes = ' ' . self::mbStrToLower(\implode(' ', $this->suffixesArr)) . ' ';
        }

        $pattern = '/(' . implode('|', $this->escapedPrefixes) . ')?[:]?\s*'. $this->middleReg .'\s*(' . implode('|', $this->escapedSuffixes) . ')?/iu';

        $matches = [];
        \preg_match_all($pattern, $string, $matches, \PREG_SET_ORDER | \PREG_OFFSET_CAPTURE);

        $results = [];
        foreach ($matches as $match) {
            $prefix = $match[1][0];
            $suffix = end($match)[0];
            $inPref =             $prefix && (false !== \strpos($this->lowerSumPrefixes, ' ' . self::mbStrToLower($prefix) . ' ')); 
            $inSuff = !$inPref && $suffix && (false !== \strpos($this->lowerSumSuffixes, ' ' . self::mbStrToLower($suffix) . ' '));
            if ($inPref || $inSuff) {
                $results[] = [
                    'full_match' => $match[0][0],
                    'match_position' => $match[0][1],
                    'digits' => \preg_replace('/\D/', '', $match[0][0]),
                    'prefix' => $prefix,
                    'suffix' => $suffix
                ];
            }
        }

        return $results;
    }
    
    public function mbStrToLower($str) {
        return \mb_strtolower($str, 'UTF-8');
    }
}
