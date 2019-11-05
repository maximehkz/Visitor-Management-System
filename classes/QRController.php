<?php

include "./qrcode/qrlib.php";

    class QRController{

        private $_db,
                $_data;

        public function __construct(){
            $this->_db = DB::getInstance();
        }

        public function create($fields = array()){
            if(!$this->_db->insert("preregisters",$fields)){
                throw new Exception("There was an error");
            }
            return true;
        }

        public function generateQRCode($name, $visitorName, $address, $phone, $email, $nic){
            $visitor = array(
              "Name" => $name,
              "visitorName" => $visitorName,
              "address" => $address,
              "phone" => $phone,
              "email" => $email,
              "nic" => $nic
            );
            $filename = $visitorName.uniqid();
            $fileDirectory = "qrcode/temp/".$filename.".png";
            QRcode::png(json_encode($visitor),$fileDirectory);

            $this->_data = $filename;
        }

        public function clockTime($fields = array(), $nic){
            if(!$this->_db->update("preregisters",$fields,"nic",$nic)){
                throw new Exception("There was an error");
            }
            return true;
        }

        public function data(){
            return $this->_data;
        }

    }

?>
