<?php

class AlkimAmazonTransactionHistoryRenderer extends AlkimAmazonHandler{
    
    public static function render($data){
        return '
        <div style="margin-bottom:10px;">
            <a href="'.xtc_href_link('modules.php', 'set=payment&module=am_apa').'" class="amzButton">'.AMZ_BACK.'</a>
        </div>
        <div style="background:#741212; padding:10px; color:#fff; font-size:16px; font-weight:bold; font-family:Verdana, Arial; width:1180px; margin-bottom:10px;">
            '.AMZ_TRANSACTION_HISTORY.'
        </div>
        <table id="transactionHistoryTable" cellspacing="0">
            <thead>
            <tr class="thRow">
                <th>'.AMZ_TX_TIME_HEADING.' 
                    <span class="thSorter" data-sort="a.amz_tx_time ASC">&uArr;</span> 
                    <span class="thSorter active" data-sort="a.amz_tx_time DESC">&dArr;</span>
                </th>
                <th>'.AMZ_TX_TYPE_HEADING.'</th>
                <th>'.AMZ_TX_STATUS_HEADING.'</th>
                <th>
                    '.AMZ_TX_AMOUNT_HEADING.'
                    <span class="thSorter" data-sort="a.amz_tx_amount ASC">&uArr;</span> 
                    <span class="thSorter" data-sort="a.amz_tx_amount DESC">&dArr;</span>
                </th>
                <th>'.AMZ_TX_ID_HEADING.'</th>
                <th>'.AMZ_ORDER_REF_HEADING.'</th>
                <th>'.AMZ_TX_LAST_CHANGE_HEADING.'</th>
                <th>
                    '.AMZ_ORDERS_ID_HEADING.'
                    <span class="thSorter" data-sort="o.orders_id ASC">&uArr;</span> 
                    <span class="thSorter" data-sort="o.orders_id DESC">&dArr;</span>
                </th>
            <tr>
            <tr class="thRow">
                <td nowrap>
                    '.self::renderSearchInput('time').'
                </td>
                <td>
                    '.self::renderSearchInput('type').'
                </td>
                <td>
                    '.self::renderSearchInput('status').'
                </td>
                <td>
                    '.self::renderSearchInput('amount').'
                </td>
                <td>
                    '.self::renderSearchInput('tx_id').'
                </td>
                <td>
                    '.self::renderSearchInput('order_ref').'
                </td>
                <td nowrap>
                    '.self::renderSearchInput('last_change').'
                </td>
                <td>
                    '.self::renderSearchInput('orders_id').'
                </td>
             </tr>
             </thead>
             <tbody>
                '.self::getTableBody($data).'
             </tbody>
         </table>
         <div style="margin-top:10px;">
            <a href="'.xtc_href_link('modules.php', 'set=payment&module=am_apa').'" class="amzButton">'.AMZ_BACK.'</a>
        </div>';
    }
    
    public static function getTableBody($data, $perPage = 40){
        
        $ret = '';
        
        $whereArr = array();
        if(isset($data["time_from"]) && $data["time_from"] != ''){
            $startTime = strtotime($data["time_from"].' 00:00:00');
            $whereArr[] = " a.amz_tx_time >= ".$startTime." ";
        }
        if(isset($data["time_to"]) && $data["time_to"] != ''){
            $endTime = strtotime($data["time_to"].' 23:59:59');
            $whereArr[] = " a.amz_tx_time <= ".$endTime." ";
        }
        if(isset($data["last_change_from"]) && $data["last_change_from"] != ''){
            $startTime = strtotime($data["last_change_from"].' 00:00:00');
            $whereArr[] = " a.amz_tx_last_change >= ".$startTime." ";
        }
        if(isset($data["last_change_to"]) && $data["last_change_to"] != ''){
            $endTime = strtotime($data["last_change_to"].' 23:59:59');
            $whereArr[] = " a.amz_tx_last_change <= ".$endTime." ";
        }
        if(isset($data["type"]) && $data["type"] != ''){
            $whereArr[] = " a.amz_tx_type LIKE '%".xtc_db_input($data["type"])."%' ";
        }
        if(isset($data["status"]) && $data["status"] != ''){
            $whereArr[] = " a.amz_tx_status LIKE '%".xtc_db_input($data["status"])."%' ";
        }
        if(isset($data["amount"]) && $data["amount"] != ''){
            $whereArr[] = " a.amz_tx_amount LIKE '".xtc_db_input(str_replace(',', '.', $data["amount"]))."%' ";
        }
        if(isset($data["tx_id"]) && $data["tx_id"] != ''){
            $whereArr[] = " a.amz_tx_amz_id LIKE '%".xtc_db_input($data["tx_id"])."%' ";
        }
        if(isset($data["order_ref"]) && $data["order_ref"] != ''){
            $whereArr[] = " a.amz_tx_order_reference LIKE '%".xtc_db_input($data["order_ref"])."%' ";
        }
        if(isset($data["orders_id"]) && $data["orders_id"] != ''){
            $whereArr[] = " (o.orders_id LIKE '%".xtc_db_input($data["orders_id"])."%'
                                OR
                            o.customers_firstname LIKE '%".xtc_db_input($data["orders_id"])."%'
                                OR
                            o.customers_lastname LIKE '%".xtc_db_input($data["orders_id"])."%'
                            ) ";
        }
       
        $where = '';
        if(count($whereArr) > 0){
            $where = ' WHERE '.implode(' AND ', $whereArr)." ";
        }
        
        
        $numQ = "SELECT COUNT(*) AS num FROM
                       amz_transactions a
                       LEFT JOIN orders o ON (o.amazon_order_id = a.amz_tx_order_reference)
                       ".$where."
                 ";
        $numRs = xtc_db_query($numQ);                  
        $numR = xtc_db_fetch_array($numRs);
        
        $num = (int)$numR["num"];
        
        $page = (isset($data["page"])?(int)$data["page"]:1);
        
        $q = "SELECT * FROM
                        amz_transactions a
                        LEFT JOIN orders o ON (o.amazon_order_id = a.amz_tx_order_reference)
              ".$where."          
              ORDER BY ".(isset($data["sort"])?$data["sort"]:'amz_tx_time DESC')." 
              LIMIT ".(($page-1)*$perPage).", ".(int)$perPage;
             
        $rs = xtc_db_query($q);
        while($r = xtc_db_fetch_array($rs)){
        $ret .= '<tr>
                            <td>
                                '.date('Y-m-d H:i:s', $r["amz_tx_time"]).'
                            </td>
                            <td>
                                '.self::translateTransactionType($r["amz_tx_type"]).'
                            </td>
                            <td>
                                <span class="'.self::getClassForStatus($r["amz_tx_status"]).'">'.$r["amz_tx_status"].'</span>
                            </td>
                            <td>
                                '.self::formatAmount($r["amz_tx_amount"]).'
                            </td>
                            <td>
                                '.$r["amz_tx_amz_id"].'
                            </td>
                            <td>
                                '.$r["amz_tx_order_reference"].'
                            </td>
                            
                            
                            <td>
                                '.date('Y-m-d H:i:s', $r["amz_tx_last_change"]).'
                            </td>
                            
                            <td>
                                <a href="'.xtc_href_link('orders.php', 'oID='.$r["orders_id"].'&action=edit').'">'.$r["orders_id"].' ('.$r["customers_firstname"].' '.$r["customers_lastname"].')</a>
                            </td>
                      </tr>';
        
        }
        
        $pages = ceil($num/$perPage);
        $nav = '';
        for($i = 1; $i<=$pages; $i++){
            $nav .= '<a href="#" data-page="'.$i.'" class="pageLink">'.($i == $page?'<b>':'').$i.($i == $page?'</b>':'').'</a> ';
        }
        $ret .= '<tr>
                    <td colspan="8" class="navi">
                        '.$nav.'
                    </td>
                 </tr>';
        return $ret;
        
    }
    
    public static function renderSearchInput($name){
        $html = '';
        
        switch($name){
            case 'type':
                $html = '<select class="transactionFilterInput" name="'.$name.'">
                            <option value=""></option>';
                $types = array('auth', 'order_ref', 'capture', 'refund');
                foreach($types AS $type){
                    $html .= '<option value="'.$type.'">'.self::translateTransactionType($type).'</option>';
                }
                $html .= '</select>';
                break;
            case 'time':
                $html = xtc_draw_input_field('time_from', $data['time_from'], ' class="transactionFilterInput datepicker timeInput" ').
                        '-'.
                        xtc_draw_input_field('time_to', $data['time_from'], ' class="transactionFilterInput datepicker timeInput" ');
                break;  
            case 'last_change':
                $html = xtc_draw_input_field('last_change_from', $data['last_change_from'], ' class="transactionFilterInput datepicker timeInput" ').
                        '-'.
                        xtc_draw_input_field('last_change_to', $data['last_change_from'], ' class="transactionFilterInput datepicker timeInput" ');
                break;        
            case 'status':
                $html = '<select class="transactionFilterInput" name="'.$name.'">
                            <option value=""></option>';
                $q = "SELECT DISTINCT amz_tx_status FROM amz_transactions";
                $rs = xtc_db_query($q);
                $arr = array();
                while($r = xtc_db_fetch_array($rs)){
                    $arr[] = $r["amz_tx_status"];
                }
                
                foreach($arr AS $status){
                    $html .= '<option value="'.$status.'">'.$status.'</option>';
                }
                $html .= '</select>';
                break;    
            

                
            default:
                $html = xtc_draw_input_field($name, $data[$name], ' class="transactionFilterInput" ');
                break;
        }
        return $html;
    }

}
