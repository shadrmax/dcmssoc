<?









$k_n= dbresult(dbquery("SELECT COUNT(*) FROM `adm_chat` WHERE `time` > '$ftime'",$db), 0);




if ($k_n==0)$k_n=NULL;




else $k_n='+'.$k_n;




echo " <font color='red'>$k_n</font> ";




?>




