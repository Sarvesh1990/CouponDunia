<?php
    require_once("DBSingleton.php");
    
    $arrayVariables = $_POST['arrayVariables'] ;

    $offerType = $arrayVariables[0];
    $store = $arrayVariables[2];
    $category = $arrayVariables[3];
    
    $offerTypeWOCount = array();
    $storeWOCount = array();
    
    foreach ($offerType as $value){
      $temp = explode("(", $value);
      if($temp[0]==="Coupons")
        array_push($offerTypeWOCount, 0);
      else
        array_push($offerTypeWOCount, 1);
    }
    foreach ($store as $value) {
      $temp = explode("(", $value);
      array_push($storeWOCount, $temp[0]);
    }
    $offerType = implode("','",$offerTypeWOCount);
    $store = implode("','",$storeWOCount);

    $mysqlGetSubCategory = "SELECT DISTINCT csc.Name, COUNT(DISTINCT c.CouponID) AS totalCoupon
                FROM couponcategoryinfo ci, couponcategories cc, couponsubcategories csc, coupon c, website w
                WHERE cc.URLKeyword = '".$category[0]."' AND ci.CategoryID = cc.CategoryID AND csc.SubCategoryID = ci.SubCategoryID 
                AND c.CouponID = ci.CouponID AND c.WebsiteID = w.WebsiteID AND c.IsDeal IN ('".$offerType."') AND w.WebsiteName IN ('".$store."')
                GROUP BY csc.Name";

    $conn = DBConnection::getInstance()->database;
    $retval = mysqli_query($conn, $mysqlGetSubCategory);
    if(!$retval )
    {
      echo "Could not connect to database"."<br>";
      die('Could not get data: ' . mysql_error());
    }

    $resultArray = array();
    array_push($resultArray, "All Sub-Categories");

    while($row = mysqli_fetch_array($retval, MYSQL_ASSOC))
    {
        array_push($resultArray, "$row[Name]($row[totalCoupon])");
    }
    echo json_encode($resultArray);
    mysqli_close($conn);
?>