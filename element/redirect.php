<?php 

	session_start();
	// require_once "connection.php";
	$db_host = "localhost";
	$db_user = "root";
	$db_pass = "";
	$db_name = "binary";
	$conn = mysqli_connect($db_host,$db_user,$db_pass,$db_name);

	if (!$conn) {
		echo "Not connected.";
	}

	if (isset($_REQUEST['profile_update_password'])) {
		$my_id = $_REQUEST['my_user_id'];
		$password = $_REQUEST['password'];
		$c_password = $_REQUEST['confirm_password'];

		if ($password == $c_password) {
			mysqli_query($conn,"UPDATE 'users' SET 'password'='$c_password' WHERE 'user_id'='$my_id'");
		}
		header("Location: ../profile.php");
	}

	if (isset($_REQUEST['profile_update_basic'])) {
		$my_id = $_REQUEST['my_user_id'];
		$dob = $_REQUEST['dob'];
		$gender = $_REQUEST['gender'];
		$address = $_REQUEST['address'];
		$pan_no = $_REQUEST['pan_no'];
		$aadhar_no = $_REQUEST['aadhar_no'];
		$mob = $_REQUEST['mob'];
		
		mysqli_query($conn,"UPDATE 'users' SET 'mobile'='$mob','dob'='$dob','gender'='$gender','address'='$address','pan_no'='$pan_no','aadhar_no'='$aadhar_no' WHERE 'user_id'='$my_id'");
		header("Location: ../profile.php");
	}

	if (isset($_REQUEST['withdrawal_request'])) {
		$amt = $_REQUEST['amount'];
		$user_id = $_REQUEST['user_id'];
		$data = mysqli_fetch_array(mysqli_query($conn,"SELECT 'wallet' FROM 'users' WHERE 'user_id'='$user_id'"));
		
		if ($amt <= $data['wallet']) {
			$date = date("Y-m-d");
			mysqli_query($conn,"UPDATE 'users' SET 'wallet'='wallet'-$amt WHERE 'user_id'='$user_id'");
			mysqli_query($conn,"INSERT INTO 'income_history' ('user_id','amt','desp','cr_dr') VALUES ('$user_id','$amt','$desp','$cr_dr')");
			mysqli_query($conn,"INSERT INTO 'withdrawal' ('user_id','amt','request_date') VALUES ('$user_id','$amt','$date')");
		}
		header("Location: ../withdraw.php");
	}

	if (isset($_REQUEST['login'])) {
		$user_id = $_REQUEST['user_id'];
		$password = $_REQUEST['password'];
		move_to_dashboard($user_id,$password);
	}

	if (isset($_REQUEST['user_registration'])) {
		$s_id = $_REQUEST['sponsor_id'];
		$pin = $_REQUEST['pin'];
		$name = $_REQUEST['user_name'];
		$pos = $_REQUEST['position'];
		$mobile = $_REQUEST['user_mob'];
		$password = $_REQUEST['password'];

		if (check_in($pin)) {
			insert_into_users($s_id,$name,$pos,$mobile,$password);
		}
		header("Location: ../registration.php");
	}

	function binary_count($spons,$pos){
		global $conn;

		if ($pos == 0) {
			$pos = "left_count";
		}else{
			$pos = "right_count";
		}
		$spons = find_placement_id($spons);

		while ($spons != 0) {
			mysqli_query($conn,"UPDATE 'users' SET 'pos'='$pos'+1 WHERE 'user_id'='$spons'");
			$data = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM 'users' WHERE 'user_id'='$spons'"));
			$is_first_pair_generate = $data['is_first_pair'];

			if ($is_first_pair_generate) {
				is_pair_generate($spons,$pos);
			}else{
				check_first_pair_condition($spons);
			}
			$pos = find_position($spons);
			$spons = find_placement_id($spons);
		}
	}

	function check_first_pair_condition($spons){
		global $conn;
		$data = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM 'users' WHERE 'user_id'='$spons'"));
		$left_count = $data['left_count'];
		$right_count = $data['right_count'];

		if ($left_count>0 && $right_count>0) {
			if ($left_count>$right_count || $left_count<$right_count) {
				if ($left_count>$right_count) {
					mysqli_query($conn,"UPDATE 'users' SET 'is_first_pair'='1','pair_deduct'='left_count','left_count'='left_count'-1 WHERE 'user_id'='$spons'");
					insert_into_pair($spons);
				} else {
					mysqli_query($conn,"UPDATE 'users' SET 'is_first_pair'='1','pair_deduct'='right_count','right_count'='right_count'-1 WHERE 'user_id'='$spons'");
				}
				insert_into_pair($spons);
			}
		}
	}

	function is_pair_generate($spons,$pos){
		global $conn;
		$compare_pos = ($pos=="left_count")?"right_count":"left_count";
		$pla_data = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM 'users' WHERE 'user_id'='$spons'"));

		if ($pla_data[$pos]<=$pla_data[$compare_pos]) {
			insert_into_pair($spons);
		}
	}

// Code Reusability
	function insert_into_pair($spons){
		global $conn;
		$date = date("Y-m-d");
		$data = mysqli_query($conn, "SELECT * FROM 'pair_count' WHERE 'date'='$date' AND 'user_id'='$spons'");

		if (mysqli_num_rows($data)==1) {
			mysqli_query($conn, "UPDATE 'pair_count' SET 'no_of_pair'='no_of_pair'+1 WHERE 'date'='$date' AND 'user_id'='$spons'");
		} else {
			mysqli_query($conn, "INSERT INTO 'pair_count'('user_id','date','no_of_pair') VALUES ('$spons','$date','$no_of_pair')");
		}
		
	}

	function check_pin($pin){
		global $conn;
		$query = mysqli_query($conn,"SELECT * FROM 'pin' WHERE 'pin_value'='$pin' AND 'status'='0'");

		if (mysqli_num_rows($query)==1) {
			mysqli_query($conn,"UPDATE 'pin' SET 'status'='1' WHERE 'pin_value'='$pin'");
			return true;
		}
		return false;
	}

	function insert_into_users($s_id,$name,$pos,$mobile,$password){
		global $conn;
		$user_id = rand(11111111,99999999);
		mysqli_query($conn,"INSERT INTO 'users' ('user_id','name','password','mobile','position','sponsor_id') VALUES ('$spons','$name','$password','$mob','$pos','$s_id')");
		level_distribution($s_id);
		placement_id($user_id,$s_id,$pos);
	}

	function placement_id($user_id,$s_id,$pos){
		global $conn;
		$spons_data = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM 'users' WHERE 'user_id'='$s_id'"));

		if ($pos == 0) {
			if ($spons_data['left_side']==0) {
				mysqli_query($conn,"UPDATE 'users' SET 'left_side'='$user_id' WHERE 'user_id'='$s_id'");
				mysqli_query($conn,"UPDATE 'users' SET 'placement_id'='$s_id' WHERE 'user_id'='$user_id'");
				binary_count($user_id,$pos);
			}
			else{
				placement_id($user_id,$spons_data['left_side'],$pos);
			}
		}
		else{
			if ($spons_data['right_side']==0) {
				mysqli_query($conn,"UPDATE 'users' SET 'right_side'='$user_id' WHERE 'user_id'='$s_id'");
				mysqli_query($conn,"UPDATE 'users' SET 'placement_id'='$s_id' WHERE 'user_id'='$user_id'");
				binary_count($user_id,$pos);
			}
			else{
				placement_id($user_id,$spons_data['right_side'],$pos);
			}
		}
	}

	function level_distribution($s_id){
		global $conn;
		$a = 0;
		$income = [20,10,5,5,5,5];
		while ($a<6 && $s_id!=0) {
			mysqli_query($conn,"UPDATE 'users' SET 'wallet'='wallet'+$income[$a] WHERE 'user_id'='$s_id'");
			mysqli_query($conn,"INSERT INTO 'income_history' ('user_id','amt','desp','cr_dr') VALUES ('$s_id','$amt','$desp','$cr_dr')");
			$next_id = find_sponsor_id($s_id);
			$s_id = $next_id;
			$a++;
		}
	}

	function find_sponsor_id($s_id){

	}

?>