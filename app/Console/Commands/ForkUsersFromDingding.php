<?php

namespace App\Console\Commands;

use App\Entity\User\DingdingUser;
use App\Services\Util;
use Illuminate\Console\Command;
use Wangjian\Dingding\DingdingClient;
use Log;

class ForkUsersFromDingding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fork_users:dingding';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将钉钉里面的人员信息同步到用户中心';

    /**
     * dingding client
     * @var \Wangjian\Dingding\DingdingClient
     */
    protected $client;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(DingdingClient $client)
    {
        parent::__construct();

        $this->client = $client;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //获取所有部门
        $departments = $this->client->getDepartmentList(Util::getDingdingAccessToken(), 1);
        if($departments['errcode'] > 0) {
            Log::error("用户同步失败({$departments['errcode']}): {$departments['errmsg']}");
            exit;
        }

        foreach($departments['department'] as $department) {
            $page = 1;
            $countPerPage = 100;

            while(true) {
                $users = $this->client->getDepartmentUsers(Util::getDingdingAccessToken(), $department['id'], ($page-1) * $countPerPage, $countPerPage, null, null, false);
                if($users['errcode'] > 0) {
                    $page++;
                    continue;
                }

                //将用户插入到用户表中
                foreach($users['userlist'] as $user) {
                    DingdingUser::getGlobalUserId($user);
                }

                $page++;
                if(!$users['hasMore']) {
                    break;
                }
            }
        }

        Log::info('用户同步成功');
    }
}
