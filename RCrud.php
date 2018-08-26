<?php

/*
CreatedBy : Asep Yayat
Date : 26 Aug 2018
Name : Crud Library
*/

class Http{

    /**
    * HTTP METHOD
    **/
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const PATCH = 'PATCH';
}

class Request extends Http{

}

class RCrud extends Request{

    /**
    * Mendapatkan instance CI
    **/
    private $CI;

    /**
    * Mendefinisikan variable untuk response
    **/
    private $response;

    /**
    * Pesan error
    **/
    private $err_method = 'ERR, Please set HTTP Method';

    /**
    * constructor
    **/
    public function __construct(){
        $this->CI =& get_instance();
    }

    /**
    * Mengambil semua parameter yang di request
    * melalui HTTP method
    **/
    public function getParams($options=array()){
        $data = array();
        $opt = (object) $options;

        //case http method
        switch (isset($opt->method)) {
            case Request::POST:
                $request = $this->post();
                break;
            default:
                $request = $this->get();
                break;
        }

        //try
        try {
            switch(isset($opt->type)){
                //jika ingin mendapatkan 1 string
                case 'first':
                    $string = $opt->string;
                    $data[$string] = $request[$string];
                    break;
                //ini defaultnya
                default:
                    $option = '';

                    //check kondisi exclude atau inclue
                    if(isset($opt->include)){
                        $option = 'include';
                    } else if(isset($opt->exclude)){
                        $option = 'exclude';
                    } else {
                        //
                    }

                    //kondisi untuk mendaptakan include
                    $include = isset($opt->include) ? $opt->include : array();

                    //kondisi untuk mendaptakan exclude
                    $exclude = isset($opt->exclude) ? $opt->exclude : array();
                    
                    //main foreach
                    foreach($request as $key => $val) { 

                        switch($option){
                            case 'include':
                               // $data[$key] = $val;
                                break;
                            case 'exclude':
                                $data[$key] = $val;
                                break;
                            default:
                                $data[$key] = $val;
                                break;
                        }
                    }   

                    //jika terdapat opsi pengecualian
                    if(count($exclude) > 0){
                        foreach($exclude as $keyExclude){
                            unset($data[$keyExclude]);
                        }
                    }   

                    //jika terdapat opsi include        
                    if(count($include) > 0){
                        foreach($include as $keyInclude){
                            $data[$keyInclude] = $_POST[$keyInclude];
                        }
                    }   

                    break;
            }
        } catch(Exception $e){
            // error
            echo "ERRP 001, Please try again.";
        }

        //if method not set
        if(!isset($opt->method)){
            return false;
        } else {
            return $data;
        }
    }

    //insert data
    public function setData($table=null, $options=array()){
        $data = $this->getParams($options);
        if($table != '' && $data != false){
            try {
                //insert data
                $this->CI->db
                    ->set($data)
                    ->insert($table);
                $this->response = 1;
            } catch (Exception $e) {
                //error
                echo "ERRP 002, ".$e->getMessage();
                $this->response = 0;
            }
        } else {
            echo $this->err_method;
        }

        return $this->response;
    }

    //update data
    public function updateData($table=null, $options=array()){
        $data = $this->getParams($options);
        if($table != '' && $data != false){
            try {
                //update Data
                $this->CI->db
                    ->where($options['where'])
                    ->set($data)
                    ->update($table);
                $this->response = 1;
            } catch (Exception $e) {
                //error
                echo "ERRP 002, ".$e->getMessage();
                $this->response = 0;
            }
        } else {
            echo $this->err_method;
        }

        return $this->response ;
    }

    //delete
    public function deleteData($table=null, $options=array()){
        $data = $this->getParams($options);
        if($table != '' && $data != false){
            try {
                //update Data
                $this->CI->db
                    ->where($options['where'])
                    ->set($data)
                    ->delete($table);
                $this->response = 1;
            } catch (Exception $e) {
                //error
                echo "ERRP 002, ".$e->getMessage();
                $this->response = 0;
            }
        } else {
            echo $this->err_method;
        }

        return $this->response ;
    }

    public function post(){
        //return $_POST;
        return $this->CI->input->post(null, TRUE);
    }

    public function get(){
        //return $_GET;
        return $this->CI->input->get(null, TRUE);
    }    

    public function insert(){
        $data = array(
            'name' => $this->input->post('name')
        );

        $this->m_contact->insert($data);
    }
}
