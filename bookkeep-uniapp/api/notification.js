// 通知设置相关API

// 获取通知设置
export const getNotificationSettings = () => {
  return new Promise((resolve) => {
    // 实际项目中替换为真实API调用
    // return uni.request({
    //   url: '/api/notification/settings',
    //   method: 'GET'
    // })
    
    // 模拟API调用
    setTimeout(() => {
      resolve({
        code: 0,
        data: {
          newMessage: true,
          sound: true,
          vibration: true,
          nightMode: false
        }
      })
    }, 500)
  })
}

// 更新通知设置
export const updateNotificationSettings = (data) => {
  return new Promise((resolve) => {
    // 实际项目中替换为真实API调用
    // return uni.request({
    //   url: '/api/notification/settings',
    //   method: 'POST',
    //   data
    // })
    
    // 模拟API调用
    setTimeout(() => {
      resolve({
        code: 0,
        message: '设置已更新',
        data
      })
    }, 300)
  })
}