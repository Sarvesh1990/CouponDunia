$("#category").on("click", ".hover-grey", function(e){
    var idValue = $(this).children().text();
    var url = "category.php?category=" + idValue;
    window.location.href=url;
})

$(".filterby").click(function(e){
    e.preventDefault();
    $(this).siblings().removeClass("selected");
 	$(this).siblings().addClass("hover-grey");
 	if($(this).hasClass("selected")){
        $(this).addClass("hover-grey");
        $(this).removeClass("selected");
        $("#sub-nav").css("height", "0px");
    }
    else{
        $(this).removeClass("hover-grey");
 	    $(this).addClass("selected");
        $("#sub-nav").css("height", "160px");
    }
    if($(this).text()==='OfferType'){
        $(".offerType").toggle();
        $(".subCategory").css("display", "none");
        $(".store").css("display", "none");
    }
    else if($(this).text()==='SubCategory'){
        $(".offerType").css("display", "none");
        $(".subCategory").toggle();
        $(".store").css("display", "none");
    }
    else if($(this).text()==='Store'){
        $(".offerType").css("display", "none");
        $(".subCategory").css("display", "none");
        $(".store").toggle();
    }
})

$("#sub-nav").on("click", ".sub-menu li", (function(e){
    if(!$(this).siblings().hasClass("subSelected")){
        alert("Please select atleast one parameter!!");
        return;
    }
    if($(this).hasClass("subSelected")){
        $(this).removeClass("subSelected");
        $(this).siblings(".all").removeClass("subSelected");
    }
    else{
        $(this).addClass("subSelected");
        if($(this).siblings().not(".subSelected").length <= 1){
            $(this).siblings(".all").addClass("subSelected");
        }
        if($(this).text().indexOf("All")>=0){
            $(this).siblings().addClass("subSelected");
        }
    }

    $(".filterby").addClass("hover-grey");
    $(".filterby").removeClass("selected");
    $(this).parent().css("display", "none");
    var offerType = [];
    var subCategory = [];
    var store = [];
    var category = [];
    var pageNumber = [];
    $(".subCategory li").each(function() {
        if($(this).hasClass("subSelected")){
            subCategory.push($(this).text());  
        }
    })
    $(".offerType li").each(function() {   
        if($(this).hasClass("subSelected")){
            offerType.push($(this).text());
        }
    })
    $(".store li").each(function() {
        if($(this).hasClass("subSelected")){
            store.push($(this).text());     
        }
    })

    if (window.location.search.split('?').length > 1) {
        var param = window.location.search.split('?')[1];
        var category_name = decodeURIComponent(param.split('=')[1]);
        category.push(category_name);
    }

    pageNumber.push(0);

    var arrayVariables = [];
    arrayVariables.push(offerType);
    arrayVariables.push(subCategory);
    arrayVariables.push(store);
    arrayVariables.push(category);
    arrayVariables.push(pageNumber);
    if(!$(this).parent().hasClass("offerType")){
        $.ajax({ 
            type: "POST",
            url: 'onFilterGetOfferType.php',
            data: (
                {arrayVariables: arrayVariables}
            ),
            datatype: "json",
            success: function(result)
            {
                resultArray = eval(result);
                var i = 0;
                $('.offerType li').each(function(){
                    var splitLi = $(this).text().split('(');
                    if(resultArray[i]){
                        var splitResult = resultArray[i].split('(');
                        if(splitResult[0] == splitLi[0]){          
                            $(this).text(resultArray[i]);
                            i++;
                        }
                        else{
                            $(this).text(splitLi[0]+"(0)");
                        }
                    }
                    else{
                        $(this).text(splitLi[0]+"(0)");
                    }
                })
            }
        })
    }
   if(!$(this).parent().hasClass("subCategory")){
        $.ajax({ 
            type: "POST",
            url: 'onFilterGetSubCategory.php',
            data: (
                {arrayVariables: arrayVariables}
            ),
            datatype: "json",
            success: function(result)
            {
                resultArray = eval(result);
                var i = 0;
                $('.subCategory li').each(function(){
                    var splitLi = $(this).text().split('(');
                    if(resultArray[i]){
                        var splitResult = resultArray[i].split('(');
                        if(splitResult[0] == splitLi[0]){
                            $(this).text(resultArray[i]);
                            i++;
                        }
                        else{
                            $(this).text(splitLi[0]+"(0)");
                        }
                    }
                    else{
                        $(this).text(splitLi[0]+"(0)");
                    }   
                })
            }
        })
    }
    if(!$(this).parent().hasClass("store")){
        $.ajax({ 
            type: "POST",
            url: 'onFilterGetStore.php',
            data: (
                {arrayVariables: arrayVariables}
            ),
            datatype: "json",
            success: function(result)
            {
                resultArray = eval(result);
                var i = 0;
                $('.store li').each(function(){
                    var splitLi = $(this).text().split('(');
                    if(resultArray[i]){
                        var splitResult = resultArray[i].split('(');
                        if(splitResult[0] == splitLi[0]){
                            $(this).text(resultArray[i]);
                            i++;
                        }
                        else{
                            $(this).text(splitLi[0]+"(0)");
                        }
                    }
                    else{
                            $(this).text(splitLi[0]+"(0)");
                    }
                })
            }
        })
    }
    $.ajax({ 
        type: "POST",
        url: 'onFilterGetCoupons.php',
        data: (
            {arrayVariables: arrayVariables}
        ),
        datatype: "html",
        success: function(result)
        {
            $("#coupons-page").empty();
            $("#coupons-page").append(result);
        }
    })
    $("#sub-nav").css("height", "0px");
})
)

$(document).on("click", ".pageNumbers li", (function(e){
    $(this).addClass("pageSelected");
    $(this).siblings().removeClass("pageSelected");
    var offerType = [];
    var subCategory = [];
    var store = [];
    var category = [];
    var pageNumber = [];
    
    $(".subCategory li").each(function() {
        if($(this).hasClass("subSelected")){
            subCategory.push($(this).text());      
        }
    })
    $(".offerType li").each(function() {   
        if($(this).hasClass("subSelected")){
            offerType.push($(this).text());
        }
    })
    $(".store li").each(function() {
        if($(this).hasClass("subSelected")){
            store.push($(this).text());    
        }
    })

    if (window.location.search.split('?').length > 1) {
        var param = window.location.search.split('?')[1];
        var category_name = decodeURIComponent(param.split('=')[1]);
        category.push(category_name);
    }

    pageNumber.push($(this).text());

    var arrayVariables = [];
    arrayVariables.push(offerType);
    arrayVariables.push(subCategory);
    arrayVariables.push(store);
    arrayVariables.push(category);
    arrayVariables.push(pageNumber);
    $.ajax({ 
        type: "POST",
        url: 'onFilterGetCoupons.php',
        data: (
            {arrayVariables: arrayVariables}
        ),
        datatype: "html",
        success: function(result)
        {
            $("#coupons ul").empty();
            $("#coupons ul").append(result);
        }
    })
})
)
