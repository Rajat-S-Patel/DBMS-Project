<!DOCTYPE html>
<html lang="en">

<head>
    <title>Slider</title>

    <link rel="stylesheet" href="css/sliderCSS2.css" rel="text/css">
    <script src="js/jquery.min.js"></script>
    <script src="js/Cards.js"></script>
    
</head>


<body>
    <div id="carousel_space">
        
    </div>
    <div class="slider-wrapper">
        <div class="filter box">
            <h3>Filters</h3>
            <hr>
            <div class="row-filter">
            <label style="margin-top: 8px;">Sort By:</label>
            <select class="form-control" style="width: 70%;" id="sort_by">
                <option value="time desc">time:latest first</option>
                <option value="time asc">time:oldest first</option>
                <option value="price asc">price:low-high</option>
                <option value="price desc">price:high-low</option>
                <option value="views desc">views:high-low</option>
                <option value="views asc">views:low-high</option>
                <option value="likes asc">likes:low-high</option>
                <option value="likes desc">likes:high-low</option>
            </select>
            </div>
            <h4 style="border-bottom:1px solid lightgray;">Price</h4>
            
            <div class="row-filter">
            <label style="margin-top:10px;font-size:large">&#8377</label>
            <input min="0" class="form-control" type="number" placeholder="Min" id="min_price">
                <label style="margin-top:12px;">-</label>
                <input min="0" class="form-control" type="number" placeholder="Max" id="max_price">
            </div>

            <div id="options" style="margin:5px;overflow:scroll;max-height:300px;">

            </div>

            <button onclick="filter()" class="btn btn-primary apply-btn">Apply Filter</button>

        </div>

        <div class="card_list" id="card_items">

        </div>
    </div>

</body>

<script>
    $(document).ready(function() {
        var activetab=sessionStorage.getItem('activeTab');
        console.log("active tab = "+activetab);
        // getData('HOME');
        // getFilterData(activetab);
    });
</script>

</html>