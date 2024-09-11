seagate 授权认证的查询

curl -O -J "https://seagate.guangz.online/test_man.php?table_name=sg_distributors"
curl -X POST -F "sql_file=@/tmp/sg_distributors_export.sql" https://seagate.guangz.online/test_man.php
