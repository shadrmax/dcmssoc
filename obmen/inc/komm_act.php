<?




if (user_access('obmen_komm_del') && isset($_GET['del_post']) && dbresult(dbquery("SELECT COUNT(*) FROM `obmennik_komm` WHERE `id` = '".intval($_GET['del_post'])."' AND `id_file` = '$file_id[id]'"),0))




{









dbquery("DELETE FROM `obmennik_komm` WHERE `id` = '".intval($_GET['del_post'])."' LIMIT 1");









msg ('Комментарий успешно удален');




}




?>