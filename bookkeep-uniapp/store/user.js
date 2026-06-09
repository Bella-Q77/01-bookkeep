// 用户数据存储
import { getUserInfo, updateUserInfo } from '@/api/user'

const userStore = {
  state: {
    userInfo: null,
    genders: ['男', '女', '其他']
  },
  
  // 获取用户信息
  async fetchUserInfo() {
    try {
      const res = await getUserInfo()
      if (res.code === 0) {
        this.state.userInfo = res.data
        return res.data
      } else {
        throw new Error(res.message || '获取用户信息失败')
      }
    } catch (error) {
      console.error('获取用户信息失败:', error)
      throw error
    }
  },
  
  // 更新用户信息
  async updateUserInfo(data) {
    try {
      const res = await updateUserInfo(data)
      if (res.code === 0) {
        // 更新本地存储的用户信息
        if (this.state.userInfo) {
          Object.assign(this.state.userInfo, data)
        } else {
          this.state.userInfo = data
        }
        return res.data
      } else {
        throw new Error(res.message || '更新用户信息失败')
      }
    } catch (error) {
      console.error('更新用户信息失败:', error)
      throw error
    }
  }
}

export default userStore