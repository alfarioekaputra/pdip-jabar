<?php
# Security
if (!defined('_IN_PHP')){ header("HTTP/1.1 403 Forbidden"); exit(); }
#
#
# WEBCORE (c)
#

function init_do_list(){
  global $_QUERY;

  if ($_QUERY['sorting']&&$_QUERY['ordering']){
    $_SESSION['sort'][_TABLE_NAME]['by']=$_QUERY['sorting'];
    $_SESSION['sort'][_TABLE_NAME]['order']=$_QUERY['ordering'];
  }

  if ($_POST['reset_search']){
    unset($_SESSION['sort'][_TABLE_NAME]['filter']);
    unset($_SESSION['sort'][_TABLE_NAME]['filterby']);
  }
  elseif ($_POST['filter_keyword']&&$_POST['filter_cat']){
    $_SESSION['sort'][_TABLE_NAME]['filter']=$_POST['filter_keyword'];
    $_SESSION['sort'][_TABLE_NAME]['filterby']=$_POST['filter_cat'];
  }


  return array(
    $_SESSION['sort'][_TABLE_NAME]['by'],
    $_SESSION['sort'][_TABLE_NAME]['order'],
    $_SESSION['sort'][_TABLE_NAME]['filter'],
    $_SESSION['sort'][_TABLE_NAME]['filterby']
  );
}
function get_repvar(){
  return $_SESSION['sort'][_TABLE_NAME];
}
function set_repvar($var){
  $_SESSION['sort'][_TABLE_NAME]=$var;
}
function trimPostED($str){
    $res=str_replace("\r","",$str);
    $res=str_replace("\n","",$res);
    $res=str_replace("\r","",$str);
    $res=str_replace("\n","",$res);
    $res=trim($res);
    return $res;
}
function trimEditor($str){
    $res=trimPostED(htmlspecialchars($str));
    return $res;
}

function make_link($link){
  return _net.'/admin/m/'.($_SERVER['MODULE_ADMIN']).$link;
}
# Header Creator
function make_head($title,$tools){
  echo '<div class="page-header">';
  echo '<div class="page-header-title">'.htmlspecialchars($title).'</div><div style="padding-left:2px">';
  for ($i=0;$i<count($tools);$i++){
    if ($tools[$i][3]){
        $onclick_val=$tools[$i][2];
        if ($tools[$i][0]=='save')
            $onclick_val="this.style.visibility='hidden';{$tools[$i][2]}";
      echo '<a href="#" onclick="'.($onclick_val).'" class="tool-'.($tools[$i][0]).'">'.htmlspecialchars($tools[$i][1]).'</a>';
    }
    else
      echo '<a href="'.make_link($tools[$i][2]).'" class="tool-'.($tools[$i][0]).'">'.htmlspecialchars($tools[$i][1]).'</a>';
    if ($i<count($tools)-1)
      echo '<span class="sep">|</span>';
  }
  echo '</div></div>';
}

# Calendar Builder
function make_calendar($tagName,$firstval){
  echo '<div id="'.$tagName.'_div" style="line-height:14px">';
  echo '<img src="about:blank" alt="" class="hideObj" onerror="calender_write('.
       "'{$tagName}_div','{$tagName}',";
        if ($firstval)
          echo str_replace('-',',',$firstval);
        else
          echo '0,0,0';
  echo ",'input1','width:80px;');".
       '" width="0" height="0" /></div>';
}

# Header List Sorting
function make_sorthead($title,$sortby,$addurl=""){
  $sorted=$_SESSION['sort'][_TABLE_NAME];
  $ordered='ASC';
  $postfix='';
  if ($sorted['by']==$sortby){
    $ordered=($sorted['order']=='ASC')?'DESC':'ASC';
    $pf=($sorted['order']=='ASC')?'d':'u';
    $postfix="<img border=\"0\" width=\"9\" height=\"9\" alt=\"\" src=\""._shr."/ico/{$pf}.gif\" style=\"float:right\" />";
  }
  echo $postfix.'<a href="'.make_link("/sorting/{$sortby}/ordering/{$ordered}{$addurl}").'">'.$title.'</a>';
}

# FILTERED
function make_filter($category,$url_form="/",$white=false){
  $filtered=$_SESSION['sort'][_TABLE_NAME];
  $fkey=$filtered['filter'];
  $fby=$filtered['filterby'];

  echo '<form style="margin:0" method="post" action="'.make_link($url_form).'"><table class="box-kit"';
  if ($white) echo ' style="border:0;border-bottom:1px solid #888888;background:url(\''._shr.'/img/bar_info_hrule.gif\') repeat-x left bottom #ffffff"';
  echo '><tr>';
  echo '<td><img src="'._shr.'/ico/view.gif" width="16" height="16" alt="" /></td>';
  echo '<td>Saring :</td><td><input value="'.htmlspecialchars($fkey).'" type="text" name="filter_keyword" id="q_filter_keyword" class="inputbox" /></td><td>Pada :</td><td>';
  echo '<select name="filter_cat" class="selectbox">';
  for ($i=0;$i<count($category);$i++){
    echo '<option '.(($category[$i][1]==$fby)?'selected="selected"':'').' value="'.($category[$i][1]).'">'.($category[$i][0]).'</option>';
  }
  echo '</select></td>';
  echo '<td><input type="submit" value="Cari" class="button" name="do_search" /></td>';
  echo '<td width="100%">';
  if ($fkey)
    echo '<input type="submit" value="Semua Data" class="button" name="reset_search" />';
  echo '&nbsp;</td>';
  echo '</tr></table></form>';
}
?>
