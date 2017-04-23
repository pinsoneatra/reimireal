<?php

namespace models\directory;

class Opendir {

    public function __construct() {
        
    }

    public function targetFile($folder) {
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . URL_SUB_FOLDER . "/storage/" . $folder . "/";
        $target_dir = str_replace('//', '/', $target_dir);
        return $target_dir;
    }

    public function deleteFile($file, $folder) {
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . URL_SUB_FOLDER . "/storage/" . $folder . "/";
        $target_dir = str_replace('//', '/', $target_dir);
        if (unlink($target_dir . $file)) {
            return true;
        } else {
            return false;
        }
    }

    public function createFolder($path, $folder = "", $method = "") {
        $newpath = $this->targetFile($path);
        $dateTimeFolder = "";
        $oldmask = "";
        switch ($method) {
            case "datetime":
                $dateTimeFolder = strtotime(date('Y-m-d H:i:s'));
                if (!file_exists($newpath . $dateTimeFolder)) {
                    $oldmask = umask(0);
                    mkdir($newpath . $dateTimeFolder);
                    umask($oldmask);
                }
                break;
            default :
                if (!file_exists($path . $folder)) {
                    $oldmask = umask(0);
                    mkdir($path . $folder, 0777);
                    umask($oldmask);
                }
                break;
        }
        return $folder;
    }
}

?>
