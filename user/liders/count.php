<?







$k_p = dbresult(dbquery("SELECT COUNT(*) FROM `liders` WHERE `time` > '$time'"), 0);







$k_n = dbresult(dbquery("SELECT COUNT(*) FROM `liders` WHERE `time` > '$time' AND `time_p` > '$ftime'"), 0);







if ($k_n == 0)$k_n = NULL;







else $k_n = '+' . $k_n;







echo '(' . $k_p . ') <font color="red">' . $k_n . '</font>';







?>