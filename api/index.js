import request from '@/utils/request'

// 省数据
export const company= data => request.get('/api/company', data)