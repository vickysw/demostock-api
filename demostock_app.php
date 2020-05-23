<?php 
 error_reporting('E_ALL');
 include_once('connection.php');
  include_once('demostock_app_functions.php');

  $postData=true;
  if($postData)
  {
	  $json_data=$_REQUEST;

	  $demostock_app_function = new Demostock_app();
	  if($json_data['action'])
		{
			if (method_exists($demostock_app_function,$json_data['action']))
			{
				$result = $demostock_app_function->$json_data['action']($json_data);
			}
			else
			{
				$result = "Operation does not exists." ;
			}
		}
		 echo json_encode($result,JSON_HEX_TAG);
  }
?>