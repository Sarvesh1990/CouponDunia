<!doctype html>
<html>
<head>
    <!-- Include css stylesheet -->
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
	<!-- List of categories -->
  	<div>
  		<h1>Coupon Dunia Task 1 </h1>
  	</div>
  	<div>
  		<h2> <b> Select a category </b> </h2>  	

    <?php
    //Singleton file to create database mysql object
    require_once("DBSingleton.php");
    $mysqlGetCoupons = 'SELECT c.Name, c.URLKeyword
            FROM couponcategories c';
    $conn = DBConnection::getInstance()->database;
    $retval = mysqli_query($conn, $mysqlGetCoupons);
    if(!$retval )
    {
      echo "Could not connect to database";
      die('Could not get data: ' . mysqli_error($conn)); 
    } ?>

    <div id="categories">
      <ul id="category">

    <?php while($row = mysqli_fetch_array($retval, MYSQL_ASSOC)){
      echo "<li class='hover-grey'>{$row['Name']}
        <span style='display:none;'>{$row['URLKeyword']}</span>
      </li>"; 
    }
    mysqli_close($conn);
    ?>
  	
     </ul>
    </div>

</body>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
<script src="js/app.js"></script>

</html>