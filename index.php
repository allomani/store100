<?
require("global.php");

define(THIS_PAGE,"index");
require(CWD . "/includes/framework_start.php");   
//----------------------------------------------------


  
 // --------------------------- Votes ---------------------------------
  if($action =="votes" || $action == "vote_add"){
      $vote_id = intval($vote_id);
      
      compile_hook('votes_start');  
      
          if ($action=="vote_add")
          {
           
               
            if(!$_COOKIE['vote_added']){
                  db_query("update store_votes set cnt=cnt+1 where id='$vote_id'");
                  }else{
                          open_table();

                          print "<center>".str_replace('{vote_expire_hours}',$settings['votes_expire_hours'],$phrases['err_vote_expire_hours'])."</center>" ;
                      close_table();
                      }

          }

 
 $qr =  db_query("select * from store_votes_cats where ".iif($id,"id='$id'","active=1")); 
 if(db_num($qr)){ 
  $data = db_fetch($qr);   
          open_table("$data[title]");


      
          $qr_stat=db_query("select * from store_votes where cat='$data[id]'");


if (db_num($qr_stat)){
while($data_stat=db_fetch($qr_stat)){
$votes[] = $data_stat;
$total = $total + $data_stat['cnt'];
}

    if($total){
         print "<br>";

  $l_size = @getimagesize("$style[images]/leftbar.gif");
    $m_size = @getimagesize("$style[images]/mainbar.gif");
    $r_size = @getimagesize("$style[images]/rightbar.gif");


 print "<table cellspacing=\"0\" cellpadding=\"2\" border=\"0\" align=\"center\">";
foreach($votes as $data_stat){

    $rs[0] = $data_stat['cnt'];
    $rs[1] =  substr(100 * $data_stat['cnt'] / $total, 0, 5);
    $title = $data_stat['title'];

    print "<tr><td>";


   print " $title:</td><td dir=ltr align='$global_align'><img src=\"$style[images]/leftbar.gif\" height=\"$l_size[1]\" width=\"$l_size[0]\">";
    print "<img src=\"$style[images]/mainbar.gif\"  height=\"$m_size[1]\" width=". $rs[1] * 2 ."><img src=\"$style[images]/rightbar.gif\" height=\"$r_size[1]\" width=\"$l_size[0]\">
    </td><td>
    $rs[1] % ($rs[0])</td>
    </tr>\n";

}
print "</table>";
}else{
        print "<center> $phrases[no_results] </center>";
        }
}else{
    print "<center> $phrases[no_options] </center>";
}
 }else{
     print "<center>$phrases[err_wrong_url]</center>";
 }

close_table();

if($settings['other_votes_show']){
  $qr = db_query("select id,title from store_votes_cats where ".iif($id,"id != '$id'","active != 1")." order by $settings[other_votes_orderby] limit $settings[other_votes_limit]");
if(db_num($qr)){  
open_table("$phrases[prev_votes]");
while($data=db_fetch($qr)){
    print "<li><a href='index.php?action=votes&id=$data[id]'>$data[title]</li>";
}
close_table();
}
}
 compile_hook('votes_end'); 
  }

 //---------------------------- Pages -------------------------------------
if($action=="pages"){
        $qr = db_query("select * from store_pages where active=1 and id='".intval($id)."'");

         compile_hook('pages_start');

         if(db_num($qr)){
         $data = db_fetch($qr);
          compile_hook('pages_before_data_table');
         open_table("$data[title]");
          compile_hook('pages_before_data_content');
                  run_php($data['content']);
           compile_hook('pages_after_data_content');
                  close_table();
          compile_hook('pages_after_data_table');
                  }else{
                  open_table();
                          print "<center> $phrases[err_no_page] </center>";
                          close_table();
                          }
             compile_hook('pages_end');
             }
//--------------------- Copyrights ----------------------------------
 if($action=="copyrights"){
     global $global_lang;

     open_table();
if($global_lang=="arabic"){
     print "<center>
     ���� �� : $_SERVER[HTTP_HOST]   �� <a href='http://allomani.com/' target='_blank'>  �������� ������� �������� </a> <br><br>

   ���� ���� ������� ������
                        <a target=\"_blank\" href=\"http://allomani.com/\">
                       ������� ������� ��������
                        � ".SCRIPT_YEAR;
  }else{
       print "<center>
     Licensed for : $_SERVER[HTTP_HOST]   by <a href='http://allomani.com/' target='_blank'>Allomani&trade; Programming Services </a> <br><br>

   <p align=center>
Programmed By <a target=\"_blank\" href=\"http://allomani.com/\"> Allomani&trade; Programming Services </a> � ".SCRIPT_YEAR;
      }
     close_table();
         }

//---------------------------- Forget Password -------------------------
 if($action == "forget_pass" || $action=="lostpwd" ||  $action=="rest_pwd"){
     if($action == "forget_pass"){$action="lostpwd";}

        connector_members_rest_pwd($action,$useremail);
         }
//-------------------------- Resend Active Message ----------------
if($action=="resend_active_msg"){

   $qr = db_query("select * from ".members_table_replace('store_clients') ." where ".members_fields_replace('email')."='".db_escape($email)."'",MEMBER_SQL);
   if(db_num($qr)){
           $data = db_fetch($qr) ;
           open_table();
   if(in_array($data[members_fields_replace('usr_group')],$members_connector['allowed_login_groups'])){
    print "<center> $phrases[this_account_already_activated] </center>";
    }elseif(in_array($data[members_fields_replace('usr_group')],$members_connector['disallowed_login_groups'])){
            print "<center> $phrases[closed_account_cannot_activate] </center>";
    }elseif(in_array($data[members_fields_replace('usr_group')],$members_connector['waiting_conf_login_groups'])){
   snd_email_activation_msg($data[members_fields_replace('id')]);
   print "<center>  $phrases[activation_msg_sent_successfully] </center>";
   }
   close_table();
   }else{
           open_table();
           print "<center>  $phrases[email_not_exists] </center>";
           close_table();
           }
        }
//-------------------------- Active Account ------------------------
if($action == "activate_email"){
        open_table("$phrases[active_account]");
        $qr = db_query("select * from store_confirmations where code='".db_escape($code)."'");
if(db_num($qr)){
$data = db_fetch($qr);

$qr_member=db_query("select ".members_fields_replace('id')." from ".members_table_replace('store_clients') ." where ".members_fields_replace('id')."='$data[cat]'  and ".members_fields_replace('usr_group')."='".$members_connector['waiting_conf_login_groups'][0]."'",MEMBER_SQL);

 if(db_num($qr_member)){
      db_query("update ".members_table_replace('store_clients') ." set ".members_fields_replace('usr_group')."='".$members_connector['allowed_login_groups'][0]."' where ".members_fields_replace('id')."='$data[cat]'",MEMBER_SQL);
      db_query("delete from store_confirmations where code='".db_escape($code)."'");
    print "<center> $phrases[active_acc_succ] </center>" ;
 }else{
      print "<center> $phrases[active_acc_err] </center>" ;
 }
        }else{
      print "<center> $phrases[active_acc_err] </center>" ;
 }
        close_table();
        }

//-------------------------- Confirmations ------------------------
if($action == "confirmations"){
    //----- email change confirmation ------//
if($op=="member_email_change"){
open_table();
$qr=db_query("select * from store_confirmations where code='".db_escape($code)."' and type='".db_escape($op)."'");

if(db_num($qr)){
$data = db_fetch($qr);

      db_query("update ".members_table_replace('store_clients')." set ".members_fields_replace('email')."='".$data['new_value']."' where ".members_fields_replace('id')."='$data[cat]'",MEMBER_SQL);
      db_query("delete from store_confirmations where code='".db_escape($code)."'");
    print "<center> $phrases[your_email_changed_successfully] </center>" ;
}else{
     print "<center> $phrases[err_wrong_url] </center>" ;
}
 close_table();
}

        }
        
 //----------- Client CP ------//
 require(CWD . "/client_cp.php");
//------------------------ Members Login ---------------------------
 if($action=="login"){
 if(@file_exists("login_form.php")){
     include "login_form.php";
 }else{
    $re_link = htmlspecialchars($re_link) ;

         open_table();
print "<script type=\"text/javascript\" src=\"js/md5.js\"></script>

<form method=\"POST\" action=\"login.php\" onsubmit=\"md5hash(password, md5pwd, md5pwd_utf, 1)\">

<input type=hidden name='md5pwd' value=''>
<input type=hidden name='md5pwd_utf' value=''>


<input type=hidden name=action value=login>
<input type=hidden name=re_link value=\"$re_link\">

<table border=\"0\" width=\"200\">
        <tr>
                <td height=\"15\"><span>$phrases[username] :</span></td>
                <td height=\"15\"><input type=\"text\" name=\"username\" size=\"10\"></td>
        </tr>
        <tr>
                <td height=\"12\"><span>$phrases[password]:</span></td>
                <td height=\"12\" ><input type=\"password\" name=\"password\" size=\"10\"></td>
        </tr>
        <tr>
                <td height=\"23\" colspan=2>
                <p align=\"center\"><input type=\"submit\" value=\"$phrases[login]\"></td>
        </tr>
        <tr>
                <td height=\"38\" colspan=2><span>
                <a href=\"index.php?action=register\">$phrases[newuser]</a><br>
                <a href=\"index.php?action=forget_pass\">$phrases[forgot_pass]</a></span></td>
        </tr>
</table>
</form>\n";
close_table();
 }
         }
                
//---------------------------------------------------
require(CWD . "/includes/framework_end.php"); 
?>