var adid;
var category,action=null;
var isPosted=false;
	$(document).ready(function(){
		var url = new URL(window.location.href);
		action = url.searchParams.get("action");
		console.log("action = "+action);
		if(action==null)
		category=sessionStorage.getItem("category")
		else{
		adid=url.searchParams.get('adid');
		category=url.searchParams.get('category');
		}
		
		$("#category").text(category);
		addFields(category);
		
		
		
	});
	
	window.onbeforeunload = function (e) {
		e = e || window.event;
	
		// For IE and Firefox prior to version 4
		if (e) {
			e.returnValue = 'Sure?';
		}
		
		// For Safari
		return 'Sure?';
	};

	$('#post_btn').click(function(){
		postData();
	});

	
	
	function addFields(category){
		var table=$("#ad_table");
		console.log(table);
		if(category=='BOOKS'){
			var div='<tr id="publication_row"><th><div class="form-group" id="publication">';
			div+='<label class="labels">Publication</label>';
			div+="<select id='select_publication' type='text' name='publication' class='form-control input-control'></select>";
			div+='</div></th></tr>';
			$('#title_row').after(div);

			var options=[];

			$.post("functions.php", {action: "getPublication",option:1}, function(result){
				result=JSON.parse(result);
				
				var options=[];
				
				for(var i=0;i<result.length;i++){
					
					options.push('<option value="'+result[i]+'">'+result[i]+'</option>');
				};
				$("#select_publication").html(options.join(''));
			});

			var author='<tr id="author_row"><th><div class="form-group" id="author">';
			author+='<label class="labels">Author</label>';
			author+='<input type="text" placeholder="enter author name" name="author" class="form-control input-control">';
			author+='</div></th></tr>';

			$("#publication_row").after(author);
				
		}
		else if(category=='LAPTOP/MOBILE'||category=='LAPTOP'){
			var div='<tr id="brand_row"><th><div class="form-group" id="brand">';
			div+='<label class="labels">Brand</label>';
			div+='<select id="select_brand" type="text" name="brand" class="form-control input-control"></select>';
			div+='</div></th></tr>';
			$('#title_row').after(div);

			var options=[];

			$.post("functions.php", {action: "getPublication",option:2}, function(result){
				result=JSON.parse(result);
				
				var options=[];
				
				for(var i=0;i<result.length;i++){
					
					options.push('<option value="'+result[i]+'">'+result[i]+'</option>');
				};
				$("#select_brand").html(options.join(''));
			});
			var model='<tr id="model_row"><th><div class="form-group" id="model">';
			model+='<label class="labels">Model Name/No.</label>';
			model+='<input type="text" placeholder="enter model name/no." name="model" class="form-control input-control">';
			model+='</div></th></tr>';

			$("#brand_row").after(model);
        }
        else if(category=='PG/HOSTEL'||category=='PG_HOSTEL'){
            var roommates='<tr id="room_row"><th><div class="form-group" id="room">';
			roommates+='<label class="labels">Roommates</label>';
			roommates+='<input type="number" name="room" class="form-control input-control">';
			roommates+='</div></th></tr>';
			$('#title_row').after(roommates);
			
			var area='<tr id="area_row"><th><div class="form-group" id="area">';
			area+='<label class="labels">Area</label>';
			area+='<select id="select_area" type="text" name="area" class="form-control input-control"></select>';
			area+='</div></th></tr>';
			$('#room_row').after(area);

			var options=[];
            
            $.post("functions.php", {action: "getPublication",option:3}, function(result){
				result=JSON.parse(result);
				
				var options=[];
				
				for(var i=0;i<result.length;i++){
					
					options.push('<option value="'+result[i]+'">'+result[i]+'</option>');
				};
				$("#select_area").html(options.join(''));
			});
			
		}
		else if(category=='OTHER'){
			var info='<tr id="info_row"><th><div class="form-group" id="info">';
			info+='<label class="labels">Information</label>';
			info+='<input type="text" name="info" class="form-control input-control">';
			info+='<label style="font-size:small;color:red" class="labels">** enter size/model/type or whatever required information according to product type</label>'
			info+='</div></th></tr>';
			$('#title_row').after(info);
		}
		console.log("action "+action);
		if(action!=null){
			loadAdData();
		}
		else{
			adid=generateAdId(10);
			console.log(adid);
		}
		
	}


	function SaveImages() {
		console.log("save images called");
		var formData = new FormData();
		var total = $("#files")[0].files.length;
		
		//console.log('ad id : '+adid);
		for(var i=0;i<total;i++){
			formData.append('files[]', document.getElementById('files').files[i]);
		}
		formData.append('adid',adid);
		console.log('adid = '+adid);
		formData.append('category',category);
		formData.append('request', 1);		// request code for saving images
		console.log('category = '+category);
		console.log(files);
		console.log(formData);
		$.ajax({
			url:'postAd.php',
			//url: 'adtest.php',
			type: 'POST',
			data: formData,
			dataType: 'JSON',
			contentType: false,
			processData: false,
			success: function(response){
				if(response!=false)
					addImage(response)
			},
			error(data) {
				console.log(data.statusText);
				alert("error");
			}

		});
	}

	function addImage(response)
	{
		console.log("addImage called");

		if(response!=0){
			console.log(response);
			for(var res=0;res<response.length;res++){

				var count=$('.ad-image-div').length;
				count=Number(count)+1;
				console.log("res = "+res);
				console.log("count = "+count);
				var li = document.createElement("LI");
				li.id = "image_" + count;
				var div = document.createElement("DIV");
				div.className = "ad-image-div box";
				div.style.background = 'url(' + response[res] + ')';
				div.style.backgroundSize = "100% 100%";
				var span = document.createElement("span");
				span.id = "cancelspan_" + count;
				
				span.className += "cancel-span";
				if (count==1) {
					var spanCover = document.createElement("span");
					spanCover.className += "cover-span";
					spanCover.textContent = "COVER";
					div.appendChild(spanCover);
				}
				div.appendChild(span);
				li.appendChild(div);

				$('#ad-image-list').append(li);
			}
		}
		else{
			alert('file not uploaded');
		}

	}

	function postData()
	{	
		$('#post_btn').prop('disabled',true);
		//var category=sessionStorage.getItem("category");
		var formData=new FormData(document.getElementById('form-ad-data'));
		if(action==null)
			formData.append('request',3);   // For Inserting data
		else
			formData.append('request',4);	// For Updating data
		formData.append('adid',adid);	
		console.log("category = "+category);
		
		if(category=='BOOKS'){

			var author=$('input[name="author"]').val();
			var publication=$('#select_publication').find(":selected").text();
			console.log('adid: '+adid);
			formData.append('category','BOOKS');
			formData.append('author',author);
			formData.append('publication',publication);
		}
		else if(category=='LAPTOP/MOBILE'){
			var brand=$('#select_brand').find(":selected").text();
			var model=$('input[name="model"]').val();
			formData.append('category','LAPTOP');
			formData.append('brand',brand);
			formData.append('model',model);
			console.log('model = '+model);
		}
		else if(category=='PG/HOSTEL'){
			var area=$('#select_area').find(":selected").text();
			var roommates=$('input[name="room"]').val();
			formData.append('category','PG_HOSTEL');
			formData.append('area',area);
			formData.append('roommates',roommates);

		}
		else if(category=='OTHER'){
			var info=$('input[name="info"]').val();
			formData.append('info',info);
			formData.append('category',category);
		}
			$.ajax({
				url:'postAd.php',
				type:'POST',
				data: formData,
				dataType:'JSON',
				contentType: false,
				processData: false,
				error(res){
					console.log(res.statusText);
				}

			}).done(function(res){
				if(res==1)
					alert('Ad Successfully Placed');
					$('#post_btn').prop('disabled',false);
				console.log('success');
				isPosted=true;
				console.log(res);
				document.location="AdView.html?adid=" + adid + "&" + "category=" + category;
				
			});
		
	}

	function generateAdId(length)
	{
		var id='';
		var chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		chars+='abcdefghijklmnopqrstuvwxyz0123456789';

		for(var i=length;i>0;i--){
			id+=chars[Math.floor(Math.random()*chars.length)];
		}
		return id;
	}
	function loadAdData(){
		console.log('load');
		$.post("functions.php", {
            action: "getAdData",
			adid: adid,
			category:category
        }, function(result) {
			console.log(result);
			 console.log('success');
			
			setData(JSON.parse(result));
        });
	}
	function setData(result) {
		console.log(result);
		$('input[name="title"]').val(result.TITLE);
		$('input[name="price"]').val(result.PRICE);
		$('#description_text').val(result.DESCRIPTION);
		if(category=='BOOKS')
		{
			$('input[name="author"]').val(result.AUTHOR);
			$('#select_publication').val(result.PUBLICATION);
		}
		else if(category=='LAPTOP'){
			$('#select_brand').val(result.BRAND);
			$('input[name="model"]').val(result.MODEL_NAME);
		}
		else if(category=='PG_HOSTEL'){
			$('#select_area').val(result.AREA);
			$('input[name="room"]').val(result.ROOMMATES);
		}
		else if(category=='OTHER'){
			$('input[name="info"]').val(result.INFO);

		}
		addImage(result.images);
	}
	





