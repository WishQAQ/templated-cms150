<?php
function litimgurls($imgid=0)
{
    global $lit_imglist,$dsql;
    //��ȡ���ӱ�
    $row = $dsql->GetOne("SELECT c.addtable FROM #@__archives AS a LEFT JOIN #@__channeltype AS c 
                                                            ON a.channel=c.id where a.id='$imgid'");
    $addtable = trim($row['addtable']);
    
    //��ȡͼƬ���ӱ�imgurls�ֶ����ݽ��д���
    $row = $dsql->GetOne("Select imgurls From `$addtable` where aid='$imgid'");
    
    //����inc_channel_unit.php��ChannelUnit��
    $ChannelUnit = new ChannelUnit(2,$imgid);
    
    //����ChannelUnit����GetlitImgLinks������������ͼ
    $lit_imglist = $ChannelUnit->GetlitImgLinks($row['imgurls']);
    
    //���ؽ��
    return $lit_imglist;
}


/*dedecms����ɸѡ���� By scv ����֧��Ⱥ��217479292 �ַ����˺���*/
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
*  �����Զ����(���ڷ���)
*
* @access    public
* @param     string  $fieldset  �ֶ��б�
* @param     string  $loadtype  ��������
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
    $dede_addonfields .= '<div class="scv-shaixuan"><b>'.$ctag->GetAtt('itemname').'��</b>';
    switch ($type) {
     case 1:
      $dede_addonfields .= (preg_match("/&".$ctag->GetName()."=/is",$filterarr,$regm) ? '<a title="ȫ��" href="'.str_replace("&".$ctag->GetName()."=".$fields_value,"",$filterarr).'">ȫ��</a>' : '<span>ȫ��</span>').'&nbsp;';
     
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
       '.'<option value="'.str_replace("&".$ctag->GetName()."=".$fields_value,"",$filterarr).'">ȫ��</option>';
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
//����ɸѡOver


//��ȡͼ����һ��ͼ
function firstimg($str_pic)  
{  
$str_sub=str_replace("-lp","","$str_pic");  //������ͼ�е�'"-lp"'  ɾ�����ǵ�һ��ͼ�ĵ�ַ�ˡ�
return $str_sub;  
}  

//��ȡTAG��̬��ַ��  by NIC QQ:2384419
function nic_tag_url($tid)
{
	global $dsql;
	$sql = "select * from #@__tagindex  where id='$tid' ";
    if ($arcRow = $dsql->GetOne($sql)){     
	   
	   //$str = $GLOBALS['cfg_cmspath']."/tag/".ceil($tid/100)."/".Getpinyin($arcRow["tag"])."_".$tid."_1.html";  //Ŀ¼�ṹΪ��ÿ100��IDΪһ��Ŀ¼���� /tag/1/ ��ʼ
       
	   $pinyin_title = GetPinyin(stripslashes($arcRow["tag"]));
       $pinyin_title = str_replace("?","",$pinyin_title);
	   $pinyin_title = str_replace(":","",$pinyin_title);
	   $str = $GLOBALS['cfg_cmspath']."/tag/".$pinyin_title."_".$tid."_1.html";  //Ŀ¼�ṹΪ��/tag/ƴ��_ID_1.html ��ʼ
	}else $str="IDΪ$tid��TAG�ѱ�ɾ����";

	return $str;
}


//��ȡָ�����µ�TAG�����TAG�б�ҳ�ĵ�ַ��  by NIC QQ:2384419
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
//��ʾʱ���� Сʱ ǰ
function GetDateuk($mktime) 
{ 
$oktime=time(); 
if ($oktime-$mktime<60) 

{ 
return "�ո�"; 
} 

if (($oktime-$mktime>=60) && ($oktime-$mktime<3600) ) 

{ 
$a=trim(ceil(($oktime-$mktime)/60)); 
return $a.'����ǰ'; 
} 
if (($oktime-$mktime>=3600) && ($oktime-$mktime<86400) ) 

{ 
$a=trim(ceil(($oktime-$mktime)/3600)); 
return $a.'Сʱǰ'; 
} 
if (($oktime-$mktime>=86400) && ($oktime-$mktime<864000)) 

{ 
$a=trim(ceil(($oktime-$mktime)/86400)); 
return $a."��ǰ"; 
} 

if ($oktime-$mktime>=864000 ) 
{ 
return MyDate("Y-m-d",$mktime); 
} 

}