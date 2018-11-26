<?php

    $host = '127.0.0.1';
    $db   = 'test';
    $user = 'root';
    $pass = '';
    $charset = 'utf8';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $user, $pass, $opt);
    
    $db2   = 'sf001nx';

    $dsn2 = "mysql:host=$host;dbname=$db2;charset=$charset";
    $pdo2 = new PDO($dsn2, $user, $pass, $opt);
    
    function localization($block,$html) {
        $lang_id=1;
        global $pdo2;

        //$sql = "SELECT `loc_texts`.`text`,CONCAT('%TEXT_',`loc_texts`.`text_id`,'%') AS 'id' FROM `loc_texts` JOIN `loc_blocks` ON `loc_blocks`.`id`= `loc_texts`.`block_id` WHERE `loc_blocks`.`name` = '{block}' AND `loc_texts`.`lang_id`={lang_id};";
        //$res = $this->select($sql,array('block'=>$block,'lang_id'=>$lang_id));
        
        $res = $pdo2->prepare("SELECT `loc_texts`.`text`,CONCAT('%TEXT_',`loc_texts`.`text_id`,'%') AS 'id' FROM `loc_texts` JOIN `loc_blocks` ON `loc_blocks`.`id`= `loc_texts`.`block_id` WHERE `loc_blocks`.`name` = ? AND `loc_texts`.`lang_id`=?;");
        $res->execute([$block,$lang_id]);

        $dict = array();
        foreach($res as $val) {
            $dict[$val['id']] = $val['text'];
        }

        return strtr($html, $dict);
    }
    
    
    if(($_POST['act'] == 'getroom')&&($_POST['uid'] != '')) {
        $res = $db->select("SELECT IFNULL(`data`,'0') AS 'data', IFNULL(`wall_height`,350) AS 'height' FROM `rooms` WHERE `user_id`={uid}",array('uid'=>$_POST['uid']));
        if(count($res)>0) {
            $res[0]['data'] = str_replace('\\"','"',$res[0]['data']);
            echo json_encode($res[0]);
        } else {
            echo json_encode(array('data'=>0,'height'=>350));
        }
        exit;
    }
    if($_POST['act'] == 'saveroom') {
        $res = $db->select("SELECT `id` FROM `rooms` WHERE `user_id`={uid}",array('uid'=>MYID));
        $_POST['data'] = str_replace('\\"','"',$_POST['data']);
        if(count($res)>0) {
            $db->query("UPDATE `rooms` SET `data`={d} WHERE `user_id`={uid}",array('uid'=>MYID,'d'=>$_POST['data']));
        } else {
            $db->insert("INSERT INTO `rooms` (`user_id`,`data`) VALUES({uid},{d})",array('uid'=>MYID,'d'=>$_POST['data']),'id');
        }
        echo 1;
        exit;
    }
    if($_POST['act'] == 'check') {
        $hoi = localization('room','%TEXT_6%');
        $ski = localization('room','%TEXT_7%');
        /*if($_COOKIE['lang']==2){
            $hoi = 'Все файлы модели должны находиться в одной директории. Модель состоит из файла OBJ, дополнительного mtl файла и текстур (файлы картинок).';
            $ski = 'Sky (<a href="https://ru.wikipedia.org/wiki/%D0%A1%D0%BA%D0%B0%D0%B9%D0%B1%D0%BE%D0%BA%D1%81" target="_blank">skybox</a>) это куб с текстурами наложенными на грани (6 изображений), которые должны содержать соотвествующие маски в своем имени (front, back, left, right, top или up, bottom или down). Например: верхняя грань examp_up.jpg или examp_top.jpg, передняя грань frontexamp.jpg и т.д.';
        }else{
            $hoi = 'All model files must be in the same directory. Model consists of OBJ file additional mtl file and textures (image files).';
            $ski = 'Sky (<a href="https://en.wikipedia.org/wiki/Skybox_(video_games)" target="_blank">skybox</a>) is a cube with textures imposed on the faces (6 pictures), which should contain the appropriate mask in his name (front, back, left, right, top, or up, bottom or down). For example, the upper bound is examp_up.jpg or examp_top.jpg, the front face frontexamp.jpg etc.';
        }*/
        $block = '<div id="bbl_menu" style="background-color: #cccccc;
                            position: absolute;
                            top: 3px;
                            left: 3px;
                            height: 42px;
                            width: 232px;
                            border-radius: 4px;
                            -webkit-border-radius: 4px;
                            -moz-border-radius: 5px;
                            -khtml-border-radius: 10px;">
            <img id="settings" src="settings.png" style="display: inline-block; height: 40px; width: 36px; margin: 1px 0 0 0;">
            <img id="import" src="import.png" style="display: inline-block; height: 34px; width: 34px; margin: 0 0 3px 0;">
            <img id="position" class="inst" src="position.png" style="display: inline-block; height: 34px; width: 34px; margin: 0 0 3px 0;">
            <img id="scaling" class="inst" src="scaling.png" style="display: inline-block; height: 34px; width: 34px; margin: 0 0 3px 0;">
            <img id="rotation" class="inst" src="rotation.png" style="display: inline-block; height: 34px; width: 34px; margin: 0 0 3px 0;">
            <img id="delete" class="inst" src="delete.png" style="display: inline-block; height: 34px; width: 34px; margin: 0 0 3px 0;">
        </div>
        <div id="model_block" style="display: none; overflow-y: auto; position: absolute; top:48px; left: 3px; width: 238px; max-height: 760px; opacity: 0.8; background: rgb(114, 114, 112); color: #fff;">
            <div style="display: block;">
                <span style="display: block; margin: 6px;">%TEXT_8%</span>
                %BUILDINGS%
            </div>
            <div style="display: block;">
                <span style="display: block; margin: 6px;">Objects</span>
                %OBJ%
            </div>
            <div style="display: block;">
                <span style="display: block; margin: 6px;">%TEXT_9%</span>
                %SKY%
            </div>
            <div style="display: block;">
                <span style="display: block; margin: 6px;">Terrain</span>
                %TERRAIN%
            </div>
        </div>
        <div id="bbl_upload" style="display: none; position: absolute; top:48px; left: 3px; width: 228px; opacity: 0.8; background: #727270; padding: 6px; color: #fff;">
            <label class="import" for="upload_type">%TEXT_10%</label><br/>
            <select class="import" id="upload_type">
                <option value="0">%TEXT_12%</option>
                <option value="1">Home</option>
                <option value="2">Object</option>
                <option value="3">Sky</option>
                <option value="3">Terrain</option>
            </select>
            <img id="info" src="/home/info.png">
            <br/>

            <label class="import" for="upload_name">%TEXT_13%</label><br/>
            <input class="import" id="upload_name" name="name" type="text"><br/>

            <div class="import" id="add_photo">%TEXT_14%</div>
            <div class="import" id="photo_preview" style="height:100px; width:100px; border: 1px solid black; padding: 0"><span style="position: relative; left: 30px; top: 40px;">Image</span></div>
            <input type="file" id="file_photo" name="photo" style="display:none">

            <label class="import" for="upload_files">%TEXT_15%</label><br/>
            <input class="import" id="upload_files" type="file" multiple="multiple"><br/>

            <input class="import" type="button" id="upload_button" value="%TEXT_16%"></button><img id="loading" src="/images/loading.gif" style="position: relative;
                    top: 5px;
                    width: 20px;
                    height: 20px;
                    display: none;"/>

            <div id="upload_status"></div>
        </div>
        <div id="block_info">
            <span id="home_info">'.$hoi.'</span>
            <span id="sky_info">'.$ski.'</span>
        </div>
        <div id="block_scaling">
            <span style="display: block;">X</span>
            <input id="scalingX" type="number" name="rotationX" min="0.01" max="100" step="0.01" style="display: block;"/>
            <div id="sliderX"></div>
            <span style="display: block;">Y</span>
            <input id="scalingY" type="number" name="rotationX" min="0.01" max="100" step="0.01" style="display: block;"/>
            <div id="sliderY"></div>
            <span style="display: block;">Z</span>
            <input id="scalingZ" type="number" name="rotationX" min="0.01" max="100" step="0.01" style="display: block;"/>
            <div id="sliderZ"></div>
        </div>
        <div id="block_rotation">
            <span style="display: block;">X</span>
            <input id="rotationX" type="number" name="rotationX" min="0" max="360" style="display: block;"/>
            <div id="sliderRX"></div>
            <span style="display: block;">Y</span>
            <input id="rotationY" type="number" name="rotationY" min="0" max="360" style="display: block;"/>
            <div id="sliderRY"></div>
            <span style="display: block;">Z</span>
            <input id="rotationZ" type="number" name="rotationZ" min="0" max="360" style="display: block;"/>
            <div id="sliderRZ"></div>
        </div>
        <div id="del_obj_scn">
            Delete <span></span> from scene?<br/>
            <div id="cnl_btn" class="btn">Cancel</div>
            <div id="del_btn" class="btn">Delete</div>
        </div>';
        $img = '<div class="%CLASS%" data="%ID%" style="display: inline-block; background-image: url(%IMG%); background-repeat: no-repeat; background-size: cover; width: 100px; height: 100px; margin: 5px 5px 0px 5px;">%DEL%</div>';
        $del_img = '<img class="del" src="/images/close_white.gif" style="width: 15px; height: 15px; position: relative; top: 3px; left: 82px;">';
        $buildings_list = '';
        $buildings = $pdo->prepare("SELECT `id`, `name`, `folder`, `img` FROM `homebbl` WHERE `visible` = 1;");
        $buildings->execute();
        //$buildings = $db->select("SELECT `id`, `name`, `folder`, `img` FROM `homebbl` WHERE `visible` = 1;");
        if(count($buildings)>0) {
            foreach ($buildings as $value) {
                if(($value['name'] == 'barcelona-new') || ($value['name'] == 'simple')){
                    $buildings_list .= str_replace(array('%CLASS%','%ID%','%IMG%','%DEL%'), array('home',$value['id'],'buildings/'.$value['folder'].'/preview_'.$value['img'],''), $img);
                }else{
                    $buildings_list .= str_replace(array('%CLASS%','%ID%','%IMG%','%DEL%'), array('home',$value['id'],'buildings/'.$value['folder'].'/preview_'.$value['img'],$del_img), $img);    
                }
            }
        }
        $obj_list = '';
        $obj = $pdo->prepare("SELECT `id`, `name`, `folder`, `img` FROM `objbbl` WHERE `visible`=1;");
        $obj->execute();
        //$obj = $db->select("SELECT `id`, `name`, `folder`, `img` FROM `objbbl` WHERE `visible`=1;");
        if(count($obj)>0) {
            foreach ($obj as $value) {
                if(($value['name'] == 'tv') || ($value['name'] == 'skydark')) {
                    $obj_list .= str_replace(array('%CLASS%','%ID%','%IMG%','%DEL%'), array('obj',$value['id'],'objects/'.$value['folder'].'/preview_'.$value['img'],''), $img);
                }else{
                    $obj_list .= str_replace(array('%CLASS%','%ID%','%IMG%','%DEL%'), array('obj',$value['id'],'objects/'.$value['folder'].'/preview_'.$value['img'],$del_img), $img);
                }
            }
        }
        $skybox_list = '';
        $skybox = $pdo->prepare("SELECT `id`, `name`, `folder`, `img` FROM `skybbl` WHERE `visible`=1;");
        $skybox->execute();
        //$skybox = $db->select("SELECT `id`, `name`, `folder`, `img` FROM `skybbl` WHERE `visible`=1;",array('mid'=>MYID));
        if(count($skybox)>0) {
            foreach ($skybox as $value) {
                if(($value['name'] == 'winter') || ($value['name'] == 'skydark')) {
                    $skybox_list .= str_replace(array('%CLASS%','%ID%','%IMG%','%DEL%'), array('skybox',$value['id'],'skybox/'.$value['folder'].'/preview_'.$value['img'],''), $img);
                }else{
                    $skybox_list .= str_replace(array('%CLASS%','%ID%','%IMG%','%DEL%'), array('skybox',$value['id'],'skybox/'.$value['folder'].'/preview_'.$value['img'],$del_img), $img);
                }
            }
        }
        $terrain_list = '';
        $terrain = $pdo->prepare("SELECT `id`, `name`, `file` FROM `terrainbbl` WHERE `visible`=1;");
        $terrain->execute();
        //$skybox = $db->select("SELECT `id`, `name`, `folder`, `img` FROM `skybbl` WHERE `visible`=1;",array('mid'=>MYID));
        if($terrain->columnCount()>0) {
            foreach ($terrain as $value) {
                if($value['name'] == 'ground') {
                    $terrain_list .= str_replace(array('%CLASS%','%ID%','%IMG%','%DEL%'), array('terrain',$value['id'],'terrain/'.$value['file'],''), $img);
                }else{
                    $terrain_list .= str_replace(array('%CLASS%','%ID%','%IMG%','%DEL%'), array('terrain',$value['id'],'terrain/'.$value['file'],$del_img), $img);
                }
            }
        }
        $block = localization('room',$block);
        $block = str_replace(array('%BUILDINGS%','%OBJ%','%SKY%','%TERRAIN%'), array($buildings_list,$obj_list,$skybox_list,$terrain_list), $block);
        $settings = $pdo->prepare("SELECT `homebbl`.`id` AS 'hid', `homebbl`.`folder` AS 'hfolder', `homebbl`.`file` AS 'hfile', `skybbl`.`id` AS 'sid', `skybbl`.`folder` AS 'sfolder' FROM `homebbl` JOIN `skybbl` ON `skybbl`.`id` = `homebbl`.`sky_id` WHERE `homebbl`.`set` = 1;");
        $settings->execute();
        //$settings = $set->fetchColumn();
        
        /*$settings = $db->select("SELECT `bbl_home`.`id` AS 'hid', `bbl_home`.`folder` AS 'hfolder', `bbl_home`.`file` AS 'hfile', `bbl_sky`.`id` AS 'sid', `bbl_sky`.`folder` AS 'sfolder' "
                . "FROM `homebbl` "
                . "JOIN `bbl_sky` ON `bbl_sky`.`id` = `bbl_home`.`sky_id` "
                . "WHERE `homebbl`.`set` = 1;");*/
        if(count($settings)>0){
                $value = $settings->fetch();
                $set = array('hid' => $value['hid'], 'hfolder' => $value['hfolder'], 'hfile' => $value['hfile'], 'sid' => $value['sid'], 'sfolder' => $value['sfolder'], 'my_room' => 1);
        }
        echo json_encode(array('html'=>$block, 'set'=>$set));
        
    }

    if(($_POST['act'] == 'change_home') && ($_POST['hid']!='')) {
        $check = $pdo->prepare("SELECT `id`,`folder`,`file` FROM `homebbl` WHERE `id` = ?;")->execute($_POST['hid'])->fetch();
        //$check = $db->select("SELECT `id`,`folder`,`file` FROM `bbl_home` WHERE `user_id` = {mid} AND `id` = {hid};",array('mid'=>MYID, 'hid'=>$_POST['hid']));
        if(count($check)>0){
            echo json_encode(array('folder'=>$check['folder'], 'file'=>$check['file']));
            $pdo->prepare("UPDATE `homebll` SET `set`= 0 WHERE `set`=0")->execute();
            $pdo->prepare("UPDATE `homebbl` SET `set` = 1 WHERE `id`=?")->execute($check['id']);
            //$db->query("UPDATE `user` SET `home_id`= {hid} WHERE `user_id`={mid}",array('mid' => MYID, 'hid' => $check[0]['id']));
        }else{
            echo '';
        }
    }

    if(($_POST['act'] == 'change_sky') && ($_POST['sid']!='')) {
        $check = $pdo->prepare("SELECT `id`,`folder` FROM `skybbl` WHERE `id` = ?;")->execute()-fetch($_POST['sid']);
        //$check = $db->select("SELECT `id`,`folder` FROM `bbl_sky` WHERE `user_id` = {mid} AND `id` = {sid};",array('mid'=>MYID, 'sid'=>$_POST['sid']));
        if(count($check)>0){
            echo $check['folder'];
            $pdo->prepare("UPDATE `homebbl` SET `sky_id`= ? WHERE `set`=1")->execute($check['id']);
            //$db->query("UPDATE `bbl_home` SET `sky_id`= {sid} WHERE `id`={hid}",array('hid' => $user_home[0]['home_id'], 'sid' => $check[0]['id']));
        }else{
            echo '';
        }
    }
    
    if(($_POST['act'] == 'change_terrain') && ($_POST['tid']!='')) {
        $check = $pdo->prepare("SELECT `id`,`folder`,`file` FROM `terrainbbl` WHERE `id` = ?;")->execute($_POST['tid'])->fetch();
        if(count($check)>0){
            echo json_encode(array('folder'=>$check['folder'], 'file'=>$check['file']));
            $pdo->prepare("UPDATE `terrainbbl` SET `set`= 0 WHERE `set`=1")->execute();
            $pdo->prepare("UPDATE `terrainbbl` SET `set`= 1 WHERE `id`=?")->execute($check['id']);
        }else{
            echo '';
        }
    }

    if(($_POST['act'] == 'add_obj') && ($_POST['oid']!='')) {
        $check = $pdo->prepare("SELECT `objbbl`.`id`,`objbbl`.`folder`,`objbbl`.`file`,`objbbl`.`name` FROM `objbbl` WHERE `objbbl`.`id` = ?;")->execute($_POST['oid'])->fetch();
        //$check = $db->select("SELECT `bbl_obj`.`id`,`bbl_obj`.`folder`,`bbl_obj`.`file`,`bbl_obj`.`name`,`user`.`home_id` FROM `bbl_obj` JOIN `user` ON `user`.`user_id` = `bbl_obj`.`user_id` WHERE `bbl_obj`.`user_id` = {mid} AND `bbl_obj`.`id` = {o_id};",array('mid'=>MYID, 'o_id'=>$_POST['oid']));
        if(count($check)>0){
            $home = $pdo->query("SELECT `id` FROM `homebbl` WHERE `set`=1")->fetch();
            $pdo->prepare("INSERT INTO `homebblobj` (`home_id`,`obj_id`) VALUES (?,?);")->execute($home['id'],$_POST['oid']);
            //$hoid =  $db->insert("INSERT INTO `bbl_home_obj` (`home_id`,`obj_id`) VALUES ({hid},{o_id});", array('hid' => $check[0]['home_id'], 'o_id' => $_POST['oid']), 'id');
            echo json_encode(array('folder'=>$check['folder'], 'file'=>$check['file'], 'hoid'=> $pdo->lastInsertId('id'), 'name'=>$check['name']));
        }else{
            echo '';
        }
    }

    if(($_POST['act'] == 'del_obj_scn') && ($_POST['oid']!='')) {
        
        $pdo->prepare("UPDATE `homebblobj` SET `visible`= 0 WHERE `id`=?")->execute($_POST['oid']);
        //$db->query("UPDATE `bbl_home_obj` SET `visible`= 0 WHERE `id`={o_id}",array('o_id' => $_POST['oid']));
        echo '1';

        /*$check = $db->select("SELECT `bbl_home_obj`.`id` FROM `bbl_home_obj` WHERE `bbl_home_obj`.`user_id` = {mid} AND `bbl_obj`.`id` = {o_id};",array('mid'=>MYID, 'o_id'=>$_POST['oid']));
        if(count($check)>0){
            $hoid =  $db->insert("INSERT INTO `bbl_home_obj` (`home_id`,`obj_id`) VALUES ({hid},{o_id});", array('hid' => $check[0]['home_id'], 'o_id' => $_POST['oid']), 'id');
            echo json_encode(array('folder'=>$check[0]['folder'], 'file'=>$check[0]['file'], 'hoid'=> $hoid, 'name'=>$check[0]['name']));
        }else{
            echo '';
        }*/
    }

    if($_POST['act'] == 'load_obj') {
        $res = array();

        $obj_list = $pdo->query("SELECT 
            `homebblobj`.`id`, 
            `objbbl`.`name`, 
            `objbbl`.`folder`, 
            `objbbl`.`file`, 
            `homebblobj`.`position_x`, 
            `homebblobj`.`position_y`, 
            `homebblobj`.`position_z`, 
            `homebblobj`.`scaling_x`, 
            `homebblobj`.`scaling_y`, 
            `homebblobj`.`scaling_z`,
            `homebblobj`.`rotation_x`,
            `homebblobj`.`rotation_y`,
            `homebblobj`.`rotation_z` 
            FROM `homebbl` 
            JOIN `homebblobj` ON `homebblobj`.`home_id`=`homebbl`.`id` 
            JOIN `objbbl` ON `objbbl`.`id`=`homebblobj`.`obj_id` 
            WHERE `homebbl`.`set` = 1 AND `homebblobj`.`visible` = 1 ;")-fetchAll();
        //$check->execute($_POST['bid']);
        /*$obj_list = $db->select("SELECT 
            `bbl_home_obj`.`id`, 
            `bbl_obj`.`name`, 
            `bbl_obj`.`folder`, 
            `bbl_obj`.`file`, 
            `bbl_home_obj`.`position_x`, 
            `bbl_home_obj`.`position_y`, 
            `bbl_home_obj`.`position_z`, 
            `bbl_home_obj`.`scaling_x`, 
            `bbl_home_obj`.`scaling_y`, 
            `bbl_home_obj`.`scaling_z`,
            `bbl_home_obj`.`rotation_x`,
            `bbl_home_obj`.`rotation_y`,
            `bbl_home_obj`.`rotation_z` FROM `user` JOIN `bbl_home_obj` ON `bbl_home_obj`.`home_id`=`user`.`home_id` JOIN `bbl_obj` ON `bbl_obj`.`id`=`bbl_home_obj`.`obj_id` WHERE `user`.`user_id` = {mid} AND `bbl_home_obj`.`visible` = 1 ;",array('mid'=>$id));
        */
        if(count($obj_list)>0) {
            foreach ($obj_list as  $value) {
                $res[] = array('id' => $value['id'], 'name' => $value['name'], 'folder' => $value['folder'], 'file' => $value['file'], 'px' => $value['position_x'], 'py' => $value['position_y'], 'pz' => $value['position_z'], 'sx' => $value['scaling_x'], 'sy' => $value['scaling_y'], 'sz' => $value['scaling_z'], 'rx' => $value['rotation_x'], 'ry' => $value['rotation_y'], 'rz' => $value['rotation_z']);
            }
            echo json_encode($res);
        }
    }

    if(($_POST['act'] == 'change_position') && ($_POST['bid']!='')) {
        $check = $pdo->prepare("SELECT `homebblobj`.`id` FROM `homebblobj` WHERE `homebblobj`.`id` = ?;")->execute($_POST['bid']);
        //$check = $db->select("SELECT `bbl_home_obj`.`id` FROM `bbl_home_obj` JOIN `user` ON (`user`.`home_id`=`bbl_home_obj`.`home_id`) WHERE `user`.`user_id` = {mid} AND `bbl_home_obj`.`id` = {bid};",array('mid'=>MYID, 'bid'=>$_POST['bid']));
        if($check->columnCount()>0){
            if(($_POST['px'] != '') && ($_POST['py'] != '') && ($_POST['pz'] != '')) {
                $val = $check->fetch();
                $upd = $pdo->prepare("UPDATE `homebblobj` SET `position_x`= ?, `position_y`= ?, `position_z`= ? WHERE `id`=?");
                $upd->execute([$_POST['px'],$_POST['py'],$_POST['pz'],$val['id']]);
                //$db->query("UPDATE `bbl_home_obj` SET `position_x`= {px}, `position_y`= {py}, `position_z`= {pz} WHERE `id`={bid}",array('bid' => $check[0]['id'], 'px' => $_POST['px'], 'py'=>$_POST['py'], 'pz'=>$_POST['pz']));
                echo 'ok';
            }
        }else{
            echo '';
        }
    }

    if(($_POST['act'] == 'change_scaling') && ($_POST['bid']!='')) {
        $check = $pdo->prepare("SELECT `homebblobj`.`id` FROM `homebblobj` WHERE `homebblobj`.`id` = ?;")->execute($_POST['bid']);
        //$check = $db->select("SELECT `bbl_home_obj`.`id` FROM `bbl_home_obj` JOIN `user` ON (`user`.`home_id`=`bbl_home_obj`.`home_id`) WHERE `user`.`user_id` = {mid} AND `bbl_home_obj`.`id` = {bid};",array('mid'=>MYID, 'bid'=>$_POST['bid']));
        if($check->columnCount()>0){
            if(($_POST['sx'] != '') && ($_POST['sy'] != '') && ($_POST['sz'] != '')) {
                $val = $check->fetch();
                $upd = $pdo->prepare("UPDATE `homebblobj` SET `scaling_x`= {sx}, `scaling_y`= {sy}, `scaling_z`= {sz} WHERE `id`={bid}");
                $upd->execute([$_POST['sx'],$_POST['sy'],$_POST['sz'],$val['id']]);
                //$db->query("UPDATE `bbl_home_obj` SET `scaling_x`= {sx}, `scaling_y`= {sy}, `scaling_z`= {sz} WHERE `id`={bid}",array('bid' => $check[0]['id'], 'sx' => $_POST['sx'], 'sy'=>$_POST['sy'], 'sz'=>$_POST['sz']));
                echo 'ok';
            }
        }else{
            echo '';
        }
    }

    if(($_POST['act'] == 'change_rotation') && ($_POST['bid']!='')) {
        $check = $pdo->prepare("SELECT `homebblobj`.`id` FROM `homebblobj` WHERE `homebblobj`.`id` = ?;")->execute($_POST['bid']);
        //$check = $db->select("SELECT `bbl_home_obj`.`id` FROM `bbl_home_obj` JOIN `user` ON (`user`.`home_id`=`bbl_home_obj`.`home_id`) WHERE `user`.`user_id` = {mid} AND `bbl_home_obj`.`id` = {bid};",array('mid'=>MYID, 'bid'=>$_POST['bid']));
        if($check->columnCount()>0){
            if(($_POST['rx'] != '') && ($_POST['ry'] != '') && ($_POST['rz'] != '')) {
                $val = $check->fetch();
                $upd = $pdo->prepare("UPDATE `homebblobj` SET `rotation_x`= {rx}, `rotation_y`= {ry}, `rotation_z`= {rz} WHERE `id`={bid}");
                $upd->execute([$_POST['rx'],$_POST['ry'],$_POST['rz'],$val['id']]);
                //$db->query("UPDATE `bbl_home_obj` SET `rotation_x`= {rx}, `rotation_y`= {ry}, `rotation_z`= {rz} WHERE `id`={bid}",array('bid' => $check[0]['id'], 'rx' => $_POST['rx'], 'ry'=>$_POST['ry'], 'rz'=>$_POST['rz']));
                echo 'ok';
            }
        }else{
            echo '';
        }
    }

    if(($_POST['act'] == 'check_name') && ($_POST['name'] != '') && ($_POST['type']>0)) {

        switch ($_POST['type']) {
            case '1':
                $db_check = $pdo->prepare("SELECT `id` FROM `homebbl` WHERE `name` = ?;");
                //$db_check = $db->select("SELECT `id` FROM `bbl_home` WHERE `user_id` = {mid} AND `name` = {name};",array('mid'=>MYID, 'name'=>$_POST['name']));
                break;

            case '2':
                $db_check = $pdo->prepare("SELECT `id` FROM `objbbl` WHERE `name` = ?;");
                //$db_check = $db->select("SELECT `id` FROM `bbl_obj` WHERE `user_id` = {mid} AND `name` = {name};",array('mid'=>MYID, 'name'=>$_POST['name']));
                break;

            case '3':
                $db_check = $pdo->prepare("SELECT `id` FROM `skybbl` WHERE `name` = ?;");
                //$db_check = $db->select("SELECT `id` FROM `bbl_sky` WHERE `user_id` = {mid} AND `name` = {name};",array('mid'=>MYID, 'name'=>$_POST['name']));
                break;

            default:
                //$db_check = array($_POST['name']);
                break;
        }
        $db_check->execute();
        
        if($db_check->rowCount()>0){
            echo "Model with that name already loaded. Change name model.";
        }else{
            echo "";
        }
    }

    if($_POST['act'] == 'load_preview') {
        //var_dump($_FILES);

        $uploaddir = ROOT.'/home/tmp_img/';

        if(!is_dir($uploaddir)) {
            mkdir($uploaddir, 0777, true);
        }

        if(preg_match('/\..*$/', basename($_FILES[0]['name']), $fl)){
            $fl[0] = strtolower($fl[0]);
            if(($fl[0] == '.jpg') || ($fl[0] == '.jpeg') || ($fl[0] == '.png') || ($fl[0] == '.bmp')) {
                if(!move_uploaded_file($_FILES[0]['tmp_name'], $uploaddir.MYID.$fl[0])) {
                    echo  'File upload error: '.basename($_FILES[0]['name']);
                }else{
                    echo '<img src="/home/tmp_img/'.MYID.$fl[0].'" style="width: 100px;" data="'.MYID.$fl[0].'">';
                }
            }else{
                echo 'File is not supported: '.basename($_FILES[0]['name']);
            }
        }

    }

    if(($_POST['act'] == 'load') && ($_POST['name']!='') && ($_POST['type']>0)) {
        switch ($_POST['type']) {
            case '1':
                $db_check = $pdo->prepare("SELECT `id` FROM `homebbl` WHERE `name` = ?;");
                //$db_check = $db->select("SELECT `id` FROM `bbl_home` WHERE `user_id` = {mid} AND `name` = {name};",array('mid'=>MYID, 'name'=>$_POST['name']));
                break;

            case '2':
                $db_check = $pdo->prepare("SELECT `id` FROM `bbl_obj` WHERE `name` = ?;");
                //$db_check = $db->select("SELECT `id` FROM `bbl_obj` WHERE `user_id` = {mid} AND `name` = {name};",array('mid'=>MYID, 'name'=>$_POST['name']));
                break;

            case '3':
                $db_check = $pdo->prepare("SELECT `id` FROM `bbl_sky` WHERE `name` = ?;");
                //$db_check = $db->select("SELECT `id` FROM `bbl_sky` WHERE `user_id` = {mid} AND `name` = {name};",array('mid'=>MYID, 'name'=>$_POST['name']));
                break;

            default:
                $db_check = array();
                break;
        }
        $db_check->execute($_POST['name']);
        if($db_check->columnCount()<1){
            include_once($_SERVER['DOCUMENT_ROOT'].'/conf/defines.php');
            include_once(ROOT.'/lib/classes/upload.class.php');
            include_once(ROOT.'/lib/classes/simpleimage.class.php');
            $si = new SimpleImage();

            $data = array();
            $files = array();
            $data['obj'] = 0;
            $data['status'] = 0;

            $folder = rs(12);
            $img = rs(20);

            switch ($_POST['type']) {
                case '1':
                    $uploaddir = ROOT.'/home/buildings/'.$folder.'/';
                    break;

                case '2':
                    $uploaddir = ROOT.'/home/objects/'.$folder.'/';
                    break;

                case '3':
                    $uploaddir = ROOT.'/home/skybox/'.$folder.'/';
                    break;

                default:
                    $uploaddir = '';
                    break;
            }


            if(!is_dir($uploaddir)) {
                mkdir($uploaddir, 0777, true);
            }

            $ext = explode(".", $_POST['img']);
            $img .= ".".$ext[count($ext) - 1];
            copy(ROOT.'home/tmp_img/'.$_POST['img'], $uploaddir.$img);
            if(is_file(ROOT.'home/tmp_img/'.$_POST['img'])){
                unlink(ROOT.'home/tmp_img/'.$_POST['img']);
            }

            $cnt = 0;
            foreach( $_FILES as $file ) {
                if(preg_match('/\..*$/', basename($file['name']), $fl)){
                    $fl[0] = strtolower($fl[0]);
                    if(($fl[0] == '.obj') && ($_POST['type']>0) && ($_POST['type']<3)) {
                        $check = true;
                        if(!move_uploaded_file($file['tmp_name'], $uploaddir.basename($file['name']))) {
                            $data['status'] = 1;
                            $data[] = 'File upload error: '.basename($file['name']);
                        }else{
                            $lfile = basename($file['name']);
                        }
                    }else if(($fl[0] != '.jpg') && ($fl[0] != '.jpeg') && ($fl[0] != '.png') && ($fl[0] != '.bmp') && ($fl[0] != '.mtl')) {
                        $data['status'] = 1;
                        $data[] = 'File is not supported: '.basename($file['name']);
                    }else{
                        if(($_POST['type']>0) && ($_POST['type']<3)) {
                            if(!move_uploaded_file($file['tmp_name'], $uploaddir.basename($file['name']))) {
                                $data['status'] = 1;
                                $data[] = 'File upload error: '.basename($file['name']);
                            }
                        }else if(($_POST['type']==3) && ($fl[0] != '.mtl')) {
                            if(preg_match('/front|back|left|right|top|bottom|up|down/', basename($file['name']), $sk)) {
                                $sk_img = '';
                                switch ($sk[0]) {
                                    case 'front':
                                        $sk_img = '_pz'.$fl[0];
                                        break;

                                    case 'back':
                                        $sk_img = '_nz'.$fl[0];
                                        break;

                                    case 'left':
                                        $sk_img = '_nx'.$fl[0];
                                        break;

                                    case 'right':
                                        $sk_img = '_px'.$fl[0];
                                        break;

                                    case 'top':
                                        $sk_img = '_py'.$fl[0];
                                        break;

                                    case 'bottom':
                                        $sk_img = '_ny'.$fl[0];
                                        break;

                                    case 'up':
                                        $sk_img = '_py'.$fl[0];
                                        break;

                                    case 'down':
                                        $sk_img = '_ny'.$fl[0];
                                        break;
                                }
                                if(!move_uploaded_file($file['tmp_name'], $uploaddir.$sk_img)) {
                                    $data['status'] = 1;
                                    $data[] = 'File upload error: '.basename($file['name']);
                                }else{
                                    $cnt++;
                                }
                            }else{
                                $data['status'] = 1;
                                $data[] = 'No matches found for masks (front, back, left, right, top, bottom, up, down) in the name of the downloaded files';
                            }
                        }
                    }
                }
            }

            if(($cnt < 6) && ($_POST['type'] == 3)) {
                $data['status'] = 1;
                $data[] = 'Should be six files';
            }

            //
            $si->load($uploaddir.$img);
            $h = $si->getHeight();
            $w = $si->getWidth();
            $size = $w;
            if ($w > $h) {
                $size = $h;
                $x0 = round(($w - $h) / 2);
                $y0 = 0;
                $si->crop($uploaddir.$img, $uploaddir."preview_".$img, $x0, $y0, $h, $h);
            }
            if ($w < $h) {
                $x0 = 0;
                $y0 = round(($h - $w) / 2);
                $si->crop($uploaddir.$img, $uploaddir."preview_".$img, $x0, $y0, $w, $w);
            }
            if($w == $h) {
                copy($uploaddir.$img, $uploaddir."preview_".$img);
            }

            if($size > 128){
                $si->load($uploaddir."preview_".$img);
                $si->resizeToWidth(128);
                unlink($uploaddir."preview_".$img);
                $si->save($uploaddir."preview_".$img);
            }

            if(($check != true) && ($_POST['type']>0) && ($_POST['type']<3)){
                $data['obj'] = 1;
            }

            if(($data['status'] == 0) && ($data['obj'] == 0)){
                switch ($_POST['type']) {
                    case '1':
                        if($check == true){
                            $sky = $pdo->prepare("SELECT `id` FROM `skybbl` ORDER BY `id` ASC LIMIT 1;");
                            $sky->execute();
                            $val = $sky->fetch();
                            //$sky = $db->select("SELECT `id` FROM `bbl_sky` WHERE `user_id` = {mid} ORDER BY `id` ASC LIMIT 1;",array('mid'=>MYID));
                            $ins = $pdo->prepare("INSERT INTO `homebbl` (`sky_id`,`name`,`folder`,`file`,`img`) VALUES (?,?,?,?,?);");
                            $ins->execute([$val['id'],$_POST['name'],$folder,$lfile,$img]);
                            //$db->insert("INSERT INTO `bbl_home` (`user_id`,`sky_id`,`name`,`folder`,`file`,`img`) VALUES ({mid},{sid},{name},{folder},{file},{img});", array('mid' => MYID, 'sid' => $sky[0]['id'], 'name' => $_POST['name'], 'folder' => $folder, 'file' => $lfile, 'img' => $img), 'id');
                        }
                        break;

                    case '2':
                        if($check == true){
                            $ins = $pdo->prepare("INSERT INTO `objbbl` (`name`,`folder`,`file`,`img`) VALUES (?,?,?,?);");
                            $ins->execute([$_POST['name'],$folder,$lfile,$img]);
                            //$db->insert("INSERT INTO `bbl_obj` (`user_id`,`name`,`folder`,`file`,`img`) VALUES ({mid},{name},{folder},{file},{img});", array('mid' => MYID, 'name' => $_POST['name'], 'folder' => $folder, 'file'=>$lfile, 'img' => $img), 'id');
                        }
                        break;

                    case '3':
                        $ins = $pdo->prepare("INSERT INTO `skybbl` (`name`,`folder`,`img`) VALUES (?,?,?);");
                        $ins->execute([$_POST['name'],$folder,$img]);
                        //$db->insert("INSERT INTO `bbl_sky` (`user_id`,`name`,`folder`,`img`) VALUES ({mid},{name},{folder},{img});", array('mid' => MYID, 'name' => $_POST['name'], 'folder' => $folder, 'img' => $img), 'id');
                        break;

                    default:
                        $uploaddir = '';
                        break;
                }
            }else{
               // removeDirectory($uploaddir);
            }

        }else{
            $data['status'] = 1;
            $data[] = "Model with that name already loaded. Change name model.";
        }
        echo json_encode($data);
    }

    if(($_POST['act'] == 'del_home') && ($_POST['hid'] > 0)) {
        $buildings = $pdo->prepare("SELECT `id`, `name` FROM `homebbl` WHERE `id` = ? AND `set` != 1;");
        $buildings->execute($_POST['hid']);
        if($buildings->rowCount()>0) {
            $upd = $pdo->prepare("UPDATE `homebbl` SET `visible` = 0 WHERE `id` = ?;");
            $upd->execute();
            //$db->query("UPDATE `bbl_home` SET `visible` = 0 WHERE `id` = {hid};",array('hid' => $db_check[0]['id']));
            echo 'del';
        }else{
            echo '<div class="warning">It can not be removed</div>';
        }
    }

    if(($_POST['act'] == 'del_obj') && ($_POST['oid'] > 0)) {
        //$current = $db->select("SELECT `bbl_obj`.`id`, IFNULL(`bbl_home_obj`.`id`,0) AS 'hid' FROM `bbl_obj` LEFT JOIN `bbl_home_obj` ON `bbl_home_obj`.`obj_id` = `bbl_obj`.`id` WHERE `bbl_obj`.`user_id` = {mid} AND `bbl_obj`.`id`={o_id};",array('mid'=>MYID,'o_id'=>$_POST['oid']));
        $current = $pdo->prepare("SELECT `objbbl`.`id`, IFNULL(`homebblobj`.`id`,0) AS 'hid' FROM `objbbl` LEFT JOIN `homebblobj` ON `homebblobj`.`obj_id` = `objbbl`.`id` WHERE `bbl_obj`.`id`=?;");
        $current->execute($_POST['oid']);
        if($current->rowCount()>0){
            $val = $current->fetch();
            if($val['hid'] == 0) {
                $upd = $pdo->prepare("UPDATE `objbbl` SET `visible` = 0 WHERE `id` = ?;");
                $upd->execute($val['id']);
                echo 'del';
            }else{
                echo '<div class="warning">Delete object from scene</div>';
            }  
        }else{
            echo 'count';
        }  
    }

    if(($_POST['act'] == 'del_sky') && ($_POST['sid'] > 0)) {
        //$current = $db->select("SELECT `bbl_home`.`sky_id` FROM `user` JOIN `bbl_home` ON `bbl_home`.`id` = `user`.`home_id` WHERE `user`.`user_id` = {mid};",array('mid'=>MYID));
        $current = $pdo->prepare("SELECT `homebbl`.`sky_id` FROM `homebbl` WHERE set != 1;");
        $current->execute();
        $val = $current->fetch();
        if(($current->rowCount()>0) && ($val['sky_id'] != $_POST['sid'])){
            //$db_check = $db->select("SELECT `id`, `name` FROM `bbl_sky` WHERE `id` = {sid} AND `user_id` = {mid};",array('mid'=>MYID, 'sid'=>$_POST['sid']));
            $db_check = $pdo->prepare("SELECT `id`, `name` FROM `skybbl` WHERE `id` = ?;");
            $db_check->execute($_POST['sid']);
            if($db_check->rowCount()>0) {
                $value = $db_check->fetch();
                $up = $pdo->prepare("UPDATE `skybbl` SET `visible` = 0 WHERE `id` = ?;");
                $up->execute($value['id']);
                //$db->query("UPDATE `bbl_sky` SET `visible` = 0 WHERE `id` = {sid};",array('sid' => $db_check[0]['id']));
                echo 'del';
            }else{
                echo '';
            }  
        }else{
            echo '<div class="warning">Change default sky</div>';
        }  
    }

    if(($_POST['act'] == 'saveWallHeight')&&(isset($_POST['h']))&&(is_numeric($_POST['h']))) {
        $db->query("UPDATE `rooms` SET `wall_height`={h} WHERE `user_id`={mid}",array('h'=>$_POST['h'],'mid'=>MYID));
        echo 1;
    }

    function rs($l) {
        $a = 'abcdefghjklmnopqrstuvwxyz0123456789_';
        $r = '';
        for($i=0;$i<$l;$i++){
            $r.=substr($a,rand(0,strlen($a)-1),1);
        }
        return $r;
    }

    function removeDirectory($dir) {
        if ($objs = glob($dir."/*")) {
            foreach($objs as $obj) {
                is_dir($obj) ? removeDirectory($obj) : unlink($obj);
            }
        }
        rmdir($dir);
    }
?>

