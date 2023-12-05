PriceFinder
===========

Overview
--------

`PriceFinder` is a PHP class designed to detect and analyze price information in text strings. It's especially useful for processing multi-currency data, with support for a wide range of global currencies and cryptocurrencies.

Features
--------

- **Currency Support:** The class can recognize and work with a diverse set of currencies, including major ones like USD, EUR, and GBP, along with popular cryptocurrencies like Bitcoin (BTC), Ethereum (ETH), and Dogecoin (DOGE).
- **Flexible Text Analysis:** It can find prices within text strings, even when they're presented in various formats or surrounded by different types of text.
- **Customizable Prefixes and Suffixes:** Users can define their own prefixes and suffixes for price detection, in addition to the default ones provided.
- **Multi-Language Support:** The class recognizes price prefixes in multiple languages, enhancing its usability in international contexts.

How It Works
------------

1. **Initialization:** The constructor initializes currency codes and symbols, along with default and customizable prefixes and suffixes for price detection.
2. **Price Detection:** The `findPrices` method scans a given text string for price-related patterns, including currency symbols and numeric values.
3. **Currency Recognition:** It determines the currency by analyzing the prefixes, suffixes, and actual symbols used in the text.
4. **Result Compilation:** The method returns an array of detected prices with detailed information like the full matched string, the numeric value, and the identified currency.

Usage Example
-------------

```php
$priceFinder = new dynoser\textworks\PriceFinder();
$pricesArr = $priceFinder->findPrices("The price is $100 or €85.33, or 8,000 рублей");
print_r($pricesArr);
// This will return an array with details of the detected prices in USD and EUR:
Array
(
    [0] => Array
        (
            [full_match] => $100 or
            [digits] => 100
            [currency] => USD
            [match_position] => 13
        )

    [1] => Array
        (
            [full_match] => €85.33
            [digits] => 85.33
            [currency] => EUR
            [match_position] => 21
        )

    [2] => Array
        (
            [full_match] => 8,000 рублей
            [digits] => 8000
            [currency] => RUB
            [match_position] => 33
        )
)

```

Customization
-------------

- **Adding Custom Prefixes/Suffixes:** Users can add their own prefixes or suffixes by passing them as arrays to the constructor or using `setPrefixes` and `setSuffixes` methods.
- **Support for Additional Currencies:** The class can be extended to support more currencies by updating the `$currenciesArr` array.

Limitations
-----------

- **Language Dependency:** While the class supports multiple languages, it may require customization for optimal performance in specific linguistic contexts.
- **Complex Formats:** Extremely complex or unconventional price formats might not be detected accurately.

Conclusion
----------

The `PriceFinder` class is a versatile tool for detecting and analyzing prices in text, supporting a wide range of currencies and languages. It is highly customizable, making it suitable for various applications where price data extraction from text is required.
