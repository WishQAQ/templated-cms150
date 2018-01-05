<?php
/**
 *
 * �Զ����
 *
 * @version        $Id: diy.php 1 15:38 2010��7��8��Z tianya $
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
    showMsg('�Ƿ�����!', 'javascript:;');
    exit();
}
//���ӱ����ֶ��ж�
if($required!=''){
if(preg_match('/,/', $required))
    {
        $requireds = explode(',',$required);
        foreach($requireds as $field){
            if($$field==''){
                showMsg('��*�ŵ�Ϊ�������ݣ�����ȷ��д', '-1');
                exit();
            }
        }
    }else{
        if($required==''){
            showMsg('��*�ŵ�Ϊ�������ݣ�����ȷ��д', '-1');
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
//�ж��ֻ������Ƿ���ȷ 
if(!eregi("^18[0-9]{9}|13[0-9]{9}|15[0-9]{9}$",$shouji)) 
{ 
showMsg('�ֻ����벻��,����ȷ��д', '-1'); 
exit(); 
} 

//��֤���� 
if (!eregi("^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3}$",$youxiang)) { 
showMsg('������д��ȷ��E-Mail ��ַ!', '-1'); 
exit(); 

}

//��֤����֤

        $svali = GetCkVdValue();

         if(preg_match("/1/",$safe_gdopen)){

             if(strtolower($vdcode)!=$svali || $svali=='')

             {

                 ResetVdValue();

                 ShowMsg('��֤�����', '-1');

                 exit();

             }  

         }
//�жϽ���
    {
        $dede_fields = empty($dede_fields) ? '' : trim($dede_fields);
        $dede_fieldshash = empty($dede_fieldshash) ? '' : trim($dede_fieldshash);
        if(!empty($dede_fields))
        {
            if($dede_fieldshash != md5($dede_fields.$cfg_cookie_encode))
            {
                showMsg('����У�鲻�ԣ����򷵻�', '-1');
                exit();
            }
        }
        $diyform = $dsql->getOne("select * from #@__diyforms where diyid='$diyid' ");
        if(!is_array($diyform))
        {
            showmsg('�Զ����������', '-1');
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
                $bkmsg = '�����ɹ�������ת����б�ҳ...';
            }
            else
            {
$goto = !empty($cfg_cmspath) ? $cfg_cmspath : '/';
//                $bkmsg = 'Ԥ����Ϣ�����ɹ�����ȴ�������Ա�����뱣��ͨѶ��ͨ�����ǻ��ڵ�һʱ�����ȷ�ϣ�лл��......';
				echo "<script>alert('Ԥ���ɹ����뱣���ֻ���ͨ������������֪ͨ��ݵ��ţ�'); history.go(index.php)</script>";
            }
        
			showmsg($bkmsg, $goto,0,0);
        }//�����ʼ���ʼ

			    $email="13611050312@139.com";//�������������

                $mailtitle = "{$spmc}\r\n {$xingming}\r\n �ֻ�:{$shouji}";

                $mailbody = "{$spmc}\r\n ����:{$xingming}\r\n��ַ:{$shouhuo}\r\n  �ֻ�:{$shouji} ";

                $mailbody .=$tc.",  ".$xm.",  ".$dh.",  ".$dz.",  ".$fk.",  ".$ly.",  "."\r\n";

                $headers = "From: ".$cfg_adminemail."\r\nReply-To: ".$cfg_adminemail;

                $mailtype = 'TXT';

                require_once(DEDEINC.'/mail.class.php');

                $smtp = new smtp($cfg_smtp_server,$cfg_smtp_port,true,$cfg_smtp_usermail,$cfg_smtp_password);

                $smtp->debug = false;

                $smtp->sendmail($email,$cfg_webname,$cfg_smtp_usermail, $mailtitle, $mailbody, $mailtype);

				//�����ʼ�����
    }
}
/*----------------------------
function list(){ }
---------------------------*/
else if($action == 'list')
{
    if(empty($diy->public))
    {
        showMsg('��̨�ر�ǰ̨���', 'javascript:;');
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
        showMsg('��̨�ر�ǰ̨���' , 'javascript:;');
        exit();
    }

    if(empty($id))
    {
        showMsg('�Ƿ�������δָ��id', 'javascript:;');
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
        showmsg('����ʵļ�¼�����ڻ�δ�����', '-1');
        exit();
    }

    $fieldlist = $diy->getFieldList();
    include DEDEROOT."/templets/plus/{$diy->viewTemplate}";
}

