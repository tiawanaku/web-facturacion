<?php

		$db_host = "45.55.134.230:9599";
		$db_user = "root";
		$db_pass = "Franz1987!";
		$db_name = "cegepafacturacion";
		
		$con = mysqli_connect($db_host,$db_user,$db_pass,$db_name);
		
		if (mysqli_connect_error()){
			echo 'error';
		}
			else{
				echo '';
				}

?>