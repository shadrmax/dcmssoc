<?

$koll = dbresult(dbquery("SELECT COUNT(*) FROM `user`",$db), 0);

$k_new = dbresult(dbquery("SELECT COUNT(*) FROM `user` where `date_reg` > '$ftime' ",$db), 0);

if ($_new > 0) $k_new = '<font color="red">+' . $k_new . '</font>'; else $k_new = null;

echo '(' . $koll . ') ' . $k_new;

?>

