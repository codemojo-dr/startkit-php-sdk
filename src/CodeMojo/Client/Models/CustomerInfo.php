<?php

namespace CodeMojo\Client\Models;

class CustomerInfo {

    private $name;
    private $email;
    private $phone;
    private $apn;
    private $gcm;
    private $wpn;
    private $gender;

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @param mixed $apn
     */
    public function setApplePushId($apn)
    {
        $this->apn = $apn;
    }

    /**
     * @param mixed $gcm
     */
    public function setAndroidPushId($gcm)
    {
        $this->gcm = $gcm;
    }

    /**
     * @param mixed $wpn
     */
    public function setWindowsPushId($wpn)
    {
        $this->wpn = $wpn;
    }

    /**
     * @param mixed $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    function __call($name, $arguments)
    {
        if(str_contains($name, 'set') !== false){
            $name = strtolower(str_replace('set', '', $name));
            $this->$name = $arguments[0];
        }elseif(str_contains($name, 'get') !== false){
            $name = strtolower(str_replace('get', '', $name));
            return $this->$name;
        }
    }

    function toArray(){
        return get_object_vars($this);
    }

}