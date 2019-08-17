# NCUHome 2018 Hack Week

**ARCHIVED 2018-12-16**

## API 说明

- 需要验证的请求会在头部 `Authorization` 字段中附带 Token。
- 下列请求数据均为实例，内容以实际为准。

## 活动 `/api/activity`

### 发布

- 方法 `POST`
- 请求 **需要验证**

```json
{
  "username": "发布活动的用户名",
  "title": "活动名",
  "category": "分类名",
  "place": "活动地点",
  "time": "发布时间（YY-MM-DD HH:mm）",
  "description": "活动详细描述",
  "images": ["/image1.jpg", "/image2.jpg", "..."]
}
```

- 响应

```json
{
  "success": true,
  "id": "发布的活动 ID"
}
```

```json
{
  "success": false,
  "message": "发布失败"
}
```

### 查询

- 方法 `GET`
- 请求 `/api/activity?id=活动 ID`
- 响应 （参照**发布**的请求）

### 首页/搜索

- 方法 `GET`
- 请求 - `/api/activity` 首页 - `/api/activity?keyword=关键词` - `/api/activity?place=地点` - `/api/activity?keyword=关键词&place=地点`
- 响应

```json
[
  {
    "参照 发布 的请求": "无匹配结果则返回空数组"
  },
  {
    "...": "..."
  }
]
```

### 编辑

- 方法 `PUT`
- 请求 **需要验证**

```json
{
  "username": "执行修改的用户名",
  "id": "修改的活动 ID",
  "其他": "参照 发布 的请求"
}
```

- 响应

```json
{
  "success": true
}
```

```json
{
  "success": false,
  "message": "修改失败"
}
```

### 删除

- 方法 `DELETE`
- 请求 `/api/activity?id=活动 ID` **需要验证**
- 响应

```json
{
  "success": true
}
```

```json
{
  "success": false,
  "message": "删除失败"
}
```

## 用户 `/api/user`

### 注册

- 方法 `POST`
- 请求

```json
{
  "username": "用户名",
  "password": "密码 (SHA1 加密)",
  "nickname": "昵称",
  "realname": "真实姓名（可选）",
  "groupname": "组织名（可选）",
  "bio": "简介（可选）"
}
```

- 响应

```json
{
  "success": true,
  "token": "注册后可直接登录"
}
```

```json
{
  "success": false,
  "message": "用户名已存在"
}
```

### 登录

如果 `POST` 请求中只提供了用户名和密码则为登录。

- 方法 `POST`
- 请求

```json
{
  "username": "用户名",
  "password": "密码 (SHA1 加密)"
}
```

- 响应

```json
{
  "success": true,
  "token": "..."
}
```

```json
{
  "success": false,
  "message": "密码错误"
}
```

### 查询

- 方法 `GET`
- 请求 - `/api/user` **返回 Token 对应的用户信息** - `/api/user?name=用户名` **不返回真实姓名**
- 响应

```json
{
  "success": true,
  "avatar": "头像地址",
  "verified": "是否已验证（布尔值）",
  "其他": "参照 注册"
}
```

```json
{
  "success": false,
  "message": "用户不存在"
}
```

### 修改

- 方法 `PUT`
- 请求 （参照**注册**的请求）**需要验证；用户只能设置一次 `realname` 和 `groupname`**
- 响应

```json
{
  "success": true,
  "token": "请求中有新密码则返回新 Token"
}
```

```json
{
  "success": false,
  "message": "修改失败"
}
```

### 删除

- 方法 `DELETE`
- 请求 `/api/user?name=用户名` **需要验证**
- 响应

```json
{
  "success": true
}
```

```json
{
  "success": false,
  "message": "删除失败"
}
```
