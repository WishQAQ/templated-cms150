<?php

/**
 * 文档digg处理ajax文件
 *
 */
require_once(dirname(__FILE__)."/../include/common.inc.php");

$action = isset($action) ? trim($action) : '';
$id = empty($id)? 0 : intval(preg_replace("/[^\d]/",'', $id));

if($id < 1)
{
	exit();
}
$maintable = '#@__archives';
if($action == 'good')
{
	$dsql->ExecuteNoneQuery("Update `$maintable` set scores = scores + {$cfg_caicai_add},goodpost=goodpost+1,lastpost=".time()." where id='$id'");
}
else if($action=='bad')
{
	$dsql->ExecuteNoneQuery("Update `$maintable` set scores = scores - {$cfg_caicai_sub},badpost=badpost+1,lastpost=".time()." where id='$id'");
}
$digg = '';
$row = $dsql->GetOne("Select goodpost,badpost,scores From `$maintable` where id='$id' ");
if(!is_array($row))
{
	exit();
}
if($row['goodpost']+$row['badpost'] == 0)
{
	$row['goodper'] = $row['badper'] = 0;
}
else
{
	$row['goodper'] = number_format($row['goodpost']/($row['goodpost']+$row['badpost']),3)*100;
	$row['badper'] = 100-$row['goodper'];
}

if(empty($formurl)) $formurl = '';
if($formurl=='caicai')
{
	if($action == 'good') $digg = $row['goodpost'];
	if($action == 'bad') $digg  = $row['badpost'];
}
else
{
	$row['goodper'] = trim(sprintf("%4.2f", $row['goodper']));
	$row['badper'] = trim(sprintf("%4.2f", $row['badper']));
	$digg = '<a id="Addlike" class="action" 
href="javascript:" onclick="javascript:postDigg(\'good\','.$id.')"> <i class="fa fa-heart-o"></i>喜欢(<span class="count"><span>'.$row['goodpost'].'</span></span>)</a>';
}
AjaxHead();
echo $digg;
exit();
