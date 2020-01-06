function IsJsonString(str) {
	try {
		JSON.parse(str);
	} catch (e) {
		return false;
	}
	return true;
}

$("#claimBonusBtn").click(function() {
	$.post("/func/claim", {}, function (data) {
		if(IsJsonString(data)) {
			parsed_data = JSON.parse(data);
			if(parsed_data.status == 'error') {
				alert(parsed_data.error);
			} else {
				$("#voucherCode").html(parsed_data.data.code);
				UIkit.modal('#voucherBonusModal').show();
			}
		} else {
			alert(data);
		}
	});
	return false;
});