<?php
global $post;

$texts['unsub'] = 'Not interested anymore? <a href="{unsubscription_url}">click here to unsubscribe</a>.';
$texts['header'] = '';

@include(dirname(__FILE__) . '/' . WPLANG . '.php');

query_posts('showposts=5' . nt_option('posts', 5) . '&post_status=publish');
$empty_image = get_option('blogurl') . '/wp-content/plugins/newsletter/themes/custom/empty.gif';
?>

<div style="background:#efefef; border:1px solid #000;">
	<img align="center" style="display:block; margin:0 auto; width:610px; height:181px; text-align:center;" 
	src="<?php echo get_bloginfo('siteurl'); ?>/wp-content/plugins/newsletter-custom/themes/custom/header.png"/>


<table cellspacing=0 cellpadding=5 align="center" width=580px style="background:#fff; margin:30px auto; padding:10px;">
<tbody>
<?php while (have_posts()) { the_post(); ?>
    <th colspan="2" align="left"> 
             <a style="text-decoration:none;" href="<?php echo get_permalink(); ?>">
            <h2 style="color:#54aacd; text-decoration:none; font-size:18px; padding:10px; margin:0; background:#efefef;">
            <?php the_title(); ?>
            </h2>
            </a>
   </th>
   
   <tr>
   		<td valign="middle" style="border-bottom:10px #54aacd solid; margin-bottom:10px; padding:5px 10px;">
   			 <a href="<?php echo get_permalink(); ?>"><img style="border:1px solid #54aacd; padding:5px; background:#efefef;" src="<?php echo catch_that_image(get_the_content()); ?>" width="100" height="100"/></a>
   		</td>
        <td valign="top" align="left" style="border-bottom:10px #54aacd solid; margin-bottom:10px; padding:5px 10px;">           

            <div style="font-size:12px; padding:5px 0;"><?php  
  $content = get_the_excerpt();  
  $postOutput = preg_replace('/<img[^>]+./','', $content);  
  echo $postOutput;  
?></div>
           
            <div style="font-size:10px; margin:5px;">
            	Posted <?php the_time('F j, Y'); ?> -- 
            	<a style="color:#54aacd; text-decoration:none;" href="<?php echo get_permalink(); ?>">Read More</a> 
            </div>
        </td>
    </tr>
    <tr style="height:10px;"></tr>
<?php } ?>
</tbody>
</table>
<div style="width:580px; margin:10px auto; text-align:center; padding:10px 0"><?php echo $texts['unsub']; ?></div>
</div>

<?php wp_reset_query(); ?>