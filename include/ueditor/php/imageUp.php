<?php
    /**
     * Created by JetBrains PhpStorm.
     * User: taoqili
     * Date: 12-7-18
     * Time: ����10:42
     */
    header("Content-Type: text/html; charset=gbk");
    error_reporting(E_ERROR | E_WARNING);
    include "Uploader.class.php";
    //�ϴ�ͼƬ���е����������ƣ�
    $title = htmlspecialchars($_POST['pictitle'], ENT_QUOTES);
    $path = htmlspecialchars($_POST['dir'], ENT_QUOTES);

    //�ϴ�����
    $config = array(
	   "savePath" => "../../../uploads/allimg" , //����·��
        "maxSize" => 1000, //��λKB
        "allowFiles" => array(".gif", ".png", ".jpg", ".jpeg", ".bmp")
    );

    //�����ϴ�ʵ����������ϴ�
    $up = new Uploader("upfile", $config);

    /**
     * �õ��ϴ��ļ�����Ӧ�ĸ�������,����ṹ
     * array(
     *     "originalName" => "",   //ԭʼ�ļ���
     *     "name" => "",           //���ļ���
     *     "url" => "",            //���صĵ�ַ
     *     "size" => "",           //�ļ���С
     *     "type" => "" ,          //�ļ�����
     *     "state" => ""           //�ϴ�״̬���ϴ��ɹ�ʱ���뷵��"SUCCESS"
     * )
     */
    $info = $up->getFileInfo();
	$info["url"] = str_replace('../','',$info[ "url" ]);

	/*$activepath = '/'.$info["url"];
	$filename = $info["originalName"];
	$imgwidthValue = "";
	$imgheightValue = "";
	$imgsize = $info["size"];
	$nowtme = time();
	if ($info["state"]=='SUCCESS'){
		 $inquery = "INSERT INTO `#@__uploads`(arcid,title,url,mediatype,width,height,playtime,filesize,uptime,mid)
		VALUES ('0','$filename','$activepath','1','$imgwidthValue','$imgheightValue','0','{$imgsize}','{$nowtme}','".$cuserLogin->getUserID()."'); ";
	if(!empty($arcid)){
		$inquery = "INSERT INTO `#@__uploads`(arcid,title,url,mediatype,width,height,playtime,filesize,uptime,mid)VALUES ('$arcid','$filename','$activepath','1','$imgwidthValue','$imgheightValue','0','{$imgsize}','{$nowtme}','".$cuserLogin->getUserID()."'); ";
		}
	$dsql->ExecuteNoneQuery($inquery);
	$fid = $dsql->GetLastID();
	AddMyAddon($fid, $activepath.'/'.$filename);}*/
    /**
     * ���������������json����
     * {
     *   'url'      :'a.jpg',   //�������ļ�·��
     *   'title'    :'hello',   //�ļ���������ͼƬ��˵��ǰ�˻���ӵ�title������
     *   'original' :'b.jpg',   //ԭʼ�ļ���
     *   'state'    :'SUCCESS'  //�ϴ�״̬���ɹ�ʱ����SUCCESS,�����κ�ֵ��ԭ��������ͼƬ�ϴ�����
     * }
     */
    echo "{'url':'" . $info["url"] . "','title':'" . $title . "','original':'" . $info["originalName"] . "','state':'" . $info["state"] . "'}";

