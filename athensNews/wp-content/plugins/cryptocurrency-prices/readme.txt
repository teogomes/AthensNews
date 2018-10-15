=== Cryptocurrency All-in-One ===
Contributors: byankov
Donate link: http://creditstocks.com/donate/
Tags: bitcoin, cryptocurrency, bitcoin, ethereum, ripple, exchange, prices, rates, trading, payments, orders, token, btc, eth, etc, dash, nmc, nvc, ppc, dsh, candlestick, usd, eur  
Requires at least: 3.0
Tested up to: 4.9.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Cryptocurrency features: displaying prices and exchange rates, candlestick price chart, calculator, accepting orders and payments, accepting donations.

== Description ==

= This plugin provides multiple cryptocurrency features: = 
* displaying prices and exchange rates, 
* cryptocurrency calculator,
* list of all cryptocurrencies, 
* displaying candlestick price charts,
* accepting orders and payments,
* accepting donations. 

= Instructions to display cryptocurrency calculator and exchange rates in a nicely formatted table. = 

To show cryptocurrency prices, add a shortcode to the text of the pages or posts where you want the cryptocurrency prices to apperar. Exapmle shortcodes:

`[currencyprice currency1="btc" currency2="usd,eur,ltc,eth,jpy,gbp,chf,aud,cad"]`
`[currencyprice currency1="ltc" currency2="usd,eur,btc" feature="all"]`
`[currencyprice currency1="eth" currency2="usd,btc" feature="prices"]`
`[currencyprice currency1="eth" currency2="usd,btc" feature="calculator"]`

Major cryptocurrencies are fully supported with icons: Bitcoin BTC, Ethereum ETH, XRP, DASH, LTC, ETC, XMR, XEM, REP, MAID, PIVX, GNT, DCR, ZEC, STRAT, BCCOIN, FCT, STEEM, WAVES, GAME, DOGE, ROUND, DGD, LISK, SNGLS, ICN, BCN, XLM, BTS, ARDR, 1ST, PPC, NAV, NXT, LANA. Partial suport for over 1000 cryptocurrencies. Fiat currencies conversion supported: AUD, USD, CAD, GBP, EUR, CHF, JPY, CNY.
[Live demo](https://creditstocks.com/cryptocurrency-prices/current-litecoin-price/)

= Instructions to display cryptocurrency candlestick price chart = 

To show cryptocurrency candlestick chart graphic, add a shortcode to the text of the pages or posts where you want the chart to apperar. Exapmle shortcodes:

`[currencygraph currency1="btc" currency2="usd"]`
`[currencygraph currency1="dash" currency2="btc"]`

[Live demo](https://creditstocks.com/cryptocurrency-prices/current-bitcoin-price/)

= Instructions to display a list of all cryptocurrencies on your web site. =

Add a shortcode to the text of the pages or posts where you want to display a list of all cryptocurrencies. The list includes coin icon, name, algorithm, PoS/PoW, total supply, URL. The shortcode supports adjustments with parameters. Exapmle shortcodes:

`[allcurrencies]`
`[allcurrencies algorithm="no" supply="no" url="yes"]`

[Live demo](https://creditstocks.com/cryptocurrency-prices/list-of-all-cryptocurrencies/)

= Instructions to accept orders and bitcoin payments on your web site. = 

Open the plugin settings and under "Payment settings" fill in your BTC wallet addresses to receive payments and an email for receiving payment notifications. 
The plugin does not store your wallet's private keys. It uses one of the addresses from the provided list for every payment, by rotating all addresses and starting over from the first one. The different addresses are used to idenfiry if a specific payment has been made. You must provide enough addresses - more than the number of payments you will receive a day. 
Add a shortcode to the text of the pages or posts where you want to accept payments (typically these pages would contain a product or service that you are offering). The amount may be in BTC (default) or in fiat currency which will be converted it to BTC - USD, EUR, etc. 
Exapmle shortcodes:

`[cryptopayment item="Advertising services" amount="0.003"]`
`[cryptopayment item="Publish a PR article" amount="50 USD"]`

[Live demo](https://creditstocks.com/payment-demo/)

= Instructions to accept cryptocurrency donations on your web site. = 

Add a shortcode to the text of the pages or posts where you want to accept donations. Supported currencies are Bitcoin (BTC) (default), Ethereum (ETH), Litecon (LTC), Monero (XMR), Zcash (ZEC). Exapmle shortcodes (do not forget to put your wallet address):

`[cryptodonation address="1ABwGVwbna6DnHgPefSiakyzm99VXVwQz9"]`
`[cryptodonation address="0xc85c5bef5a9fd730a429b0e04c69b60d9ef4c64b" currency="eth"]`
`[cryptodonation address="463tWEBn5XZJSxLU6uLQnQ2iY9xuNcDbjLSjkn3XAXHCbLrTTErJrBWYgHJQyrCwkNgYvyV3z8zctJLPCZy24jvb3NiTcTJ" paymentid="a1be1fb24f1e493eaebce2d8c92dc68552c165532ef544b79d9d36d1992cff07" currency="xmr"]`

[Live demo](https://creditstocks.com/donate/)

= Instructions for Ethereum node integration = 

Currently supported features are: check Ethereum address balance, view ethereum block. Before using the shortcodes you need to fill in your Ethereum node API URL in the plugin settings (http://localhost:8545 or a public node at infura.io). Exapmle shortcodes:

`[cryptoethereum feature="balance"]`
`[cryptoethereum feature="block"]`

[Live demo](https://creditstocks.com/ethereum/)

= Instructions to use the plugin in a widget or from the theme =

To use the plugin in a widget, use the provided "CP Shortcode Widget" and put the shortcode in the "Content" section.
You can also call all plugin features directly from the theme - see the plugin settings page for PHP samples.

This plugin uses data from third party public APIs. By installing this plugin you agree with their terms: [CryptoCompare Public API](https://www.cryptocompare.com/api/) - no API key required, under [Creative Commons license](https://creativecommons.org/licenses/by-nc/3.0/), [Google Charts API](https://developers.google.com/chart/terms). Special thanks to: Emil Samsarov.

### Donations

Thank you so much for considering supporting my work. If you have benefited from this WordPress plugin and feel led to send me a monetary donation, please follow the link [here](http://creditstocks.com/donate/). I am truly thankful for your hard earned giving.

== Installation ==

1. Unzip the `cryptocurrency-prices.zip` folder.
2. Upload the `cryptocurrency-prices` folder to your `/wp-content/plugins` directory.
3. In your WordPress dashboard, head over to the *Plugins* section.
4. Activate *Cryptocurrency Prices*.

== Frequently Asked Questions ==

= Can I show the plugin from the theme code or from another plugin? =

Yes. You can use a PHP code, which handles and shows the plugin shortcode - see the plugin settings page for PHP sample. 

= Can I show the plugin in a widget? =

Yes! Use the provided "CP Shortcode Widget" and put the shortcode in the "Content" section, for example: [currencyprice currency1="btc" currency2="usd,eur"].

= The plugin does not work - I just see the shortcode? =

Make sure you have activated the plugin. Try to add the shortcode to a page to see if it works. If you use a widget - add the shortcode in the widget provided by the plugin. If you call the plugin from the theme, make sure the code is integrated correctly. 

= The plugin does not work - I see no data or an error message? =

Try to activate compatibility mode from the plugin settings. It may be due to data provider server downtime. 

= How to style the plugon? / I don't like the design? =

The plugin takes the styles from your theme. You can write CSS code for custom styles - use the "Custom design" field in the plugin settings. Check the support forum for examples https://wordpress.org/support/plugin/cryptocurrency-prices/ 

= Can the plugin cache the data? =

The plugin itself does not cache the data. But it is compatible with caching plugins. 

= How to remove the credit (link to developer)? =

You can easily remove the link from the plugin settings page.

= How can I add my coin in the plugin? =

If you want official support of your coin by the plugin (payments, donations, custom icon), send me an email at boian_iankov@abv.bg 

== Screenshots ==

1. Example of table with LTC prices.

== Changelog ==

= 2.5.4 =
* Fixed bugs. Improved plugin styling capabilities.

= 2.5.3 =
* Plugin is now translatable. German translation is provided. Minor improvements.

= 2.5.2 =
* Added support of parameters for [allcurrencies] shortcode.

= 2.5.1 =
* Added support for Ethereum block viewer by connecting to an Ethereum blockchain node. Other minor improvements.

= 2.5 =
* Added Ethereum blockchain node support with web3.js. Removed Counterparty support. Bugfixes.

= 2.4.5 =
* Improved cryptocurrency payments: amount can be specified in fiat currency, multiple payment forms supported on single page, orders can be deleted.  Added a feature for accepting donations in Litecon (LTC), Monero (XMR), Zcash (ZEC).

= 2.4.4 =
* Added a feature for accepting donations in Ethereum (ETH). Improved help section.

= 2.4.3 =
* Bugfixes and improvements of the payments module. Some code rewritten in OOP.

= 2.4.2 =
* Minor improvements of the payments module.

= 2.4.1 =
* Minor improvements.

= 2.4 =
* Added a basic feature for accepting payments in BTC.

= 2.3.4 =
* Added support for multiple charts per page. Added Bitcoin Cash (BCC / BCH) cryptocurrency with its icons supported.

= 2.3.3 =
* Added 30 more cryptocurrencies with their icons supported: dgb, iot, btcd, xpy, prc, craig, xbs, ybc, dank, give, kobo, geo, ac, anc, arg, aur, bitb, blk, xmy, moon, sxc, qtl, btm, bnt, cvc, pivx, ubq, lenin, bat, plbt

= 2.3.2 =
* Added feature to support custom CSS. Fixed minor bugs.

= 2.3.1 =
* Added feature to show only calculator or prices table. Added compatibility mode for servers without CURL support. Fixed minor bugs. 

= 2.3 =
* Changed plugin name. Added better widget support. Improved plugin administration. Improved readme. 

= 2.2 =
* Added coins list feature. Improved plugin code architecture. 

= 2.1.1 =
* Improved price formatting and support of currencies with smaller prices. Added Lana coin icon.

= 2.1 =
* Added cryptocurrency charts feature. Added icons for many currencies: GBP, JPY, XRP, DASH, ZEC, etc.

= 2.0 =
* Major release with many new features: more cryptocurrencies, fiat currencies support, cryptocurrency donations support, counterparty assets explorer support. The new version is backward compatible - you need to update!

= 1.1 =
* Bugs fixed - you need to update.

= 1.0 =
* Plugin released.  Everything is new!

== Upgrade Notice ==

### No upgrades yet. ###