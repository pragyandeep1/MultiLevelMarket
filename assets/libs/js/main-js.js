function show_details(a){
	$('#exampleModal').modal("show");
	var agent_id = $(a).attr('id');
	$('#exampleModalLabel').html("agent_id");
	$.ajax({
		url: 'ajax.php',
		type: 'post',
		data: {agent_id:agent_id},
		success: function(response){
			$('#agent_detail_show_on_model').html(response);
		}
	})
}