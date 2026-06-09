/**
 * 统一请求函数
 * 封装uni.request，统一处理请求、响应和错误
 */

// 基础配置
// 使用条件编译处理不同平台的URL
export const BASE_URL = (() => {
  // #ifdef H5
  return process.env.NODE_ENV === 'development' 
    ? 'https://jz.demo.07fly.net' // H5开发环境API地址
    : 'https://jz.demo.07fly.net'; // H5生产环境API地址
  // #endif
  
  // #ifdef MP-WEIXIN
  return 'https://jz.demo.07fly.net'; // 小程序环境API地址（三级域名）
  // #endif
  
  // 默认配置
  return 'https://jz.demo.07fly.net';
})();
const TIMEOUT = 10000; // 超时时间：10秒

/**
 * 统一请求函数
 * @param {Object} options - 请求配置
 * @param {string} options.url - 请求地址
 * @param {string} [options.method='GET'] - 请求方法
 * @param {Object} [options.data={}] - 请求数据
 * @param {Object} [options.header={}] - 请求头
 * @param {boolean} [options.loading=true] - 是否显示加载提示
 * @param {boolean} [options.toast=true] - 是否显示错误提示
 * @param {boolean} [options.auth=true] - 是否需要认证
 * @returns {Promise} 请求结果Promise
 */
export const myRequest = (options = {}) => {
  // 默认配置
  const defaultOptions = {
    method: 'GET',
    data: {},
    header: {
      'content-type': 'application/json'
    },
    loading: true,
    toast: true,
    auth: true
  };
  

  // 合并配置
  const mergedOptions = {
    ...defaultOptions,
    ...options,
    header: { ...defaultOptions.header, ...options.header }
  };

  // 处理URL
  // 使用条件编译处理不同平台的URL拼接
  const url = (() => {
    // #ifdef H5
    // H5环境下，开发环境使用相对路径（让代理生效），生产环境使用完整URL
    return /^(http|https):\/\//.test(mergedOptions.url)
      ? mergedOptions.url
      : process.env.NODE_ENV === 'development' 
        ? mergedOptions.url
        : `${BASE_URL}${mergedOptions.url}`;
    // #endif
    
    // #ifdef MP-WEIXIN
    // 小程序环境下，始终使用完整URL
    return /^(http|https):\/\//.test(mergedOptions.url)
      ? mergedOptions.url
      : `${BASE_URL}${mergedOptions.url}`;
    // #endif
    
    // 默认处理
    return /^(http|https):\/\//.test(mergedOptions.url)
      ? mergedOptions.url
      : `${BASE_URL}${mergedOptions.url}`;
  })();

  // 处理认证
  if (mergedOptions.auth) {
    const token = uni.getStorageSync('token');
    if (token) {
      mergedOptions.header.Authorization = `Bearer ${token}`;
    }
  }
  
  
  // 处理认证 - 将token作为参数添加到data中
    if (mergedOptions.auth) {
      const token = uni.getStorageSync('token');
	    const access_token = uni.getStorageSync('access_token');
      if (token) {
        // 添加user_token参数到请求数据
        mergedOptions.data = {
          ...mergedOptions.data,
          user_token: token,
		      access_token:access_token
        };
      }
    }

  // 显示加载提示
  if (mergedOptions.loading) {
    uni.showLoading({
      title: '加载中...',
      mask: true
    });
  }



  // 发起请求
  return new Promise((resolve, reject) => {
    uni.request({
      url,
      method: mergedOptions.method,
      data: mergedOptions.data,
      header: mergedOptions.header,
      timeout: TIMEOUT,
      success: (res) => {
        // 请求成功，处理响应
        const { statusCode, data } = res;
        
        console.log('API Response:', { statusCode, data });

        // 模拟API响应结构
        // 实际项目中根据后端API响应结构调整
        if (statusCode >= 200 && statusCode < 300) {
			
          // 业务状态码处理
          if (data.code === 1 || data.code === '1') {
            resolve(data);
          } else if(data.code === 1000006){
			  uni.showToast({
			    title: '请登录',
			    icon: 'none',
			    duration: 2000
			  });
			  setTimeout(() => {
			    uni.navigateTo({
			      url: '/pages/login/index'
			    });
			  }, 1);
			  
		  }else {
            // 业务错误
            const error = {
              code: data.code,
              message: data.message || data.msg || '请求失败',
              data: data.data
            };
            
            if (mergedOptions.toast) {
              uni.showToast({
                title: error.message,
                icon: 'none',
                duration: 2000
              });
            }
            reject(error);
          }
        } else if (statusCode === 401) {
          // 未授权，清除token并跳转到登录页
          uni.removeStorageSync('token');
          uni.showToast({
            title: '登录已过期，请重新登录',
            icon: 'none',
            duration: 2000
          });
          
          setTimeout(() => {
            uni.navigateTo({
              url: '/pages/login/index'
            });
          }, 1500);
          
          reject({ code: 401, message: '未授权' });
        } else {
          // 其他HTTP错误
          const error = {
            code: statusCode,
            message: `请求错误(${statusCode})`,
            data: data
          };
          
          if (mergedOptions.toast) {
            uni.showToast({
              title: error.message,
              icon: 'none',
              duration: 2000
            });
          }
          
          reject(error);
        }
      },
      fail: (err) => {
        // 请求失败
        const error = {
          code: -1,
          message: err.errMsg || '网络异常',
          data: err
        };
        
        if (mergedOptions.toast) {
          uni.showToast({
            title: error.message,
            icon: 'none',
            duration: 2000
          });
        }
        reject(error);
      },
      complete: () => {
        // 隐藏加载提示
        if (mergedOptions.loading) {
          uni.hideLoading();
        }
      }
    });
  });
};

/**
 * 模拟请求函数 - 用于开发阶段模拟API响应
 * @param {Object} options - 请求配置
 * @param {Object} mockData - 模拟数据
 * @param {number} [delay=500] - 延迟时间(毫秒)
 * @returns {Promise} 请求结果Promise
 */
export const mockRequest = (options = {}, mockData = {}, delay = 500) => {
  const defaultOptions = {
    loading: true,
    toast: true
  };

  const mergedOptions = { ...defaultOptions, ...options };

  if (mergedOptions.loading) {
    uni.showLoading({
      title: '加载中...',
      mask: true
    });
  }

  return new Promise((resolve) => {
    setTimeout(() => {
      if (mergedOptions.loading) {
        uni.hideLoading();
      }
      resolve(mockData);
    }, delay);
  });
};

export default myRequest;