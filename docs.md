# 文档
## 模块：用户
### 接口：注册
* 地址：/user
* 类型：POST
* 状态码：200
* 请求接口格式：

```
├─ username: String
├─ password: String
├─ bio: String (个人简介)
├─ realname: String (真实姓名)
└─ groupname: String (所属组织)

```

* 返回接口格式：

```
└─ msg: String

```


### 接口：登录
* 地址：/user
* 类型：GET
* 状态码：200
* 请求接口格式：

```
├─ username: String
└─ password: String

```

* 返回接口格式：

```
├─ token: RegExp (登录成功时返回)
└─ msg: String

```


### 接口：获取信息
* 地址：/user/:username
* 类型：GET
* 状态码：200
* 请求接口格式：

```
└─ Authorization: RegExp

```

* 返回接口格式：

```
├─ avatar: String
├─ bio: String (个人简介)
├─ realname: String (真实姓名（有 Authorization 请求头时返回）)
├─ groupname: String (所属组织)
├─ verified: String (是否实名验证)
└─ msg: String

```


### 接口：修改信息
* 地址：/user/:username
* 类型：PUT
* 状态码：200
* 请求接口格式：

```
├─ Authorization: RegExp
├─ oldPassword: String
├─ newPassword: String
├─ avatar: String
├─ bio: String (个人简介)
└─ groupname: String (所属组织)

```

* 返回接口格式：

```
├─ token: RegExp (修改密码成功时返回)
└─ msg: String

```


### 接口：注销
* 地址：/user/:username
* 类型：DELETE
* 状态码：200
* 请求接口格式：

```
└─ Authorization: RegExp

```

* 返回接口格式：

```
└─ msg: String

```
