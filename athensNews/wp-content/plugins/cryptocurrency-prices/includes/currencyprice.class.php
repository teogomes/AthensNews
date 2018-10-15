<?php
class CPCurrencyInfo {
  
  public static function cp_currencyprice_shortcode( $atts ) {
    if (isset($atts['currency1']) and $atts['currency1']!=''){
      //set first currency
      $currency1 = $atts['currency1'];
      //set default data for first currency
      $currency1_data = self::cp_prepare_currency_data($currency1, 1); 
      
      //set second currency
      if (isset($atts['currency2']) and $atts['currency2']!=''){
        $currency2_arr = explode(',', $atts['currency2']);
      } else {
        $currency2_arr = array('usd');
      }
      
      //set active shortcode feature
      if (isset($atts['feature']) and $atts['feature']!=''){
        $feature = $atts['feature'];
      } else {
        $feature = 'all';
      }
      
      $html_prices = '';
      $html_calc = '';
      
      //get data about cryptocurrencies
      $data_json = self::cp_convert_currency($currency1_data['name'], $currency2_arr);
      
      if (isset($data_json) and $data_json!=''){
        $data_all_currencies_raw = json_decode($data_json, true);
        $data_all_currencies = array();
        //prepare data for easy search
        foreach ($data_all_currencies_raw as $data_all_currencies_raw_key => $data_all_currencies_raw_value){
          $key_lower = trim(mb_strtolower($data_all_currencies_raw_key));
          $data_all_currencies[$key_lower] = $data_all_currencies_raw_value;
        }
        
        $html_calc_id = $currency1_data['name'].'_calc';
        
        $html_prices .= '<h2>'.mb_strtoupper($currency1_data['name']).' '.__('price', 'cryprocurrency-prices').':</h2>';  
        $html_prices .= '<table class="prices-table">';
    
        $html_calc .= '
          <h2>'.mb_strtoupper($currency1_data['name']).' '.__('calculator', 'cryprocurrency-prices').':</h2>
          <form id="'.$html_calc_id.'">
          <input type="text" class="currency1value" value="1" /> '.$currency1_data['name'].' 
          = 
          <input type="text" class="currency2value" value="?" />  
        ';
        $html_calc .= '<select class="currency_switcher">';
        
        foreach ($currency2_arr as $currency2) {  
          $currency2_filtered = trim(mb_strtolower($currency2));
          $currency2_data = self::cp_prepare_currency_data($currency2_filtered, $data_all_currencies[$currency2_filtered]);
          
          $html_prices .= self::cp_render_price($currency1_data, $currency2_data);
    
          $html_calc .= self::cp_render_calc_option($currency1_data, $currency2_data);
        }
        $html_prices .= '</table>';
        
        $html_calc .= '</select>';
        $html_calc .= '</form>';
    
        //generate javascript for the calculator      
        $html_calc .= '
          <script type="text/javascript">
            function setCalculatorValue'.$html_calc_id.'(){
              var currency1valueold = jQuery("#'.$html_calc_id.' .currency1value").val();
              var currency2valueunit = jQuery("#'.$html_calc_id.' .currency_switcher").val();
              jQuery("#'.$html_calc_id.' .currency2value").val(currency1valueold*currency2valueunit);
            }
            function setCalculatorValue2'.$html_calc_id.'(){
              var currency2valueold = jQuery("#'.$html_calc_id.' .currency2value").val();
              var currency2valueunit = jQuery("#'.$html_calc_id.' .currency_switcher").val();
              jQuery("#'.$html_calc_id.' .currency1value").val(currency2valueold/currency2valueunit);
            }
            setCalculatorValue'.$html_calc_id.'(); //call at start
            jQuery("#'.$html_calc_id.' .currency1value").keyup(setCalculatorValue'.$html_calc_id.');
            jQuery("#'.$html_calc_id.' .currency_switcher").change(setCalculatorValue'.$html_calc_id.');
            jQuery("#'.$html_calc_id.' .currency2value").keyup(setCalculatorValue2'.$html_calc_id.');
          </script>
        ';
        
      } else {
        $error = 'Error: No data from the server!';
      }
      
    } else {
      $error = 'Error: No currency is set!';
    }
    
    //prepate final data
    $html = '';
    if (!isset($error) or !$error){
      if ($feature == 'calculator' or $feature == 'all'){
        $html .= $html_calc;
      }
      if ($feature == 'prices' or $feature == 'all'){
        $html .= $html_prices;
      }
    } else {
      $html = $error;
    }
    
    $html .= CPCommon::cp_get_plugin_credit('cryptocompare');
    
  	return $html;
  }

  public static function cp_currencygraph_shortcode( $atts ) {
    global $cp_chart_js_loaded;
    $html = '';
    
    //load javascript library
    wp_enqueue_script( "gcharts", "https://www.gstatic.com/charts/loader.js" );
  
    if (isset($atts['currency1']) and $atts['currency1']!=''){
      $currency1 = $atts['currency1'];
      
      if (isset($atts['currency2']) and $atts['currency2']!=''){
        $currency2 = $atts['currency2'];
      } else {
        $currency2 = array('btc');
      }
  
      //generate random chart id
      $chart_id = rand(1000,9999);
  
      //check if library is loaded
      if (!$cp_chart_js_loaded){   
                
        //load javascript functions
        $html .= '
        <script type="text/javascript">
          function setCandlestickPeriod(candlestickChartDataOptions, period){
            if (period == "1hour"){
              candlestickChartDataOptions.group_by = "minute";
              candlestickChartDataOptions.data_points = 20;
              candlestickChartDataOptions.aggregate = 3;
            } else if (period == "24hours"){
              candlestickChartDataOptions.group_by = "hour";
              candlestickChartDataOptions.data_points = 24;
              candlestickChartDataOptions.aggregate = 1;
            } else if  (period == "30days"){
              candlestickChartDataOptions.group_by = "day";
              candlestickChartDataOptions.data_points = 30;
              candlestickChartDataOptions.aggregate = 1;        
            } else if  (period == "1year"){
              candlestickChartDataOptions.group_by = "day";
              candlestickChartDataOptions.data_points = 73;
              candlestickChartDataOptions.aggregate = 5;        
            }
          }
          
          function candlestickLoadData(candlestickChartDataOptions, chart_id){ 
            var candlestickDataUrl = "https://min-api.cryptocompare.com/data/"+
              "histo"+candlestickChartDataOptions.group_by+
              "?fsym="+candlestickChartDataOptions.currency1+
              "&tsym="+candlestickChartDataOptions.currency2+
              "&limit="+candlestickChartDataOptions.data_points+
              "&aggregate="+candlestickChartDataOptions.aggregate+
              "&e=CCCAGG";
            jQuery.get(candlestickDataUrl, function( rawData ) {
              console.log("Data loaded");
              
              //reset any old data
              var candlestickChartData = [];
              rawData.Data.forEach(function(rawDataSingle) {
                var singleDateTime = convertCandlestickTime(candlestickChartDataOptions, rawDataSingle.time);
                candlestickChartData.push([singleDateTime, rawDataSingle.low, rawDataSingle.open, rawDataSingle.close, rawDataSingle.high]);
              });
            
              google.charts.load("current", {"packages":["corechart"]});
              google.charts.setOnLoadCallback( function(){drawChart(candlestickChartDataOptions, candlestickChartData, chart_id);} );
            });
          }
          
          function drawChart(candlestickChartDataOptions, candlestickChartData, chart_id) {
            var data = google.visualization.arrayToDataTable(candlestickChartData, true);
            var options = {
              legend:"none",
              title:candlestickChartDataOptions.currency1+" price in "+candlestickChartDataOptions.currency2,
              bar: { groupWidth: "70%" }, // sets space between bars
              candlestick: {
                fallingColor: { strokeWidth: 0, fill: "#a52714" }, // red
                risingColor: { strokeWidth: 0, fill: "#0f9d58" }   // green
              }
            };
            var chart = new google.visualization.CandlestickChart(document.getElementById(chart_id));
            
            chart.draw(data, options);
          }
          
          function convertCandlestickTime(candlestickChartDataOptions, UNIX_timestamp){
            var a = new Date(UNIX_timestamp * 1000);
            var year = a.getFullYear();
            var month = dateFormatNumber(a.getMonth()+1, 2);
            var date = dateFormatNumber(a.getDate(), 2);
            var hour = dateFormatNumber(a.getHours(), 2);
            var min = dateFormatNumber(a.getMinutes(), 2);
            var sec = dateFormatNumber(a.getSeconds(), 2);
    
            if (candlestickChartDataOptions.group_by == "minute"){
              var time = hour+":"+min;
            } else if (candlestickChartDataOptions.group_by == "hour"){
              var time = hour+":"+min+" "+date+"."+month;
            } else {
              var time = date+"."+month+"."+year;
            }
            
            return time;
          }
          
          function dateFormatNumber(n, p, c) {
            var pad_char = typeof c !== "undefined" ? c : "0";
            var pad = new Array(1 + p).join(pad_char);
            return (pad + n).slice(-pad.length);
          }
        </script>      
        ';
        
        //set flag - chart js is loaded
        $cp_chart_js_loaded = 1;
      }
      
      //generate javascript for the graphic      
      $html .= '
        <script type="text/javascript">
          candlestickChartDataOptions_'.$chart_id.' = {
            currency1 : "'.mb_strtoupper($currency1).'", 
            currency2 : "'.mb_strtoupper($currency2).'", 
            group_by: "day", 
            data_points: 30, 
            aggregate: 1
          };
          
          jQuery( document ).ready(function() {
            setCandlestickPeriod(candlestickChartDataOptions_'.$chart_id.', "30days");
            candlestickLoadData(candlestickChartDataOptions_'.$chart_id.', "'.$chart_id.'");
            
            jQuery( "select#chart_period_'.$chart_id.'" ).change(function() {
              setCandlestickPeriod(candlestickChartDataOptions_'.$chart_id.', jQuery(this).val());
              candlestickLoadData(candlestickChartDataOptions_'.$chart_id.', "'.$chart_id.'");
            });
          });
        </script>
      ';
  
      //generate html for the graphic    
      $html .= '
        <div class="chart_wrap">
          <div class="chart_options">
            <form>
              <label>'.__('Select interval', 'cryprocurrency-prices').':</label>
              <select name="chart_period" id="chart_period_'.$chart_id.'">
                <option value="1hour">'.__('1 hour', 'cryprocurrency-prices').'</option>
                <option value="24hours">'.__('24 hours', 'cryprocurrency-prices').'</option>
                <option value="30days" selected="selected">'.__('30 days', 'cryprocurrency-prices').'</option>
                <option value="1year">'.__('1 year', 'cryprocurrency-prices').'</option>
              </select>
            </form>
          </div>
          <div id="'.$chart_id.'"></div>
        </div>
      ';
      
      //discard old data
      unset($data_json);
    } else {
      $html .= 'Error: No currency is set!';
    }
    
    $html .= CPCommon::cp_get_plugin_credit('cryptocompare');
    
  	return $html;
  }

  public static function cp_all_currencies_shortcode($atts){
    $html = '';
    
    if (isset($atts['algorithm']) and $atts['algorithm']=='no'){
      $display_algorithm = 0;
    } else {
      $display_algorithm = 1;
    }
    if (isset($atts['supply']) and $atts['supply']=='no'){
      $display_supply = 0;
    } else {
      $display_supply = 1;
    }
    if (isset($atts['url']) and $atts['url']=='yes'){
      $display_url = 1;
    } else {
      $display_url = 0;
    }
  
    $data_url = 'https://www.cryptocompare.com/api/data/coinlist/';
    //send api request
    $data_json = CPCommon::cp_get_url_data_curl($data_url);
    $data_all_currencies_raw = json_decode($data_json, true);
    $data_all_currencies = $data_all_currencies_raw['Data'];
    
    //sort currencies by order
    usort($data_all_currencies, array('CPCurrencyInfo', 'sortByOrder') );
    
    $html .= '<table class="cryptocurrencies-table">';
    $html .= '<tr>';
    $html .= '<th>'.__('Coin', 'cryprocurrency-prices').'</th>';
    if ($display_algorithm){ $html .= '<th>'.__('Algorithm; Proof type', 'cryprocurrency-prices').'</th>'; }
    if ($display_supply){ $html .= '<th>'.__('Total supply', 'cryprocurrency-prices').'</th>'; }
    $html .= '</tr>';
    
    foreach ($data_all_currencies as $data_currency){
      $picture = CPCommon::cp_get_currency_image($data_currency['Name']);
      
      if ( isset($data_currency['TotalCoinSupply']) && $data_currency['TotalCoinSupply']!= 0 ){
        $total_supply = htmlspecialchars($data_currency['TotalCoinSupply']);
      } else {
        $total_supply = '-';
      }
      
      if ($display_url){
        $url_start = '<a href="https://www.cryptocompare.com'.$data_currency['Url'].'" target="_blank">';
        $url_end = '</a>';
      } else {
        $url_start = '';
        $url_end = '';
      }
      
      $html .=  '<tr>';
      $html .=  '
        <td>
          '.$url_start.'  
          <img src="'.$picture.'" alt="'.htmlspecialchars($data_currency['FullName']).'" />
          '.htmlspecialchars($data_currency['FullName']).'
          '.$url_end.'
        </td>
      ';      
      if ($display_algorithm){
        $html .=  '<td>'.htmlspecialchars($data_currency['Algorithm']).'; '.htmlspecialchars($data_currency['ProofType']).' </td>';      
      }
      if ($display_supply){
        $html .=  '<td>'.$total_supply.'</td>';
      }
      $html .=  '</tr>';  
    }
    
    $html .= '</table>';
    
    $html .= CPCommon::cp_get_plugin_credit('cryptocompare');
    
    return $html;
  }

  public static function cp_convert_currency($currency1, $currency2_arr){
    //prepate api url
    $data_url_currency_1 = trim(mb_strtoupper($currency1));
    $data_url_currency_2 = '';
    foreach ($currency2_arr as $currency2) {
      if ($data_url_currency_2 != ''){
        $data_url_currency_2 .= ',';
      }
      $data_url_currency_2 .= trim(mb_strtoupper($currency2));
    }    
    $data_url = 'https://min-api.cryptocompare.com/data/price?fsym='.$data_url_currency_1.'&tsyms='.$data_url_currency_2;
    //send api request
    $data_json = CPCommon::cp_get_url_data_curl($data_url);

    return $data_json; 
  }
  
  private static function cp_render_price($currency1, $currency2) {  
    //draws the actual ticker prices for table
  
    $picture1 = CPCommon::cp_get_currency_image($currency1['name']);
    $picture2 = CPCommon::cp_get_currency_image($currency2['name']);
    
    //calculate the price
    $price_per_unit = self::cp_calculate_price_per_unit($currency1['price'], $currency2['price']);
    if ($price_per_unit >= 10000){
      $price_per_unit_string = number_format(round($price_per_unit, 4), 4, '.', '');
    } elseif ($price_per_unit >= 1000){
      $price_per_unit_string = number_format(round($price_per_unit, 5), 5, '.', '');
    } elseif ($price_per_unit >= 100){
      $price_per_unit_string = number_format(round($price_per_unit, 6), 6, '.', '');
    } elseif ($price_per_unit >= 10){
      $price_per_unit_string = number_format(round($price_per_unit, 7), 7, '.', '');  
    } else {
      $price_per_unit_string = number_format(round($price_per_unit, 8), 8, '.', '');
    }
    
    $result = '
      <tr>
    		<td>
    			<img src="'.$picture1.'" title="'.$currency1['name'].'" class="crypto-ticker-icon" />
          1 '.mb_strtoupper($currency1['name']).' = 
    		</td>
    		<td>
          <img src="'.$picture2.'" title="'.$currency2['name'].'" class="crypto-ticker-icon" />
    			'.$price_per_unit_string.' '.mb_strtoupper($currency2['name']).'
    		</td>
      </tr>
    ';
  
    return $result;
  }

  private static function cp_render_calc_option($currency1, $currency2) {
    //select options for the calculator
    
    $price_per_unit = self::cp_calculate_price_per_unit($currency1['price'], $currency2['price']);
    
    $result = '<option value="'.$price_per_unit.'">'.$currency2['name'].'</option>';
    
    return $result;
  }
  
  private static function cp_calculate_price_per_unit($currency1, $currency2){
    //calculate the price
    if ($currency2 != 0){
      $price_per_unit = $currency1 / $currency2;
    } else {
      //error in the data, avoid diviion by zero 
      $price_per_unit = 0;
    }
    
    return $price_per_unit; 
  }
  
  private static function cp_prepare_currency_data($currency, $currency_price){
    $currency_data = array(
      'name' => trim(mb_strtolower($currency)),
    );
    
    if (!isset($currency_price) or $currency_price == 0 or $currency_price == null){
      //fix null price value
      $currency_data['price'] = 0;
    } else {
      //price is ok
      $currency_data['price'] = 1/$currency_price;
    }
    
    return $currency_data;
  }

  private static function sortByOrder($a, $b) {
    return $a['SortOrder'] - $b['SortOrder'];
  }

}