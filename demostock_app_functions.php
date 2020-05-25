<?php 
class Demostock_app extends Connection
{
/*===========================================================
					Parameter Validation
===========================================================*/

	function param_validation($paramarray,$data)
	{
		$NovalueParam = '';
		foreach($paramarray as $val)
		{
			if(!array_key_exists($val,$data))
			{				
				$NovalueParam[] = $val; 
			}
		}
		if(is_array($NovalueParam) && count($NovalueParam)>0)
		{
			$returnArr['error'] = true;
			$returnArr['msg'] = 'Sorry, that is not valid input. You missed '.implode(',',$NovalueParam).' parameters';
			return $returnArr;
		}
		else
		{ 
			return false;
		}
	}
/*===========================================================
					Search
http://localhost/demostock-api/demostock_app.php?action=search					
===========================================================*/
	 function search($data)
	 {
		 
		$user_data=array('customer_Id','shape','color','clarity','discount');		
		$validation=$this->param_validation($user_data,$data); 
         
		if($validation)
		    return $validation;

			if($data['customer_Id'] == "")
				 $data['customer_Id'] = "CUS100700007"; // set default customer id

			// Check customer exists in price quote table or not	
		    $is_customer_exist = "SELECT * FROM customer_price_quote WHERE dpq_customer_id = '".$data['customer_Id']."'";
			$customer_result = $this->qry($is_customer_exist,3);

			if($customer_result > 0){
				$paramQuery = "SELECT * FROM customer_price_quote_parameters WHERE (1=1)";
				$where = "";
				$cond  = " AND ";
				$customerData = [];
				$is_customer_quote_exits = true;
				if($data['shape'] != "")
				{
					$shapeData = explode(',', $data['shape']);
					foreach($shapeData as $shape){
						$where .= $cond." (".$shape."  BETWEEN dp_shape_from AND dp_shape_to )";
						$cond = " OR ";
					}

					// $shape_result = $this->qry($paramQuery . $where,3);
					if($this->qry($paramQuery . $where,3) > 0 ){
							$customerData['shape'] = $data['shape'];
							$is_customer_quote_exits = true;
						}else
						$is_customer_quote_exits = false;
					$where = "";
					$cond  = " AND ";
				}
				if($data['color'] != "")
				{
					$colorData = explode(',', $data['color']);
					foreach($colorData as $color){
						$where .= $cond." (".$color."  BETWEEN dp_color_from AND dp_color_to )";
						$cond = " OR ";
					}
					// $color_result = $this->qry($paramQuery . $where,3);
					if($this->qry($paramQuery . $where,3) > 0 ){
							$customerData['color'] = $data['color'];
							$is_customer_quote_exits = true;
						}else
						$is_customer_quote_exits = false;
					$where = "";
					$cond  = " AND ";
				}
				// if($data['size'] != "")
				// {
				// 	$where .= $cond."(".$data['shape']."  BETWEEN dp_shape_from AND dp_shape_to )";
				// 	$cond = " OR ";
				// }
				if($data['clarity'] != "")
				{
					$clarityData = explode(',', $data['clarity']);
					foreach($clarityData as $clarity){
						$where .= $cond." (".$clarity."  BETWEEN dp_clarity_from AND dp_clarity_to )";
						$cond = " OR ";
					}

					// $clarity_result = $this->qry($paramQuery . $where,3);
					if($this->qry($paramQuery . $where,3) > 0 ){
							$customerData['clarity'] = $data['clarity'];
							$is_customer_quote_exits = true;
						}else
						$is_customer_quote_exits = false;
					$where = "";
					$cond  = " AND ";
			
				}
				if($data['discount'] != "")
				{

					$discountData = explode(',', $data['discount']);
					foreach($discountData as $discount){
						$where .= $cond." (".$discount."  BETWEEN dp_discount_from AND dp_discount_to )";
						$cond = " OR ";
					}

					// $discount_result = $this->qry($paramQuery . $where,3);
					if($this->qry($paramQuery . $where,3) > 0 ){
							$customerData['discount'] = $data['discount'];
							$is_customer_quote_exits = true;
						}else
						$is_customer_quote_exits = false;
					$where = "";
					$cond  = " AND ";
				}

				 if(!empty($customerData)){
				 	// echo '<pre>'; print_r($customerData); die;
				 	 if( $is_customer_quote_exits){
				 	 	$return_arr = $this->getDiamondData($customerData);
				 	 }else{
				 	 	$return_arr['flag'] = true;
						$return_arr['data'] = [];
				 	}
				 }else
				 {
					$return_arr['flag'] = true;
					$return_arr['data'] = [];
				 }
			}else{
				$return_arr = $this->getDiamondData($data);
			}

			return $return_arr;
	 }

/*===========================================================
					Function for get diamond parameter					
===========================================================*/

	public function getDiamondData($data)
	{
		$paramQuery = "SELECT dp_parameters_code,dp_table_type FROM diamond_parameters WHERE ( 1 = 1 )";
		$where = "";
		$cond  = " AND ";
		if($data['shape'] != "")
		{
			$where .= $cond." (dp_table_type = 0 AND dp_position IN (".$data['shape'].") )";
			$cond = " OR ";
		}
		if($data['color'] != "")
		{
			$where .= $cond." ( dp_table_type = 2 AND dp_position IN (".$data['color'].") )";
			$cond = " OR ";
		}
		if($data['size'] != "")
		{
			$where .= $cond." (dp_table_type = 43 AND dp_position IN (".$data['size'].") )";
			$cond = " OR ";
		}
		if($data['clarity'] != "")
		{
			$where .= $cond." (dp_table_type = 3 AND dp_position IN (".$data['clarity'].") )";
			$cond = " OR ";
		}
		if($data['discount'] != "")
		{
			$where .= $cond." (dp_table_type = 0 AND dp_position IN (".$data['discount'].") )";
		}

		$resources = $this->qry($paramQuery . $where,2);
		// echo '<pre>';  print_r($resources); die;
		$tableTypeArray = array_column($resources, 'dp_table_type');
	 	$arrangeArray = array_column($resources, 'dp_parameters_code');
		// $imp = "'" . implode( "','", ($arrangeArray) ) . "'";
		foreach($resources as $key=>$value)
		{
			if($value['dp_table_type'] == 0 )
			{
				$shapeData[] = $value['dp_parameters_code'];
			}
			if($value['dp_table_type'] == 2 )
			{
				$colorData[] = $value['dp_parameters_code'];
			}
			if($value['dp_table_type'] == 3 )
			{
				$clarityData[] = $value['dp_parameters_code'];
			}
			// if($value['dp_table_type'] == 0 )
			// {
			// 	$shapeData[] = $value['dp_parameters_code'];
			// }

		}

		 $stockDataQuery = "SELECT * FROM stock WHERE 1=1 ";
		 // st_shape IN ("."'" . implode( "','", ($shapeData) ) . "'".")  AND st_col IN ("."'" . implode( "','", ($colorData) ) . "'".") AND st_size = '".$data['size']."' AND st_cla IN ("."'" . implode( "','", ($clarityData) ) . "'".") AND st_dis = '".$data['discount']."'  " ;

		$where = "";
		
		if(!empty($shapeData)){
			$where .=  " AND st_shape IN ("."'" . implode( "','", ($shapeData) ) . "'".")";
		}
		if(!empty($colorData)){
			$where .=  " AND st_col IN ("."'" . implode( "','", ($colorData) ) . "'".")";
		}
		if(!empty($clarityData)){
			$where .=  " AND st_cla IN ("."'" . implode( "','", ($clarityData) ) . "'".")";
		}
		if($data['size'] != ''){
			$where .=  " AND st_size = '".$data['size']."' ";
		}
		if($data['discount'] != ''){
			$where .=  " AND st_dis = '".$data['discount']."' ";
		}
		// echo $stockDataQuery. $where;
		$stockResources = $this->qry($stockDataQuery. $where,2);
		// echo $this->qry($stockDataQuery . $where,3);
		$return_arr['flag'] = true;
		$return_arr['data'] = !empty($stockResources) ? $stockResources : [];

		return $return_arr;
	}
}
?>