<!--
 /**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement(EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 * @category  Ced
 * @package   CedEbay
 */
 -->
<?php if(isset($ebay_categories) && !empty($ebay_categories)) { ?>
<option value="">--Select--</option>
<?php foreach($ebay_categories as $ebay_cat) {
 if(isset($profile_ebay_categories) && !empty($profile_ebay_categories)) {
   if(isset($profile_ebay_categories['level_'.$ebay_cat['level']]) && $profile_ebay_categories['level_'.$ebay_cat['level']] == $ebay_cat['id']){ ?>
    <option value="<?php echo $ebay_cat['id'] ?>" selected="selected" isLeaf="<?php echo $ebay_cat['isLeaf'] ?>"><?php echo $ebay_cat['name'] ?></option>
  <?php } else { ?>
<option value="<?php echo $ebay_cat['id'] ?>" isLeaf="<?php echo $ebay_cat['isLeaf'] ?>"><?php echo $ebay_cat['name'] ?></option>

<?php }
 } else { ?>
<option value="<?php echo $ebay_cat['id'] ?>" isLeaf="<?php echo $ebay_cat['isLeaf'] ?>"><?php echo $ebay_cat['name'] ?></option>
 <?php }
} ?>
<?php } ?>
