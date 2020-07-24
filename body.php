<?php
    include 'header.php';
    include 'slider.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Document</title>
</head>
<body>
    
</body>
</html>
<script>
    $(document).ready(function(){
        var url = new URL(document.location);
        var tab = url.searchParams.get('tab');
        console.log('tab body = '+tab);
        if(tab!=null){
            sessionStorage.setItem('activeTab',tab);
            tabSelected(tab);
            if(tab=='HOME')
            {
                $('#carousel_space').load('Carousel.html');
            }

        }

    });
</script>


