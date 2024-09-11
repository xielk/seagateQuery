import request from '@/utils/request'

// 省数据
export const company= data => request.get('/fansclub/company.php', data)