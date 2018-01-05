<?php
/**
 *
 * 自定义表单
 *
 * @version        $Id: diy.php 1 15:38 2010年7月8日Z tianya $
 * @package        DedeCMS.Site
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/../include/common.inc.php");

$diyid = isset($diyid) && is_numeric($diyid) ? $diyid : 0;
$action = isset($action) && in_array($action, array('post', 'list', 'view')) ? $action : 'post';
$id = isset($id) && is_numeric($id) ? $id : 0;

if(empty($diyid))
{
    showMsg('非法操作!', 'javascript:;');
    exit();
}
//增加必填字段判断
if($required!=''){
if(preg_match('/,/', $required))
    {
        $requireds = explode(',',$required);
        foreach($requireds as $field){
            if($$field==''){
                showMsg('带*号的为必填内容，请正确填写', '-1');
                exit();
            }
        }
    }else{
        if($required==''){
            showMsg('带*号的为必填内容，请正确填写', '-1');
            exit();
        }
    }
}
//end
require_once DEDEINC.'/diyform.cls.php';
$diy = new diyform($diyid);

/*----------------------------
function Post(){ }
---------------------------*/
if($action == 'post')
{
    if(empty($do))
    {
        $postform = $diy->getForm(true);
        include DEDEROOT."/templets/plus/{$diy->postTemplate}";
        exit();
    }
    elseif($do == 2)
//判断手机号码是否正确 
if(!eregi("^18[0-9]{9}|13[0-9]{9}|15[0-9]{9}$",$shouji)) 
{ 
showMsg('手机号码不对,请正确填写', '-1'); 
exit(); 
} 

//验证邮箱 
if (!eregi("^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3}$",$youxiang)) { 
showMsg('请您填写正确的E-Mail 地址!', '-1'); 
exit(); 

}

//验证码验证

        $svali = GetCkVdValue();

         if(preg_match("/1/",$safe_gdopen)){

             if(strtolower($vdcode)!=$svali || $svali=='')

             {

                 ResetVdValue();

                 ShowMsg('验证码错误！', '-1');

                 exit();

             }  

         }
//判断结束
    {
        $dede_fields = empty($dede_fields) ? '' : trim($dede_fields);
        $dede_fieldshash = empty($dede_fieldshash) ? '' : trim($dede_fieldshash);
        if(!empty($dede_fields))
        {
            if($dede_fieldshash != md5($dede_fields.$cfg_cookie_encode))
            {
                showMsg('数据校验不对，程序返回', '-1');
                exit();
            }
        }
        $diyform = $dsql->getOne("select * from #@__diyforms where diyid='$diyid' ");
        if(!is_array($diyform))
        {
            showmsg('自定义表单不存在', '-1');
            exit();
        }

        $addvar = $addvalue = '';

        if(!empty($dede_fields))
        {

            $fieldarr = explode(';', $dede_fields);
            if(is_array($fieldarr))
            {
                foreach($fieldarr as $field)
                {
                    if($field == '') continue;
                    $fieldinfo = explode(',', $field);
                    if($fieldinfo[1] == 'textdata')
                    {
                        ${$fieldinfo[0]} = FilterSearch(stripslashes(${$fieldinfo[0]}));
                        ${$fieldinfo[0]} = addslashes(${$fieldinfo[0]});
                    }
                    else
                    {
                        ${$fieldinfo[0]} = GetFieldValue(${$fieldinfo[0]}, $fieldinfo[1],0,'add','','diy', $fieldinfo[0]);
                    }
                    $addvar .= ', `'.$fieldinfo[0].'`';
                    $addvalue .= ", '".${$fieldinfo[0]}."'";
                }
            }

        }

        $query = "INSERT INTO `{$diy->table}` (`id`, `ifcheck` $addvar)  VALUES (NULL, 0 $addvalue); ";

        if($dsql->ExecuteNoneQuery($query))
        {
            $id = $dsql->GetLastID();
            if($diy->public == 2)
            {
                //diy.php?action=view&diyid={$diy->diyid}&id=$id
                $goto = "diy.php?action=list&diyid={$diy->diyid}";
                $bkmsg = '发布成功，现在转向表单列表页...';
            }
            else
            {
$goto = !empty($cfg_cmspath) ? $cfg_cmspath : '/';
//                $bkmsg = '预订信息发布成功，请等待工作人员处理，请保持通讯畅通，我们会在第一时间和您确认，谢谢！......';
				echo "<script>alert('预定成功！请保持手机畅通！发货后会短信通知快递单号！'); history.go(index.php)</script>";
            }
        
			showmsg($bkmsg, $goto,0,0);
        }//发送邮件开始

			    $email="13611050312@139.com";//这里填你的邮箱

                $mailtitle = "{$spmc}\r\n {$xingming}\r\n 手机:{$shouji}";

                $mailbody = "{$spmc}\r\n 姓名:{$xingming}\r\n地址:{$shouhuo}\r\n  手机:{$shouji} ";

                $mailbody .=$tc.",  ".$xm.",  ".$dh.",  ".$dz.",  ".$fk.",  ".$ly.",  "."\r\n";

                $headers = "From: ".$cfg_adminemail."\r\nReply-To: ".$cfg_adminemail;

                $mailtype = 'TXT';

                require_once(DEDEINC.'/mail.class.php');

                $smtp = new smtp($cfg_smtp_server,$cfg_smtp_port,true,$cfg_smtp_usermail,$cfg_smtp_password);

                $smtp->debug = false;

                $smtp->sendmail($email,$cfg_webname,$cfg_smtp_usermail, $mailtitle, $mailbody, $mailtype);

				//发送邮件结束
    }
}
/*----------------------------
function list(){ }
---------------------------*/
else if($action == 'list')
{
    if(empty($diy->public))
    {
        showMsg('后台关闭前台浏览', 'javascript:;');
        exit();
    }
    include_once DEDEINC.'/datalistcp.class.php';
    if($diy->public == 2)
        $query = "SELECT * FROM `{$diy->table}` ORDER BY id DESC";
    else
        $query = "SELECT * FROM `{$diy->table}` WHERE ifcheck=1 ORDER BY id DESC";

    $datalist = new DataListCP();
    $datalist->pageSize = 10;
    $datalist->SetParameter('action', 'list');
    $datalist->SetParameter('diyid', $diyid);
    $datalist->SetTemplate(DEDEINC."/../templets/plus/{$diy->listTemplate}");
    $datalist->SetSource($query);
    $fieldlist = $diy->getFieldList();
    $datalist->Display();
}
else if($action == 'view')
{
    if(empty($diy->public))
    {
        showMsg('后台关闭前台浏览' , 'javascript:;');
        exit();
    }

    if(empty($id))
    {
        showMsg('非法操作！未指定id', 'javascript:;');
        exit();
    }
    if($diy->public == 2)
    {
        $query = "SELECT * FROM {$diy->table} WHERE id='$id' ";
    }
    else
    {
        $query = "SELECT * FROM {$diy->table} WHERE id='$id' AND ifcheck=1";
    }
    $row = $dsql->GetOne($query);

    if(!is_array($row))
    {
        showmsg('你访问的记录不存在或未经审核', '-1');
        exit();
    }

    $fieldlist = $diy->getFieldList();
    include DEDEROOT."/templets/plus/{$diy->viewTemplate}";
}

