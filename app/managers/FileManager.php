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

    public static function GetCSV($aux)
    {
        $array[] = array();

        if (($open = fopen($aux, "r")) !== FALSE) {

            $count = 0;
            $registros=0;
            while (($data =fgetcsv($open, 0, ",")) !== FALSE) {

                $count++;

                if ($count == 1) { continue; } //salto el encabezado

                if(!Producto::obtenerProductoByName($data[0])){ //valido que el producto no exista
                    $array[] = $data;
                    $registros++;

                }else { continue;}
            }

            fclose($open);

            if($registros!=0){
               //var_dump($array);
                return $array;
            }
            else{
                return null;
            }
        }
    }

    // public static function SaveToCSV($data)
    // {

    //     $filename = '../app/salida.csv';

    //     $f = fopen($filename, 'w+');

    //     if ($f === false
    //     ) {
    //         die('Error' . $filename);
    //     }

    //     foreach ($data as $row) {
    //         fputcsv($f, (array)$row);
    //     }

    //     fclose($f);

    //     return true;
    // }

    public static function SaveToCSV($data, $fields)
    {

        $filename = 'salida.csv';

        //create a file pointer
        $f = fopen('php://memory', 'w');

        if (
            $f === false
        ) {
            die('Error' . $filename);
        }

        fputcsv($f, $fields, ",");

        foreach ($data as $row) {
            fputcsv($f, (array)$row);
        }
        
        //move back to beginning of file
        fseek($f, 0);

        //set headers to download file rather than displayed
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');

        //output all remaining data on a file pointer
        fpassthru($f);
        
        fclose($f);

        return true;
    }
}
