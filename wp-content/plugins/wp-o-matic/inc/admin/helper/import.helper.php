<?php

  function import_process_tree($opml, $campaigns) {
    import_process_item($opml, $campaigns);
  }                                          
  
  function import_process_item($opml, $campaigns, $count = 0) {   
  
    $i = 0;
    
    foreach($opml['OUTLINE'] as $key => $item)  
    {          
      ?>
      <li class="<?php echo $i % 2 ? 'even' : 'odd' ?>">
      <?php
      if(isset($item['OUTLINE'])) {
      	// Campaign Category
        $count++;
        ?>
        <?php echo input_hidden_tag('campaign['.$count.']', import_get_item_title($item)) ?>
        <div class="check">
          <a href="#">(un)check all</a>
        </div>
        <h4><?php echo import_get_item_title($item) ?></h4> 
        <span>Existing Campaign?:</span>
        <?php echo select_tag('use_this_campaign-'.$count, options_for_select($campaigns)) ?>
        <span>Category</span><?php wp_dropdown_categories(array('name'=> 'cat['.$count.']')); ?>
        <ul>
          <?php import_process_item($item, $campaigns, $count) ?>
        </ul>
        <?php   
      } else {
      	// Item
        $url = urlencode(import_get_item_xmlurl($item));
        
        if(!$count)
        {
          $count++;
          echo input_hidden_tag('campaign['.$count.']', import_get_item_title($item));
        }
        
        if($url):
      ?>
        <ul class="import_links">
          <li><?php echo checkbox_tag('feed['.$count.']['.$url.']', 1, (isset($_REQUEST['add']) ? (isset($_REQUEST['feed']) ? _data_value($_REQUEST['feed'][$count], $url) : false) : true )) ?></li>          
          <li><a class="feed_rss" href="<?php echo import_get_item_xmlurl($item) ?>">RSS</a></li>          
          <?php if(import_get_item_htmlurl($item)): ?>
          <li><a class="feed_link" href="<?php echo import_get_item_htmlurl($item) ?>">Website</a></li>
          <?php endif; ?>
        </ul>
        <h4><label for="feed_<?php echo $count ?>_<?php echo $url ?>"><?php echo import_get_item_title($item) ?></label></h4>
      <?php 
        endif;
      }       
                                          
      echo '</li>'; 
      $i++;                                 
    }              
  }
  
  function import_get_item_title($item)
  {
  	if ($item['ATTRIBUTES']['TEXT'] == 'feed' )
  		return import_get_item_xmlurl($item);
    return $item['ATTRIBUTES']['TEXT'];
  }
  
  function import_get_item_xmlurl($item) 
  {
    return $item['ATTRIBUTES']['XMLURL'];
  }

  function import_get_item_htmlurl($item) 
  {
    return $item['ATTRIBUTES']['HTMLURL'];
  }


?>