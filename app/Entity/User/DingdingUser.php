<?php
namespace App\Entity\User;

use App\Model\User as UserModel;
use App\Model\OauthUser as OauthUserModel;

class DingdingUser extends OauthUser {
    public $jsonable = ['id', 'mobile', 'email', 'realname', 'gender', 'birthday', 'avatar', 'oauth_type'];

    public $oauth_type = 'dingding';

    public $userid = '';

    public $name = '';

    public $tel = '';

    public $workPlace = '';

    public $remark = '';

    public $active = true;

    public $orderInDepts = [];

    public $isAdmin = false;

    public $isBoss = false;

    public $dingId = '';

    public $unionid = '';

    public $isLeaderInDepts = [];

    public $isHide = false;

    public $department = [];

    public $position = '';

    public $jobnumber = '';

    public $extattr = [];

    public $roles = [];

    public static function getGlobalUserId($userInfo) {
        $user = UserModel::where('mobile', $userInfo['mobile'])->first();

        //如果不存在，那么插入到users表
        if(empty($user)) {
            $user = new UserModel;
            $user->realname = $userInfo['name'];
            $user->mobile = $userInfo['mobile'];
            $user->email = $userInfo['email'];
            $user->avatar = $userInfo['avatar'];
            $user->password = '';
            $user->status = 1;
            $user->gender = -1;

            $user->save();
        } else {
            $oldUser = clone $user;

            if(empty($user->realname)) {
                $user->realname = $userInfo['name'];
            }

            if(empty($user->email)) {
                $user->email = $userInfo['email'];
            }

            if(empty($user->avatar)) {
                $user->avatar = $userInfo['avatar'];
            }

            if($user != $oldUser) {
                $user->save();
            }
        }

        $oauthUser = OauthUser::where('user_id', $user->id)->where('type', 'dingding')->first();

        //如果不存在，那么插入到oauth_users表
        if(empty($oauthUser)) {
            $oauthUser = new OauthUserModel;
            $oauthUser->user_id = $user->id;
            $oauthUser->type = 'dingding';
            $oauthUser->identifier = $userInfo['unionid'];
            $oauthUser->data = json_encode($userInfo);

            $oauthUser->save();
        } else {
            $oauthUser->data = json_encode($userInfo);

            $oauthUser->save();
        }

        //返回用户ID
        return $user->id;
    }
}