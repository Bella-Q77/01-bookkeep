/**
 * 用户相关API
 */
import myRequest, { BASE_URL } from '@/common/utils/request';

/**
 * 用户登录
 * @param {Object} data - 登录数据
 * @param {string} data.username - 用户名/邮箱
 * @param {string} data.password - 密码
 * @returns {Promise} 登录结果Promise
 */
export const login = (data) => {
  return myRequest({
    url: '/portalmember/api.Login/login',
    method: 'POST',
    data: {
      username: data.username,
      password: data.password
    },
    loading: false, // 登录页面自己控制loading
    toast: false    // 登录页面自己处理错误提示
  });
};

/**
 * 微信登录
 * @param {Object} data - 微信登录数据
 * @param {string} data.code - 微信登录code
 * @returns {Promise} 登录结果Promise
 */
export const wechatLogin = (data) => {
  return myRequest({
    url: '/portalmember/api.Login/wechatLogin',
    method: 'POST',
    data: {
      code: data.code
    },
    loading: false,
    toast: false
  });
};

/**
 * 微信OpenID登录
 * @param {Object} data - 微信登录数据
 * @param {string} data.openid - 微信用户openid
 * @param {string} data.nickname - 用户昵称（可选）
 * @param {string} data.avatar - 用户头像（可选）
 * @returns {Promise} 登录结果Promise
 */
export const wechatOpenidLogin = (data) => {
  return myRequest({
    url: '/portalmember/api.Login/wechatOpenidLogin',
    method: 'POST',
    data: {
      openid: data.openid,
      nickname: data.nickname || '',
      avatar: data.avatar || ''
    },
    loading: false,
    toast: false
  });
};

/**
 * 获取微信用户信息（包含openid）
 * @returns {Promise} 用户信息Promise
 */
export const getWechatUserInfo = () => {
  return new Promise((resolve, reject) => {
    // 先获取登录code
    uni.login({
      provider: 'weixin',
      success: (loginRes) => {
        if (!loginRes.code) {
          reject(new Error('获取微信登录code失败'));
          return;
        }
        
        // 获取用户信息
        uni.getUserInfo({
          provider: 'weixin',
          success: (userInfoRes) => {
            resolve({
              code: loginRes.code,
              userInfo: userInfoRes.userInfo
            });
          },
          fail: (error) => {
            reject(new Error('获取微信用户信息失败: ' + error.errMsg));
          }
        });
      },
      fail: (error) => {
        reject(new Error('微信登录失败: ' + error.errMsg));
      }
    });
  });
};

/**
 * 用户注册
 * @param {Object} data - 注册数据
 * @param {string} data.username - 用户名
 * @param {string} data.email - 邮箱
 * @param {string} data.password - 密码
 * @param {string} data.confirmPassword - 确认密码
 * @returns {Promise} 注册结果Promise
 */
export const register = (data) => {
  return myRequest({
    url: '/portalmember/api.Login/reg',
    method: 'POST',
    data,
    loading: false,
    toast: false
  });
};

/**
 * 刷新token
 * @param {string} refreshToken - 刷新token
 * @returns {Promise} 刷新结果Promise
 */
export const refreshToken = (refreshToken) => {
  return myRequest({
    url: '/portalmember/api.Login/refresh',
    method: 'POST',
    data: { refreshToken },
    auth: false,
    loading: false,
    toast: false
  });
};

/**
 * 用户登出
 * @returns {Promise} 登出结果Promise
 */
export const logout = () => {
  return myRequest({
    url: '/portalmember/api.Login/logout',
    method: 'POST',
    loading: false
  });
};

/**
 * 修改密码
 * @param {Object} data - 密码数据
 * @param {string} data.oldPassword - 旧密码
 * @param {string} data.newPassword - 新密码
 * @returns {Promise} 修改结果Promise
 */
export const changePassword = (data) => {
  return myRequest({
    url: '/portalmember/api.Member/changePassword',
    method: 'POST',
    data: data
  });
};

/**
 * 获取用户信息
 * @returns {Promise} 用户信息Promise
 */
export const getUserInfo = () => {
  return myRequest({
    url: '/portalmember/api.Member/getInfo',
    method: 'POST'
  });
};

/**
 * 更新用户信息
 * @param {Object} data - 用户信息数据
 * @returns {Promise} 更新结果Promise
 */
export const updateUserInfo = (data) => {
  return myRequest({
    url: '/portalmember/api.Member/editInfo',
    method: 'PUT',
    data
  });
};

/**
 * 上传头像
 * @param {string} filePath - 文件路径
 * @returns {Promise} 上传结果Promise
 */
export const uploadAvatar = (filePath) => {

 console.log('BASE_URL');


  console.log(BASE_URL);

  return new Promise((resolve, reject) => {
    const userToken = uni.getStorageSync('user_token') || uni.getStorageSync('token');
    const accessToken = uni.getStorageSync('access_token');
    
    if (!userToken) {
      reject(new Error('用户未登录'));
      return;
    }
    
    uni.uploadFile({
      url: `${BASE_URL}/portalmember/api.Member/avatar`,
      filePath: filePath,
      name: 'avatar',
      formData: {
        user_token: userToken || '',
        access_token: accessToken || ''
      },
      header: {
        'Authorization': `Bearer ${userToken}`
      },
      success: (uploadRes) => {
        try {
          const data = JSON.parse(uploadRes.data);
          // 根据实际API响应结构调整
          if (data.code === 1 || data.code === 0) {
            // 成功响应，返回头像URL
            resolve(data.data || data.url || '');
          } else {
            reject(new Error(data.msg || data.message || '上传失败'));
          }
        } catch (e) {
          console.error('头像上传响应解析失败:', e);
          reject(new Error('上传响应解析失败'));
        }
      },
      fail: (error) => {
        console.error('头像上传失败:', error);
        reject(new Error(error.errMsg || '网络错误，上传失败'));
      }
    });
  });
};

// 导出用户API模块
export default {
  login,
  register,
  refreshToken,
  logout,
  changePassword,
  getUserInfo,
  updateUserInfo,
  uploadAvatar,
  wechatLogin,
  wechatOpenidLogin,
  getWechatUserInfo
};