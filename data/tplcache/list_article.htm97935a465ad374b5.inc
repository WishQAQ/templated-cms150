<?php
$z[0]=Array("field","",98,117);
$z[0][4]['name']="title";
$z[1]=Array("global","",118,144);
$z[1][4]['name']="cfg_webname";
$z[2]=Array("field","",184,213);
$z[2][4]['name']="keywords";
$z[3]=Array("field","",251,309);
$z[3][4]['name']="description";
$z[3][4]['function']="html2text(@me)";
$z[4]=Array("include","",1642,1677);
$z[4][4]['filename']="head.htm";
$z[5]=Array("field","",1931,1960);
$z[5][4]['name']="position";
$z[6]=Array("type","&nbsp;分类：[field:typename/]<a class=\"rss-cat-icon\"title=\"订阅这个分类\"href=\"[field:typelink /]\"><i class=\"icon-rss\"></i></a>",2557,2706);
$z[7]=Array("field","",2777,2834);
$z[7][4]['name']="description";
$z[7][4]['function']="html2text(@me)";
$z[8]=Array("list","\r\n              <li class=\"archive-thumb\">\r\n                <article>\r\n                  <h2><a href=\"[field:arcurl/]\"title=\"[field:fulltitle/]\">[field:fulltitle/]</a></h2>\r\n                  <a class=\"pic float-left\"href=\"[field:arcurl/]\"title=\"[field:fulltitle/]\"><img class=\"lazy\"src=\"[field:litpic/]\"style=\"height:120px; width:180px\"/></a>\r\n                  <p>[field:description/]...</p>\r\n                  <p class=\"post-meta\"><span><i class=\"icon-user\"></i>[field:writer/]</span><span><i class=\"icon-time\"></i>[field:pubdate function=\"MyDate('Y-m-d',@me)\"/]</span><span><i class=\"icon-eye-open\"></i>[field:click/]</span><span><i class=\"icon-comment-alt\"></i><a href=\"[field:arcurl/]#comments\"title=\"[field:fulltitle/]上的评论\"><span class=\"ds-thread-count\"data-thread-key=\"[field:id/]\"data-count-type=\"comments\"></span>评论</a></span><span><i class=\"icon-tag\"></i>[field:id function='nic_arc_tag_link(@me)'/]</span></p>\r\n                  <div class=\"clear\"></div>\r\n                </article>\r\n              </li>\r\n              ",2953,4027);
$z[8][4]['pagesize']="12";
$z[9]=Array("pagelist","",4189,4266);
$z[9][4]['listitem']="info,index,end,pre,next,pageno,option";
$z[9][4]['listsize']="5";
$z[10]=Array("include","",4371,4407);
$z[10][4]['filename']="right.htm";
$z[11]=Array("include","",4452,4489);
$z[11][4]['filename']="footer.htm";

?>