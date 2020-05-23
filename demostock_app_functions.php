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
				if($data['shape'] != "")
				{
					$where .= $cond." (".$data['shape']."  BETWEEN dp_shape_from AND dp_shape_to )";
					$cond = " OR ";
				}
				if($data['color'] != "")
				{
					$where .= $cond." (".$data['color']."  BETWEEN dp_color_from AND dp_color_to )";
					$cond = " OR ";
				}
				// if($data['size'] != "")
				// {
				// 	$where .= $cond."(".$data['shape']."  BETWEEN dp_shape_from AND dp_shape_to )";
				// 	$cond = " OR ";
				// }
				if($data['clarity'] != "")
				{
					$where .= $cond."(".$data['clarity']."  BETWEEN dp_clarity_from AND dp_clarity_to )";
					$cond = " OR ";
				}
				if($data['discount'] != "")
				{
					$where .= $cond." (".$data['discount']."  BETWEEN dp_discount_from AND dp_discount_to )";
				}

				 echo $paramQuery . $where; 
				$resources = $this->qry($paramQuery . $where,2);
					echo '<pre>'; print_r($resources); die;
				$arrangeArray = array_column($resources, 'dp_parameters_code');
			}else{
				$paramQuery = "SELECT dp_parameters_code FROM diamond_parameters WHERE ( 1 = 1 )";
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

				 // echo $paramQuery . $where; 
				$resources = $this->qry($paramQuery . $where,2);

				$arrangeArray = array_column($resources, 'dp_parameters_code');
				$imp = "'" . implode( "','", ($arrangeArray) ) . "'";
				$stockDataQuery = "SELECT * FROM stock WHERE st_shape IN (".$imp.")  OR st_col IN (".$imp.") OR st_size = '".$data['size']."' OR st_cla IN (".$imp.") OR st_dis = '".$data['discount']."'  " ;
				$stockResources = $this->qry($stockDataQuery,2);
				// echo $this->qry($stockDataQuery,3);
				$return_arr['flag'] = true;
				$return_arr['data'] = $stockResources;
			}

			return $return_arr;
	 }
	
}
?>