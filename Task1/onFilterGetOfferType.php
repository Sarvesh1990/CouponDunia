<?php
    require_once("DBSingleton.php");
    
    $arrayVariables = $_POST['arrayVariables'] ;

    $subCategory = $arrayVariables[1];
    $store = $arrayVariables[2];
    $category = $arrayVariables[3];
    
    $subCategoryWOCount = array();
    $storeWOCount = array();
    

    foreach ($subCategory as $value) {
      $temp = explode("(", $value);
      array_push($subCategoryWOCount, $temp[0]);
    }
    foreach ($store as $value) {
      $temp = explode("(", $value);
      array_push($storeWOCount, $temp[0]);
    }

    $subCategory = implode("','",$subCategoryWOCount);
    $store = implode("','",$storeWOCount);

    $mysqlGetOfferType = "SELECT c.IsDeal, COUNT(c.IsDeal) AS totalCoupon 
    FROM coupon c, couponcategoryinfo ci, couponcategories cc, couponsubcategories csc, website w
    WHERE cc.URLKeyword = '".$category[0]."' AND ci.CategoryID = cc.CategoryID AND ci.SubCategoryID = csc.SubCategoryID AND c.CouponID = ci.CouponID  
    AND c.WebsiteID = w.WebsiteID AND csc.Name IN ('".$subCategory."') AND w.WebsiteName IN ('".$store."')
    GROUP BY c.IsDeal DESC";

    $conn = DBConnection::getInstance()->database;
    $retval = mysqli_query($conn, $mysqlGetOfferType);
    if(!$retval )
    {
      echo "Could not connect to database"."<br>";
      die('Could not get data: ' . mysql_error());
    }

    $resultArray = array();
    array_push($resultArray, "All");
    $row = mysqli_fetch_array($retval, MYSQL_ASSOC);
    if($row['IsDeal']==='0'){
        array_push($resultArray, "Deals(0)");
        array_push($resultArray, "Coupons($row[totalCoupon])");
    }
    else if($row['IsDeal']==='1'){
        array_push($resultArray, "Deals($row[totalCoupon])");
        $row = mysqli_fetch_array($retval, MYSQL_ASSOC);
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
    mysqli_close($conn);
?>