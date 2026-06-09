// 用户模拟数据
export const mockUser = {
  // 账号密码登录
  login(params) {
    return new Promise((resolve, reject) => {
      setTimeout(() => {
        if (params.username && params.password) {
          resolve({
            code: 200,
            data: {
              token: 'mock-token',
              userInfo: {
                username: params.username,
                nickname: '测试用户'
              }
            },
            message: '登录成功'
          })
        } else {
          reject(new Error('账号或密码不能为空'))
        }
      }, 500)
    })
  },

  // 微信登录
  wechatLogin(params) {
    return new Promise((resolve) => {
      setTimeout(() => {
        resolve({
          code: 200,
          data: {
            token: 'mock-wechat-token',
            userInfo: {
              username: '微信用户',
              nickname: '微信用户'
            }
          },
          message: '微信登录成功'
        })
      }, 500)
    })
  },

  // 注册
  register(params) {
    return new Promise((resolve, reject) => {
      setTimeout(() => {
        const emailReg = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/
        if (!emailReg.test(params.email)) {
          reject(new Error('邮箱格式不正确'))
          return
        }
        
        if (params.password.length < 6) {
          reject(new Error('密码长度不能小于6位'))
          return
        }
        
        resolve({
          code: 200,
          data: {
            id: Math.floor(Math.random() * 10000),
            ...params
          },
          message: '注册成功'
        })
      }, 500)
    })
  },

  // 退出登录
  logout() {
    return new Promise((resolve) => {
      setTimeout(() => {
        resolve({
          code: 200,
          message: '退出成功'
        })
      }, 300)
    })
  }
}

export default mockUser;