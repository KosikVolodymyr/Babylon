<?php
class SimpleImage {

    var $image;
    var $image_type;

    function load($filename) {
        $image_info = getimagesize($filename);
        $this->image_type = $image_info[2];
        if( $this->image_type == IMAGETYPE_JPEG ) {
            $this->image = imagecreatefromjpeg($filename);
        } elseif( $this->image_type == IMAGETYPE_GIF ) {
            $this->image = imagecreatefromgif($filename);
        } elseif( $this->image_type == IMAGETYPE_PNG ) {
            $this->image = imagecreatefrompng($filename);
        }
    }
    function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {
        if( $image_type == IMAGETYPE_JPEG ) {
            imagejpeg($this->image,$filename,$compression);
        } elseif( $image_type == IMAGETYPE_GIF ) {
            imagegif($this->image,$filename);
        } elseif( $image_type == IMAGETYPE_PNG ) {
            imagepng($this->image,$filename);
        }
        if( $permissions != null) {
            chmod($filename,$permissions);
        }
    }
    function output($image_type=IMAGETYPE_JPEG) {
        if( $image_type == IMAGETYPE_JPEG ) {
            imagejpeg($this->image);
        } elseif( $image_type == IMAGETYPE_GIF ) {
            imagegif($this->image);
        } elseif( $image_type == IMAGETYPE_PNG ) {
            imagepng($this->image);
        }
    }
    function getWidth() {
        return imagesx($this->image);
    }
    function getHeight() {
        return imagesy($this->image);
    }
    function resizeToHeight($height) {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width,$height);
    }
    function resizeToWidth($width) {
        $ratio = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resize($width,$height);
    }
    function scale($scale) {
        $width = $this->getWidth() * $scale/100;
        $height = $this->getheight() * $scale/100;
        $this->resize($width,$height);
    }
    function resize($width,$height) {
        $new_image = imagecreatetruecolor($width, $height);
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
    }
    function crop($image, $out, $x_o, $y_o, $w_o, $h_o) {
        if (($x_o < 0) || ($y_o < 0) || ($w_o < 0) || ($h_o < 0)) {
            echo "Некорректные входные параметры";
            return false;
        }
        list($w_i, $h_i, $type) = getimagesize($image);
        $types = array("", "gif", "jpeg", "png");
        $ext = $types[$type];
        if ($ext) {
            $func = 'imagecreatefrom'.$ext;
            $img_i = $func($image);
        } else {
            echo 'Некорректное изображение';
            return false;
        }
        if ($x_o + $w_o > $w_i) $w_o = $w_i - $x_o;
        if ($y_o + $h_o > $h_i) $h_o = $h_i - $y_o;
        $img_o = imagecreatetruecolor($w_o, $h_o);
        imagecopy($img_o, $img_i, 0, 0, $x_o, $y_o, $w_o, $h_o);
        $func = 'image'.$ext;
        return $func($img_o, $out);
    }
}
?>