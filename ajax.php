<?php
include_once("element/connection.php");

if (isset($_REQUEST['agent_id'])) {
	$agent_id = $_REQUEST['agent_id'];
	$data = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM 'users' WHERE 'user_id'='$agent_id'"));
	$data['pair_deduct'];

	$left_count = $data['left_count'];
	$right_count = $data['right_count'];

	if ($data['pair_deduct']!='') {
		if ($data['pair_deduct']=='left_count') {
			$left_count++;
		} else {
			$right_count++;
		}
		
	}
	

	/*($data['pair_deduct']=='left_count')?($data['left_count']+1):$data['left_count'];
	($data['pair_deduct']=='right_count')?($data['right_count']+1):$data['right_count'];*/

	echo '
	<table class="table table-striped">
		<thead>
			<tr>
				<th scope="col" colspan="2">Name: '.$data['name'].'</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th scope="row">Left Count</th>
				<td>'.$left_count.'</td>
			</tr>
			<tr>
				<th scope="row">Right Count</th>
				<td>'.$right_count.'</td>
			</tr>
			<tr>
				<th scope="row">Left User</th>
				<td>'.$data['left_side'].'</td>
			</tr>
			<tr>
				<th scope="row">Right User</th>
				<td>'.$data['right_side'].'</td>
			</tr>
		</tbody>
	</table>
	';
}

?>

