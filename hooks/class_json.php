<?php

// 
// Author: Alejandro Landini
// toDo: 
// revision: 0
// 

class JsonFile {
    public $menssage; //return control message
    public $path; //path to put file
    public $filename; //insput the file name
    public $json; //return string json format data
    public $data; //return array object
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

        private function GetFile(){ //get content file and update de json object and data
            if ($this->existFile()){
                $this->json = file_get_contents($this->file);
                return json_decode($this->json,true);
            }else{
                return '';
            }
        }
        
        public function Add($json=""){ //to file and push in array object
            if ($json){
                $a[]=json_decode($json,true);
                if (empty($this->data)){
                    $this->data=$a;
                }else{
                    array_push($this->data, $a);
                }
            }
            $this->CreateFile();
            return;   
        }
}


//test
// $hooks_dir = dirname(__FILE__);
// $a = new JsonFile($hooks_dir.'/../language/',"config_prueba.json");
// $a->Add('{"user":"admin","lang":"es"}');
// echo $a->json;
// echo "<br>";
// var_dump($a->data);
// echo "<br>";
// echo $a->menssage;
// echo "<br>";
// echo $a->filename;
// echo "<br>";
// echo $a->path;
