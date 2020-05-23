<?php 

class Connection
{
	public function Connection()
	{
		$host="http://".strtolower($_SERVER['HTTP_HOST']);
		
	
			$db_hostname = 'localhost';
			$db_username = 'root';
			$db_password = '';
			$db_database_name='demostock';


		$con = mysql_connect($db_hostname,$db_username,$db_password) or die(mysql_error());
		mysql_select_db($db_database_name,$con) or die(mysql_error());
		mysql_query("set names 'utf8'");
	}
	
	public function qry($sql,$return_format=0)
	{
		
		$res=mysql_query($sql);

		if(mysql_error())
		{
			$returnarr='';
			$returnarr['error']=true;
			$returnarr['msg']=mysql_error().$sql;
			return $returnarr;
		}
		switch($return_format)
		{
			case 1:
					return mysql_fetch_assoc($res);
					break;
			case 2:
					$ReturnArr='';
					if (@mysql_num_rows($res)> 0)
					{
						while($row=mysql_fetch_assoc($res))
							$ReturnArr[]=$row;
					}
					return $ReturnArr;
					break;
			case 3:
					return mysql_num_rows($res);
					break;
			case 4:
					return mysql_insert_id();
					break;
					
			case 5:
					while($row=mysql_fetch_array($res))
					{
						$return_row = $row;	
				  		return $return_row;
					}
					break;
					
			case 6: 
					return mysql_fetch_row($res);
					break;	
					
			case 7:
					while($row=mysql_fetch_array($res))
					{
						$return_row[] = $row;	
					}
					return $return_row;
					break;	
					
			default:
					return true;
		}
	}
}			