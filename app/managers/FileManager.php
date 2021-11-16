<?php

class FileManager{

    public static function SaveFile ($request, $idPedido)
    {

        if ($_FILES["photo"]["error"] > 0)
        {
            echo "Error: " . $_FILES["photo"]["error"] . "<br />";
            return false;
        }
        else
        {
            $path = './uploads/';
            $file = $path . basename($idPedido.'.jpg');
            //var_dump($file);
            if(file_exists($path)){
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $file)) {

                return $file;
                }
            }
            else{
                mkdir($path);
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $file)) {

                    return $file;
                    }

            }
        }
    }
}
?>