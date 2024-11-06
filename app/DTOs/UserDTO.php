<?php


namespace App\DTOs;

class UserDTO
{
    public $id;
    public $name;
    public $mobile;
    public $photo;
    public $profile_completed;

    /**
     * سازنده کلاس UserDTO.
     *
     * @param int $id
     * @param string $name
     * @param string $mobile
     * @param string $photo
     * @param bool $profile_completed
     */
    public function __construct($id, $name, $mobile, $photo, $profile_completed)
    {
        $this->id = $id;
        $this->name = $name;
        $this->mobile = $mobile;
        $this->photo = $photo;
        $this->profile_completed = $profile_completed;
    }
}
