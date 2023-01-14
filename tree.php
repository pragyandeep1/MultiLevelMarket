<!DOCTYPE html>
<html>
<head>
	<?php include_once("element/connection.php"); ?>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="assets/vendor/fonts/circular-std/style.css">
	<link rel="stylesheet" type="text/css" href="assets/libs/css/style.css">
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.7.2/css/all.min.css">
	<link rel="stylesheet" type="text/css" href="assets/vendor/charts/morris-bundle/morris.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css">
	<title>Admin Dashboard Template</title>
	<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
	<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-slimScroll/1.3.8/jquery.slimscroll.js"></script>
	<script src="assets/libs/js/main-js.js"></script>
	<script src="assets/vendor/charts/morris-bundle/raphael-min.js"></script>
	<script src="assets/vendor/charts/morris-bundle/morris.js"></script>
	<script src="assets/vendor/charts/charts-bundle/Chart.bundle.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script src="assets/libs/js/dashboard-influencer.js"></script>
</head>
<body>
	<div class="dashboard-main-wrapper">
		<?php include_once("element/sidebar.php"); ?>
	</div>

	<div class="dashboard-wrapper">
		<div class="dashboard-influence">
			<div class="container-fluid dashboard-content">
				<div class="row">

					<div class="col-lg-8 col-sm-12 card bg-light p-0">
						<h5 class="card-header">Tree View</h5>
						<div class="card-body">
							<?php 
								$id = [$my_id];

								for ($i=0; $i <= 2; $i++) { 
									$temp_id_index = 0;
									$divide = pow(2, $i);
							?>
							<div class="row p-3">
							<?php
								for ($d=0; $d < $divide; $d++) { 
							?>
								<div class="col-<?= 12/$divide ?> p3 text-center">
									<img src="<?= ($id[$d]!=0)?"download.png":"download2.png"?>" class="image" style="width: 50px">
									<p id="<?= $id[$d] ?>" onclick="<?= ($id[$d]!=0)?:'show_data()'?>"></p>
								</div>
								<?php 
								for ($p=0; $p < 2; $p++) { 
									$temp_id[$temp_id_index] = fetch_left_right($p, $print_id);
									$temp_id_index++;
								}	 
							}
							$id = $temp_id;
							?>
							</div>
							<?php
							}

							function fetch_left_right($side, $agent_id){
								global $conn;

								if ($side == 0) {
									$pos = 'left_side';
								}else{
									$pos = 'right_side';
								}
								$data = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM 'users' WHERE 'user_id' = '$agent_id'"));

								if ($agent_id != 0) {
									return $data[$pos];
								}else{
									return 0;
								}
							}
							?>
						</div>
					</div>

					<div class="col-lg-4 col-sm-12">
						<div class="card">
							<h5 class="card-header">Sales by Social Source</h5>
							<div class="card-body p-0">
								<ul class="social-sales list-group list-group-flush">
									<li class="list-group-item social-sales-content">
										<span class="social-sales-icon"></span>
									</li>
									<li class="list-group-item social-sales-content">
										<span class="social-sales-icon"></span>
									</li>
									<li class="list-group-item social-sales-content">
										<span class="social-sales-icon"></span>
									</li>
									<li class="list-group-item social-sales-content">
										<span class="social-sales-icon"></span>
									</li>
								</ul>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelled>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel"></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true"> &times;</span>
					</button>
				</div>
				<div class="modal-body" id="agent_detail_show_on_model"></div>
				<div class="modal-footer">
					<button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
</body>
</html>