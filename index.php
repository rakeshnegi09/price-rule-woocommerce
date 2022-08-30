<?php
/**
 * Plugin Name: Price Rule
 * Description: Price Rule Plugin
 * Version: 1.0
 * Author: Rakesh Negi
 */

// create table name images and cron_images
register_activation_hook(__FILE__, "my_plugin_create_db");
function my_plugin_create_db()
{
    global $wpdb;
    $sql = [];
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . "price_rule";
    $wp_track_table = $table_prefix . "price_rule";

    if ($wpdb->get_var("show tables like '$wp_track_table'") != $wp_track_table)
    {
        $sql[] = "CREATE TABLE $table_name (
				id int(11) NOT NULL AUTO_INCREMENT,
				name varchar(255),
				type varchar(255),
				applicable varchar(255),
				price_type varchar(255),
				value varchar(255),
				date_added timestamp,
				UNIQUE KEY id (id)
			) $charset_collate;";
    }

    if (!empty($sql))
    {
        require_once ABSPATH . "wp-admin/includes/upgrade.php";
        dbDelta($sql);
    }
}

add_action("admin_menu", "my_post_options_box");

function my_post_options_box()
{
    add_menu_page("Price Rule Setting", "Price Rule Setting", "administrator", "plugin_setting_price", "shortcode_setting");
}

function shortcode_setting()
{
?>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
		<div class="container">
		<style>.updated.notice.notice-success.is-dismissible.getting-started {
			display: none;
		}</style>
	
		<?php
    global $wpdb;
    $table = $wpdb->prefix . "price_rule";

    if (isset($_POST["price_rule"]) && $_POST["price_rule"] == "true")
    {
        $wpdb->insert($table, ["name" => $_POST["name"], "applicable" => $_POST["applicable"], "type" => $_POST["type"], "price_type" => $_POST["price_type"], "value" => $_POST["value"], "date_added" => date("Y-m-d") , ]);
    }

    if (isset($_POST["uptsubmit"]))
    {
        $id = $_POST["uptid"];
        $name = $_POST["name"];
        $type = $_POST["type"];
		$price_type = $_POST["price_type"];
		$applicable = $_POST["applicable"];
        $value = $_POST["value"];
        $wpdb->query("UPDATE $table SET name='$name',type='$type',applicable='$applicable',price_type='$price_type',value='$value' WHERE id='$id'");
        echo "<script>location.replace('admin.php?page=plugin_setting_price');</script>";
    }

    if (isset($_GET["upt"]))
    {
        $upt_id = $_GET["upt"];
        $result = $wpdb->get_results("SELECT * FROM $table WHERE id='$upt_id'");
        if (!empty($result))
        { ?>
			<h2>Update Rule</h2>
				<form method="post" action="">
				  <div class="form-group  col-lg-7">
					<label for="name">Rule Name:</label>
					<input type="hidden" name="uptid" value="<?php echo $result[0]->id; ?>" required class="form-control" id="uptid">
					<input type="text" name="name" value="<?php echo $result[0]->name; ?>" required class="form-control" id="name">
				  </div>
				  <div class="form-group  col-lg-7">
					<label for="type">Rule Type</label><br>
					<select required class="form-select" name="type">
					  <option <?php if ($result[0]->type == "simple")
            {
                echo "selected";
            } ?> value="all">All Product</option>
					  <option <?php if ($result[0]->type == "category")
            {
                echo "selected";
            } ?>  value="category">category</option>
					  <option <?php if ($result[0]->type == "nocategory")
            {
                echo "selected";
            } ?>  value="nocategory">No category</option>
					  <option <?php if ($result[0]->type == "weight")
            {
                echo "selected";
            } ?>  value="weight">weight</option>
					</select>

				  </div>
				  <div class="form-group  col-lg-7">
					<label for="type">Price Type</label><br>
					<select required class="form-select" name="price_type">
					  <option <?php if ($result[0]->price_type == "percentage")
            {
                echo "selected";
            } ?> value="percentage">Percentage</option>
					  <option <?php if ($result[0]->price_type == "fixed")
            {
                echo "selected";
            } ?>  value="fixed">Fixed</option>
					  
					</select>

				  </div>
				  <div class="form-group  col-lg-7">
					<label for="type">Applicable Type</label><br>
					<select required class="form-select" name="applicable">
					  <option <?php if ($result[0]->applicable == "base")
            {
                echo "selected";
            } ?> value="base">Base Price</option>
					  <option <?php if ($result[0]->price_type == "total")
            {
                echo "selected";
            } ?>  value="total">Total Price</option>
					  
					</select>

				  </div>
				  <div class="form-group  col-lg-7">
					<label>Value</label>
					<input required type="text"  value = "<?php echo $result[0]->value; ?>" name="value"  class="form-control" id="value">
				  </div>
				  <div class="form-group col-lg-7">
					<button type="submit" name="uptsubmit" value="uptsubmit" class="btn btn-default">Submit</button>
				  </div>
				  
				</form>
		<?php
        }
    }
    else
    {
?><br><br>
	
	
  <h2>Add Rule</h2>
    <form method="post" action="">
	  <div class="form-group col-lg-7">
		<label for="name">Rule Name:</label>
		<input type="text" name="name" required class="form-control" id="name">
	  </div>
	  <div class="form-group col-lg-7">
		<label for="type">Rule Type</label><br>
		<select required class="form-select" name="type">
		  <option value="">Select Rule Type</option>
		  <option value="all">All Product</option>
		  <option value="category">Category</option>
		  <option value="nocategory">No category</option>
		  <option value="weight">weight</option>
		</select>
	  </div>
	   <div class="form-group col-lg-7">
		<label for="type">Price Type</label><br>
		<select required class="form-select" name="price_type">
		  <option value="">Select Price Type</option>
		  <option value="fixed">Fixed</option>
		  <option value="percentage">Percentage</option>
		</select>
	  </div>
	    <div class="form-group col-lg-7">
		<label for="type">Applicable Type</label><br>
		<select required class="form-select" name="applicable">
		  <option value="">Select Applicable Type</option>
		  <option value="base">Base Price</option>
		  <option value="total">Total Price</option>
		</select>
	  </div>
	  <div class="form-group col-lg-7">
		<label>Value</label>
		<input required type="text"  name="value"  class="form-control" id="value">
	  </div>
	  <div class="form-group col-lg-7">
		<button type="submit" name="price_rule" value="true" class="btn btn-default">Submit</button>
	  </div>
	  
	</form>
		<?php
    }
?>
	<table class="table">
    <thead>
      <tr>
	  <th>Sr.No.</th>
        <th>Rule Name</th>
		<th>Price Type</th>
        <th>Rule Type</th>
		<th>Applicable Type</th>
        <th>Value</th>
      </tr>
    </thead>
    <tbody>
	<?php
    if (isset($_GET["del"]))
    {
        $del_id = $_GET["del"];
        $wpdb->query("DELETE FROM $table WHERE id='$del_id'");
        echo "<script>location.replace('admin.php?page=plugin_setting_price');</script>";
    }

    $sql = "SELECT * FROM $table";
    $result = $wpdb->get_results($sql);

    if (!empty($result))
    {
        $count = 0;
        foreach ($result as $row)
        {
            $count++; ?>
      <tr>
		<td><?php echo $count; ?></td>
        <td><?php echo ucfirst($row->name); ?></td>
        <td><?php echo ucfirst($row->type); ?></td>
		<td><?php echo ucfirst($row->price_type); ?></td>
		<td><?php echo ucfirst($row->applicable); ?></td>
        <td><?php echo $row->value; ?></td>
		<td width='25%'><a href='admin.php?page=plugin_setting_price&upt=<?php echo $row->id; ?>'><button type='button'>UPDATE</button></a> <a href='admin.php?page=plugin_setting_price&del=<?php echo $row->id; ?>'><button type='button'>DELETE</button></a></td>
      </tr>
	<?php
        }
    } ?> 
    </tbody>
  </table>
	</div>
	<?php
} 


if ( ! is_admin() ) add_filter( 'woocommerce_get_price_html', 'bbloomer_alter_price_display', 9999, 2 );
 
function bbloomer_alter_price_display( $price_html, $product ) {
     global $wpdb;
	$orig_price = wc_get_price_to_display( $product );
    $price_html = $orig_price;
	$table = $wpdb->prefix . "price_rule";
	$sql = "SELECT * FROM $table";
    $result = $wpdb->get_results($sql);
    if (!empty($result)){
		$sum = 0;
		foreach($result as $row){
			$new_addon_price = 0;
			/*********all Product************/
			if($row->type == 'all'){
				if($row->price_type == 'percentage' && $row->applicable == 'base' ){					
					$new_addon_price = ($orig_price*$row->value)/100;							
				}
				if($row->price_type == 'fixed' && $row->applicable == 'base'){					
					$new_addon_price = $row->value;							
				}
			}
			
			
			/*********No Category************/
			if($row->type == 'nocategory'){
				
				if($row->price_type == 'percentage' && $row->applicable == 'base' ){					
					$new_addon_price = ($orig_price*$row->value)/100;							
				}
				if($row->price_type == 'fixed' && $row->applicable == 'base'){					
					$new_addon_price = $row->value;							
				}
			}
			
			$sum += $new_addon_price;			
		}
		
		$total_price = $orig_price + $sum;
		
		$sum = 0;
		
		foreach($result as $row){
			$new_addon_price = 0;
			if($row->type == 'all'){
				if($row->price_type == 'percentage' && $row->applicable == 'total' ){					
					$new_addon_price = ($total_price*$row->value)/100;							
				}
				if($row->price_type == 'fixed' && $row->applicable == 'total'){					
					$new_addon_price = $row->value;							
				}
			}
			$sum += $new_addon_price;		
		}
		
		$total_price_final = $total_price + $sum;
		
		$price_html = wc_price($total_price_final );
	}
	
    return $price_html;
 
}

?>
