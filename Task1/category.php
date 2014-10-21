<!doctype html>
<html>
  <head>
    <!-- Include CSS Style sheet -->
    <link rel="stylesheet" href="css/style.css" />
  </head>
  <body>
    <!-- Use this navigation div as your menu bar div -->
  	<?php
          $couponsPerPage = 20;
          require_once("DBSingleton.php");
          $queryString = $_SERVER['QUERY_STRING'];
          $words = explode( '=', $queryString);
          $category = $words[1];
    ?>
   
    <div>
  		<h1>Coupon Dunia Task 1 </h1>
      <p>
  	</div>
  
  	<div id="navigation">
  		<ul id="nav">
  			<li class="label">Filter By</li>
  			<li class="filterby hover-grey">OfferType</li>
  			<li class="filterby hover-grey">SubCategory</li>
  			<li class="filterby hover-grey">Store</li>
  		</ul>
  	</div>
  	
    <div id = "sub-nav">
  		
      <ul class=" sub-menu offerType ">  
        <?php
          $mysqlGetOfferType = "SELECT c.IsDeal , COUNT(c.IsDeal) AS totalCoupon
                FROM coupon c, couponcategoryinfo ci, couponcategories cc 
                WHERE cc.URLKeyword = '".$category."' AND ci.CategoryID = cc.CategoryID AND c.CouponID = ci.CouponID
                GROUP BY c.IsDeal ORDER BY c.IsDeal DESC";
          $conn = DBConnection::getInstance()->database;
          $retval = mysqli_query($conn, $mysqlGetOfferType);
          if(!$retval )
          {
            echo "Could not connect to database"."<br>";
            die('Could not get data: ' . mysql_error());
          }
          echo "<li class = 'subSelected all'>All</li>";
          $row = mysqli_fetch_array($retval, MYSQL_ASSOC);
          if($row['IsDeal']==='0'){
            echo "<li class='subSelected'>Deals(0)</li>";
            echo "<li class = 'subSelected'>Coupons({$row['totalCoupon']})</li>";
          }
          else if($row['IsDeal']==='1')
          {
            echo "<li class='subSelected'>Deals({$row['totalCoupon']})</li>";
            $row = mysqli_fetch_array($retval, MYSQL_ASSOC);
            if($row['totalCoupon']>0)
              echo "<li class='subSelected'>Coupons({$row['totalCoupon']})</li>";
            else
              echo "<li class='subSelected'>Coupons(0)</li>";
          }
          else{
            echo "<li class='subSelected'>Deals(0)</li>";
            echo "<li class='subSelected'>Coupons(0)</li>"; 
          }
        ?>
      </ul>
  		
      <ul class=" sub-menu subCategory">
        <?php
          $mysqlGetSubCat = "SELECT DISTINCT csc.Name, COUNT(DISTINCT ci.CouponID) AS totalCoupon
                FROM couponcategoryinfo ci, couponcategories cc, couponsubcategories csc
                WHERE cc.URLKeyword = '".$category."' AND ci.CategoryID = cc.CategoryID AND csc.SubCategoryID = ci.SubCategoryID
                GROUP BY csc.Name";
          $conn = DBConnection::getInstance()->database;
          $retval = mysqli_query($conn, $mysqlGetSubCat);
          if(!$retval )
          {
            echo "Could not connect to database";
            die('Could not get data: ' . mysql_error());
          }
          echo "<li class='subSelected all'>All Sub-Categories</li>";
          while($row = mysqli_fetch_array($retval, MYSQL_ASSOC))
          {
              echo "<li class='subSelected'>{$row['Name']}({$row['totalCoupon']})</li>";
          }
        ?>
      </ul>
  		
      <ul class=" sub-menu store">
        <?php
          $count =0;
          $mysqlGetStores = "SELECT DISTINCT w.WebsiteName, COUNT(DISTINCT c.CouponID) AS totalCoupon
                FROM website w, coupon c, couponcategoryinfo ci, couponcategories cc 
                WHERE cc.URLKeyword = '".$category."' AND ci.CategoryID = cc.CategoryID AND c.CouponID = ci.CouponID 
                AND c.WebsiteID = w.WebsiteID
                GROUP BY w.WebsiteName ORDER BY w.Views DESC";
          $conn = DBConnection::getInstance()->database;
          $retval = mysqli_query($conn, $mysqlGetStores);
          if(!$retval )
          {
            echo "Could not connect to database"."<br>";
            die('Could not get data: ' . mysqli_error($conn));
          }
          echo "<li class='subSelected all'>All Stores</li>";
          while($row = mysqli_fetch_array($retval, MYSQL_ASSOC))
          {
              $count = $count+"$row[totalCoupon]";
              echo "<li class='subSelected'>{$row['WebsiteName']}({$row['totalCoupon']})</li>";
          }
        ?>
      </ul>
    </div>
    
    <div id = "coupons-page">
    <div id = "coupons">
      <ul>
        <?php
          $mysqlGetCoupons = "SELECT DISTINCT c.CouponCode, c.Title, w.WebsiteName
                FROM website w, coupon c, couponcategoryinfo ci, couponcategories cc 
                WHERE cc.URLKeyword = '".$category."' AND ci.CategoryID = cc.CategoryID AND c.CouponID = ci.CouponID 
                AND c.WebsiteID = w.WebsiteID
                ORDER BY c.CouponPopularityScore DESC LIMIT 0,".$couponsPerPage." ";
          $conn = DBConnection::getInstance()->database;
          $retval = mysqli_query($conn, $mysqlGetCoupons);
          if(!$retval )
          {
            echo "Could not connect to database"."<br>";
            die('Could not get data: ' . mysqli_error($conn));
          }
        ?>

        <h2>List of Coupons below</h2>

        <?php
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
        ?>
      </ul>
    </div>
    <div id = "page">
      <?php
        $pageNo = 1;
        echo "<br>";
        echo "<ul class = pageNumbers>";
        echo "<li class = pageSelected>$pageNo</li>";
        while((int)(($count-1)/$couponsPerPage) > 0){
            $pageNo++;
            echo "<li>$pageNo</li>";
            $count = $count-$couponsPerPage;
        }
        echo "</ul>";
      ?>
    </div>    
    </div> 
  
  </body>
  
  <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
  <script src="js/app.js"></script>

</html>