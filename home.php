<?php
    include_once 'mysql.php';

    class Home {
        
        private $db;
        private $host = '127.0.0.1';
        private $database = 'test';
        private $user = 'root';
        private $pass = '';
        private $data;


        public function __construct(){
            $this->db = NEW MySQL($this->host,$this->database,$this->user,$this->pass);
        }
        
        private function localization($block,$html) {
            $lang_id=1;
            $db2 = NEW MySQL($this->host,'sf001nx',$this->user,$this->pass);

            $res = $db2->select("SELECT `loc_texts`.`text`,CONCAT('%TEXT_',`loc_texts`.`text_id`,'%') AS 'id' FROM `loc_texts` JOIN `loc_blocks` ON `loc_blocks`.`id`= `loc_texts`.`block_id` WHERE `loc_blocks`.`name` = ? AND `loc_texts`.`lang_id`=?;",[$block,$lang_id]);

            $dict = array();
            foreach($res as $val) {
                $dict[$val['id']] = $val['text'];
            }

            return strtr($html, $dict);
        }
        
        public function check() {
            $hoi = $this->localization('room','%TEXT_6%');
            $ski = $this->localization('room','%TEXT_7%');
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
            <div id="model_block" style="display: none; overflow-y: auto; position: absolute; top:48px; left: 3px; width: 238px; max-height: 565px; opacity: 0.8; background: rgb(114, 114, 112); color: #fff;">
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
                    <option value="4">Terrain</option>
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
            $buildings = $this->db->select("SELECT `id`, `name`, `folder`, `img` FROM `homebbl` WHERE `visible` = 1;");
            if($buildings) {
                foreach ($buildings as $value) {
                    if(($value['name'] == 'barcelona-new') || ($value['name'] == 'simple')){
                        $buildings_list .= str_replace(array('%CLASS%','%ID%','%IMG%','%DEL%'), array('home',$value['id'],'buildings/'.$value['folder'].'/preview_'.$value['img'],''), $img);
                    }else{
                        $buildings_list .= str_replace(array('%CLASS%','%ID%','%IMG%','%DEL%'), array('home',$value['id'],'buildings/'.$value['folder'].'/preview_'.$value['img'],$del_img), $img);    
                    }
                }
            }
            
            $obj_list = '';
            $obj = $this->db->select("SELECT `id`, `name`, `folder`, `img` FROM `objbbl` WHERE `visible`=1;");
            if($obj) {
                foreach ($obj as $value) {
                    if(($value['name'] == 'tv') || ($value['name'] == 'skydark')) {
                        $obj_list .= str_replace(array('%CLASS%','%ID%','%IMG%','%DEL%'), array('obj',$value['id'],'objects/'.$value['folder'].'/preview_'.$value['img'],''), $img);
                    }else{
                        $obj_list .= str_replace(array('%CLASS%','%ID%','%IMG%','%DEL%'), array('obj',$value['id'],'objects/'.$value['folder'].'/preview_'.$value['img'],$del_img), $img);
                    }
                }
            }
            
            $skybox_list = '';
            $skybox = $this->db->select("SELECT `id`, `name`, `folder`, `img` FROM `skybbl` WHERE `visible`=1;");
            if($skybox) {
                foreach ($skybox as $value) {
                    if(($value['name'] == 'winter') || ($value['name'] == 'skydark')) {
                        $skybox_list .= str_replace(array('%CLASS%','%ID%','%IMG%','%DEL%'), array('skybox',$value['id'],'skybox/'.$value['folder'].'/preview_'.$value['img'],''), $img);
                    }else{
                        $skybox_list .= str_replace(array('%CLASS%','%ID%','%IMG%','%DEL%'), array('skybox',$value['id'],'skybox/'.$value['folder'].'/preview_'.$value['img'],$del_img), $img);
                    }
                }
            }
            
            $terrain_list = '';
            $terrain = $this->db->select("SELECT `id`, `name`, `folder`, `file` FROM `terrainbbl` WHERE `visible` = 1;");
            if($terrain) {
                foreach ($terrain as $value) {
                    if($value['name'] == 'ground') {
                        $terrain_list .= str_replace(array('%CLASS%','%ID%','%IMG%','%DEL%'), array('terrain',$value['id'],'terrain/'.$value['folder'].'/'.$value['file'],''), $img);
                    }else{
                        $terrain_list .= str_replace(array('%CLASS%','%ID%','%IMG%','%DEL%'), array('terrain',$value['id'],'terrain/'.$value['folder'].'/'.$value['file'],$del_img), $img);
                    }
                }
            }
            
            $block = $this->localization('room',$block);
            $block = str_replace(array('%BUILDINGS%','%OBJ%','%SKY%','%TERRAIN%'), array($buildings_list,$obj_list,$skybox_list,$terrain_list), $block);
            
            $settings = $this->db->select("SELECT `homebbl`.`id` AS 'hid', `homebbl`.`folder` AS 'hfolder', `homebbl`.`file` AS 'hfile', `skybbl`.`id` AS 'sid', `skybbl`.`folder` AS 'sfolder', `terrainbbl`.`id` AS 'tid', `terrainbbl`.`folder` AS 'tfolder', `terrainbbl`.`file` AS 'tfile' FROM `homebbl` JOIN `skybbl` ON `skybbl`.`id` = `homebbl`.`sky_id` JOIN `terrainbbl` ON `terrainbbl`.`id` = `homebbl`.`terrain_id` WHERE `homebbl`.`sel` = 1;",null,'one');
            if($settings){
                    $set = array('hid' => $settings['hid'], 'hfolder' => $settings['hfolder'], 'hfile' => $settings['hfile'], 'sid' => $settings['sid'], 'sfolder' => $settings['sfolder'], 'tid' => $settings['tid'], 'tfolder' => $settings['tfolder'], 'tfile' => $settings['tfile'], 'my_room' => 1);
            }
            return json_encode(array('html'=>$block, 'set'=>$set));
        }
        
        public function change_home(){
            if($_POST['hid']!='') {
                $check = $this->db->select("SELECT `id`,`folder`,`file` FROM `homebbl` WHERE `id` = ?;",[$_POST['hid']],'one');
                if($check){
                    echo json_encode(array('folder'=>$check['folder'], 'file'=>$check['file']));
                    $this->db->query("UPDATE `homebbl` SET `sel` = 0 WHERE `sel` = 1");
                    $this->db->query("UPDATE `homebbl` SET `sel` = 1 WHERE `id` = ?",[$check['id']]);
                }else{
                    echo 'err';
                }
            }
        }
        
        public function change_sky(){
            if($_POST['sid']!='') {
                $check = $this->db->select("SELECT `id`,`folder` FROM `skybbl` WHERE `id` = ?;",[$_POST['sid']],'one');
                if($check){
                    echo $check['folder'];
                    $this->db->query("UPDATE `homebbl` SET `sky_id`= ? WHERE `sel`=1",[$check['id']]);
                }else{
                    echo '';
                }
            }
        }
        
        public function change_terrain(){
            if($_POST['tid']!='') {
                $check = $this->db->select("SELECT `id`,`folder`,`file` FROM `terrainbbl` WHERE `id` = ?;",[$_POST['tid']],'one');
                if($check){
                    echo $check['folder'].'/'.$check['file'];
                    $this->db->query("UPDATE `homebbl` SET `terrain_id`= ? WHERE `sel`=1",[$check['id']]);
                }else{
                    echo '';
                }
            }
        }
        
        public function add_obj(){
            if($_POST['oid']!='') {
                $check = $this->db->select("SELECT `objbbl`.`id`,`objbbl`.`folder`,`objbbl`.`file`,`objbbl`.`name` FROM `objbbl` WHERE `objbbl`.`id` = ?;",[$_POST['oid']],'one');
                if($check){
                    $home = $this->db->select("SELECT `id` FROM `homebbl` WHERE `sel`=1",null,'one');
                    $id = $this->db->insert("INSERT INTO `homebblobj` (`home_id`,`obj_id`) VALUES (?,?);",[$home['id'],$_POST['oid']],'id');
                    echo json_encode(array('folder'=>$check['folder'], 'file'=>$check['file'], 'hoid'=> $id, 'name'=>$check['name']));
                }else{
                    echo '';
                }
            }
        }
        
        public function del_obj_scn(){
            if($_POST['oid']!='') {
                $this->db->query("UPDATE `homebblobj` SET `visible`= 0 WHERE `id`=?",[$_POST['oid']]);
                echo '1';
            }
        }

        public function load_obj() {
            $res = array();

            $obj_list = $this->db->select("SELECT 
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
                WHERE `homebbl`.`sel` = 1 AND `homebblobj`.`visible` = 1 ;");
           
            if($obj_list) {
                foreach ($obj_list as  $value) {
                    $res[] = array('id' => $value['id'], 'name' => $value['name'], 'folder' => $value['folder'], 'file' => $value['file'], 'px' => $value['position_x'], 'py' => $value['position_y'], 'pz' => $value['position_z'], 'sx' => $value['scaling_x'], 'sy' => $value['scaling_y'], 'sz' => $value['scaling_z'], 'rx' => $value['rotation_x'], 'ry' => $value['rotation_y'], 'rz' => $value['rotation_z']);
                }
                echo json_encode($res);
            }
        }

        public function change_position(){
            if($_POST['bid']!='') {
                $check = $this->db->select("SELECT `homebblobj`.`id` FROM `homebblobj` WHERE `homebblobj`.`id` = ?;",[$_POST['bid']],'one');
                $check = $pdo->prepare("SELECT `homebblobj`.`id` FROM `homebblobj` WHERE `homebblobj`.`id` = ?;")->execute($_POST['bid']);
                //$check = $db->select("SELECT `bbl_home_obj`.`id` FROM `bbl_home_obj` JOIN `user` ON (`user`.`home_id`=`bbl_home_obj`.`home_id`) WHERE `user`.`user_id` = {mid} AND `bbl_home_obj`.`id` = {bid};",array('mid'=>+MYID+, 'bid'=>$_POST['bid']));
                if($check){
                    if(($_POST['px'] != '') && ($_POST['py'] != '') && ($_POST['pz'] != '')) {
                        $this->db->query("UPDATE `homebblobj` SET `position_x`= ?, `position_y`= ?, `position_z`= ? WHERE `id`=?",[$_POST['px'],$_POST['py'],$_POST['pz'],$check['id']]);
                        echo 'ok';
                    }
                }else{
                    echo '';
                }
            }
        }

        public function change_scaling(){
            if($_POST['bid']!='') {
                $check = $this->db->select("SELECT `homebblobj`.`id` FROM `homebblobj` WHERE `homebblobj`.`id` = ?;",[$_POST['bid']],'one');
                if($check){
                    if(($_POST['sx'] != '') && ($_POST['sy'] != '') && ($_POST['sz'] != '')) {
                        $this->db->query("UPDATE `homebblobj` SET `scaling_x`= ?, `scaling_y`= ?, `scaling_z`= ? WHERE `id`=?",[$_POST['sx'],$_POST['sy'],$_POST['sz'],$val['id']]);
                        echo 'ok';
                    }
                }else{
                    echo '';
                }
            }
        }

        public function change_rotation(){
            if($_POST['bid']!='') {
                $check = $this->db->select("SELECT `homebblobj`.`id` FROM `homebblobj` WHERE `homebblobj`.`id` = ?;",[$_POST['bid']],'one');
                if($check){
                    if(($_POST['rx'] != '') && ($_POST['ry'] != '') && ($_POST['rz'] != '')) {
                        $this->db->query("UPDATE `homebblobj` SET `rotation_x`= ?, `rotation_y`= ?, `rotation_z`= ? WHERE `id`=?",[$_POST['rx'],$_POST['ry'],$_POST['rz'],$check['id']]);
                        echo 'ok';
                    }
                }else{
                    echo '';
                }
            }
        }
        
        public function check_name(){
            if(($_POST['name'] != '') && ($_POST['type']>0)) {

                switch ($_POST['type']) {
                    case '1':
                        $this->db->addSQL('check_name',"SELECT `id` FROM `homebbl` WHERE `name` = ?;");
                        break;

                    case '2':
                        $this->db->addSQL('check_name',"SELECT `id` FROM `objbbl` WHERE `name` = ?;");
                        break;

                    case '3':
                        $this->db->addSQL('check_name',"SELECT `id` FROM `skybbl` WHERE `name` = ?;");
                        break;
                    
                    case '4':
                        $this->db->addSQL('check_name',"SELECT `id` FROM `terrainbbl` WHERE `name` = ?;");
                        break;

                    default:
                        //$db_check = array($_POST['name']);
                        break;
                }
                
                $db_check = $this->db->execSQL('check_name',[$_POST['name']],'one');

                if($db_check){
                    echo "Model with that name already loaded. Change name model.";
                }else{
                    echo "";
                }
            }
        }

        public function load_preview() {
            //var_dump($_FILES);

            $uploaddir = 'tmp_img/';

            if(!is_dir($uploaddir)) {
                mkdir($uploaddir, 0777, true);
            }

            if(preg_match('/\..*$/', basename($_FILES[0]['name']), $fl)){
                $fl[0] = strtolower($fl[0]);
                if(($fl[0] == '.jpg') || ($fl[0] == '.jpeg') || ($fl[0] == '.png') || ($fl[0] == '.bmp')) {
                    if(!move_uploaded_file($_FILES[0]['tmp_name'], $uploaddir.'tmp'.$fl[0])) {
                        echo  'File upload error: '.basename($_FILES[0]['name']);
                    }else{
                        echo '<img src="tmp_img/tmp'.$fl[0].'" style="width: 100px;" data="tmp'.$fl[0].'">';
                    }
                }else{
                    echo 'File is not supported: '.basename($_FILES[0]['name']);
                }
            }

        }

        public function load(){
            if(($_POST['name']!='') && ($_POST['type']>0)) {
                switch ($_POST['type']) {
                    case '1':
                        $this->db->addSQL('check_name',"SELECT `id` FROM `homebbl` WHERE `name` = ?;");
                        break;

                    case '2':
                        $this->db->addSQL('check_name',"SELECT `id` FROM `objbbl` WHERE `name` = ?;");
                        break;

                    case '3':
                        $this->db->addSQL('check_name',"SELECT `id` FROM `skybbl` WHERE `name` = ?;");
                        break;
                    
                    case '4':
                        $this->db->addSQL('check_name',"SELECT `id` FROM `terrainbbl` WHERE `name` = ?;");
                        break;

                    default:
                        //$db_check = array($_POST['name']);
                        break;
                }
                
                $db_check = $this->db->execSQL('check_name',[$_POST['name']],'one');
                
                if(!$db_check){
                    //include_once($_SERVER['DOCUMENT_ROOT'].'/conf/defines.php');
                    //include_once(ROOT.'/lib/classes/upload.class.php');
                    //include_once(ROOT.'/lib/classes/simpleimage.class.php');
                    include_once('upload.class.php');
                    include_once('simpleimage.class.php');
                    $si = new SimpleImage();

                    $data = array();
                    $files = array();
                    $data['obj'] = 0;
                    $data['status'] = 0;

                    $folder = $this->rs(12);
                    $img = $this->rs(20);

                    switch ($_POST['type']) {
                        case '1':
                            $uploaddir = 'buildings/'.$folder.'/';
                            break;

                        case '2':
                            $uploaddir = 'objects/'.$folder.'/';
                            break;

                        case '3':
                            $uploaddir = 'skybox/'.$folder.'/';
                            break;
                        
                        case '4':
                            $uploaddir = 'terrain/'.$folder.'/';
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
                    copy('tmp_img/'.$_POST['img'], $uploaddir.$img);
                    if(is_file('tmp_img/'.$_POST['img'])){
                        unlink('tmp_img/'.$_POST['img']);
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
                                    $val = $this->db->select("SELECT `id` FROM `skybbl` ORDER BY `id` ASC LIMIT 1;",null,'one');
                                    $this->db->insert("INSERT INTO `homebbl` (`sky_id`,`name`,`folder`,`file`,`img`) VALUES (?,?,?,?,?);",[$val['id'],$_POST['name'],$folder,$lfile,$img]);
                                }
                                break;

                            case '2':
                                if($check == true){
                                    $this->db->insert("INSERT INTO `objbbl` (`name`,`folder`,`file`,`img`) VALUES (?,?,?,?);",[$_POST['name'],$folder,$lfile,$img]);
                                }
                                break;

                            case '3':
                                $this->db->insert("INSERT INTO `skybbl` (`name`,`folder`,`img`) VALUES (?,?,?);",[$_POST['name'],$folder,$img]);
                                break;
                            
                            case '4':
                                $this->db->insert("INSERT INTO `terrainbbl` (`name`,`folder`,`file`) VALUES (?,?,?);",[$_POST['name'],$folder,$img]);
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
        }
        
        public function del_home(){
            if($_POST['hid'] > 0) {
                $buildings = $this->db->select("SELECT `id`, `name` FROM `homebbl` WHERE `id` = ? AND `sel` != 1;",[$_POST['hid']],'one');
                if($buildings) {
                    $this->db->query("UPDATE `homebbl` SET `visible` = 0 WHERE `id` = ?;",[$buildings['id']]);
                    echo 'del';
                }else{
                    echo '<div class="warning">It can not be removed</div>';
                }
            }
        }

        public function del_obj(){
            if($_POST['oid'] > 0) {
                $current = $this->db->select("SELECT `objbbl`.`id`, IFNULL(`homebblobj`.`id`,0) AS 'hid' FROM `objbbl` LEFT JOIN `homebblobj` ON `homebblobj`.`obj_id` = `objbbl`.`id` WHERE `objbbl`.`id`=?;",[$_POST['oid']],'one');
                if($current){
                    if($current['hid'] == 0) {
                        $this->db->query("UPDATE `objbbl` SET `visible` = 0 WHERE `id` = ?;",[$current['id']]);
                        echo 'del';
                    }else{
                        echo '<div class="warning">Delete object from scene</div>';
                    }  
                }else{
                    echo 'count';
                }  
            }
        }

        public function del_sky(){
            if($_POST['sid'] > 0) {
                $current = $this->db->select("SELECT `homebbl`.`sky_id` FROM `homebbl` WHERE sel != 1;",null,one);
                if(($current) && ($current['sky_id'] != $_POST['sid'])){
                    $db_check = $this->db->select("SELECT `id`, `name` FROM `skybbl` WHERE `id` = ?;",[$_POST['sid']],'one');
                    if($db_check) {
                        $this->db->query("UPDATE `skybbl` SET `visible` = 0 WHERE `id` = ?;",[$db_check['id']]);
                        echo 'del';
                    }else{
                        echo '';
                    }  
                }else{
                    echo '<div class="warning">Change default sky</div>';
                }  
            }
        }
        
        public function del_terrain(){
            if($_POST['tid'] > 0) {
                $current = $this->db->select("SELECT `homebbl`.`terrain_id` FROM `homebbl` WHERE sel != 1;",null,'one');
                if(($current) && ($current['terrain_id'] != $_POST['tid'])){
                    $db_check = $this->db->select("SELECT `id`, `name` FROM `terrainbbl` WHERE `id` = ?;",[$_POST['tid']],'one');
                    if($db_check) {
                        $this->db->query("UPDATE `terrainbbl` SET `visible` = 0 WHERE `id` = ?;",[$db_check['id']]);
                        echo 'del';
                    }else{
                        echo '';
                    }  
                }else{
                    echo '<div class="warning">Change default sky</div>';
                }  
            }
        }

        private function rs($l) {
            $a = 'abcdefghjklmnopqrstuvwxyz0123456789_';
            $r = '';
            for($i=0;$i<$l;$i++){
                $r.=substr($a,rand(0,strlen($a)-1),1);
            }
            return $r;
        }

        private function removeDirectory($dir) {
            if ($objs = glob($dir."/*")) {
                foreach($objs as $obj) {
                    is_dir($obj) ? $this->removeDirectory($obj) : unlink($obj);
                }
            }
            rmdir($dir);
        }
    }
?>