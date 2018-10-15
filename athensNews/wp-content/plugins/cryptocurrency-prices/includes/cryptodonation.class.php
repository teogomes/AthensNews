<?php
class CPCryptoDonation {

  public static function cp_cryptodonation_shortcode( $atts ) {
    if (isset($atts['address']) and $atts['address']!=''){
      $default_currency = 'bitcoin';
      $donation_address = $atts['address'];
      if (isset($atts['paymentid'])){
        $payment_id = $atts['paymentid'];
      } else {
        $payment_id = '';
      }
      
      if (isset($atts['currency']) and $atts['currency']!=''){
        if (strcasecmp($atts['currency'], 'btc') == 0 or strcasecmp($atts['currency'], 'bitcoin') == 0){
          $donation_currency = 'bitcoin';  
        } elseif (strcasecmp($atts['currency'], 'eth') == 0 or strcasecmp($atts['currency'], 'ethereum') == 0){
          $donation_currency = 'ethereum';
        } elseif (strcasecmp($atts['currency'], 'ltc') == 0 or strcasecmp($atts['currency'], 'litecoin') == 0){
          $donation_currency = 'litecoin';
        } elseif (strcasecmp($atts['currency'], 'xmr') == 0 or strcasecmp($atts['currency'], 'monero') == 0){
          $donation_currency = 'monero';
        } elseif (strcasecmp($atts['currency'], 'zec') == 0 or strcasecmp($atts['currency'], 'zcash') == 0){
          $donation_currency = 'zcash';
        } else {
          $donation_currency = $default_currency;
        }
      } else {
        $donation_currency = $default_currency;      
      }
      
       
      if ($donation_currency != 'monero'){
        $html = '
          <p>
            <strong>
              '.__('To donate', 'cryprocurrency-prices').' '.$donation_currency.__(', scan the QR code or copy and paste the', 'cryprocurrency-prices').' '.$donation_currency.__(' wallet address:', 'cryprocurrency-prices').'
            </strong> <br /><br />
            <span class="donation-address" style="font-size: larger;">'.$donation_address.'</span><br /><br />
            <img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl='.$donation_currency.':'.urlencode($donation_address).'&choe=UTF-8" /><br /><br />
            <strong>'.__('Thank you!', 'cryprocurrency-prices').'</strong>
          </p>
        ';
      } elseif ($donation_currency == 'monero'){
        // special rules apply for monero: address and paymentid are necessary
        $html = '
          <p>
            <strong>To donate '.$donation_currency.', use the address and payment_id in the transfer command: "transfer 1 [Base Addresss] [amount] [Payment_Id]":</strong> <br /><br />
            <span class="donation-address" style="font-size: larger;"><strong>Address:</strong> '.$donation_address.'</span><br />
            <span class="donation-payment-id" style="font-size: larger;"><strong>Payment id:</strong> '.$payment_id.'</span><br /><br />
            <strong>Thank you!</strong>
          </p>
        ';
      }
    } else {
      $html = '<p>Error: Donation address missing!</p>';
    }
    
    $html .= CPCommon::cp_get_plugin_credit();
    
    return $html;
  }
}