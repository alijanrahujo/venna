<html>
  <head>
    <title>Untitled</title>
	
	<?php
        $company_id = 7; //isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        
        //  $aphone_no = "";isset($this->request_data['aphone_no']) ? $this->request_data['aphone_no'] : "";
          $description = "";
          $stock_quantity = 2;
          $start = 0;
          $count = 10;
          $where_query = array('id' > 0);
          $where_group_like_query = "";
          $where_group_or_like_query = "";
          $order_query = "";
  
          $user_list->Api_Model->get_datatables_list("vny_user_stock_auto", "user_id",$where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
          
          
          foreach ($user_list as $row) {
  
              //$ausername = $row['user_id'];
              // $uid = $this->Api_Model->get_rows_info(TBL_USER, "username", array('id' => $row['user_id'], 'active' => 1));
              $user_id = $row['user_id'];
         
             // $username = "";$this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
             
             $stock_balance->Api_Model->get_rows_info(TBL_STOCK, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'user_id' => $user_id));
             $total_credit = isset($stock_balance['total_credit']) ? $stock_balance['total_credit'] : 0;
             $total_debit = isset($stock_balance['total_debit']) ? $stock_balance['total_debit'] : 0;
             $available_stock_balance = $total_credit - $total_debit;
             
  
  
         
  
              if ($available_stock_balance <= 1) {
                  $new_stock_balance = 0;
              } else {
                  $new_stock_balance = $available_stock_balance - $stock_quantity;
              }
  
              $data_stock = array(
                      'user_id' => $user_id,
                      'company_id' => $company_id,
                      'description' => $description,
                      'balance' => $new_stock_balance
                  );
                
              
              $data_stock['debit'] = $stock_quantity;
              $this->Api_Model->insert_data(TBL_STOCK, $data_stock);
  
              // update total quantity to agent acc
              $data_user_update = array(
                      'total_stock' => $new_stock_balance
                  );
              $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_user_update);
		  }
			 echo "Success!";
	?>
  </head>
  <body>

  </body>
</html>
