<?php

// 
// Author: Alejandro Landini
// toDo: 
// revision:
// 

class JsonFile {
    public $menssage;
    public $path;
    public $filename;
    public $json;
    public $data;
    private $file;

    public function __construct($path='', $filename='') {
        $this->path = $path;
        $this->filename = $filename;
        $this->file = $this->path.$this->filename;
        $this->data = $this->GetFile();
    }
    
        private function CreateFile(){
            $this->json = json_encode($this->data);
            if (!$this->existFile()){
                $handle = fopen($this->file,'w+');
                fwrite($handle, $this->json);
                fclose($handle);
                chmod($this->file, 0777); 
                $this->menssage= 'Data successfully saved in new file';
            }else{
                if(file_put_contents($this->file, $this->json)) {
                    $this->menssage= 'Data successfully saved';
                }else{ 
                    $this->menssage= "error";
                }
            }
            return;
        }
        
        private function existFile(){
            return file_exists($this->file);
        }

        private function GetFile(){
            if ($this->existFile()){
                $this->json = file_get_contents($this->file);
                return json_decode($this->json,true);
            }else{
                return '';
            }
        }
        
        public function Add($json=""){ //to file
            if ($json){
                if (empty($this->data)){
                    $a[]=json_decode($json,true);
                    $this->data=$a;
                }else{
                    array_push($this->data, json_decode($json,true));
                }
            }
            $this->CreateFile();
            return;   
        }
}


//test
$hooks_dir = dirname(__FILE__);
$a = new JsonFile($hooks_dir.'/../language/',"config_prueba.json");
$a->Add('{"user":"admin","lang":"es"}');
echo $a->json;
echo "<br>";
var_dump($a->data);
echo "<br>";
echo $a->menssage;
echo "<br>";
echo $a->filename;
echo "<br>";
echo $a->path;
