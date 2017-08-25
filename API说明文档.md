# API文档

* [获取已登陆用户的信息](#1)
* [获取用户中心的用户列表](#2)

## 说明
接口以json格式返回，无论请求是否成功，都会返回errcode和errmsg，请求成功时，errcode为0，errmsg为''，可以根据errcode来判断请求是否成功

## <span id="1">获取已登陆用户的信息</span>

### 说明
凭票据获取已登陆的用户信息

### 请求地址
https://passport.linghit.com/api/userInfo?appKey=appKey&appSecret=appSecret&code=code

### 接口请求方式
GET

### 参数
| 参数名 | 是否必填 | 类型 | 说明 |
| ----- | ------- | ---- | ---- |
| appKey | 是 | string | app key |
| appSecret | 是 | string | app secret |
| code | 是| string | 临时票据 |

### 返回
此接口以json格式返回数据，无论请求是否成功，都会返回errcode和errmsg，可以利用errcode来判断请求是否成功

#### 正确返回样式
	{
		"errcode": 0,
		"errmsg": "",
		"id": 1,
		"mobile": "13911111111",
		"email": "test@qq.com",
		"realname": "张三",
		"oauth_type": "dingding",
		"identifier": "fdafadfafa"
	}

#### 错误返回样式
	{
		"errcode": 1,
		"errmsg": "参数错误"
	}

## <span id="2">获取用户中心的用户列表</span>

### 请求地址
https://passport.linghit.com/api/users

### 接口请求方式
GET

### 参数
| 参数名 | 是否必填 | 类型 | 说明 |
| ----- | ------- | ---- | ---- |
| appKey | 是 | string | app key |
| appSecret | 是 | string | app secret |
| offset | 否 | integer | 从那一条记录开始获取 |
| size | 否 | integer | 获取多少条记录，默认获取15条，最大为100 |
| order | 否 | string | 默认按创建时间顺序排列，created_time_asc为按创建时间顺序排列，created_time_desc为按创建时间倒序排列 |

### 返回
```
{
    "errcode": 0,
    "errmsg": "",
    "hasmore": false,
    "userlist": [
        {
            "id": 10,
            "mobile": "13911111111",
            "email": "***@linghit.com",
            "password": "",
            "status": 1,
            "realname": "***",
            "gender": -1,
            "birthday": "0000-00-00",
            "avatar": "",
            "dingding_unionid": "****"
        },
        {
            "id": 7,
            "mobile": "13922222222",
            "email": "****@linghit.com",
            "password": "",
            "status": 1,
            "realname": "****",
            "gender": -1,
            "birthday": "0000-00-00",
            "avatar": "",
            "dingding_unionid": "******"
        }
	]
}
```

