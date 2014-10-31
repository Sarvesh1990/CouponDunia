<?php
  require_once("DBSingleton.php");
  class CategoryModel
  {
    public function __construct()
    {
        parent::__construct();
    }
    public function getCategories ()
    {
        return DBConnection::getData("SELECT c.Name, c.URLKeyword
            FROM CouponCategories c");
    }
    public function getOfferType ($category){
      return DBConnection::getData("SELECT c.IsDeal , COUNT(c.IsDeal) AS totalCoupon
                FROM Coupon c, CouponCategoryInfo ci, CouponCategories cc 
                WHERE cc.URLKeyword = '".$category."' AND ci.CategoryID = cc.CategoryID AND c.CouponID = ci.CouponID
                GROUP BY c.IsDeal ORDER BY c.IsDeal DESC");
    }
    public function getSubCategory ($category){
      return DBConnection::getData("SELECT DISTINCT csc.Name, COUNT(DISTINCT ci.CouponID) AS totalCoupon
                FROM CouponCategoryInfo ci, CouponCategories cc, CouponSubCategories csc
                WHERE cc.URLKeyword = '".$category."' AND ci.CategoryID = cc.CategoryID AND csc.SubCategoryID = ci.SubCategoryID
                GROUP BY csc.Name");
    }
    public function getStore ($category){
      return DBConnection::getData("SELECT DISTINCT w.WebsiteName, COUNT(DISTINCT c.CouponID) AS totalCoupon
                FROM Website w, Coupon c, CouponCategoryInfo ci, CouponCategories cc 
                WHERE cc.URLKeyword = '".$category."' AND ci.CategoryID = cc.CategoryID AND c.CouponID = ci.CouponID 
                AND c.WebsiteID = w.WebsiteID
                GROUP BY w.WebsiteName ORDER BY w.Views DESC");
    }
    public function getCoupons ($category, $couponsPerPage){
      return DBConnection::getData("SELECT DISTINCT c.CouponCode, c.Title, w.WebsiteName
                FROM Website w, Coupon c, CouponCategoryInfo ci, CouponCategories cc 
                WHERE cc.URLKeyword = '".$category."' AND ci.CategoryID = cc.CategoryID AND c.CouponID = ci.CouponID 
                AND c.WebsiteID = w.WebsiteID
                ORDER BY c.CouponPopularityScore DESC LIMIT 0,".$couponsPerPage."");
    }
    public function getFilteredOfferType ($category, $subCategory, $store){
      return DBConnection::getData("SELECT c.IsDeal, COUNT(c.IsDeal) AS totalCoupon 
                FROM Coupon c, CouponCategoryInfo ci, CouponCategories cc, CouponSubCategories csc, Website w
                WHERE cc.URLKeyword = '".$category."' AND ci.CategoryID = cc.CategoryID AND ci.SubCategoryID = csc.SubCategoryID AND c.CouponID = ci.CouponID  
                AND c.WebsiteID = w.WebsiteID AND csc.Name IN ('".$subCategory."') AND w.WebsiteName IN ('".$store."')
                GROUP BY c.IsDeal DESC");
    }
    public function getFilteredSubCategory ($category, $offerType, $store){
      return DBConnection::getData("SELECT DISTINCT csc.Name, COUNT(DISTINCT c.CouponID) AS totalCoupon
                FROM CouponCategoryInfo ci, CouponCategories cc, CouponSubCategories csc, Coupon c, Website w
                WHERE cc.URLKeyword = '".$category."' AND ci.CategoryID = cc.CategoryID AND csc.SubCategoryID = ci.SubCategoryID 
                AND c.CouponID = ci.CouponID AND c.WebsiteID = w.WebsiteID AND c.IsDeal IN ('".$offerType."') AND w.WebsiteName IN ('".$store."')
                GROUP BY csc.Name");
    }
    public function getFilteredStore ($category, $offerType, $subCategory){
      return DBConnection::getData("SELECT DISTINCT w.WebsiteName, COUNT(DISTINCT c.CouponID) AS totalCoupon
                FROM CouponCategoryInfo ci, CouponCategories cc, CouponSubCategories csc, Coupon c, Website w
                WHERE cc.URLKeyword = '".$category."' AND ci.CategoryID = cc.CategoryID AND ci.SubCategoryID = csc.SubCategoryID
                AND c.CouponID = ci.CouponID AND w.WebsiteID = c.WebsiteID AND c.IsDeal IN ('".$offerType."') AND csc.Name IN ('".$subCategory."') 
                GROUP BY w.WebsiteName ORDER BY w.Views DESC");
    }
    public function getFilteredCoupons ($category, $offerType, $subCategory, $store, $lowerLimit, $couponsPerPage){
      if($lowerLimit===0){
        return DBConnection::getData("SELECT DISTINCT c.CouponCode, c.Title, w.WebsiteName
                    FROM CouponCategoryInfo ci, CouponCategories cc, CouponSubCategories csc, Coupon c, Website w
                    WHERE cc.URLKeyword = '".$category."' AND ci.CategoryID = cc.CategoryID AND ci.SubCategoryID = csc.SubCategoryID
                    AND c.CouponID = ci.CouponID AND w.WebsiteID = c.WebsiteID 
                    AND c.IsDeal IN ('".$offerType."') AND csc.Name IN ('".$subCategory."') AND w.WebsiteName IN ('".$store."')
                    ORDER BY c.CouponPopularityScore DESC");
      }
      else{
        return DBConnection::getData("SELECT DISTINCT c.CouponCode, c.Title, w.WebsiteName
                    FROM CouponCategoryInfo ci, CouponCategories cc, CouponSubCategories csc, Coupon c, Website w
                    WHERE cc.URLKeyword = '".$category."' AND ci.CategoryID = cc.CategoryID AND ci.SubCategoryID = csc.SubCategoryID
                    AND c.CouponID = ci.CouponID AND w.WebsiteID = c.WebsiteID 
                    AND c.IsDeal IN ('".$offerType."') AND csc.Name IN ('".$subCategory."') AND w.WebsiteName IN ('".$store."')
                    ORDER BY c.CouponPopularityScore DESC LIMIT ".$lowerLimit.",".$couponsPerPage."");
      } 
    }
  }
?>