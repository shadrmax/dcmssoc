<?
function user_access($access, $u_id = null, $exit = false)
{


    if ($u_id == null)
        global $user;
    else
        $user = get_user($u_id);


    if (!isset($user['group_access']) || $user['group_access'] == null) {


        if ($exit !== false) {
            header('Location: ' . $exit);
            exit;
        } else return false;
    }

    if ($exit !== false) {

        $user['group_access2'] = 0;
        //    if (dbresult(dbquery("SELECT COUNT(*) FROM `user_group_access` WHERE (`id_group` = '$user[group_access]' and `id_group` = '$user[group_access2]')  AND `id_access` = '" . my_esc($access) . "'"), 0) == 0)

        if (dbresult(dbquery("SELECT COUNT(*) FROM `user_group_access` WHERE `id_group` = '$user[group_access]' AND `id_access` = '" . my_esc($access) . "'"), 0) == 0) {
            header("Location: $exit");
            exit;
        }
    } else {

        $user['group_access2']=0;
        return
            (dbresult(dbquery("SELECT COUNT(*) FROM `user_group_access` WHERE (`id_group` = '$user[group_access]' or `id_group` = '$user[group_access2]') and`id_access` = '" . my_esc($access) . "'"), 0) == 1 ? true : false);
    }

}


