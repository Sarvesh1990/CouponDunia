<?php
    require_once("DBSingleton.php");
    
    $arrayVariables = $_POST['arrayVariables'] ;

    $offerType = $arrayVariables[0];
    $subCategory = $arrayVariables[1];
    $category = $arrayVariables[3];
    
    $offerTypeWOCount = array();
    $subCategoryWOCount = array();
    
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
    $offerType = implode("','",$offerTypeWOCount);
    $subCategory = implode("','",$subCategoryWOCount);

    $mysqlGetStores = "SELECT DISTINCT w.WebsiteName, COUNT(DISTINCT c.CouponID) AS totalCoupon
                FROM couponcategoryinfo ci, couponcategories cc, couponsubcategories csc, coupon c, website w
                WHERE cc.URLKeyword = '".$category[0]."' AND ci.CategoryID = cc.CategoryID AND ci.SubCategoryID = csc.SubCategoryID
                AND c.CouponID = ci.CouponID AND w.WebsiteID = c.WebsiteID AND c.IsDeal IN ('".$offerType."') AND csc.Name IN ('".$subCategory."') 
                GROUP BY w.WebsiteName ORDER BY w.Views DESC";

    $conn = DBConnection::getInstance()->database;
    $retval = mysqli_query($conn, $mysqlGetStores);
    if(!$retval )
    {
      echo "Could not connect to database"."<br>";
      die('Could not get data: ' . mysql_error());
    }

    $resultArray = array();
    array_push($resultArray, "All Stores");

    while($row = mysqli_fetch_array($retval, MYSQL_ASSOC))
    {
        array_push($resultArray, "$row[WebsiteName]($row[totalCoupon])");
    }
    echo json_encode($resultArray);
    mysqli_close($conn);
?>
