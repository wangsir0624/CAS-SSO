<?php
namespace App\Entity\User;

use Illuminate\Contracts\Support\Arrayable;
use App\Model\User as UserModel;
use App\Model\OauthUser;

class User implements Arrayable {
    /**
     * 可以被转换成json的属性
     * @var array
     */
    public $jsonable = ['id', 'mobile', 'email', 'realname', 'gender', 'birthday', 'avatar'];

    public $id = 0;

    public $mobile = '';

    public $password = '';

    public $email = '';

    public $status = 1;

    public $realname = '';

    public $gender = -1;

    public $birthday = '0000-00-00';

    public $avatar = '';

    public $last_login_time = 0;

    public $last_login_ip = '';

    public static function find($value, $key = 'id', $type = 'inner') {
        $user = UserModel::where($key, $value)->first();
        if(empty($user)) {
            return null;
        }

        if($type == 'inner') {
            $oauthUser = null;
        } else {
            $oauthUser = $oauthUser = $user->oauthUsers()->where('oauth_type', $type)->first();
        }

        if(empty($oauthUser)) {
            $userEntity = new self;
        } else {
            $userEntity = self::createUser($type);

            $userEntity->oauth_id = $oauthUser->id;
            $userEntity->identifier = $oauthUser->identifier;

            $data = json_decode($oauthUser->data, true);
            foreach($data as $key => $value) {
                if(property_exists(get_class($userEntity), $key)) {
                    $userEntity->$key = $value;
                }
            }
        }

        $userEntity->id = $user->id;
        $userEntity->mobile = $user->mobile;
        $userEntity->password = $user->password;
        $userEntity->email = $user->email;
        $userEntity->status = $user->status;
        $userEntity->realname = $user->realname;
        $userEntity->gender = $user->gender;
        $userEntity->birthday = $user->birthday;
        $userEntity->avatar = $user->avatar;
        $userEntity->last_login_time = $user->last_login_time;
        $userEntity->last_login_ip = $user->last_login_ip;

        return $userEntity;
    }

    public function toArray() {
        $data = [];

        foreach($this as $key => $value){
            if(in_array($key, $this->jsonable)) {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    public static function createUser($type = 'inner') {
        $user = null;

        switch($type) {
            case 'dingding':
                $user = new DingdingUser;
                break;
            case 'inner':
            default:
                $user = new self;
                break;
        }

        return $user;
    }
}