<?php
  require_once("db.php");
  $couponsPerPage = 10;
  if(!$_GET && !$_POST){
    $categories = CategoryModel::getCategories();
    include('index.html');
  }
  else if(!$_POST){
    $category = $_GET["category"];
    $offerType = CategoryModel::getOfferType($category);
    $subCategory = CategoryModel::getSubCategory($category);
    $store = CategoryModel::getStore($category);
    $coupons = CategoryModel::getCoupons($category, $couponsPerPage);
    include('filter_view.html');
  }
  else{
    $arrayVariables = $_POST['arrayVariables'] ;

    $offerType = $arrayVariables[0];
    $subCategory = $arrayVariables[1];
    $store = $arrayVariables[2];
    $category = $arrayVariables[3];
    $pageNumber = $arrayVariables[4];
    
    $offerTypeWOCount = array();
    $subCategoryWOCount = array();
    $storeWOCount = array();
    
    foreach ($offerType as $value){
      $temp = explode("(", $value);
      if($temp[0]==="Coupons")
        array_push($offerTypeWOCount, 0);
      else
        array_push($offerTypeWOCount, 1);
    }
    foreach ($subCategory as $value) {
      $temp = explode("(", $value);
      array_push($subCategoryWOCount, $temp[0]);
    }
    foreach ($store as $value) {
      $temp = explode("(", $value);
      array_push($storeWOCount, $temp[0]);
    }

    $offerType = implode("','",$offerTypeWOCount);
    $subCategory = implode("','",$subCategoryWOCount);
    $store = implode("','",$storeWOCount);

    if($arrayVariables[5]==="OfferType"){
      $offerType = CategoryModel::getFilteredOfferType($category[0],$subCategory,$store);
      $resultArray = array();
      array_push($resultArray, "All");
      $row = mysqli_fetch_array($offerType, MYSQL_ASSOC);
      if($row['IsDeal']==='0'){
          array_push($resultArray, "Deals(0)");
          array_push($resultArray, "Coupons($row[totalCoupon])");
      }
      else if($row['IsDeal']==='1'){
          array_push($resultArray, "Deals($row[totalCoupon])");
          $row = mysqli_fetch_array($offerType, MYSQL_ASSOC);
          if($row['totalCoupon']>0){
              array_push($resultArray, "Coupons($row[totalCoupon])");
          }
          else{
              array_push($resultArray, "Coupons(0)");
          }
      }
      else{
          array_push($resultArray, "Deals(0)"); 
          array_push($resultArray, "Coupons(0)");
      }
      echo json_encode($resultArray);
    }
    if($arrayVariables[5]==="SubCategory"){
      $subCategory = CategoryModel::getFilteredSubCategory($category[0],$offerType,$store);
      $resultArray = array();
      array_push($resultArray, "All Sub-Categories");

      while($row = mysqli_fetch_array($subCategory, MYSQL_ASSOC))
      {
          array_push($resultArray, "$row[Name]($row[totalCoupon])");
      }
      echo json_encode($resultArray);
    } 
    if($arrayVariables[5]==="Store"){
      $store= CategoryModel::getFilteredStore($category[0],$offerType,$subCategory);
      $resultArray = array();
      array_push($resultArray, "All Stores");
      while($row = mysqli_fetch_array($store, MYSQL_ASSOC))
      {
          array_push($resultArray, "$row[WebsiteName]($row[totalCoupon])");
      }
      echo json_encode($resultArray);
    }
    if($arrayVariables[5]==="Coupons"){
      if($pageNumber[0]==="0"){
        $coupons= CategoryModel::getFilteredCoupons($category[0],$offerType,$subCategory,$store,0,$couponsPerPage);
        echo "<div id = 'coupons'>";
        echo "<ul>";
        echo "<h2>List of Coupons below</h2>";
        $count=0;
        while($row = mysqli_fetch_array($coupons, MYSQL_ASSOC))
        {
            if($count<$couponsPerPage){
                echo "<li>-------------------------------------------------------------------------------------------------------------------<br>
                      {$row['Title']}<br>";
                if("$row[CouponCode]" != NULL){
                    echo "{$row['CouponCode']}<br>";
                }
                else{
                    echo "Its a deal<br>";
                }
                echo"{$row['WebsiteName']}<br>
                </li>";
            }
            $count++;
        }
        echo "</ul>";
        echo "</div>";
        echo "<div id = 'page'>";
        $pageNo = 1;
        echo "<br>";
        echo "<ul class = 'pageNumbers'>";
        echo "<li class='pageSelected'>$pageNo</li>";
        while((int)(($count-1)/$couponsPerPage) > 0){
            $pageNo++;
            echo "<li>$pageNo</li>";
            $count = $count-$couponsPerPage;
        }
        echo "</ul>";
        echo "</div>";
      }
      else if($pageNumber[0]>"0"){
        $lowerLimit = (($pageNumber[0]-1)*$couponsPerPage);
        $coupons= CategoryModel::getFilteredCoupons($category[0],$offerType,$subCategory,$store,$lowerLimit,$couponsPerPage);
        echo "<h2>List of Coupons below</h2>";
        while($row = mysqli_fetch_array($coupons, MYSQL_ASSOC))
        {
            echo "<li>-------------------------------------------------------------------------------------------------------------------<br>
                  {$row['Title']}<br>";
            if("$row[CouponCode]" != NULL){
                echo "{$row['CouponCode']}<br>";
            }
            else{
                echo "Its a deal<br>";
            }
            echo"{$row['WebsiteName']}<br>
            </li>";
        }
      }
    }
  }
?>    