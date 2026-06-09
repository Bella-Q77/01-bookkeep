/**
 * 配置文件 - 管理不同环境的API配置
 */

// 环境配置
const ENV_CONFIG = {
  // 开发环境
  development: {
    baseURL: '/api', // H5开发环境使用代理路径
    sslVerify: false
  },
  // 测试环境
  test: {
    baseURL: 'https://jz.demo.07fly.net',
    sslVerify: false
  },
  // 生产环境
  production: {
    baseURL: 'https://jz.demo.07fly.net',
    sslVerify: true
  }
};

// 获取当前环境配置
const getCurrentEnv = () => {
  // #ifdef H5
  return process.env.NODE_ENV || 'development';
  // #endif
  
  // #ifdef MP-WEIXIN
  // 小程序环境判断
  const accountInfo = uni.getAccountInfoSync();
  const version = accountInfo.miniProgram?.envVersion;
  
  switch (version) {
    case 'develop': // 开发版
      return 'development';
    case 'trial':   // 体验版
      return 'test';
    case 'release': // 正式版
      return 'production';
    default:
      return 'development';
  }
  // #endif
  
  return 'development';
};

// 获取配置
export const getConfig = () => {
  const env = getCurrentEnv();
  return ENV_CONFIG[env] || ENV_CONFIG.development;
};

// 备用域名列表（用于网络故障时切换）
export const BACKUP_DOMAINS = [
  'https://jz.demo.07fly.net',
  'https://jz.07fly.net'
];

// 小程序合法域名配置（不含协议和路径）
export const VALID_DOMAINS = [
  'jz.demo.07fly.net',
  'jz.07fly.net'
];

export default {
  getConfig,
  BACKUP_DOMAINS
};