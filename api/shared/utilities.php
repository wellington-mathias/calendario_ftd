<?php
class Utilities{
  
    public function getPaging($page, $total_rows, $records_per_page, $page_url){
  
        // paging array
        $paging_arr=array();
  
        // button for first page
        $paging_arr["first"] = $page>1 ? "{$page_url}page=1" : "";
  
        // count all products in the database to calculate total pages
        $total_pages = ceil($total_rows / $records_per_page);
  
        // range of links to show
        $range = 2;
  
        // display links to 'range of pages' around 'current page'
        $initial_num = $page - $range;
        $condition_limit_num = ($page + $range)  + 1;
  
        $paging_arr['pages']=array();
        $page_count=0;
          
        for($x=$initial_num; $x<$condition_limit_num; $x++){
            // be sure '$x is greater than 0' AND 'less than or equal to the $total_pages'
            if(($x > 0) && ($x <= $total_pages)){
                $paging_arr['pages'][$page_count]["page"]=$x;
                $paging_arr['pages'][$page_count]["url"]="{$page_url}page={$x}";
                $paging_arr['pages'][$page_count]["current_page"] = $x==$page ? "yes" : "no";
  
                $page_count++;
            }
        }
  
        // button for last page
        $paging_arr["last"] = $page<$total_pages ? "{$page_url}page={$total_pages}" : "";
  
        // json format
        return $paging_arr;
    }
    
    public function validateErrorMessage($file) {
        switch ($file['error']) {
            case 1:
                return "O arquivo enviado excede o limite maximo permitido pela plataforma";
            case 2:
                return "O arquivo enviado excede o limite maximo permitido pelo sistema";
            case 3:
                return "O upload do arquivo foi feito parcialmente";
            case 4:
                return "Nenhum arquivo foi enviado";
            case 6:
                return "Pasta temporaria ausente";
            case 7:
                return "Falha ao escrever o arquivo em disco";
            case 8:
                return "Uma extensão do PHP interrompeu o upload do arquivo";
            default:
                return "Erro inesperado ao enviar o arquivo (Codigo: " . $file['error'] . ")";
        }
    }
    
    public function validateFileType($type, $validTypes) {
        foreach ($validTypes as $validType) {
            if (strcasecmp($type, $validType) == 0) {
             return true;
            }
        }
    
        return false;
    }
    
    public function validateFileSize($size, $validSize) {
        if ($size <= $validSize) {
            return true;
        } else {
            return false;
        }
    }
}
?>