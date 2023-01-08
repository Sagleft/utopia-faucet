function IsJsonString(str) {
	try {
		JSON.parse(str);
	} catch (e) {
		return false;
	}
	return true;
}

$( document ).ready(function() {
    new ClipboardJS('.btn');
});

$("#claimBonusBtn").click(function() {
	$.post("/func/claim", {}, function (data) {
		if(IsJsonString(data)) {
			parsed_data = JSON.parse(data);
			if(parsed_data.status == 'error') {
				alert(parsed_data.error);
			} else {
				initConfetti();
				render();
				$("#voucherCode").val(parsed_data.data.code);
				$("#requestVoucher").hide();
				$("#voucherResultWrap").show();
			}
		} else {
			alert(data);
		}
	});
	return false;
});
