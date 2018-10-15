<?php
class CPCryptoPayment {

  public static function cp_cryptopayment_shortcode( $atts ) {
    global $wpdb;
    $table_name = $wpdb->prefix.'cp_orders';
    
    if (isset($atts['amount']) and $atts['amount']!=''){
      $currency = 'btc';
      
      //converat amount to target currency
      if (is_numeric($atts['amount'])){
        //amount is a number in the default currency
        $amount = (float)$atts['amount'];
      } else {
        //amout is not a number - need to process the amount 
        $amount_parts = explode(' ', $atts['amount']);
        
        if (isset($amount_parts[1])){
          //amount format seems correct
          $source_currency = trim(mb_strtolower($amount_parts[1]));
          $source_amount = (float)$amount_parts[0];
          
          if ($source_currency == $currency){ 
            //amount is in the selected currency
            $amount = $source_amount;
          } else {
            //amount is not in the selected currency
            //we need to convert the currency
            
            $data_json = CPCurrencyInfo::cp_convert_currency($source_currency, array($currency));

            if (isset($data_json) and $data_json!=''){
              $data_all_currencies_raw = json_decode($data_json, true);
              $rate = $data_all_currencies_raw[mb_strtoupper($currency)];
              
              $amount = $source_amount * $rate;
            } else {
              //API error
              //stop processing and throw an error
              $html = 'Error: API error!';
              return $html; 
            }
          }          
        } else {
          //payment amount error
          //stop processing and throw an error
          $html = 'Error: Payment amount error!';
          return $html;
        }
        
      }
      
      $item = htmlspecialchars($atts['item']);
      
      $html = '<div class="cp_payment">';
      
      if (isset($_POST['cp_name']) and $_POST['cp_name']!='' and isset($_POST['cp_item']) and $_POST['cp_item']==$item){
        //ready to accept payment
        
        $payment_address = htmlspecialchars($_POST['cp_payment_address']);
        
        //record the payment in the database 
        $insert_result = $wpdb->insert($table_name, array(
            'item' => $_POST['cp_item'],
            'price' => $_POST['cp_amount'],
            'currency' => $currency,
            'payment_address' => $_POST['cp_payment_address'],
            'name' => $_POST['cp_name'],
            'email' => $_POST['cp_email'],
            'address' => $_POST['cp_address'],
            'telephone' => $_POST['cp_telephone'],
            'description' => '',
        ));
        
        //send notification to the administrator
        $to = get_option('cryptocurrency-payment-notifications-email');
        $subject = 'Pending cryptocurrency payment';
        $body = 'A user has submitted an order on '.get_site_url().'. Visit the admin panel for more details about the order and the payment.';
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail( $to, $subject, $body, $headers );
        
        //payment address
        $html .= '
          <h2>'.__('Order submitted. Please make a payment:', 'cryprocurrency-prices').'</h2>
          <strong>
            '.__('To pay', 'cryprocurrency-prices').' '.$amount.' '.mb_strtoupper($currency).__(', scan the QR code or copy and paste the ', 'cryprocurrency-prices').mb_strtoupper($currency).__(' wallet address:', 'cryprocurrency-prices').'
          </strong> <br /><br />
          <span style="font-size: big;">'.$payment_address.'</span><br /><br />
          <img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=bitcoin:'.urlencode($payment_address).'&choe=UTF-8" /><br /><br />
        ';
      } else {
        //get the list of payment addresses
        $payment_addresses = get_option('cryptocurrency-payment-addresses');
        $payment_addresses_arr = explode(" ", $payment_addresses);
  
        //get the last used payment address
        $last_payment_information = $wpdb->get_row(
          'SELECT payment_address FROM '.$table_name.' ORDER BY id DESC LIMIT 1'
        );
        
        if ($last_payment_information){
          //last payment address found
          //use the next address, or go back to the first in the list
          
          $last_payment_address = $last_payment_information->payment_address;
          $last_payment_address_index = array_search($last_payment_address, $payment_addresses_arr);
  
          //use the next address from the list
          $new_payment_address_index = $last_payment_address_index + 1;
          if ($new_payment_address_index >= count($payment_addresses_arr)){
            //this is the last address - go back to the first one
            $new_payment_address_index = 0;
          }
          
          //$payment_address_randkey = array_rand($payment_addresses_arr);
          $payment_address = trim($payment_addresses_arr[$new_payment_address_index]);
        } else {
          //no information about the last payment address
          //use the first from the list
          $payment_address = trim($payment_addresses_arr[0]);
        }
        
        //payment details form
        $html .= '
          <h2>'.__('Please enter order details:', 'cryprocurrency-prices').'</h2>
          <form action="" method="post">
            <table border="0" class="cp_payment_form_table">
              <tr>
                <td><label>'.__('Item name:', 'cryprocurrency-prices').'</label></td>
                <td>
                  <input type="hidden" name="cp_payment_address" value="'.$payment_address.'" />
                  <input type="text" name="cp_item" value="'.$item.'" readonly />
                </td>
              </tr>
              <tr>
                <td><label>'.__('Order amount:', 'cryprocurrency-prices').'</label></td>
                <td>
                  <input type="text" name="cp_amount" value="'.$amount.' '.mb_strtoupper($currency).'" readonly />
                </td>
              </tr>
              <tr>
                <td><label>'.__('Name:', 'cryprocurrency-prices').'</label></td>
                <td><input type="text" name="cp_name" required /></td>
              </tr>
              <tr>
                <td><label>'.__('Email:', 'cryprocurrency-prices').'</label></td>
                <td><input type="text" name="cp_email" required /></td>
              </tr>
              <tr>
                <td><label>'.__('Address:', 'cryprocurrency-prices').'</label></td>
                <td><input type="text" name="cp_address" /></td>
              </tr>
              <tr>
                <td><label>'.__('Telephone:', 'cryprocurrency-prices').'</label></td>
                <td><input type="text" name="cp_telephone" /></td>
              </tr>
              <tr>
                <td colspan="2"><input type="submit" value="'.__('Proceed to payment', 'cryprocurrency-prices').'" /></td>
              </tr>
            </table>
          </form>
        ';
      }
    }
    
    $html .= '</div><!--.cp_payment-->';
    
    $html .= CPCommon::cp_get_plugin_credit();
    
    
    return $html;
  }

}

