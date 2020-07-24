function cardClick(event) {
    console.log("card clicked");
}

function getFilterData(type){
    var option=null;
    if(type=='BOOKS')
        option=1;   
    else if(type=='LAPTOP')
        option=2;
    
    else if(type=='PG_HOSTEL')
    option=3;

    if(option!=null)
    $.post("functions.php", {action: "getPublication",option:option}, function(result){
        result=JSON.parse(result);
        addFilters(result,type);
    });
    
}

/*
<label class="container-checkbox">Two
  <input type="checkbox">
  <span class="checkmark"></span>
</label>
*/

function addFilters(result,type){

    // getFilterData(type);
    var options=[];
        var head="<h4 id='f_specs'>";
        if(type=='BOOKS')
        head+='Publication</h4>';
        else if(type=='LAPTOP')
        head+='Brand';
        else if(type=='PG_HOSTEL')
        head+='Area';

        head+='</h4>';
    for(var i=0;i<result.length;i++){
        var check="";
        if(i==0)
        check+=head;
        check+='<label class="container-checkbox">'+result[i];
        check+='<input class="check-input" type="checkbox" name="'+result[i] +'"><span class="checkmark"></span></label>';
        options.push(check);
       // options.push('<option value="'+result[i]+'">'+result[i]+'</option>');
    };
    $("#options").html(options.join(''));
}

function getData(func) {
    console.log("getData called");

    $.ajax({
        type: 'POST',
        url: 'data_test.php',
        data: {
            action: func
        },
        dataType: "json",
        success: function (response) {
            console.log(response);
            if (func != 'favourite' && func != 'myads') {
                const min = $('#min_price').val(response['PRICE_DATA'].MIN_PRICE);
                min.attr('min', response['PRICE_DATA'].MIN_PRICE);
                const max = $('#max_price').val(response['PRICE_DATA'].MAX_PRICE);
                max.attr('max', response['PRICE_DATA'].MAX_PRICE);

                setCards(response['PRODUCTS'], func);
            }
            else {
                setCards(response, func);
            }
        },
        error(response) {
            console.log("error 1");
            console.log(response.statusText);
        }
    });
}

function addToFavourite(element) {
    var adid = element.id;
    if (element.className == 'span-favourite-off') {

        $.post("functions.php", {
            action: "addToFavourite",
            adid: adid
        }, function (result) {
            console.log(result);
            element.className = "span-favourite-on";
        });
    } else {
        $.post("functions.php", {
            action: "removeFavourite",
            adid: adid
        },
            function (result) {
                console.log(result);
                element.className = "span-favourite-off";
            });
    }
}

function setCards(response, func) {
    $('#card_items').empty();
    console.log(response.length);
    //console.log(response);
    for (var i = 0; i < response.length; i++) {
        var div = document.createElement("div");
        div.className += "wrapper";
        var anchor = document.createElement("a");
        anchor.href = "AdView.html?adid=" + response[i].ADID + "&" + "category=" + response[i].CATEGORY;
        if (func == 'myads')
            anchor.href += '&action=' + func;
        anchor.className = "anchor-text";
        var itembox = document.createElement("DIV");
        itembox.className += "card_item";

        var itemimage = document.createElement("DIV");
        itemimage.className += "card_img";
        var itemtext = document.createElement("DIV");
        itemtext.className += "card_text";

        var fav = document.createElement("span");
        if (response[i].isfavourite)
            fav.className += "span-favourite-on";
        else
            fav.className += "span-favourite-off";

        fav.id = response[i].ADID;
        fav.onclick = function () {
            addToFavourite(this)
        };

        div.appendChild(fav);

        var img = document.createElement("img");
        img.setAttribute("src", response[i].COVER_IMG);
        itemimage.appendChild(img);

        var price = document.createElement("span");
        price.textContent = "\u20b9" + " " + response[i].PRICE;
        price.className = "price-text";
        itemtext.appendChild(price);

        var title = document.createElement("span");
        title.textContent = response[i].TITLE;
        title.className = "title-text";
        itemtext.appendChild(title);

        var desc = document.createElement("span");
        desc.textContent = response[i].DESCRIPTION;
        desc.className += "title-text";
        itemtext.append(desc);

        var date = document.createElement("span");
        date.textContent = response[i].DATE_OF_POST;
        date.className += "title-text date";
        itemtext.append(date);

        itembox.appendChild(itemimage);
        itembox.appendChild(itemtext);
        anchor.appendChild(itembox);
        div.appendChild(anchor);
        document.getElementById("card_items").appendChild(div);
    }



}

function filter() {
    var active=sessionStorage.getItem('activeTab');
    var options=[];
    if(active!='HOME')
    {   
       var checkbox=$('.check-input')
        for(var i=0;i<checkbox.length;i++){
            if(checkbox[i].checked){
                options.push(checkbox[i].name);
            }
        }
      
    }
    console.log('filter');
    var criteria = $('#sort_by').val();
    var priceMin = $('#min_price').val();
    var priceMax = $('#max_price').val();
    var type='filter-all';
    var params={action:type,criteria:criteria,MIN_PRICE:priceMin,MAX_PRICE:priceMax};
    if(active!='HOME'){
        params['options']=JSON.stringify(options);
        params['for']=active;
        params['f_specs']=$('#f_specs').text(); 
    }
    console.log(params);
    // var title=args[0];
    // var category=args[1];
    // console.log('category = '+category);
    // console.log('title = '+title);
    $.post('functions.php',params, //title:title,category:category
    function(data){
         console.log(data);
        setCards(JSON.parse(data));
    });
}