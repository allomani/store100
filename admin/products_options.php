<?
require('./start.php'); 

if(!$action ||  $action=="product_options_add_ok"){
    
    

 $qrp=db_query("select * from store_products_data where id='$id'"); 
 
 if(db_num($qrp)){
     
     $datap= db_fetch($qrp);
     
     if_products_cat_admin($datap['cat']);
      
    print_admin_path_links($datap['cat'],"<a href='products.php?action=product_edit&id=$id'>$datap[name]</a> / Options");
   

//--------------------------------------
if($action=="product_options_add_ok"){
    db_query("insert into store_products_options (name,`type`,product_id) values ('".db_escape($name)."','".db_escape($type)."','$id')");
    db_query("update store_products_data set count_options = (select count(*) from store_products_options where product_id='".$id."') where id='".$id."'");
}
//------------------------------------

   
  print "<p><a href='products_options.php?action=product_options_add&id=$id' class='add'>Add field</a></p>";
   
   $qr = db_query("select * from store_products_options where product_id='$id'");
   
   if(db_num($qr)){
   print "<table width=100% class=grid>";    
   while($data=db_fetch($qr)){
   
   if($tr_class=='row_1'){$tr_class='row_2';}else{$tr_class='row_1';} 
   
       print "<tr class='$tr_class'><td><a href='products_options.php?action=product_options_edit&id=$id&option_id=$data[id]'>$data[name]</a></td>
       <td>$data[type]</td>
       </tr>";
   }
   
   print "</table>";
   }else{
         print_admin_table("<center> No Options </center>");
   } 
    
    
      
 }else{
     print_admin_table("<center> $phrases[err_wrong_url] </center>");
 }
 
}



//---------------------- add ----------------------------
if($action=="product_options_add"){
    
    $qrp=db_query("select * from store_products_data where id='$id'"); 
 
 if(db_num($qrp)){
     
     $datap= db_fetch($qrp);
     
     if_products_cat_admin($datap['cat']);
      
    print_admin_path_links($datap['cat'],"<a href='products.php?action=product_edit&id=$id'>$datap[name]</a> / <a href='products_options.php?id=$id'>Options</a> / $phrases[add]");
   
   
 print "<center>
 <form action='products_options.php' method='post'>
 <input type=hidden name='action' value='product_options_add_ok'>
 <input type=hidden name='id' value='$id'>
  <table width=90% class=grid>
 
 <tr><td><b>$phrases[the_name]</b></td><td><input type='text' name='name' size=20></td></tr> 
 <tr><td><b>$phrases[the_type]</b></td><td><select name='type'>
<option value='text'>$phrases[textbox]</option>
<option value='textarea'>$phrases[textarea]</option>
<option value='select'>$phrases[select_menu]</option>

<option value='checkbox'>$phrases[checkbox]</option>
</select>
</td></tr>


<tr><td colspan=2 align=center><input type=submit value=\"$phrases[add]\"></td></tr>
</table>
 </form>
 </center>";
    
      
 }else{
     print_admin_table("<center> $phrases[err_wrong_url] </center>");
 }
 
}



//----------------- edit ------------------
if($action=="product_options_edit" || $action=="product_options_value_add" || $action=="product_options_value_edit"){
$option_id = (int) $option_id;
    
        $qrp=db_query("select * from store_products_data where id='$id'"); 
 
 if(db_num($qrp)){
     
     $datap= db_fetch($qrp);
     
     if_products_cat_admin($datap['cat']);

$data_option = db_qr_fetch("select name,`type` from store_products_options where id='$option_id'");
      
    print_admin_path_links($datap['cat'],"<a href='products.php?action=product_edit&id=$id'>$datap[name]</a> / <a href='products_options.php?action=product_options&id=$id'>Options</a> / $data_option[name]");
  
  
  //----------- value add ---------------------
  if($action=="product_options_value_add"){
      db_query("insert into store_products_options_data (cat,name,price_prefix,price) values ('".db_escape($option_id)."','".db_escape($name)."','".db_escape($price_prefix)."','".db_escape($price)."')");
  }
  //------------ value edit ------------------
   if($action=="product_options_value_edit"){ 
   for($i=0;$i<count($value_id);$i++){
   if($del[$i]){
   db_query("delete from store_products_options_data where id='".db_escape($value_id[$i])."'");
   }else{
   db_query("update store_products_options_data set name='".db_escape($name[$i])."',price_prefix='".db_escape($price_prefix[$i])."',price='".db_escape($price[$i])."' where id='".db_escape($value_id[$i])."'");
   }
   }  
   }
  //------------------------------------------
  
  
  if($data_option['type'] == "select"  || $data_option['type'] == "checkbox"){
  
 //------------ add -----------
  print "
  <form action='products_options.php' method=post>
  <input type=hidden name='action' value='product_options_value_add'>
   <input type=hidden name='id' value='$id'>
   <input type=hidden name='option_id' value='$option_id'>
  <table width=100% class=grid>
 
<tr>
 <td>$phrases[the_name] : <input type='text' name='name' value=\"$data[name]\"></td>
 <td>
 $phrases[the_price] :";
 print_select_row("price_prefix",array("+"=>"+","-"=>"-"));
 print "<input type='text' name='price' size=5 dir=ltr>  
 </td>
 <td><input type=submit value=\"$phrases[add]\"></td>
 </tr>
 </table>
 </form><br><br>";
 //--------------------------
 
  
 $qr  = db_query("select * from store_products_options_data where cat='$option_id'");
 if(db_num($qr)){
 print "
 <form action='products_options.php' method='post'>
 <input type=hidden name='action' value='product_options_value_edit'>
   <input type=hidden name='id' value='$id'>
   <input type=hidden name='option_id' value='$option_id'>
   
 <table width=100% class=grid>
 <tr><td><b>$phrases[the_name]</b></td><td><b>$phrases[the_price]</b><td align=center><b>$phrases[delete]</b></td>";
 
 $i=0;
 while($data = db_fetch($qr)){
     
 if($tr_class=='row_1'){$tr_class='row_2';}else{$tr_class='row_1';}
 
 print "<input type=hidden name=\"value_id[$i]\" value=\"$data[id]\">
<tr class='$tr_class'><td><input type='text' name=\"name[$i]\" value=\"$data[name]\"></td>
 <td>";
 print_select_row("price_prefix[$i]",array("+"=>"+","-"=>"-"),$data['price_prefix']);
 print "<input type='text' name=\"price[$i]\" size=5 value=\"$data[price]\" dir=ltr>
 </td>
 <td align=center><input type=checkbox name=\"del[$i]\" value=\"1\"></tr>";    
 $i++;                                      
 }
   print "
 <tr><td colspan=3 align=center><input type='submit' value=\"$phrases[edit]\"></td></tr>
 </table>
 </form>"; 
 }else{
     print_admin_table("<center> No Option Values </center>");
 }
  
 
  }else{
       print_admin_table("<center> No Option values for this type </center>"); 
  }
   
     
 }else{
     print_admin_table("<center> $phrases[err_wrong_url] </center>");
 } 
   
}


//-----------end ----------------
 require(ADMIN_DIR.'/end.php');