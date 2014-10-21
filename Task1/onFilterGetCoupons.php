<?php
    require_once("DBSingleton.php");
    $couponsPerPage = 20;
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

    if($pageNumber[0]==="0"){
        $mysqlGetCoupons = "SELECT DISTINCT c.CouponCode, c.Title, w.WebsiteName
                FROM couponcategoryinfo ci, couponcategories cc, couponsubcategories csc, coupon c, website w
                WHERE cc.URLKeyword = '".$category[0]."' AND ci.CategoryID = cc.CategoryID AND ci.SubCategoryID = csc.SubCategoryID
                AND c.CouponID = ci.CouponID AND w.WebsiteID = c.WebsiteID 
                AND c.IsDeal IN ('".$offerType."') AND csc.Name IN ('".$subCategory."') AND w.WebsiteName IN ('".$store."')
                ORDER BY c.CouponPopularityScore DESC";

        $conn = DBConnection::getInstance()->database;
        $retval = mysqli_query($conn, $mysqlGetCoupons);
        if(!$retval )
        {
          echo "Could not connect to database"."<br>";
          die('Could not get data: ' . mysqli_error($conn));
        }
        echo "<div id = 'coupons'>";
        echo "<ul>";
        echo "<h2>List of Coupons below</h2>";
        $count=0;
        while($row = mysqli_fetch_array($retval, MYSQL_ASSOC))
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
        mysqli_close($conn);
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
        $mysqlGetCoupons = "SELECT DISTINCT c.CouponCode, c.Title, w.WebsiteName
                    FROM couponcategoryinfo ci, couponcategories cc, couponsubcategories csc, coupon c, website w
                    WHERE cc.URLKeyword = '".$category[0]."' AND ci.CategoryID = cc.CategoryID AND ci.SubCategoryID = csc.SubCategoryID
                    AND c.CouponID = ci.CouponID AND w.WebsiteID = c.WebsiteID 
                    AND c.IsDeal IN ('".$offerType."') AND csc.Name IN ('".$subCategory."') AND w.WebsiteName IN ('".$store."')
                    ORDER BY c.CouponPopularityScore DESC LIMIT ".$lowerLimit." ,$couponsPerPage";

        $conn = DBConnection::getInstance()->database;
        $retval = mysqli_query($conn, $mysqlGetCoupons);
        if(!$retval )
        {
          echo "Could not connect to database"."<br>";
          die('Could not get data: ' . mysqli_error($conn));
        }
        echo "<h2>List of Coupons below</h2>";
        while($row = mysqli_fetch_array($retval, MYSQL_ASSOC))
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
        mysqli_close($conn);
    }
?>
