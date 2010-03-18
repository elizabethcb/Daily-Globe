create or replace view mynewview as
select 'wp_6_options' as topictbl
, wblog.option_value as blogname
,wsite.option_value as siteurl
from wp_6_options as wblog, wp_6_options wsite
where wblog.option_name='blogname' AND wsite.option_name = 'siteurl'
UNION ALL
select 'wp_8_options' as topictbl
    ,wblog.option_value as blogname
    ,wsite.option_value as siteurl
from wp_8_options as wblog, wp_8_options wsite
where wblog.option_name='blogname' AND wsite.option_name = 'siteurl';
