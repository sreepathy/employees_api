<?php

if(!file_exists('.employees.db'))
{
	$db = new SQLite3(".employees.db") or die('{"error": "DB Connection failed"}');

	$query = 'create table empmaster(
	empid integer primary key,    
 	ename text not null,
 	date_of_birth text,
 	emailid text,
 	mobile text,
 	address text,
 	profilepic blob,
 	latt real,
 	long real
	)';

	echo "DB initializing: STATUS -> ";
 	var_dump($db->exec($query));
 	exit;

}
/* init queries */ 
$db = new SQLite3(".employees.db") or die('{"error": "DB Connection failed"}');

$P_action = '';
$P_empid = '';
$P_ename = '';
$P_dob = '';
$P_email = '';
$P_mobile = '';
$P_addr = '';
$P_profilepic = '';
$P_latt = '';
$P_long = '';


extract($_POST, EXTR_PREFIX_ALL, 'P');

switch($P_action)
{
	case 'CREATE':
		if(!is_numeric($P_empid))
		{
			$db->close();
			echo '{"error": "Invalid employee ID","code":"1" }';
			exit;
		}
		$query = "select count(*) as CNT from empmaster where empid = '{$P_empid}' ";
		$ret = $db->query($query);
		$row = $ret->fetchArray(SQLITE3_ASSOC);
		if($row['CNT'])
		{
			$db->close();

			echo '{"error": "Employee already exists","code":"1"}';
			exit;
		}
		if(!preg_match('/^[a-zA-Z ]{3,40}$/', $P_ename))
		{
			$db->close();
			echo '{"error": "Employee name is invalid","code":"2"}';
			exit;
		}
		if(!isValidDate($P_dob,'Y-m-d'))
		{
			$db->close();
			echo '{"error": "Employee DOB is invalid","code":"3"}';
			exit;
		}
		if(!filter_var($P_email, FILTER_VALIDATE_EMAIL) )
		{
			$db->close();
			echo '{"error": "Employee email is invalid","code":"4"}';
			exit;
		}
		if(!preg_match('/^[6-9]{1}[0-9]{9}$/', $P_mobile))
		{
			$db->close();
			echo '{"error": "Employee mobile is invalid","code":"5"}';
			exit;
		}
		if(!preg_match('/^[a-zA-Z0-9 \s\.\,\#\/]{10,300}$/', $P_addr))
		{
			$db->close();
			echo '{"error": "Employee address is invalid","code":"6"}';
			exit;
		}
		if(!is_numeric($P_latt))
		{
			$db->close();
			echo '{"error": "Employee location lattitude is invalid","code":"7"}';
			exit; 
		}
		if(!is_numeric($P_long))
		{
			$db->close();
			echo '{"error": "Employee location lattitude is invalid","code":"8"}';
			exit; 
		}
		$P_profilepic_decoded =base64_decode($P_profilepic);
		$mimetype = getImageMimeType($P_profilepic_decoded);
		if($mimetype != 'jpeg')
		{
			$db->close();
			echo '{"error": "Employee image type is invalid ['.$mimetype.']","code":"9"}';
			exit;
		}
		else if(strlen($P_profilepic_decoded) > 300*1024)
		{
			$db->close();
			echo '{"error": "Employee image is too big","code":"9"}';
			exit;
		}

		$query = "insert into empmaster(empid,ename,date_of_birth,emailid,mobile,address,profilepic,latt,long)
		values(:empid, :ename, :dob, :email, :mobile, :addr, :profilepic,:latt,:long)";
		if($sth = $db->prepare($query))
		{
			$sth->bindValue(':empid', $P_empid, SQLITE3_TEXT);
			$sth->bindValue(':ename', $P_ename, SQLITE3_TEXT);
			$sth->bindValue(':dob', $P_dob, SQLITE3_TEXT);
			$sth->bindValue(':email', $P_email, SQLITE3_TEXT);
			$sth->bindValue(':mobile', $P_mobile, SQLITE3_TEXT);
			$sth->bindValue(':addr', $P_addr, SQLITE3_TEXT);
			$sth->bindValue(':profilepic', $P_profilepic, SQLITE3_BLOB );
			$sth->bindValue(':latt', $P_latt, SQLITE3_FLOAT);
			$sth->bindValue(':long', $P_long, SQLITE3_FLOAT);
			

			if($sth->execute())
			{
				$sth->close();
				echo '{"error":null,"msg":"Employee created successfully","code":"-99"}';
				exit;
			}
			else
			{
				$db->close();
				echo '{"error": "Employee creation failed","code":"-2"}';
				exit;
			}
			$db->close();
		}
		else
		{
			$db = null;
			echo '{"error": "Database error 1 ","code":"-1"}';
			exit; 
		}

	break;
	case 'READ':
		if(!is_numeric($P_empid))
		{
			$db->close();
			echo '{"error": "Invalid employee ID","code":"1" }';
			exit;
		}
		$query = "select *  from empmaster where empid = '{$P_empid}' ";
		$results = $db->query($query);
		$resp = [
			"error" => null,
			"data" => []
		];
		while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    		
			file_put_contents("test.log", var_export($row,true));
    		$resp["data"][] = $row;

		}
		//$results->close();
		$resp["code"] = "0";
		if(count($resp["data"]) ==0)
		{
			$resp["error"] = "No data found";
			$resp["code"] = "-99";
		}
		$db->close();
		echo json_encode($resp);
	break;
	
	case 'READALL':
		$query = "select *  from empmaster order by empid";
		$results = $db->query($query);
		$resp = [
			"error" => null,
			"data" => []
		];
		while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    		
			file_put_contents("test.log", var_export($row,true));
    		$resp["data"][] = $row;

		}
		//$results->close();
		$resp["code"] = "0";
		if(count($resp["data"]) ==0)
		{
			$resp["error"] = "No data found";
			$resp["code"] = "-2";
		}
		$db->close();
		echo json_encode($resp);
	break;
	case 'UPDATE':
		if(!is_numeric($P_empid))
		{
			$db->close();
			echo '{"error": "Invalid employee ID","code":"1" }';
			exit;
		}
		$query = "select count(*) as CNT from empmaster where empid = '{$P_empid}' ";
		$ret = $db->query($query);
		$row = $ret->fetchArray(SQLITE3_ASSOC);
		if($row['CNT'] ==0)
		{
			$ret->close();
			$db->close();

			echo '{"error": "Employee does not exist","code":"1"}';
			exit;
		}
		if(!preg_match('/^[a-zA-Z ]{3,40}$/', $P_ename))
		{
			$db->close();
			echo '{"error": "Employee name is invalid","code":"2"}';
			exit;
		}
		if(!isValidDate($P_dob,'Y-m-d'))
		{
			$db->close();
			echo '{"error": "Employee DOB is invalid","code":"3"}';
			exit;
		}
		if(!filter_var($P_email, FILTER_VALIDATE_EMAIL) )
		{
			$db->close();
			echo '{"error": "Employee email is invalid","code":"4"}';
			exit;
		}
		if(!preg_match('/^[6-9]{1}[0-9]{9}$/', $P_mobile))
		{
			$db->close();
			echo '{"error": "Employee mobile is invalid","code":"5"}';
			exit;
		}
		if(!preg_match('/^[a-zA-Z0-9 \s\.\,\#\/]{10,300}$/', $P_addr))
		{
			$db->close();
			echo '{"error": "Employee address is invalid","code":"6"}';
			exit;
		}
		if(!is_numeric($P_latt))
		{
			$db->close();
			echo '{"error": "Employee location lattitude is invalid","code":"7"}';
			exit; 
		}
		if(!is_numeric($P_long))
		{
			$db->close();
			echo '{"error": "Employee location lattitude is invalid","code":"8"}';
			exit; 
		}
		$query = "update empmaster set ename = :ename,date_of_birth = :dob,emailid = :email,mobile = :mobile,address = :addr,latt= :latt, long=:long ";
		if($P_profilepic != null && strlen($P_profilepic) > 1024)
		{
			$P_profilepic_decoded =base64_decode($P_profilepic);
			$mimetype = getImageMimeType($P_profilepic_decoded);
			if($mimetype != 'jpeg')
			{
				$db->close();
				echo '{"error": "Employee image type is invalid","code":"9"}';
				exit;
			}
			else if(strlen($P_profilepic_decoded) > 300*1024)
			{
				$db->close();
				echo '{"error": "Employee image is too big","code":"9"}';
				exit;
			}

			$query .= ' ,profilepic = :profilepic ';	
		}	
		$query .= ' where empid = :empid';
			
			if($sth = $db->prepare($query))
			{
				$sth->bindValue(':empid', $P_empid, SQLITE3_TEXT);
				$sth->bindValue(':ename', $P_ename, SQLITE3_TEXT);
				$sth->bindValue(':dob', $P_dob, SQLITE3_TEXT);
				$sth->bindValue(':email', $P_email, SQLITE3_TEXT);
				$sth->bindValue(':mobile', $P_mobile, SQLITE3_TEXT);
				$sth->bindValue(':addr', $P_addr, SQLITE3_TEXT);
				$sth->bindValue(':latt', $P_latt, SQLITE3_FLOAT);
				$sth->bindValue(':long', $P_long, SQLITE3_FLOAT);

				if($P_profilepic != null && strlen($P_profilepic) > 1024)
				{
					$sth->bindValue(':profilepic', $P_profilepic, SQLITE3_BLOB );
				}
				
				if($sth->execute())
				{
					echo '{"error":null,"msg":"Employee updated successfully","code":"-99"}';
					exit;
				}
				else
				{
					
					echo '{"error": "Employee update failed","code":"-2"}';
					exit;
				}
			}

			else
			{
				$db = null;
				echo '{"error": "Database error 1 ","code":"-1"}';
				exit; 
			}

			
			$db->close();
		
	break;
	case 'DELETE':
		if(!is_numeric($P_empid))
		{
			echo '{"error": "Invalid employee ID","code":"1" }';
			exit;
		}
		$query = "select count(*) as CNT from empmaster where empid = '{$P_empid}' ";
		$ret = $db->query($query);
		$row = $ret->fetchArray(SQLITE3_ASSOC);
		if($row['CNT'] ==0)
		{
			$db->close();

			echo '{"error": "Employee does not exist","code":"1"}';
			exit;
		}
		else
		{
			$query = "delete from  empmaster where empid = '{$P_empid}'";
			if($db->exec($query))
			{
				$db->close();
				echo '{"error":null,"msg":"Employee deleted successfully","code":"-99"}';
				exit;
			}
			else
			{
				$db->close();
				echo '{"error": "Employee delete failed","code":"-2"}';
				exit;	
			}

		}
	break;
	default: 
		echo '{"error": "Action not defined"}';
		exit;	



}



function isValidDate($date, $format= 'Y-m-d'){
    return $date == date($format, strtotime($date));
}

function getBytesFromHexString($hexdata)
{
  for($count = 0; $count < strlen($hexdata); $count+=2)
    $bytes[] = chr(hexdec(substr($hexdata, $count, 2)));

  return implode($bytes);
}

function getImageMimeType($imagedata)
{
  $imagemimetypes = array( 
    "jpeg" => "FFD8", 
    "png" => "89504E470D0A1A0A", 
    "gif" => "474946",
    "bmp" => "424D", 
    "tiff" => "4949",
    "tiff" => "4D4D"
  );

  foreach ($imagemimetypes as $mime => $hexbytes)
  {
    $bytes = getBytesFromHexString($hexbytes);
    if (substr($imagedata, 0, strlen($bytes)) == $bytes)
      return $mime;
  }

  return NULL;
}


// function getImageMimeType($imagedata)
// {
// 	echo "---->".$imagedata;
//   $imagedata = base64_decode($imagedata);

// 	$f = finfo_open();

// 	$mime_type = finfo_buffer($f, $imagedata, FILEINFO_MIME_TYPE);
// 	return $mime_type;
// }
