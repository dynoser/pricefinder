<?php
namespace dynoser\textworks;

class PriceFinder
{
    public $currenciesArr = [
        'BTC' => '₿',   // Биткоин
        'ETH' => 'Ξ',   // Ethereum
        'LTC' => 'Ł',   // Litecoin
        'XRP' => 'XRP', // Ripple
        'DOGE' => 'DOGE',//Dogecoin
        'DASH' => 'DASH',//Dash
        'XMR' => 'XMR', //Monero
        'RUB' => '₽',  // Российский рубль
        'RUR' => '₽',  // Российский рубль альтернативно
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
        'PKR' => '₨',  // Пакистанская рупия
        'BDT' => '৳',   // Бангладешская така
        'VND' => '₫',   // Вьетнамский донг
        'EGP' => '£',   // Египетский фунт
        'IRR' => '﷼',   // Иранский риал
        'COP' => 'Col$',// Колумбийский песо
        'ARS' => 'AR$', // Аргентинский песо
        'CLP' => 'CLP$',// Чилийский песо
        'VEF' => 'Bs', // Венесуэльский боливар (старый)
        'VES' => 'Bs.S',// Венесуэльский боливар (новый)
        'ZWL' => 'Z$',  // Зимбабвийский доллар
        'NGN' => '₦',  // Нигерийская найра
        'GHS' => '₵',  // Ганский седи
        'BYN' => 'Br',  // Белорусский рубль
        'KZT' => '₸',   // Казахский тенге
        'SEK' => 'SEK', // Шведская крона
        'NOK' => 'NOK', // Норвежская крона
        'DKK' => 'DKK', // Датская крона
    ];
    
    public $prefixesArr = [
        'Цена',
        'Ціна', 'Price', 'Precio', 'Preis', 'Prix', 'Preço', '価格', '价格', 'قیمت', 'मूल्य',
        'Fiyat',
        'Giá',
        'Harga',
        '가격',
        'तका',    // Хинди (Индия)
        'Цэнь',  // Белорусский (Беларусь)
        'Kaina', // Литовский (Литва)
        'Cena',  // Чешский/Словацкий (Чехия/Словакия)
        'Pris',  // Шведский/Норвежский (Швеция/Норвегия)
        'Preț',  // Румынский (Румыния)
    ];

    public $currAddArr = [
        'РУБ' => 'RUB',
        'ГРН' => 'UAH',
        'ГРИВ' => 'UAH',
        'ДОЛ' => 'USD',
        'ЕВР' => 'EUR',
    ];

    public $suffixesArr = [];
    public $suffixBreakChars = " .,;:!*(-+=){}[]\n\r\t";

    public $escapedPrefixes = '';
    public $escapedSuffixes = '';
    
    public $upperSumPrefixes = '';
    public $upperSumSuffixes = '';

    public $middleReg  = '(\d{1,3}(?:[\s,\.]\d{3})*|\d+)(\.|\,)?(\d{1,2})?';
    
    public function __construct($addPrefixesArr = [], $addSuffixesArr = []) {
        $currCodesArr  = \array_keys($this->currenciesArr);
        $currCharsArr = \array_values($this->currenciesArr);
        $this->prefixesArr = \array_merge($this->prefixesArr, $currCharsArr, $addPrefixesArr);
        $currAddArr = \array_keys($this->currAddArr);
        $this->suffixesArr = \array_merge($currAddArr, $currCharsArr, $currCodesArr, $addSuffixesArr);
    }
    
    public function setPrefixes($prefixesArr) {
        assert(\is_array($prefixesArr));
        $this->prefixesArr = $prefixesArr;
        $this->escapedPrefixes = '';
        $this->upperSumPrefixes = '';
    }

    public function setSuffixes($suffixesArr) {
        assert(\is_array($suffixesArr));
        $this->suffixesArr = $suffixesArr;
        $this->escapedSuffixes = '';
        $this->upperSumSuffixes = '';
    }

    function findPrices($srcString, $currencyRequired = true) {
        if (!$this->escapedPrefixes) {
            $this->escapedPrefixes = \array_map(function($item) { return \preg_quote($item, '/'); }, $this->prefixesArr);
        }
        if (!$this->escapedSuffixes) {
            $this->escapedSuffixes = \array_map(function($item) { return \preg_quote($item, '/'); }, $this->suffixesArr);
        }
        if (!$this->upperSumPrefixes) {
            $this->upperSumPrefixes = ' ' . self::mbStrToUpper(\implode(' ', $this->prefixesArr)) . ' ';
        }
        if (!$this->upperSumSuffixes) {
            $this->upperSumSuffixes = ' ' . self::mbStrToUpper(\implode(' ', $this->suffixesArr)) . ' ';
        }

        $pattern = '/(' . implode('|', $this->escapedPrefixes) . ')?[:\=\-]?\s*'. $this->middleReg .'\s*(' . implode('|', $this->escapedSuffixes) . ')?/iu';

        $matches = [];
        \preg_match_all($pattern, $srcString, $matches, \PREG_SET_ORDER | \PREG_OFFSET_CAPTURE);

        $results = [];
        foreach ($matches as $match) {
            $prefix = $match[1][0];
            $upPrefix = self::mbStrToUpper($prefix);
            $suffix = \end($match)[0];
            $upSuffix = self::mbStrToUpper($suffix);
            $inPref =             $prefix && (false !== \strpos($this->upperSumPrefixes, ' ' . $upPrefix . ' ')); 
            $inSuff = !$inPref && $suffix && (false !== \strpos($this->upperSumSuffixes, ' ' . $upSuffix . ' '));
            if ($inPref || $inSuff) {
                $currency = $this->currencyDetect($upPrefix, $prefix, $upSuffix, $suffix);
                if (!$currencyRequired || $currency) {
                    $matchPos  = $match[0][1];
                    $full_match = $match[0][0];
                    $posAfterMatch = $matchPos + \strlen($full_match);
                    $add_to_full_match = \substr($srcString, $posAfterMatch, \strcspn($srcString, $this->suffixBreakChars, $posAfterMatch));
                    $full_match .= $add_to_full_match;
                    $results[] = [
                        'full_match' => \trim($full_match),
                        'digits' => \preg_replace('/[^\d.]/', '', $match[0][0]),
                        'currency' => $currency,
                        'prefix' => $prefix,
                        'suffix' => $suffix . $add_to_full_match,
                        'match_position' => $matchPos,
                    ];
                }
            }
        }

        return $results;
    }
    
    public function currencyDetect($upPrefix, $prefix, $upSuffix, $suffix) {
        if (isset($this->currenciesArr[$upPrefix])) {
            return $upPrefix;
        }
        if (isset($this->currenciesArr[$upSuffix])) {
            return $upSuffix;
        }
        if (isset( $this->currAddArr[$upSuffix])) {
            return $this->currAddArr[$upSuffix];
        }
        if (isset( $this->currAddArr[$upPrefix])) {
            return $this->currAddArr[$upPrefix];
        }
        $currency = \array_search($prefix, $this->currenciesArr);
        if (false !== $currency) {
            return $currency;
        }
        $currency = \array_search($suffix, $this->currenciesArr);
        if (false !== $currency) {
            return $currency;
        }
        return '';
    }
    
    public function mbStrToUpper($str) {
        return \mb_strtoupper($str, 'UTF-8');
    }
}
