function search(val) {
    //event.stopImmediatePropagation(); 
   
    clear();

    var searchVal = val.trim().toLowerCase() ;//event.target.value.trim().toLowerCase();
    var category=$('#search_category').val();
    
    console.log(category);
    if(searchVal.length==0)
        getData('HOME');
    //var exp=/^[a-zA-Z0-9]+$/;
    console.log(searchVal);
    if ((searchVal.length > 0)) {
        $.post('search.php', { data: searchVal,category:category}, function (data) {
            data = JSON.parse(data);
            console.log(data);
            //setCards(data);
            setList(data,category);
        });
    }

}
function setList(data,category){
   
    for(var i=0;i<data.length;i++){
        var a=document.createElement('a');
        a.className="list-group-item list-group-item-action";
        a.textContent=data[i].TITLE;
        a.href='searchResult.php'+"?"+"search="+data[i].TITLE+"&category="+category; 
       
        $('#search_result').append(a);
    }   
   
}
function clear() {
    $('#search_result').empty();
}
$('#search_btn').click(function(){
    console.log('cl');
});
function searchResult(title,category){      // Search with specialized and general category
    console.log('title = '+title);
    $.post('data_test.php',{action:'searchTitle',data:title,category:category},function(data){
        data=JSON.parse(data);
        console.log(data);
        setCards(data,'123');
    });
   
}
