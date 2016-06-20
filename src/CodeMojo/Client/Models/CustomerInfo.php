<?php

namespace CodeMojo\Client\Models;

class CustomerInfo {

    private $name;
    private $email;
    private $phone;
    private $apple_push_id;
    private $android_push_id;
    private $windows_push_id;
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
     * @param mixed $apple_push_id
     */
    public function setApplePushId($apple_push_id)
    {
        $this->apple_push_id = $apple_push_id;
    }

    /**
     * @param mixed $android_push_id
     */
    public function setAndroidPushId($android_push_id)
    {
        $this->android_push_id = $android_push_id;
    }

    /**
     * @param mixed $windows_push_id
     */
    public function setWindowsPushId($windows_push_id)
    {
        $this->windows_push_id = $windows_push_id;
    }

    /**
     * @param mixed $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }



    function toArray(){
        return array('name' => $this->name, 'email' => $this->email, 'phone' => $this->phone, 'gender' => $this->gender,
            'apn' => $this->apple_push_id, 'gcm' => $this->android_push_id, 'wpn' => $this->windows_push_id);
    }


}