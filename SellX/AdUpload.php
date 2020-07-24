<?php

session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
	header("location:login.php");
	exit;
}

$_SESSION["total_images"] = 0;
if(isset($_SESSION['images'])){
	unset($_SESSION['images']);
}
?>

<!DOCTYPE html>
<html>

<head>
	<title>Ad Upload</title>
	<script src="js/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/ad_post.css">
	<script src="js/ad_upload.js"></script>

</head>

<body>

	<div class="card-main" id="main_card">
		<center>
			<h2 class="main-head">POST YOUR AD</h2>
		</center>

		<table class="ad-table" id="ad_table">
			<form id="form-ad-data">
				<tr>
					<th>
						<div class="head-1">
							<h3><b>Selected Category</b></h3>
							<h4 id="category"></h4>
						</div>

					</th>
				</tr>
				<tr id="title_row">
					<th>
						<div class="form-group" id="title">
							<label class="labels">Ad Title</label>
							<input type="text" name="title" class="form-control input-control">
						</div>
					</th>
				</tr>
				<tr id="price_row">
					<th>
						<div class="form-group" id="price">
							<label class="labels">Set Price</label>
							<input type="number" name="price" class="form-control input-control">
						</div>
					</th>
				</tr>
				<tr id="description_row">
					<th>
						<div class="form-group" id="description">
							<label class="labels">Description</label>
							<textarea name='description' class="form-control input-control" id="description_text"></textarea>
						</div>
					</th>
				</tr>
			</form>
			<tr>
				<th>
					<label class="labels">Upload Upto 5 photos</label>
					<div class="upload">
						<label id="label_preview" for="files">
							<div class="btn-upload box">
								<center>
									<img width="70px;" src="system_img/cam-icon.png">
									<h5>Add photo</h5>
								</center>
							</div>
						</label>
						<form enctype="multipart/form-data" action="adtest.php" method="post" id="form-ad">
							<input accept="image/*" id="files" type="file" style="display: none;" name="files[]" onchange="SaveImages()" multiple>
						</form>
					</div>
					<ul class="ad-image-list" id="ad-image-list">
						<!--<li>
								<div class="ad-image-div box" style="background: url('system_img/account-icon.png');background-size: 100%;">
									<span class="cancel-span"></span>
									<span class="cover-span">COVER</span>
								</div>
							</li>-->
					</ul>
				</th>
			</tr>
			<tr>
				<th>
					<input id="post_btn" type="button" value="POST AD" onclick="postData()">
				</th>
			</tr>
		</table>

	</div>


</body>
<script>
	$('.ad-image-list').on('click', '.ad-image-div .cancel-span', function() {
		console.log("delete called");
		var id = this.id;
		var split_id = id.split('_');
		var num = split_id[1];
	
		var url = $('#image_' + num + ' div').css('background-image');
		url = url.replace(/url\(("|')(.+)("|')\)/gi, '$2');
		var imagesrc = "";
		for (var i = 0; i < 2; i++) {
			var temp = url.lastIndexOf("/");
			imagesrc = "/" + url.substring(temp + 1, url.length) + imagesrc;
			url = url.substring(0, temp);
		}
		imagesrc = imagesrc.substring(1, imagesrc.length);
		var deletefile = confirm("Do you really want to delete?");
		console.log("imagesrc= " + imagesrc);
		if (deletefile == true) {
			if (action == null) {
				$.ajax({
					url:'postAd.php',
					//url: 'adtest.php',
					type: 'POST',
					data: {
						request: 2,		// request code for delete image
						location: imagesrc.toString()
					},
					success: function(response) {
						console.log("success delete " + response);
						if (response == 1) {
							$('#image_' + num).remove();
							var ul = document.getElementById("ad-image-list");
							if (ul.childElementCount) {
								var element = ul.getElementsByTagName('li')[0].getElementsByTagName('div')[0];

								if (element.querySelector(".cover-span") == null) {
									var spanCover = document.createElement("span");
									spanCover.className += "cover-span";
									spanCover.textContent = "COVER";
									element.appendChild(spanCover);
								}
							}
						}
					},
					error(response) {
						alert("Error");
					}
				});
			}

			else{
				$.post('postAd.php',{request:2,location:imagesrc.toString()},function(response){
					console.log('successfully removed');
					console.log(response);
					if (response == true) {
							$('#image_' + num).remove();
							var ul = document.getElementById("ad-image-list");
							if (ul.childElementCount) {
								var element = ul.getElementsByTagName('li')[0].getElementsByTagName('div')[0];

								if (element.querySelector(".cover-span") == null) {
									var spanCover = document.createElement("span");
									spanCover.className += "cover-span";
									spanCover.textContent = "COVER";
									element.appendChild(spanCover);
								}
							}
						}
				});
			}

		}
	});
</script>

</html>