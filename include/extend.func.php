<?php
function litimgurls($imgid=0)
{
    global $lit_imglist,$dsql;
    //获取附加表
    $row = $dsql->GetOne("SELECT c.addtable FROM #@__archives AS a LEFT JOIN #@__channeltype AS c 
                                                            ON a.channel=c.id where a.id='$imgid'");
    $addtable = trim($row['addtable']);
    
    //获取图片附加表imgurls字段内容进行处理
    $row = $dsql->GetOne("Select imgurls From `$addtable` where aid='$imgid'");
    
    //调用inc_channel_unit.php中ChannelUnit类
    $ChannelUnit = new ChannelUnit(2,$imgid);
    
    //调用ChannelUnit类中GetlitImgLinks方法处理缩略图
    $lit_imglist = $ChannelUnit->GetlitImgLinks($row['imgurls']);
    
    //返回结果
    return $lit_imglist;
}


/*dedecms联动筛选功能 By scv 技术支持群：217479292 字符过滤函数*/
function wwwcms_filter($str,$stype="inject") {
if ($stype=="inject")  {
  $str = str_replace(
         array( "select", "insert", "update", "delete", "alter", "cas", "union", "into", "load_file", "outfile", "create", "join", "where", "like", "drop", "modify", "rename", "'", "/*", "*", "../", "./"),
      array("","","","","","","","","","","","","","","","","","","","","",""),
      $str);
} else if ($stype=="xss") {
  $farr = array("/\s+/" ,
                "/<(\/?)(script|META|STYLE|HTML|HEAD|BODY|STYLE |i?frame|b|strong|style|html|img|P|o:p|iframe|u|em|strike|BR|div|a|TABLE|TBODY|object|tr|td|st1:chsdate|FONT|span|MARQUEE|body|title|\r\n|link|meta|\?|\%)([^>]*?)>/isU",
       "/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU",
       );
  $tarr = array(" ",
                "",
       "\\1\\2",
       );
  $str = preg_replace($farr, $tarr, $str);
  $str = str_replace(
         array( "<", ">", "'", "\"", ";", "/*", "*", "../", "./"),
      array("&lt;","&gt;","","","","","","",""),
      $str);
}
return $str;
}
/**
*  载入自定义表单(用于发布)
*
* @access    public
* @param     string  $fieldset  字段列表
* @param     string  $loadtype  载入类型
* @return    string
*/

function AddFilter($channelid, $type=1, $fieldsnamef, $defaulttid, $loadtype='autofield')
{
global $tid,$dsql,$id;
$tid = $defaulttid ? $defaulttid : $tid;
if ($id!="")
{
  $tidsq = $dsql->GetOne(" Select typeid From `dede_archives` where id='$id' ");
  $tid = $tidsq["typeid"];
}
$nofilter = (isset($_REQUEST['TotalResult']) ? "&TotalResult=".$_REQUEST['TotalResult'] : '').(isset($_REQUEST['PageNo']) ? "&PageNo=".$_REQUEST['PageNo'] : '');
$filterarr = wwwcms_filter(stripos($_SERVER['REQUEST_URI'], "list.php?tid=") ? str_replace($nofilter, '', $_SERVER['REQUEST_URI']) : $GLOBALS['cfg_cmsurl']."/plus/list.php?tid=".$tid);
    $cInfos = $dsql->GetOne(" Select * From  `dede_channeltype` where id='$channelid' ");
$fieldset=$cInfos['fieldset'];
$dtp = new DedeTagParse();
    $dtp->SetNameSpace('field','<','>');
    $dtp->LoadSource($fieldset);
    $dede_addonfields = '';
    if(is_array($dtp->CTags))
    {
        foreach($dtp->CTags as $tid=>$ctag)
        {
            $fieldsname = $fieldsnamef ? explode(",", $fieldsnamef) : explode(",", $ctag->GetName());
   if(($loadtype!='autofield' || ($loadtype=='autofield' && $ctag->GetAtt('autofield')==1)) && in_array($ctag->GetName(), $fieldsname) )
            {
                $href1 = explode($ctag->GetName().'=', $filterarr);
    $href2 = explode('&', $href1[1]);
    $fields_value = $href2[0];
    $dede_addonfields .= '<div class="scv-shaixuan"><b>'.$ctag->GetAtt('itemname').'：</b>';
    switch ($type) {
     case 1:
      $dede_addonfields .= (preg_match("/&".$ctag->GetName()."=/is",$filterarr,$regm) ? '<a title="全部" href="'.str_replace("&".$ctag->GetName()."=".$fields_value,"",$filterarr).'">全部</a>' : '<span>全部</span>').'&nbsp;';
     
      $addonfields_items = explode(",",$ctag->GetAtt('default'));
      for ($i=0; $i<count($addonfields_items); $i++)
      {
       $href = stripos($filterarr,$ctag->GetName().'=') ? str_replace("=".$fields_value,"=".urlencode($addonfields_items[$i]),$filterarr) : $filterarr.'&'.$ctag->GetName().'='.urlencode($addonfields_items[$i]);//echo $href;
       $dede_addonfields .= ($fields_value!=urlencode($addonfields_items[$i]) ? '<a title="'.$addonfields_items[$i].'" href="'.$href.'">'.$addonfields_items[$i].'</a>' : '<span>'.$addonfields_items[$i].'</span>')."&nbsp;";
      }
      $dede_addonfields .= '</div>';
     break;
     
     case 2:
      $dede_addonfields .= '<select name="filter"'.$ctag->GetName().'>
       '.'<option value="'.str_replace("&".$ctag->GetName()."=".$fields_value,"",$filterarr).'">全部</option>';
      $addonfields_items = explode(",",$ctag->GetAtt('default'));
      for ($i=0; $i<count($addonfields_items); $i++)
      {
       $href = stripos($filterarr,$ctag->GetName().'=') ? str_replace("=".$fields_value,"=".urlencode($addonfields_items[$i]),$filterarr) : $filterarr.'&'.$ctag->GetName().'='.urlencode($addonfields_items[$i]);
       $dede_addonfields .= '<option value="'.$href.'"'.($fields_value==urlencode($addonfields_items[$i]) ? ' selected="selected"' : '').'>'.$addonfields_items[$i].'</option>
       ';
      }
      $dede_addonfields .= '</select><br/>
      ';
     break;
    }
            }
        }
    }
echo $dede_addonfields;
}
//联动筛选Over


//获取图集第一张图
function firstimg($str_pic)  
{  
$str_sub=str_replace("-lp","","$str_pic");  //把缩略图中的'"-lp"'  删掉就是第一张图的地址了。
return $str_sub;  
}  

//获取TAG静态地址，  by NIC QQ:2384419
function nic_tag_url($tid)
{
	global $dsql;
	$sql = "select * from #@__tagindex  where id='$tid' ";
    if ($arcRow = $dsql->GetOne($sql)){     
	   
	   //$str = $GLOBALS['cfg_cmspath']."/tag/".ceil($tid/100)."/".Getpinyin($arcRow["tag"])."_".$tid."_1.html";  //目录结构为：每100个ID为一个目录，从 /tag/1/ 开始
       
	   $pinyin_title = GetPinyin(stripslashes($arcRow["tag"]));
       $pinyin_title = str_replace("?","",$pinyin_title);
	   $pinyin_title = str_replace(":","",$pinyin_title);
	   $str = $GLOBALS['cfg_cmspath']."/tag/".$pinyin_title."_".$tid."_1.html";  //目录结构为：/tag/拼音_ID_1.html 开始
	}else $str="ID为$tid的TAG已被删除！";

	return $str;
}


//获取指定文章的TAG到相关TAG列表页的地址，  by NIC QQ:2384419
function nic_arc_tag_link($aid)
{
	global $dsql;
	$sql = "select tid from #@__taglist  where aid='$aid' group by tid ";
    $dsql->Execute('ala',$sql);
	while($row=$dsql->GetObject('ala')){ 
	   $url=nic_tag_url($row->tid);
       if ($arcRow = $dsql->GetOne("select * from #@__tagindex  where id='".$row->tid."' ")) $tag=$arcRow["tag"];
	   else $tag="";
	   $str.=" <a href='".$url."' target=_blank><b>".$tag."</b></a> ";
	}
	return $str;
}
//显示时间天 小时 前
function GetDateuk($mktime) 
{ 
$oktime=time(); 
if ($oktime-$mktime<60) 

{ 
return "刚刚"; 
} 

if (($oktime-$mktime>=60) && ($oktime-$mktime<3600) ) 

{ 
$a=trim(ceil(($oktime-$mktime)/60)); 
return $a.'分钟前'; 
} 
if (($oktime-$mktime>=3600) && ($oktime-$mktime<86400) ) 

{ 
$a=trim(ceil(($oktime-$mktime)/3600)); 
return $a.'小时前'; 
} 
if (($oktime-$mktime>=86400) && ($oktime-$mktime<864000)) 

{ 
$a=trim(ceil(($oktime-$mktime)/86400)); 
return $a."天前"; 
} 

if ($oktime-$mktime>=864000 ) 
{ 
return MyDate("Y-m-d",$mktime); 
} 

}